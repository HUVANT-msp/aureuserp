<?php

use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Support\ItemRoles;
use Huvant\Orders\Support\LabUnits;
use Huvant\Orders\Support\Recipes;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\EditProduct;
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
    LabUnits::ensureUnits();

    $this->grams = LabUnits::unit('g');
    $this->mL = LabUnits::unit('mL');
    $this->pva = Product::query()->findOrFail(ManufacturingHelper::product(['name' => 'PVA Kuraray', 'reference' => 'H-148', 'huvant_role' => ItemRole::Material->value, 'uom_id' => $this->grams->id, 'uom_po_id' => $this->grams->id])->id);
    $this->glycerine = Product::query()->findOrFail(ManufacturingHelper::product(['name' => 'Glicerina', 'reference' => 'H-121', 'huvant_role' => ItemRole::Material->value, 'uom_id' => $this->mL->id, 'uom_po_id' => $this->mL->id, 'huvant_density' => 1.26])->id);
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

it('writes what one piece takes as the bill of materials, in each material unit', function () {
    // 120 g of PVA, and glycerine weighed: 378 g at 1.26 g/mL is 300 mL.
    Recipes::save($this->pad, [
        ['product_id' => $this->pva->id, 'quantity' => 0.12, 'unit' => LabUnits::unit('kg')->id],
        ['product_id' => $this->glycerine->id, 'quantity' => 378, 'unit' => $this->grams->id],
    ]);

    $bom = Recipes::bom($this->pad);

    expect((float) $bom->quantity)->toBe(1.0)
        ->and($bom->lines()->orderBy('sort')->get()->map(fn ($line) => [$line->product_id, round((float) $line->quantity, 2), $line->uom_id])->all())
        ->toBe([[$this->pva->id, 120.0, $this->grams->id], [$this->glycerine->id, 300.0, $this->mL->id]])
        ->and(Recipes::lines($this->pad))->toHaveCount(2);

    Recipes::save($this->pad, [['product_id' => $this->pva->id, 'quantity' => 100, 'unit' => $this->grams->id]]);
    expect(Recipes::bom($this->pad)->lines()->count())->toBe(1);
});

it('edits the materials per piece on the product page', function () {
    Livewire::test(EditProduct::class, ['record' => $this->pad->getRouteKey()])
        ->assertOk()
        ->assertFormFieldIsVisible('huvant_recipe')
        ->assertFormFieldIsHidden('huvant_cas_number')
        ->fillForm(['huvant_recipe' => [
            ['product_id' => $this->pva->id, 'quantity' => 120, 'unit' => $this->grams->id],
        ]])
        ->call('save')
        ->assertHasNoFormErrors();

    expect((float) Recipes::bom($this->pad)->lines()->sole()->quantity)->toBe(120.0);

    Livewire::test(EditProduct::class, ['record' => $this->pva->getRouteKey()])
        ->assertOk()
        ->assertFormFieldIsHidden('huvant_recipe')
        ->assertFormFieldIsVisible('huvant_cas_number');
});

it('offers only raw materials and products as components, and lists items by role', function () {
    $options = Recipes::componentOptions($this->pad);

    expect($options)->toHaveKeys([$this->pva->id, $this->glycerine->id])
        ->and($options)->not->toHaveKey($this->pad->id)
        ->and(BillOfMaterialForm::$componentQueryUsing)->not->toBeNull()
        ->and(ItemRoles::scope(Product::query(), ItemRole::sellable())->pluck('id')->all())->toContain($this->pad->id)->not->toContain($this->pva->id)
        ->and(array_keys(ItemRoles::presetViews()))->toBe(['products', 'rentals', 'services', 'materials', 'archived'])
        ->and(ListProducts::$presetViewsUsing)->not->toBeNull();
});

it('starts a new raw material from the raw materials page', function () {
    $this->get(ProductResource::getUrl('create', ['role' => 'material']))->assertOk();

    Livewire::withQueryParams(['role' => 'material'])
        ->test(CreateProduct::class)
        ->assertFormSet(['huvant_role' => ItemRole::Material]);
});
