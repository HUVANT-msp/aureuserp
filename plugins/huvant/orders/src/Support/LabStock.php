<?php

namespace Huvant\Orders\Support;

use Carbon\CarbonInterface;
use Huvant\Orders\Settings\OrdersSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\OrderPointTrigger;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OrderPoint;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;

/**
 * The lab inventory that lived in "Inventario Laboratorio", as two stores. Production stock is
 * tracked to the gram: goods in are loaded, manufacturing orders consume it. R&D gets portions moved
 * over from production and is not tracked use by use: the lab only says what is left, or that it is
 * finished. Quantities are always in the item's main unit (see LabUnits).
 */
class LabStock
{
    public const PRODUCTION = 'production';

    public const RESEARCH = 'research';

    /** Production stock is the warehouse stock; R&D sits beside it, outside what manufacturing reserves from. */
    public static function location(Warehouse $warehouse, string $use): Location
    {
        if ($use === self::PRODUCTION) {
            return $warehouse->lotStockLocation;
        }

        return Location::query()->firstOrCreate(
            ['name' => 'R&D', 'parent_id' => $warehouse->view_location_id, 'company_id' => $warehouse->company_id],
            ['type' => LocationType::INTERNAL],
        );
    }

    public static function onHand(Product $product, Location $location): float
    {
        return (float) ProductQuantity::query()
            ->where('product_id', $product->id)
            ->whereHas('location', fn ($query) => $query->where('parent_path', 'like', $location->parent_path.'%'))
            ->sum('quantity');
    }

    /** The minimum to keep: the lowest reordering rule set on the product. */
    public static function minimum(Product $product): ?float
    {
        $minimum = OrderPoint::query()->where('product_id', $product->id)->min('product_min_qty');

        return $minimum === null ? null : (float) $minimum;
    }

    public static function setMinimum(Product $product, Warehouse $warehouse, float $minimum): void
    {
        OrderPoint::query()->updateOrCreate(
            ['product_id' => $product->id, 'warehouse_id' => $warehouse->id],
            [
                'name'            => 'Lab minimum '.$product->name,
                'trigger'         => OrderPointTrigger::MANUAL,
                'location_id'     => $warehouse->lot_stock_location_id,
                'product_min_qty' => $minimum,
                'product_max_qty' => $minimum,
                'company_id'      => $warehouse->company_id,
            ],
        );
    }

    /**
     * Lots still in stock that expire within the warning window (or have expired).
     *
     * @return Collection<int, Lot>
     */
    public static function expiringLots(?Product $product = null, ?CarbonInterface $today = null): Collection
    {
        $limit = ($today ?? today())->copy()->addDays(app(OrdersSettings::class)->expiry_warning_days);

        return Lot::query()
            ->when($product, fn ($query) => $query->where('product_id', $product->id))
            ->whereNotNull('expiration_date')
            ->where('expiration_date', '<=', $limit->endOfDay())
            ->whereHas('quantities', fn ($query) => $query->where('quantity', '>', 0)
                ->whereHas('location', fn ($query) => $query->where('type', LocationType::INTERNAL)))
            ->orderBy('expiration_date')
            ->get();
    }

    /**
     * Goods in: units into production or R&D, for lot-tracked items under a lot with its supplier
     * reference and expiry date.
     */
    public static function load(Product $product, Location $location, float $quantity, ?string $lotName = null, ?string $supplierLot = null, ?CarbonInterface $expiresOn = null): ProductQuantity
    {
        if ($quantity <= 0) {
            throw new RuntimeException('The quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($product, $location, $quantity, $lotName, $supplierLot, $expiresOn): ProductQuantity {
            $lot = null;

            if ($product->tracking !== ProductTracking::QTY) {
                if (blank($lotName)) {
                    throw new RuntimeException('This item is tracked by lot: give the lot.');
                }

                $lot = Lot::query()->firstOrCreate(
                    ['name' => $lotName, 'product_id' => $product->id],
                    ['uom_id' => $product->uom_id, 'company_id' => $location->company_id],
                );
                $lot->fill(array_filter(['reference' => $supplierLot, 'expiration_date' => $expiresOn?->endOfDay()]))->save();
            }

            return static::adjust($product, $location, $lot, $quantity);
        });
    }

    /**
     * Hands a portion of production stock over to R&D, as an internal transfer of the chosen lot.
     */
    public static function moveToResearch(Product $product, Warehouse $warehouse, float $quantity, ?Lot $lot = null): Operation
    {
        $production = static::location($warehouse, self::PRODUCTION);
        $research = static::location($warehouse, self::RESEARCH);

        static::assertAvailable($product, $production, $quantity, $lot);

        return DB::transaction(function () use ($product, $warehouse, $quantity, $lot, $production, $research): Operation {
            $operation = Operation::query()->create([
                'operation_type_id'       => $warehouse->internal_type_id,
                'source_location_id'      => $production->id,
                'destination_location_id' => $research->id,
                'move_type'               => MoveType::DIRECT,
                'origin'                  => 'To R&D',
                'scheduled_at'            => now(),
                'company_id'              => $warehouse->company_id,
            ]);

            $move = Move::query()->create([
                'name'                    => $product->name,
                'state'                   => MoveState::DRAFT,
                'product_id'              => $product->id,
                'uom_id'                  => $product->uom_id,
                'product_uom_qty'         => $quantity,
                'quantity'                => 0,
                'operation_id'            => $operation->id,
                'operation_type_id'       => $warehouse->internal_type_id,
                'source_location_id'      => $production->id,
                'destination_location_id' => $research->id,
                'warehouse_id'            => $warehouse->id,
                'company_id'              => $warehouse->company_id,
            ]);

            Inventory::confirmTransfer($operation->refresh());

            // The detailed line names the lot the lab picked (reservation alone would pick the oldest).
            Inventory::releaseMoves(collect([$move->refresh()]));
            $move->refresh()->lines()->delete();
            MoveLine::query()->create([
                'move_id'                 => $move->id,
                'operation_id'            => $operation->id,
                'product_id'              => $product->id,
                'uom_id'                  => $product->uom_id,
                'lot_id'                  => $lot?->id,
                'qty'                     => $quantity,
                'uom_qty'                 => $quantity,
                'is_picked'               => true,
                'source_location_id'      => $production->id,
                'destination_location_id' => $research->id,
                'company_id'              => $warehouse->company_id,
            ]);
            $move->update(['quantity' => $quantity, 'is_picked' => true]);

            return Inventory::completeTransfer($operation->refresh());
        });
    }

    /** What R&D says is left of a lot (0 when it is finished): the rest is written off as used. */
    public static function setResearchRemaining(Product $product, Warehouse $warehouse, float $remaining, ?Lot $lot = null): void
    {
        $research = static::location($warehouse, self::RESEARCH);
        $current = (float) ProductQuantity::query()
            ->where(['product_id' => $product->id, 'location_id' => $research->id, 'lot_id' => $lot?->id])
            ->value('quantity');

        if ($remaining < 0 || $remaining > $current) {
            throw new RuntimeException(sprintf('R&D has %s of this: the remaining quantity cannot be more.', LabUnits::format($product, $current)));
        }

        if ($remaining < $current) {
            static::adjust($product, $research, $lot, $remaining - $current);
        }
    }

    protected static function assertAvailable(Product $product, Location $location, float $quantity, ?Lot $lot): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException('The quantity must be greater than zero.');
        }

        if ($product->tracking !== ProductTracking::QTY && ! $lot) {
            throw new RuntimeException('This item is tracked by lot: choose the lot.');
        }

        $available = (float) ProductQuantity::query()
            ->where(['product_id' => $product->id, 'location_id' => $location->id, 'lot_id' => $lot?->id])
            ->sum(DB::raw('quantity - reserved_quantity'));

        if ($quantity > $available + 0.00001) {
            throw new RuntimeException(sprintf('Only %s available there.', LabUnits::format($product, $available)));
        }
    }

    /** Production goods out that no manufacturing order accounts for: expired, spilled, broken. */
    public static function unload(Product $product, Location $location, float $quantity, ?Lot $lot = null): ProductQuantity
    {
        static::assertAvailable($product, $location, $quantity, $lot);

        return static::adjust($product, $location, $lot, -$quantity);
    }

    /** The same path as an inventory count in the Quantities page: the difference becomes a move. */
    protected static function adjust(Product $product, Location $location, ?Lot $lot, float $delta): ProductQuantity
    {
        $quant = ProductQuantity::query()->firstOrNew([
            'product_id'  => $product->id,
            'location_id' => $location->id,
            'lot_id'      => $lot?->id,
            'package_id'  => null,
        ]);

        if (! $quant->exists) {
            $quant->fill([
                'quantity'                => $delta,
                'inventory_diff_quantity' => $delta,
                'company_id'              => $location->company_id,
            ])->save();

            return $quant->refresh();
        }

        $counted = (float) $quant->quantity + $delta;

        $quant->update(['inventory_diff_quantity' => $delta, 'inventory_quantity_set' => true]);
        $quant->update(['quantity' => $counted, 'inventory_quantity_set' => false]);

        // An emptied quant is purged by the inventory.
        return $quant->fresh() ?? $quant;
    }
}
