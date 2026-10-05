<?php

namespace Huvant\Orders\Support;

use Carbon\CarbonInterface;
use Huvant\Orders\Enums\Fulfilment;
use Huvant\Orders\Enums\ReturnStatus;
use Huvant\Orders\Enums\ShippingStatus;
use Huvant\Orders\Models\Delivery;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Models\OrderLine;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Warehouse;

class Shipping
{
    /**
     * The outgoing transfer for the goods of a confirmed order. Services and products without
     * stock tracking do not ship; production for stock ships nothing.
     */
    public static function createDelivery(Order $order): ?Delivery
    {
        if ($order->fulfilment === Fulfilment::Stock) {
            return null;
        }

        $lines = $order->lines()->with('product')->get()
            ->filter(fn (OrderLine $line): bool => (bool) $line->product?->is_storable);

        if ($lines->isEmpty()) {
            return null;
        }

        $warehouse = static::warehouse($order->company_id);

        $operationType = $warehouse->outType;
        $recipientId = $order->delivery_address_id ?? $order->partner_id;

        $delivery = Delivery::create([
            'huvant_order_id'         => $order->id,
            'huvant_shipping_status'  => ShippingStatus::ToShip,
            'origin'                  => $order->order_number,
            'partner_id'              => $recipientId,
            'operation_type_id'       => $operationType->id,
            'source_location_id'      => $operationType->source_location_id,
            'destination_location_id' => $operationType->destination_location_id,
            'move_type'               => MoveType::DIRECT,
            'scheduled_at'            => $order->expected_delivery_date ?? now(),
            'company_id'              => $order->company_id ?? $warehouse->company_id,
        ]);

        foreach ($lines as $line) {
            Move::create([
                'name'                    => $line->product->name,
                'state'                   => MoveState::DRAFT,
                'origin'                  => $order->order_number,
                'product_id'              => $line->product_id,
                'uom_id'                  => $line->product->uom_id,
                'product_uom_qty'         => $line->quantity,
                'quantity'                => 0,
                'partner_id'              => $recipientId,
                'operation_id'            => $delivery->id,
                'operation_type_id'       => $operationType->id,
                'source_location_id'      => $delivery->source_location_id,
                'destination_location_id' => $delivery->destination_location_id,
                'warehouse_id'            => $warehouse->id,
                'scheduled_at'            => $delivery->scheduled_at,
                'company_id'              => $delivery->company_id,
            ]);
        }

        Inventory::confirmTransfer($delivery->refresh());

        return $delivery->refresh();
    }

    /** The company's main warehouse: the first one set up, where finished goods are stocked. */
    public static function warehouse(?int $companyId): Warehouse
    {
        return Warehouse::query()
            ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->whereNotNull('out_type_id')
            ->orderBy('id')
            ->first() ?? throw new RuntimeException('No warehouse is set up for deliveries.');
    }

    /**
     * Called when an inventory transfer changes. Once a delivery of an order is validated it gets
     * its delivery note number and ship date; once a return is validated the delivery is marked returned.
     */
    public static function syncTransfer(Operation $operation): void
    {
        $delivery = Delivery::query()->find($operation->getKey());

        if (! $delivery?->huvant_order_id || $delivery->state !== OperationState::DONE) {
            return;
        }

        if ($delivery->isReturn()) {
            Delivery::query()->whereKey($delivery->return_id)->update(['huvant_shipping_status' => ShippingStatus::Returned->value]);

            return;
        }

        if ($delivery->huvant_delivery_note_number) {
            return;
        }

        $shippedAt = $delivery->closed_at ?? now();

        // Quiet update: this runs inside the transfer's own save.
        Delivery::query()->whereKey($delivery->id)->update([
            'huvant_delivery_note_number' => DocumentNumber::next(DocumentNumber::DELIVERY_NOTE, $shippedAt),
            'huvant_shipped_at'           => $shippedAt->toDateString(),
            'huvant_shipping_status'      => ShippingStatus::InTransit->value,
        ]);
    }

    public static function markDelivered(Delivery $delivery, ?CarbonInterface $deliveredAt = null): Delivery
    {
        if ($delivery->state !== OperationState::DONE) {
            throw new RuntimeException('Validate the transfer before marking it delivered.');
        }

        $delivery->update([
            'huvant_delivered_at'    => ($deliveredAt ?? today())->toDateString(),
            'huvant_shipping_status' => ShippingStatus::Delivered,
        ]);

        return $delivery;
    }

    /** Goods back from a loan, an approval or a sample: a return transfer with every unit shipped. */
    public static function registerReturn(Delivery $delivery): Delivery
    {
        if ($delivery->state !== OperationState::DONE || $delivery->isReturn()) {
            throw new RuntimeException('Only a validated delivery can come back.');
        }

        if ($delivery->returns()->where('state', '!=', OperationState::CANCELED)->exists()) {
            throw new RuntimeException('A return is already registered for this delivery.');
        }

        return DB::transaction(function () use ($delivery): Delivery {
            $quantities = $delivery->moves()
                ->where('state', MoveState::DONE)
                ->get()
                ->mapWithKeys(fn (Move $move): array => [$move->id => (float) $move->quantity])
                ->all();

            $return = Inventory::createReturn(Operation::query()->findOrFail($delivery->id), $quantities);

            Delivery::query()->whereKey($return->id)->update([
                'huvant_order_id'        => $delivery->huvant_order_id,
                'huvant_shipping_status' => ShippingStatus::ToShip->value,
            ]);

            return Delivery::query()->findOrFail($return->id);
        });
    }

    /** Only for supply types whose goods come back. */
    public static function returnStatus(Order $order): ?ReturnStatus
    {
        if (! $order->supply_type->isReturnable()) {
            return null;
        }

        $shipped = $order->deliveries()->whereNull('return_id')->where('state', OperationState::DONE)->get();

        if ($shipped->isEmpty()) {
            return ReturnStatus::NotShipped;
        }

        if ($shipped->every(fn (Delivery $delivery): bool => $delivery->huvant_shipping_status === ShippingStatus::Returned)) {
            return ReturnStatus::Returned;
        }

        return $order->expected_return_date?->isBefore(today()) ? ReturnStatus::Overdue : ReturnStatus::Out;
    }

    /** Lot names per product shipped, for the delivery note. @return array<int, array<int, string>> */
    public static function lotsByProduct(Delivery $delivery): array
    {
        return $delivery->moves()->with('lines.lot')->get()
            ->groupBy('product_id')
            ->map(fn ($moves): array => $moves->flatMap->lines
                ->map(fn ($line): ?string => $line->lot?->name ?? $line->lot_name)
                ->filter()
                ->unique()
                ->values()
                ->all())
            ->all();
    }
}
