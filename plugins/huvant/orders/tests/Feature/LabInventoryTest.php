<?php

use Filament\Actions\Testing\TestAction;
use Huvant\Documents\Support\Documents;
use Huvant\Documents\Support\DocumentSpace;
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\LabArea;
use Huvant\Orders\Enums\ShelfLifeUnit;
use Huvant\Orders\Enums\UnitStatus;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Pages\PiecesInLab;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Pages\PiecesOut;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Pages\PiecesSold;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Pages\StockByProduct;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages\CreateRecipe;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages\EditRecipe;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages\ManageRecipeDocuments;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages\ManageStock;
use Huvant\Orders\Filament\Clusters\RawMaterials\Pages\MaterialsByProduct;
use Huvant\Orders\Filament\Clusters\RawMaterials\Pages\MaterialsInLab;
use Huvant\Orders\Filament\Clusters\RawMaterials\Resources\MaterialResource\Pages\ManageMaterials;
use Huvant\Orders\Models\MaterialLot;
use Huvant\Orders\Models\ProductUnit;
use Huvant\Orders\Support\LabCatalogue;
use Huvant\Orders\Support\LabInventory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../webkul/manufacturing/tests/Helpers/ManufacturingHelper.php';

beforeEach(function () {
    foreach (['contacts', 'inventories', 'manufacturing', 'huvant-documents', 'huvant-orders'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);
    }

    Package::$plugins = Plugin::all()->keyBy('name');
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
    $this->admin = ManufacturingHelper::actingAsAdmin();

    $material = fn (array $data): Product => Product::query()->create(LabCatalogue::materialDefaults($data));
    $this->pva = $material(['name' => 'PVA Kuraray', 'reference' => 'H-148', 'huvant_package_quantity' => 20000, 'huvant_package_unit' => 'g', 'huvant_min_quantity' => 1000]);
    $this->glycerine = $material(['name' => 'Glicerina', 'reference' => 'H-121', 'huvant_package_quantity' => 1000, 'huvant_package_unit' => 'mL', 'huvant_min_quantity' => 1000]);

    $this->brain = Product::query()->create(LabCatalogue::productDefaults([
        'name' => 'High Grade Brain Pad', 'huvant_code_prefix' => 'BRN', 'huvant_shelf_life' => 6, 'huvant_shelf_life_unit' => ShelfLifeUnit::Months->value,
    ]));
    LabInventory::saveRecipe($this->brain, [
        ['material_id' => $this->pva->id, 'quantity' => 120],
        ['material_id' => $this->glycerine->id, 'quantity' => 300],
    ]);
});

it('keeps raw materials as packages with their lot, expiry and what is left', function () {
    $packages = LabInventory::addPackages($this->glycerine, 'GLY-24', Carbon::parse('2027-12-31'), 2);

    expect($packages)->toHaveCount(2)
        ->and((float) $packages->first()->remaining_quantity)->toBe(1000.0)
        ->and($packages->first()->area)->toBe(LabArea::Production)
        ->and(LabInventory::total($this->glycerine))->toBe(2000.0);

    LabInventory::setRemaining($packages->first(), 250);
    expect($packages->first()->remainingShare())->toBe(0.25)
        ->and(LabInventory::total($this->glycerine))->toBe(1250.0)
        ->and(fn () => LabInventory::setRemaining($packages->first(), 1500))->toThrow(RuntimeException::class);

    LabInventory::finish($packages->last());
    expect(LabInventory::total($this->glycerine))->toBe(250.0)
        ->and(LabInventory::availableLots($this->glycerine))->toHaveCount(1);

    // R&D packages are not counted for production.
    LabInventory::addPackages($this->glycerine, 'GLY-RD', null, 1, 500, LabArea::Research);
    expect(LabInventory::total($this->glycerine))->toBe(250.0)
        ->and(LabInventory::total($this->glycerine, null))->toBe(750.0);
});

it('rates what is left against the minimum: below, close to it, or enough', function () {
    expect(LabInventory::level($this->glycerine, 900))->toBe('below')
        ->and(LabInventory::level($this->glycerine, 1100))->toBe('low')
        ->and(LabInventory::level($this->glycerine, 1300))->toBe('ok');

    $this->glycerine->update(['huvant_min_quantity' => null]);
    expect(LabInventory::level($this->glycerine->refresh(), 0))->toBe('unset');
});

it('makes pieces: codes by acronym and date, expiry from the shelf life, recipe taken from the chosen lots', function () {
    $pva = LabInventory::addPackages($this->pva, 'PVA-1', null)->first();
    $glycerine = LabInventory::addPackages($this->glycerine, 'GLY-1', null)->first();

    $units = LabInventory::produce($this->brain, Carbon::parse('2026-10-06'), 2, [$this->pva->id => $pva->id, $this->glycerine->id => $glycerine->id]);

    expect($units->pluck('code')->all())->toBe(['BRN-20261006-01', 'BRN-20261006-02'])
        ->and($units->first()->expiry_date->toDateString())->toBe('2027-04-06')
        ->and($units->first()->status)->toBe(UnitStatus::InLab)
        ->and((float) $pva->refresh()->remaining_quantity)->toBe(19760.0)
        ->and((float) $glycerine->refresh()->remaining_quantity)->toBe(400.0)
        ->and($units->first()->materials()->pluck('lot_number')->sort()->values()->all())->toBe(['GLY-1', 'PVA-1'])
        ->and(LabInventory::nextCode($this->brain, Carbon::parse('2026-10-06')))->toBe('BRN-20261006-03')
        ->and(LabInventory::counts($this->brain))->toBe(['in_lab' => 2, 'out' => 0, 'sold' => 0]);
});

it('refuses to make pieces without a lot or with too little left in it', function () {
    $glycerine = LabInventory::addPackages($this->glycerine, 'GLY-1', null, 1, 500)->first();
    $pva = LabInventory::addPackages($this->pva, 'PVA-1', null)->first();

    expect(fn () => LabInventory::produce($this->brain, today(), 1, [$this->pva->id => $pva->id]))->toThrow(RuntimeException::class, 'Choose the lot of Glicerina')
        ->and(fn () => LabInventory::produce($this->brain, today(), 2, [$this->pva->id => $pva->id, $this->glycerine->id => $glycerine->id]))->toThrow(RuntimeException::class, '600 mL needed, 500 mL left')
        ->and((float) $pva->refresh()->remaining_quantity)->toBe(20000.0)
        ->and(ProductUnit::query()->count())->toBe(0);
});

it('creates raw materials and recipes from their simple forms', function () {
    Livewire::test(ManageMaterials::class)
        ->assertOk()
        ->mountAction('create')
        ->set('mountedActions.0.data', [
            'name'                    => 'Agar-Agar', 'reference' => 'H-108', 'huvant_lab_kind' => 'substance',
            'huvant_package_quantity' => 500, 'huvant_package_unit' => 'sacchetti', 'huvant_min_quantity' => 2, 'huvant_supplier' => 'Fisher Scientific',
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    $agar = Product::query()->where('name', 'Agar-Agar')->sole();
    expect($agar->huvant_role)->toBe(ItemRole::Material)
        ->and($agar->huvant_package_unit)->toBe('sacchetti');

    Livewire::test(CreateRecipe::class)
        ->set('data', [
            'name'          => 'Meningioma Brain Pad', 'huvant_code_prefix' => 'mgb', 'huvant_shelf_life' => 1, 'huvant_shelf_life_unit' => 'years',
            'huvant_recipe' => [['material_id' => $agar->id, 'quantity' => 40]],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $meningioma = Product::query()->where('name', 'Meningioma Brain Pad')->sole();
    expect($meningioma->huvant_role)->toBe(ItemRole::Product)
        ->and($meningioma->huvant_code_prefix)->toBe('MGB')
        ->and(LabInventory::recipe($meningioma)->pluck('material_id')->all())->toBe([$agar->id]);

    Livewire::test(EditRecipe::class, ['record' => $this->brain->getRouteKey()])
        ->assertOk()
        ->assertFormSet(['huvant_recipe' => [
            ['material_id' => $this->pva->id, 'quantity' => 120.0],
            ['material_id' => $this->glycerine->id, 'quantity' => 300.0],
        ]]);
});

it('adds packages to the lab and pieces to stock from the pages', function () {
    Livewire::test(MaterialsInLab::class)
        ->assertOk()
        ->mountAction('add')
        ->set('mountedActions.0.data', ['material_id' => $this->pva->id, 'lot_number' => 'PVA-7', 'expiry_date' => '2027-06-17', 'packages' => 1, 'quantity' => 20000, 'area' => 'production', 'received_on' => '2026-10-06'])
        ->callMountedAction()
        ->assertHasNoActionErrors();
    Livewire::test(MaterialsInLab::class)
        ->mountAction('add')
        ->set('mountedActions.0.data', ['material_id' => $this->glycerine->id, 'lot_number' => 'GLY-7', 'packages' => 1, 'quantity' => 1000, 'area' => 'production'])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    $pva = MaterialLot::query()->where('lot_number', 'PVA-7')->sole();
    $glycerine = MaterialLot::query()->where('lot_number', 'GLY-7')->sole();

    Livewire::test(MaterialsInLab::class)->assertCanSeeTableRecords([$pva, $glycerine])->assertSee('20.000 g');

    Livewire::test(ManageStock::class, ['record' => $this->brain->getRouteKey()])
        ->assertOk()
        ->mountAction('addToStock')
        ->set('mountedActions.0.data', [
            'production_date' => '2026-10-06', 'pieces' => 1,
            'lots'            => [$this->pva->id => $pva->id, $this->glycerine->id => $glycerine->id],
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    $unit = ProductUnit::query()->sole();
    expect($unit->code)->toBe('BRN-20261006-01')
        ->and((float) $glycerine->refresh()->remaining_quantity)->toBe(700.0);

    Livewire::test(ManageStock::class, ['record' => $this->brain->getRouteKey()])
        ->assertCanSeeTableRecords([$unit])
        ->mountAction(TestAction::make('materials')->table($unit))
        ->assertMountedActionModalSee('GLY-7');
});

it('moves an available piece to the trash with a required note and restores it', function () {
    $pva = LabInventory::addPackages($this->pva, 'PVA-TRASH', null)->first();
    $glycerine = LabInventory::addPackages($this->glycerine, 'GLY-TRASH', null)->first();
    $units = LabInventory::produce($this->brain, today(), 2, [$this->pva->id => $pva->id, $this->glycerine->id => $glycerine->id]);
    $available = $units[0];
    $sold = $units[1];
    $sold->update(['status' => UnitStatus::Sold, 'status_since' => today()]);

    $component = Livewire::test(ManageStock::class, ['record' => $this->brain->getRouteKey()])
        ->assertOk()
        ->filterTable('status', null)
        ->assertActionVisible(TestAction::make('delete')->table($available))
        ->assertActionHidden(TestAction::make('delete')->table($sold))
        ->callAction(TestAction::make('delete')->table($available))
        ->assertHasActionErrors(['deletion_note']);

    expect(ProductUnit::find($available->id))->not->toBeNull();

    Livewire::test(ManageStock::class, ['record' => $this->brain->getRouteKey()])
        ->filterTable('status', null)
        ->callAction(TestAction::make('delete')->table($available), data: ['deletion_note' => 'Controllo qualità non superato'])
        ->assertHasNoActionErrors();

    $trashed = ProductUnit::withTrashed()->findOrFail($available->id);

    expect(ProductUnit::find($available->id))->toBeNull()
        ->and($trashed->trashed())->toBeTrue()
        ->and($trashed->deletion_note)->toBe('Controllo qualità non superato')
        ->and($trashed->deleted_by)->toBe($this->admin->id)
        ->and($trashed->materials()->count())->toBe(2)
        ->and((float) $glycerine->refresh()->remaining_quantity)->toBe(400.0);

    Livewire::test(ManageStock::class, ['record' => $this->brain->getRouteKey()])
        ->filterTable('status', null)
        ->filterTable('trashed', false)
        ->assertCanSeeTableRecords([$trashed])
        ->assertActionVisible(TestAction::make('deletionDetails')->table($trashed))
        ->callAction(TestAction::make('restore')->table($trashed));

    expect(ProductUnit::find($available->id))->not->toBeNull();
});

it('rates only finished products that are available in stock', function () {
    $available = ProductUnit::query()->create([
        'product_id'      => $this->brain->id,
        'code'            => 'BRN-RATING-01',
        'production_date' => today(),
        'status'          => UnitStatus::InLab,
    ]);
    $sold = ProductUnit::query()->create([
        'product_id'      => $this->brain->id,
        'code'            => 'BRN-RATING-02',
        'production_date' => today(),
        'status'          => UnitStatus::Sold,
        'status_since'    => today(),
    ]);

    $component = Livewire::test(ManageStock::class, ['record' => $this->brain->getRouteKey()])
        ->assertOk()
        ->call('rateUnit', $available->id, 4);

    expect($available->refresh()->quality_rating)->toBe(4);

    $component->call('rateUnit', $sold->id, 5)->assertNotified();
    expect($sold->refresh()->quality_rating)->toBeNull();

    $component->call('rateUnit', $available->id, 6)->assertNotified();
    expect($available->refresh()->quality_rating)->toBe(4);
});

it('shows materials by product, pieces in the lab, stock cards, out and sold', function () {
    $pva = LabInventory::addPackages($this->pva, 'PVA-1', null)->first();
    $glycerine = LabInventory::addPackages($this->glycerine, 'GLY-1', null, 1, 900)->first();
    $units = LabInventory::produce($this->brain, today(), 3, [$this->pva->id => $pva->id, $this->glycerine->id => $glycerine->id]);

    $units[1]->update(['status' => UnitStatus::Out, 'status_since' => today()->subDays(3)]);
    $units[2]->update(['status' => UnitStatus::Sold, 'status_since' => today()]);

    Livewire::test(MaterialsByProduct::class)->assertOk()->assertSee('High Grade Brain Pad')->assertSee('Glicerina')->assertSee('hv-lab-below', false);
    Livewire::test(PiecesInLab::class)->assertOk()->assertSee('High Grade Brain Pad')->assertSee('1 piece');
    Livewire::test(StockByProduct::class)->assertOk()->assertSee('BRN-…');
    Livewire::test(PiecesOut::class)->assertOk()->assertCanSeeTableRecords([$units[1]])->assertCanNotSeeTableRecords([$units[0]]);
    Livewire::test(PiecesSold::class)->assertOk()->assertCanSeeTableRecords([$units[2]]);
});

it('keeps documents in folders on a recipe', function () {
    $space = DocumentSpace::recipe($this->brain->id);
    $folder = Documents::createFolder($this->admin, $space, null, 'Procedures');
    $note = Documents::createNote($this->admin, $space, $folder->id, null, 'Casting the pad', '<p>Pour at 80 °C.</p>');

    expect($folder->recipe_id)->toBe($this->brain->id)
        ->and($folder->project_id)->toBeNull()
        ->and($note->recipe_id)->toBe($this->brain->id)
        ->and(Documents::canView($note))->toBeTrue()
        ->and(Documents::folderOptions($space))->toBe([$folder->id => 'Procedures']);

    Livewire::test(ManageRecipeDocuments::class, ['record' => $this->brain->getRouteKey()])
        ->assertOk()
        ->assertSee('Procedures')
        ->call('openFolder', (string) $folder->id)
        ->assertCanSeeTableRecords([$note]);
});

it('lets colleagues add packages and pieces too', function () {
    $colleague = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true]));
    $this->actingAs($colleague);

    Livewire::test(MaterialsInLab::class)->assertOk()->assertActionVisible('add');
});
