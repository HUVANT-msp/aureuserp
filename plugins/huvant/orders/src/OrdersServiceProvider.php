<?php

namespace Huvant\Orders;

use Filament\Panel;
use Huvant\Orders\Console\ExpireOffers;
use Huvant\Orders\Console\ImportRegisters;
use Huvant\Orders\Http\Controllers\DeliveryNotePdfController;
use Huvant\Orders\Http\Controllers\OfferPdfController;
use Huvant\Orders\Support\ErpSetup;
use Huvant\Orders\Support\OrdersSchema;
use Huvant\Orders\Support\Shipping;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;
use Webkul\Inventory\Models\Operation;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Product\Models\Product;

class OrdersServiceProvider extends PackageServiceProvider
{
    public static string $name = 'huvant-orders';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasDependencies(['contacts', 'products', 'inventories', 'manufacturing'])
            ->hasMigrations([
                '2026_10_05_100000_create_huvant_orders_tables',
                '2026_10_05_120000_create_huvant_rental_categories_table',
            ])
            ->runsMigrations()
            ->hasSettings(['2026_10_05_130000_create_huvant_orders_settings'])
            ->runsSettings()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->startWith(function (InstallCommand $command): void {
                        foreach (['contacts', 'products', 'inventories', 'manufacturing'] as $dependency) {
                            if (! Package::isPluginInstalled($dependency)) {
                                $command->call($dependency.':install');
                            }
                        }
                    })
                    ->runsMigrations()
                    ->endWith(function (): void {
                        OrdersSchema::ensureColumns();
                        ErpSetup::apply();
                    });
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {});
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([ExpireOffers::class, ImportRegisters::class]);
        }

        // Offers nobody answered expire the night after their validity date.
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            if (Package::isPluginInstalled(static::$name)) {
                $schedule->command(ExpireOffers::class)->dailyAt('01:00')->timezone('Europe/Rome')->onOneServer();
            }
        });

        Partner::contributeFillable(['huvant_sdi_code', 'huvant_pec', 'huvant_customs_code', 'huvant_event_date', 'huvant_onsite_contact']);
        Partner::contributeCasts(['huvant_event_date' => 'date']);
        Product::contributeFillable(['huvant_hs_code', 'huvant_production_days', 'huvant_rental_category_id', 'huvant_cas_number', 'huvant_lab_kind', 'huvant_lab_use', 'huvant_supplier', 'huvant_supplier_code', 'huvant_package_quantity', 'huvant_package_uom_id', 'huvant_density', 'huvant_storage_position']);
        Product::contributeCasts(['huvant_production_days' => 'integer', 'huvant_density' => 'decimal:4', 'huvant_package_quantity' => 'decimal:4']);

        // A validated delivery of an order gets its delivery note number; a validated return closes the loop.
        Operation::saved(function (Operation $operation): void {
            if (Package::isPluginInstalled(static::$name)) {
                Shipping::syncTransfer($operation);
            }
        });

        // The commercial offer and the delivery note, downloaded through the ERP session.
        Route::middleware('web')
            ->get('admin/huvant/orders/{order}/offer.pdf', [OfferPdfController::class, 'show'])
            ->whereNumber('order')
            ->name('huvant.orders.offer');
        Route::middleware('web')
            ->get('admin/huvant/orders/deliveries/{delivery}/ddt.pdf', [DeliveryNotePdfController::class, 'show'])
            ->whereNumber('delivery')
            ->name('huvant.orders.delivery-note');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(OrdersPlugin::make());
        });
    }
}
