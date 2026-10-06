<?php

use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Support\ItemRoles;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Inventory\Models\Product;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Schemas\BillOfMaterialForm;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ListProducts;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../webkul/manufacturing/tests/Helpers/ManufacturingHelper.php';

beforeEach(function () {
    foreach (['contacts', 'inventories', 'manufacturing', 'huvant-orders'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);
    }

    Package::$plugins = Plugin::all()->keyBy('name');
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
    ManufacturingHelper::actingAsAdmin();
    $this->pva = Product::query()->findOrFail(ManufacturingHelper::product(['name' => 'PVA Kuraray', 'reference' => 'H-148', 'huvant_role' => ItemRole::Material->value])->id);
    $this->pad = Product::query()->findOrFail(ManufacturingHelper::product(['name' => 'Kidney Biopsy Pad', 'reference' => 'KBP', 'huvant_role' => ItemRole::Product->value])->id);
});

it('stocks each role the way it is used', function () {
    $staff = Product::query()->findOrFail(ManufacturingHelper::product(['name' => 'Support staff', 'huvant_role' => ItemRole::Service->value])->id);

    expect($this->pva->tracking)->toBe(ProductTracking::LOT)
        ->and($this->pva->is_storable)->toBeTrue()
        ->and($this->pva->use_expiration_date)->toBeTrue()
        ->and((bool) $this->pva->enable_sales)->toBeFalse()
        ->and($this->pad->tracking)->toBe(ProductTracking::LOT)
        ->and((bool) $this->pad->enable_sales)->toBeTrue()
        ->and($staff->type)->toBe(ProductType::SERVICE)
        ->and($staff->is_storable)->toBeFalse();
});

it('starts a new raw material from the raw materials page', function () {
    $this->get(ProductResource::getUrl('create', ['role' => 'material']))->assertOk();

    Livewire::withQueryParams(['role' => 'material'])
        ->test(CreateProduct::class)
        ->assertFormSet(['huvant_role' => ItemRole::Material]);
});

it('lists items by role and keeps raw materials off offers and services out of bills of materials', function () {
    expect(ItemRoles::scope(Product::query(), ItemRole::sellable())->pluck('id')->all())->toContain($this->pad->id)->not->toContain($this->pva->id)
        ->and(ItemRoles::scope(Product::query(), ItemRole::components())->pluck('id')->all())->toContain($this->pva->id)
        ->and(BillOfMaterialForm::$componentQueryUsing)->not->toBeNull()
        ->and(array_keys(ItemRoles::presetViews()))->toBe(['products', 'rentals', 'services', 'materials', 'archived'])
        ->and(ListProducts::$presetViewsUsing)->not->toBeNull();
});
