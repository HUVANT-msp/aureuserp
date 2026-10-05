<?php

namespace Huvant\Orders\Support;

use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Manufacturing\Settings\OperationSettings;

/** How the ERP is set up for the lab, applied when the plugin is installed. */
class ErpSetup
{
    public static function apply(): void
    {
        static::atomicProduction();
        static::separateLocations();
        LabUnits::ensureUnits();
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
