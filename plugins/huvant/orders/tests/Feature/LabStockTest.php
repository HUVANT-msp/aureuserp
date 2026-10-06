<?php

use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\LabItemKind;
use Huvant\Orders\Filament\Pages\LabStockPage;
use Huvant\Orders\Filament\Pages\ManageOrdersSettings;
use Huvant\Orders\Filament\Resources\OrderResource\Pages\EditOrder;
use Huvant\Orders\Filament\Resources\OrderResource\RelationManagers\ProductionRelationManager;
use Huvant\Orders\Settings\OrdersSettings;
use Huvant\Orders\Support\ErpSetup;
use Huvant\Orders\Support\LabStock;
use Huvant\Orders\Support\LabUnits;
use Huvant\Orders\Support\Orders;
use Huvant\Orders\Support\Shipping;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../webkul/inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../../../webkul/manufacturing/tests/Helpers/ManufacturingHelper.php';

beforeEach(function () {
    foreach (['contacts', 'inventories', 'manufacturing', 'huvant-orders'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);
    }

    Package::$plugins = Plugin::all()->keyBy('name');
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
    ManufacturingHelper::actingAsAdmin();
    ErpSetup::apply();

    $this->warehouse = Shipping::warehouse(ManufacturingHelper::company()->id);
    $this->production = LabStock::location($this->warehouse, LabStock::PRODUCTION);
    $this->research = LabStock::location($this->warehouse, LabStock::RESEARCH);
    $this->mL = LabUnits::unit('mL');
    $this->grams = LabUnits::unit('g');

    // An ionic liquid stocked in mL, sold in 250 mL bottles, 1.29 g/mL.
    $this->reagent = Product::query()->findOrFail(InventoryHelper::lotTrackedProduct([
        'name'            => 'EMIM-BF4, 99%', 'reference' => 'H-102', 'cost' => 0.5,
        'uom_id'          => $this->mL->id, 'uom_po_id' => $this->mL->id,
        'huvant_role'     => ItemRole::Material->value, 'huvant_lab_kind' => LabItemKind::Substance->value, 'huvant_cas_number' => '143314-16-3',
        'huvant_density'  => 1.29, 'huvant_package_quantity' => 250, 'huvant_package_uom_id' => $this->mL->id,
    ])->id);
});

it('converts what the lab enters into the item unit: litres, grams through the density, packages', function () {
    $litres = LabUnits::unit('L');
    $kilos = LabUnits::unit('kg');

    expect(LabUnits::toMain($this->reagent, 0.5, $litres->id))->toBe(500.0)
        ->and(LabUnits::toMain($this->reagent, 129, $this->grams->id))->toEqualWithDelta(100.0, 0.0001)
        ->and(LabUnits::toMain($this->reagent, 1.29, $kilos->id))->toEqualWithDelta(1000.0, 0.0001)
        ->and(LabUnits::toMain($this->reagent, 3, LabUnits::PACKAGE))->toBe(750.0)
        ->and(array_values(LabUnits::options($this->reagent, withPackage: true)))->toContain('mg', 'g', 'kg', 'mL', 'L', 'Packages (250 mL)')
        ->and(LabUnits::format($this->reagent, 1250))->toBe('1.250 mL');

    $this->reagent->update(['huvant_density' => null]);
    expect(fn () => LabUnits::toMain($this->reagent->refresh(), 100, $this->grams->id))->toThrow(RuntimeException::class, 'density');
});

it('hands production stock over to R&D as a transfer of the chosen lot', function () {
    LabStock::load($this->reagent, $this->production, 500, 'L1', '2342', Carbon::parse('2027-05-01'));
    LabStock::load($this->reagent, $this->production, 250, 'L2');
    $lot = Lot::query()->where('name', 'L2')->firstOrFail();

    $transfer = LabStock::moveToResearch($this->reagent, $this->warehouse, 100, $lot);

    expect($transfer->state)->toBe(OperationState::DONE)
        ->and(LabStock::onHand($this->reagent, $this->production))->toBe(650.0)
        ->and(LabStock::onHand($this->reagent, $this->research))->toBe(100.0)
        ->and(ProductQuantity::query()->where(['location_id' => $this->research->id, 'lot_id' => $lot->id])->value('quantity'))->toEqual(100)
        ->and($this->research->parent_path)->not->toStartWith($this->production->parent_path)
        ->and(Lot::query()->where('name', 'L1')->value('reference'))->toBe('2342')
        ->and(fn () => LabStock::moveToResearch($this->reagent, $this->warehouse, 500, $lot))->toThrow(RuntimeException::class, 'Only 150 mL available');
});

it('lets R&D say what is left, or that it is finished, without tracking each use', function () {
    LabStock::load($this->reagent, $this->production, 250, 'L1');
    $lot = Lot::query()->where('name', 'L1')->firstOrFail();
    LabStock::moveToResearch($this->reagent, $this->warehouse, 200, $lot);

    LabStock::setResearchRemaining($this->reagent, $this->warehouse, 60, $lot);
    expect(LabStock::onHand($this->reagent, $this->research))->toBe(60.0)
        ->and(fn () => LabStock::setResearchRemaining($this->reagent, $this->warehouse, 90, $lot))->toThrow(RuntimeException::class);

    LabStock::setResearchRemaining($this->reagent, $this->warehouse, 0, $lot);
    expect(LabStock::onHand($this->reagent, $this->research))->toBe(0.0)
        ->and(LabStock::onHand($this->reagent, $this->production))->toBe(50.0);
});

it('discards production stock from a lot and refuses more than is there', function () {
    LabStock::load($this->reagent, $this->production, 250, 'L1');
    $lot = Lot::query()->where('name', 'L1')->firstOrFail();

    LabStock::unload($this->reagent, $this->production, 100, $lot);

    expect(LabStock::onHand($this->reagent, $this->production))->toBe(150.0)
        ->and(fn () => LabStock::unload($this->reagent, $this->production, 200, $lot))->toThrow(RuntimeException::class, 'Only 150 mL')
        ->and(fn () => LabStock::unload($this->reagent, $this->production, 1))->toThrow(RuntimeException::class, 'choose the lot');
});

it('flags lots expiring within the warning window', function () {
    LabStock::load($this->reagent, $this->production, 10, 'L-SOON', expiresOn: Carbon::parse('2026-11-10'));
    LabStock::load($this->reagent, $this->production, 10, 'L-LATER', expiresOn: Carbon::parse('2027-06-01'));

    expect(LabStock::expiringLots($this->reagent, Carbon::parse('2026-10-05'))->pluck('name')->all())->toBe(['L-SOON']);
});

it('keeps a minimum per item', function () {
    LabStock::setMinimum($this->reagent, $this->warehouse, 250);
    LabStock::setMinimum($this->reagent, $this->warehouse, 500);

    expect(LabStock::minimum($this->reagent))->toBe(500.0);
});

it('loads packages and moves grams to R&D from the page', function () {
    Livewire::test(LabStockPage::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$this->reagent])
        ->callTableAction('load', $this->reagent, data: ['quantity' => 2, 'unit' => LabUnits::PACKAGE, 'lot' => 'L7', 'expires_on' => '2027-01-31'])
        ->assertHasNoTableActionErrors();

    expect(LabStock::onHand($this->reagent, $this->production))->toBe(500.0);

    $lot = Lot::query()->where('name', 'L7')->firstOrFail();

    Livewire::test(LabStockPage::class)
        ->callTableAction('toResearch', $this->reagent, data: ['lot_id' => $lot->id, 'quantity' => 129, 'unit' => $this->grams->id])
        ->assertHasNoTableActionErrors();

    expect(LabStock::onHand($this->reagent, $this->research))->toEqualWithDelta(100.0, 0.01);
});

it('lets everyone load, move to R&D and report R&D, and only administrators set minimums', function () {
    $colleague = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true]));
    $this->actingAs($colleague);

    Livewire::test(LabStockPage::class)
        ->assertOk()
        ->assertTableActionVisible('load', $this->reagent)
        ->assertTableActionVisible('toResearch', $this->reagent)
        ->assertTableActionVisible('researchLeft', $this->reagent)
        ->assertTableActionHidden('minimum', $this->reagent);
});

it('works out the margin from components, lab hours and shipping', function () {
    app(OrdersSettings::class)->fill(['hourly_cost' => 40.0])->save();

    $pad = ManufacturingHelper::product(['name' => 'High Grade Brain Pad', 'is_storable' => false]);
    $silicone = ManufacturingHelper::product(['name' => 'Silicone', 'cost' => 30]);
    ManufacturingHelper::bomLine(ManufacturingHelper::bom($pad), $silicone, 2);
    $staff = ManufacturingHelper::product(['name' => 'Support staff', 'is_storable' => false, 'cost' => 200]);

    $customer = Partner::create(['account_type' => AccountType::COMPANY->value, 'name' => 'Tekim']);
    $order = Orders::open(['partner_id' => $customer->id, 'vat_rate' => 22]);
    $order->lines()->create(['product_id' => $pad->id, 'quantity' => 3, 'unit_price' => 450]);
    $order->lines()->create(['product_id' => $staff->id, 'quantity' => 1, 'unit_price' => 600]);
    Orders::confirm($order->refresh());

    $mo = Orders::manufacturingOrders($order)->sole();
    Livewire::test(ProductionRelationManager::class, ['ownerRecord' => $order, 'pageClass' => EditOrder::class])
        ->assertOk()
        ->callTableAction('work', $mo, data: ['huvant_worked_hours' => 5]);

    expect(Orders::costing($order))->toMatchArray([
        'revenue'   => 1950.0,
        'materials' => 180.0,
        'labour'    => 200.0,
        'other'     => 200.0,
        'cost'      => 580.0,
        'margin'    => 1370.0,
    ]);

    Livewire::test(ManageOrdersSettings::class)->assertOk();
});

it('consumes lab material from production stock, never from R&D, when production is done', function () {
    LabStock::load($this->reagent, $this->production, 500, 'L-PROD');
    LabStock::moveToResearch($this->reagent, $this->warehouse, 100, Lot::query()->where('name', 'L-PROD')->firstOrFail());

    $pad = Product::query()->findOrFail(ManufacturingHelper::product(['name' => 'Kidney Biopsy Pad'])->id);
    // 30 mL of reagent per pad.
    ManufacturingHelper::bomLine(ManufacturingHelper::bom($pad), $this->reagent, 30);

    $customer = Partner::create(['account_type' => AccountType::COMPANY->value, 'name' => 'Vantive']);
    $order = Orders::open(['partner_id' => $customer->id, 'company_id' => ManufacturingHelper::company()->id]);
    $order->lines()->create(['product_id' => $pad->id, 'quantity' => 2]);
    Orders::confirm($order->refresh());

    $mo = Orders::manufacturingOrders($order)->sole();
    ManufacturingHelper::confirm($mo);
    ManufacturingHelper::produce($mo->refresh(), 2);

    expect(LabStock::onHand($this->reagent, $this->production))->toBe(340.0)
        ->and(LabStock::onHand($this->reagent, $this->research))->toBe(100.0)
        ->and(LabStock::onHand($pad, $this->production))->toBe(2.0);
});
