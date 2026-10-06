<?php

use Huvant\Orders\Filament\Clusters\FinishedProducts;
use Huvant\Orders\Filament\Clusters\Manufacturing;
use Huvant\Orders\Filament\Clusters\Manufacturing\Pages\Production;
use Huvant\Orders\Filament\Clusters\Manufacturing\Resources\OfferResource;
use Huvant\Orders\Filament\Clusters\RawMaterials;
use Huvant\Orders\Filament\Resources\OrderResource;
use Huvant\Orders\Support\ErpSetup;
use Huvant\Orders\Support\Navigation;
use Illuminate\Support\Facades\URL;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ScrapResource;
use Webkul\Inventory\Settings\LogisticSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Product\Settings\ProductSettings;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../webkul/manufacturing/tests/Helpers/ManufacturingHelper.php';

beforeEach(function () {
    foreach (['contacts', 'inventories', 'manufacturing', 'huvant-orders'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);
    }

    Package::$plugins = Plugin::all()->keyBy('name');
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
    ManufacturingHelper::actingAsAdmin();
});

it('takes out of the menu what the lab registers never covered, and keeps what they did', function () {
    Navigation::hide();

    foreach (Navigation::HIDDEN as $class) {
        expect($class::shouldRegisterNavigation())->toBeFalse($class);
    }

    foreach ([RawMaterials::class, FinishedProducts::class, Manufacturing::class, OfferResource::class, Production::class, OrderResource::class] as $kept) {
        expect($kept::shouldRegisterNavigation())->toBeTrue($kept);
    }

    // Hidden is not removed: the pages stay reachable by link (delivery notes open transfers).
    expect(ScrapResource::getUrl('index'))->toBeString()
        ->and(DeliveryResource::getUrl('index'))->toBeString();
});

it('turns on lots, expiry dates and units, and off variants, packagings, packages, dropshipping and routes', function () {
    ErpSetup::apply();

    expect(app(TraceabilitySettings::class)->enable_lots_serial_numbers)->toBeTrue()
        ->and(app(TraceabilitySettings::class)->enable_expiration_dates)->toBeTrue()
        ->and(app(ProductSettings::class)->enable_uom)->toBeTrue()
        ->and(app(ProductSettings::class)->enable_variants)->toBeFalse()
        ->and(app(ProductSettings::class)->enable_packagings)->toBeFalse()
        ->and(app(LogisticSettings::class)->enable_dropshipping)->toBeFalse()
        ->and(app(WarehouseSettings::class)->enable_multi_steps_routes)->toBeFalse()
        ->and(app(WarehouseSettings::class)->enable_locations)->toBeTrue();
});

it('offers only Italian and English in the language selector', function () {
    expect(array_keys(config('app.supported_locales')))->toBe(['it', 'en']);
});
