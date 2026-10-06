<?php

namespace Huvant\Orders\Support;

use Carbon\CarbonInterface;
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Enums\ProductionTaskStatus;
use Huvant\Orders\Enums\UnitStatus;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Models\OrderLine;
use Huvant\Orders\Models\ProductionTask;
use Huvant\Orders\Models\ProductUnit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

/** Turns an accepted offer into traceable stock assignments and production work. */
class ManufacturingFlow
{
    public static function stockAvailable(Product|int $product): int
    {
        return ProductUnit::query()
            ->where('product_id', $product instanceof Product ? $product->getKey() : $product)
            ->where('status', UnitStatus::InLab)
            ->count();
    }

    /** @return Collection<int, OrderLine> */
    public static function productLines(Order $order): Collection
    {
        return $order->lines()
            ->with('product')
            ->whereHas('product', fn ($query) => $query->where('huvant_role', ItemRole::Product->value))
            ->get();
    }

    /**
     * @param  array<int|string, int|string|null>  $stockQuantities  Order line id => pieces from stock.
     */
    public static function manage(Order $order, array $stockQuantities, ?User $user = null): Order
    {
        $user ??= Auth::user();

        if ($order->state !== OrderState::Confirmed) {
            throw new RuntimeException(__('huvant-orders::manufacturing.error_only_confirmed'));
        }

        if ($order->manufacturing_managed_at) {
            throw new RuntimeException(__('huvant-orders::manufacturing.error_already_managed'));
        }

        $managed = DB::transaction(function () use ($order, $stockQuantities, $user): Order {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->getKey());

            if ($lockedOrder->manufacturing_managed_at) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_already_managed'));
            }

            foreach (static::productLines($lockedOrder) as $line) {
                $requested = static::pieces($line);
                $fromStock = (int) ($stockQuantities[$line->getKey()] ?? 0);

                if ($fromStock < 0 || $fromStock > $requested) {
                    throw new RuntimeException(__('huvant-orders::manufacturing.error_stock_range', ['product' => $line->product->name]));
                }

                $units = ProductUnit::query()
                    ->where('product_id', $line->product_id)
                    ->where('status', UnitStatus::InLab)
                    ->orderByRaw('expiry_date is null, expiry_date')
                    ->orderBy('production_date')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->limit($fromStock)
                    ->get();

                if ($units->count() !== $fromStock) {
                    throw new RuntimeException(__('huvant-orders::manufacturing.error_stock_changed', [
                        'product'   => $line->product->name,
                        'available' => $units->count(),
                    ]));
                }

                foreach ($units as $unit) {
                    $unit->update([
                        'status'        => UnitStatus::Allocated,
                        'status_since'  => today(),
                        'order_id'      => $lockedOrder->getKey(),
                        'order_line_id' => $line->getKey(),
                    ]);
                }

                $toProduce = $requested - $fromStock;

                if ($toProduce > 0) {
                    ProductionTask::query()->create([
                        'order_id'      => $lockedOrder->getKey(),
                        'order_line_id' => $line->getKey(),
                        'product_id'    => $line->product_id,
                        'quantity'      => $toProduce,
                        'managed_by'    => $user?->getKey(),
                    ]);
                }

                $line->update([
                    'stock_quantity'           => $fromStock,
                    'production_quantity'      => $toProduce,
                    'manufacturing_managed_at' => now(),
                    'manufacturing_managed_by' => $user?->getKey(),
                ]);
            }

            $lockedOrder->update([
                'manufacturing_managed_at' => now(),
                'manufacturing_managed_by' => $user?->getKey(),
            ]);

            return $lockedOrder->refresh();
        });

        $order->setRawAttributes($managed->getAttributes(), true);

        return $order;
    }

    /** @return Collection<int, ProductUnit> */
    public static function completeProduction(ProductionTask $task, int $pieces, CarbonInterface $date, array $lots, ?string $notes = null, ?User $user = null): Collection
    {
        $user ??= Auth::user();

        return DB::transaction(function () use ($task, $pieces, $date, $lots, $notes, $user): Collection {
            $lockedTask = ProductionTask::query()->with(['product', 'orderLine'])->lockForUpdate()->findOrFail($task->getKey());

            if ($lockedTask->status !== ProductionTaskStatus::Pending || $pieces < 1 || $pieces > $lockedTask->remaining()) {
                throw new RuntimeException(__('huvant-orders::manufacturing.error_production_quantity'));
            }

            $units = LabInventory::produce($lockedTask->product, $date, $pieces, $lots, $notes);

            foreach ($units as $unit) {
                $unit->update([
                    'status'        => UnitStatus::Allocated,
                    'status_since'  => $date->toDateString(),
                    'order_id'      => $lockedTask->order_id,
                    'order_line_id' => $lockedTask->order_line_id,
                ]);
            }

            $completed = $lockedTask->completed_quantity + $pieces;
            $done = $completed >= $lockedTask->quantity;

            $lockedTask->update([
                'completed_quantity' => $completed,
                'status'             => $done ? ProductionTaskStatus::Completed : ProductionTaskStatus::Pending,
                'completed_at'       => $done ? now() : null,
                'managed_by'         => $user?->getKey(),
                'notes'              => $notes ?: $lockedTask->notes,
            ]);

            return $units;
        });
    }

    public static function release(Order $order): void
    {
        ProductUnit::query()
            ->where('order_id', $order->getKey())
            ->where('status', UnitStatus::Allocated)
            ->update([
                'status'        => UnitStatus::InLab->value,
                'status_since'  => today(),
                'order_id'      => null,
                'order_line_id' => null,
            ]);

        $order->productionTasks()
            ->where('status', '!=', ProductionTaskStatus::Cancelled->value)
            ->update(['status' => ProductionTaskStatus::Cancelled->value]);
    }

    protected static function pieces(OrderLine $line): int
    {
        $quantity = (float) $line->quantity;
        $pieces = (int) round($quantity);

        if ($pieces < 1 || abs($quantity - $pieces) > 0.00001) {
            throw new RuntimeException(__('huvant-orders::manufacturing.error_whole_pieces', ['product' => $line->product->name]));
        }

        return $pieces;
    }
}
