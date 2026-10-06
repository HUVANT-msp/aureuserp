<?php

namespace Huvant\Orders\Support;

use Carbon\CarbonInterface;
use Filament\Notifications\Notification;
use Huvant\Orders\Enums\ClosingState;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Enums\ProductionStatus;
use Huvant\Orders\Enums\ProductionTaskStatus;
use Huvant\Orders\Models\Delivery;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Models\OrderLine;
use Huvant\Orders\Settings\OrdersSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Manufacturing\Enums\BillOfMaterialType;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Facades\Manufacturing;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\Move;
use Webkul\Manufacturing\Models\Order as ManufacturingOrder;
use Webkul\Manufacturing\Models\Warehouse;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

class Orders
{
    public static function isAdmin(?User $user = null): bool
    {
        $user ??= Auth::user();

        return $user instanceof User
            && $user->roles()->get()->contains(fn (Role $role): bool => $role->isSystemRole());
    }

    /** Prices, discounts, totals and costs are for administrators only. */
    public static function canSeePrices(?User $user = null): bool
    {
        return static::isAdmin($user);
    }

    /**
     * Opens a new offer, numbered OFF-YYYYMMDD-NNN.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function open(array $attributes): Order
    {
        $offerDate = Carbon::parse($attributes['offer_date'] ?? today());

        return DB::transaction(fn (): Order => Order::create(array_merge($attributes, [
            'offer_date' => $offerDate->toDateString(),
            'name'       => DocumentNumber::next(DocumentNumber::OFFER, $offerDate),
            'state'      => OrderState::Draft,
        ])));
    }

    public static function send(Order $order): Order
    {
        static::guard($order, [OrderState::Draft], 'Only a draft offer can be sent.');
        $order->update(['state' => OrderState::Sent]);

        return $order;
    }

    /** On hold: the customer has not confirmed yet, but production can already see it. */
    public static function hold(Order $order): Order
    {
        static::guard($order, [OrderState::Draft, OrderState::Sent], 'Only an offer not yet confirmed can be put on hold.');
        $order->update(['state' => OrderState::Pending]);

        return $order;
    }

    /** The offer becomes an order and enters the shared manufacturing inbox. */
    public static function confirm(Order $order, bool $allowOverbooking = false): Order
    {
        static::guard($order, [OrderState::Draft, OrderState::Sent, OrderState::Pending], 'This offer can no longer be confirmed.');

        if ($order->fulfilment->needsCustomer() && ! $order->partner_id) {
            throw new RuntimeException('Choose the customer before confirming.');
        }

        if ($order->lines()->doesntExist()) {
            throw new RuntimeException('Add at least one product before confirming.');
        }

        if (Rentals::hasRentals($order) && (! $order->rental_starts_on || ! $order->rental_ends_on)) {
            throw new RuntimeException('Set the rental period before confirming.');
        }

        if (! $allowOverbooking && ($conflicts = Rentals::conflicts($order))) {
            throw new RuntimeException('Not enough units for rent. '.implode('; ', $conflicts).'.');
        }

        $confirmed = DB::transaction(function () use ($order): Order {
            $order->update([
                'state'        => OrderState::Confirmed,
                'confirmed_at' => now(),
                'order_number' => $order->order_number ?? DocumentNumber::next(DocumentNumber::ORDER, now()),
            ]);

            if ($order->deliveries()->doesntExist()) {
                Shipping::createDelivery($order);
            }

            return $order->refresh();
        });

        static::notifyManufacturing($confirmed);

        return $confirmed;
    }

    public static function reject(Order $order): Order
    {
        static::guard($order, [OrderState::Draft, OrderState::Sent, OrderState::Pending], 'Only an offer not yet confirmed can be rejected.');
        $order->update(['state' => OrderState::Rejected]);

        return $order;
    }

    /** Offers past their validity date that the customer never answered. */
    public static function expireOverdue(?CarbonInterface $today = null): int
    {
        return Order::query()
            ->whereIn('state', [OrderState::Draft, OrderState::Sent])
            ->whereNotNull('validity_date')
            ->whereDate('validity_date', '<', ($today ?? today())->toDateString())
            ->update(['state' => OrderState::Expired]);
    }

    /** Cancels the order and the manufacturing orders the lab has not finished. */
    public static function cancel(Order $order): Order
    {
        static::guard($order, [OrderState::Draft, OrderState::Sent, OrderState::Pending, OrderState::Confirmed], 'This order is already closed.');

        DB::transaction(function () use ($order): void {
            ManufacturingFlow::release($order);

            static::manufacturingOrders($order)
                ->reject(fn (ManufacturingOrder $mo): bool => in_array($mo->state, [ManufacturingOrderState::DONE, ManufacturingOrderState::CANCEL], true))
                ->each(fn (ManufacturingOrder $mo) => Manufacturing::cancelManufacturingOrder($mo));

            $order->deliveries()
                ->whereNotIn('state', [OperationState::DONE, OperationState::CANCELED])
                ->get()
                ->each(fn (Delivery $delivery) => Inventory::cancelTransfer(Operation::query()->findOrFail($delivery->id)));

            $order->update(['state' => OrderState::Cancelled]);
        });

        return $order;
    }

    public static function close(Order $order, ClosingState $closingState = ClosingState::Closed): Order
    {
        static::guard($order, [OrderState::Confirmed], 'Only a confirmed order can be closed.');
        $order->update(['closing_state' => $closingState]);

        return $order;
    }

    /**
     * What the order costs and earns, for administrators: components at their cost (consumed when
     * production is done, planned before), lab hours at the hourly cost, other lines at product cost,
     * and shipping.
     *
     * @return array{revenue: float, materials: float, labour: float, other: float, shipping: float, cost: float, margin: float, margin_percent: ?float}
     */
    public static function costing(Order $order): array
    {
        $hourlyCost = app(OrdersSettings::class)->hourly_cost;
        $materials = 0.0;
        $labour = 0.0;
        $other = 0.0;

        foreach ($order->lines()->with(['product', 'manufacturingOrder.rawMaterialMoves.product'])->get() as $line) {
            $mo = $line->manufacturingOrder;

            if (! $mo || $mo->state === ManufacturingOrderState::CANCEL) {
                $other += (float) $line->quantity * (float) $line->product?->cost;

                continue;
            }

            foreach ($mo->rawMaterialMoves as $move) {
                $quantity = $mo->state === ManufacturingOrderState::DONE ? (float) $move->quantity : (float) $move->product_uom_qty;
                $materials += $quantity * (float) $move->product?->cost;
            }

            $labour += (float) $mo->huvant_worked_hours * $hourlyCost;
        }

        $shipping = (float) $order->deliveries()->sum('huvant_shipping_cost');
        $revenue = $order->untaxedAmount();
        $cost = round($materials + $labour + $other + $shipping, 2);

        return [
            'revenue'        => $revenue,
            'materials'      => round($materials, 2),
            'labour'         => round($labour, 2),
            'other'          => round($other, 2),
            'shipping'       => round($shipping, 2),
            'cost'           => $cost,
            'margin'         => round($revenue - $cost, 2),
            'margin_percent' => $revenue > 0 ? round(($revenue - $cost) / $revenue * 100, 1) : null,
        ];
    }

    /** @return Collection<int, ManufacturingOrder> */
    public static function manufacturingOrders(Order $order): Collection
    {
        $ids = $order->lines()->whereNotNull('manufacturing_order_id')->pluck('manufacturing_order_id');

        return ManufacturingOrder::query()->whereIn('id', $ids)->get();
    }

    public static function productionStatus(Order $order): ProductionStatus
    {
        if ($order->state === OrderState::Cancelled) {
            return ProductionStatus::None;
        }

        if ($order->manufacturing_managed_at) {
            $tasks = $order->productionTasks()->get();

            if ($tasks->isEmpty() || $tasks->every(fn ($task): bool => $task->status === ProductionTaskStatus::Completed)) {
                return ProductionStatus::Done;
            }

            if ($order->expected_delivery_date?->endOfDay()->isPast()) {
                return ProductionStatus::Late;
            }

            if ($tasks->contains(fn ($task): bool => $task->completed_quantity > 0)) {
                return ProductionStatus::InProgress;
            }

            return ProductionStatus::ToStart;
        }

        $orders = static::manufacturingOrders($order)
            ->reject(fn (ManufacturingOrder $mo): bool => $mo->state === ManufacturingOrderState::CANCEL);

        if ($orders->isEmpty()) {
            return $order->state === OrderState::Confirmed ? ProductionStatus::NotTakenOn : ProductionStatus::None;
        }

        if ($orders->every(fn (ManufacturingOrder $mo): bool => $mo->state === ManufacturingOrderState::DONE)) {
            return ProductionStatus::Done;
        }

        $late = $orders->contains(fn (ManufacturingOrder $mo): bool => $mo->state !== ManufacturingOrderState::DONE
            && $mo->deadline_at
            && $mo->deadline_at->endOfDay()->isPast());

        if ($late) {
            return ProductionStatus::Late;
        }

        if ($orders->contains(fn (ManufacturingOrder $mo): bool => in_array($mo->state, [ManufacturingOrderState::PROGRESS, ManufacturingOrderState::TO_CLOSE, ManufacturingOrderState::DONE], true))) {
            return ProductionStatus::InProgress;
        }

        if ($orders->contains(fn (ManufacturingOrder $mo): bool => $mo->state === ManufacturingOrderState::DRAFT)) {
            return ProductionStatus::NotTakenOn;
        }

        return ProductionStatus::ToStart;
    }

    /** A draft manufacturing order for the line, with the components of its bill of materials. */
    protected static function raiseManufacturingOrder(Order $order, OrderLine $line): ?ManufacturingOrder
    {
        $product = $line->product;

        $billOfMaterial = BillOfMaterial::bomFindFilters(collect([$product]), null, $order->company_id, BillOfMaterialType::NORMAL)
            ->orderBy('id')
            ->first();

        if (! $billOfMaterial) {
            return null;
        }

        $warehouse = Warehouse::query()
            ->when($order->company_id, fn ($query) => $query->where('company_id', $order->company_id))
            ->whereNotNull('manu_type_id')
            ->orderBy('id')
            ->first();

        if (! $warehouse) {
            throw new RuntimeException('No warehouse is set up for manufacturing.');
        }

        $operationType = OperationType::findOrFail($warehouse->manu_type_id);

        $manufacturingOrder = ManufacturingOrder::create([
            'state'                   => ManufacturingOrderState::DRAFT,
            'consumption'             => $billOfMaterial->consumption,
            'product_id'              => $product->id,
            'uom_id'                  => $product->uom_id,
            'bill_of_material_id'     => $billOfMaterial->id,
            'quantity'                => $line->quantity,
            'quantity_producing'      => 0,
            'origin'                  => $order->order_number,
            'deadline_at'             => $order->expected_delivery_date,
            'operation_type_id'       => $operationType->id,
            'source_location_id'      => $operationType->source_location_id,
            'destination_location_id' => $operationType->destination_location_id,
            'company_id'              => $order->company_id ?? $warehouse->company_id,
        ]);

        $manufacturingOrder->refresh()->computeFinishedMoves();

        foreach ($manufacturingOrder->getMovesRawValues() as $values) {
            Move::create($values);
        }

        $line->update(['manufacturing_order_id' => $manufacturingOrder->id]);

        return $manufacturingOrder;
    }

    protected static function notifyManufacturing(Order $order): void
    {
        $users = User::query()->where('is_active', true)->get();

        if ($users->isEmpty()) {
            return;
        }

        Notification::make()
            ->title(__('huvant-orders::manufacturing.notification_title'))
            ->body(__('huvant-orders::manufacturing.notification_body', ['order' => $order->order_number]))
            ->icon('heroicon-o-wrench-screwdriver')
            ->iconColor('warning')
            ->sendToDatabase($users);
    }

    /** @param  array<int, OrderState>  $allowed */
    protected static function guard(Order $order, array $allowed, string $message): void
    {
        if (! in_array($order->state, $allowed, true)) {
            throw new RuntimeException($message);
        }
    }
}
