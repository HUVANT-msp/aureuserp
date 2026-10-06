<?php

namespace Huvant\Orders\Support;

use Webkul\Inventory\Enums\OperationType as OperationTypeKind;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\LogisticSettings;
use Webkul\Inventory\Settings\OperationSettings as InventoryOperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Manufacturing\Settings\OperationSettings;
use Webkul\Product\Settings\ProductSettings;

/** How the ERP is set up for the lab, applied when the plugin is installed. */
class ErpSetup
{
    public static function apply(): void
    {
        static::atomicProduction();
        static::separateLocations();
        static::inventoryFeatures();
    }

    /**
     * What the lab registers used: lots with expiry dates, several units of measure. What they did
     * not: variants, packagings, packages, dropshipping, multi-step routes. The traceability page
     * refuses changes once lot-tracked products exist, so this is set here.
     */
    public static function inventoryFeatures(): void
    {
        $traceability = app(TraceabilitySettings::class);
        $traceability->enable_lots_serial_numbers = true;
        $traceability->enable_expiration_dates = true;
        $traceability->save();

        $products = app(ProductSettings::class);
        $products->enable_uom = true;
        $products->enable_variants = false;
        $products->enable_packagings = false;
        $products->save();

        $operations = app(InventoryOperationSettings::class);
        $operations->enable_packages = false;
        $operations->save();

        $logistics = app(LogisticSettings::class);
        $logistics->enable_dropshipping = false;
        $logistics->save();

        OperationType::withTrashed()->where('type', OperationTypeKind::DROPSHIP)->whereNull('deleted_at')->update(['deleted_at' => now()]);

        $warehouses = app(WarehouseSettings::class);
        $warehouses->enable_multi_steps_routes = false;
        $warehouses->save();
    }

    /**
     * Production is atomic in the lab: one manufacturing order per product, no routing. Work orders,
     * work centres, operations and by-products stay off (they can be turned back on in the
     * manufacturing settings).
     */
    public static function atomicProduction(): void
    {
        $settings = app(OperationSettings::class);

        $settings->enable_work_orders = false;
        $settings->enable_work_order_dependencies = false;
        $settings->enable_byproducts = false;

        $settings->save();
    }

    /**
     * R&D is a location of its own, so storage locations are on, with the internal transfers they
     * need (as the warehouse settings page does when the option is switched on).
     */
    public static function separateLocations(): void
    {
        $settings = app(WarehouseSettings::class);

        if (! $settings->enable_locations) {
            $settings->enable_locations = true;
            $settings->save();
        }

        OperationType::withTrashed()
            ->whereIn('id', Warehouse::query()->pluck('internal_type_id')->filter())
            ->update(['deleted_at' => null]);
    }
}
