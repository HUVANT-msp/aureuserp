<?php

namespace Huvant\Orders\Support;

use Webkul\Inventory\Filament\Clusters\Configurations as InventoryConfigurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PutawayRuleResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;
use Webkul\Inventory\Filament\Clusters\Operations as InventoryOperations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource;
use Webkul\Inventory\Filament\Clusters\PluginSettings as InventoryPluginSettings;
use Webkul\Inventory\Filament\Clusters\Products as InventoryProducts;
use Webkul\Inventory\Filament\Clusters\Reporting as InventoryReporting;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\QuantityResource as StockReportResource;
use Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageLogistics;
use Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageOperations as ManageInventoryOperations;
use Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageProducts;
use Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageTraceability;
use Webkul\Inventory\Filament\Clusters\Settings\Pages\ManageWarehouses;
use Webkul\Inventory\Filament\Pages\Overview as InventoryOverview;
use Webkul\Manufacturing\Filament\Clusters\Configurations as ManufacturingConfigurations;
use Webkul\Manufacturing\Filament\Clusters\Operations as ManufacturingOperations;
use Webkul\Manufacturing\Filament\Clusters\PluginSettings as ManufacturingPluginSettings;
use Webkul\Manufacturing\Filament\Clusters\Products as ManufacturingProducts;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\LotResource as ManufacturingLotResource;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource as ManufacturingProductResource;
use Webkul\Manufacturing\Filament\Clusters\Settings\Pages\ManageOperations as ManageManufacturingOperations;

/**
 * The inventory and manufacturing menus cut down to what the lab registers covered: the rest stays
 * installed and reachable by link, only out of the menu.
 *
 * The Inventory menu shows only the lab's Raw materials and Finished products; the ERP's own
 * inventory pages (transfers behind delivery notes and returns included) stay reachable by link.
 * Manufacturing is replaced in the menu by the two-step Offers and Production workflow.
 */
class Navigation
{
    /** @var array<int, class-string> */
    public const HIDDEN = [
        // The inventory menu is the lab's: Raw materials and Finished products only.
        InventoryOverview::class,
        InventoryOperations::class,
        InventoryProducts::class,
        InventoryReporting::class,
        InventoryConfigurations::class,
        // Scrap: the lab discards from Lab stock.
        ScrapResource::class,
        // A second stock report: Operations › Quantities already shows it.
        StockReportResource::class,
        // One warehouse, set up once, with its operation types and storage rules.
        WarehouseResource::class,
        OperationTypeResource::class,
        StorageCategoryResource::class,
        PutawayRuleResource::class,
        // Settings fixed by the plugin install (lots, units, locations, atomic production).
        InventoryPluginSettings::class,
        ManageLogistics::class,
        ManageInventoryOperations::class,
        ManageProducts::class,
        ManageTraceability::class,
        ManageWarehouses::class,
        ManufacturingPluginSettings::class,
        ManufacturingOperations::class,
        ManufacturingProducts::class,
        ManufacturingConfigurations::class,
        ManageManufacturingOperations::class,
        // Duplicates of Inventory › Products and Lots.
        ManufacturingProductResource::class,
        ManufacturingLotResource::class,
    ];

    public static function hide(): void
    {
        foreach (self::HIDDEN as $class) {
            $class::$hiddenFromNavigation = true;
        }
    }
}
