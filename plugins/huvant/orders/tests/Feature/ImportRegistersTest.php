<?php

use Huvant\Orders\Support\LabStock;
use Illuminate\Support\Facades\URL;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Models\Product;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../webkul/manufacturing/tests/Helpers/ManufacturingHelper.php';

beforeEach(function () {
    foreach (['contacts', 'inventories', 'manufacturing', 'huvant-orders'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);
    }

    Package::$plugins = Plugin::all()->keyBy('name');
    URL::resolveMissingNamedRoutesUsing(fn () => '#');
    ManufacturingHelper::actingAsAdmin();

    $this->file = tempnam(sys_get_temp_dir(), 'registers');
    file_put_contents($this->file, json_encode([
        'companies' => [
            ['name' => 'Bellco S.r.l.', 'street1' => 'Via Camurana, 1', 'zip' => '41037', 'city' => 'Mirandola', 'province' => 'MO', 'country' => 'Italy', 'tax_id' => 'IT06157780963', 'phone' => null, 'sdi' => '893R9T8', 'customs' => null],
            ['name' => 'Università Federico II', 'street1' => 'Via Sergio Pansini 5', 'zip' => '80131', 'city' => 'Napoli', 'province' => 'NA', 'country' => 'Italy', 'tax_id' => 'IT00876220633', 'phone' => null, 'sdi' => null, 'customs' => null],
        ],
        'addresses' => [
            ['company' => 'Università Federico II', 'name' => 'Azienda Universitaria Federico II', 'street1' => 'Via S. Pansini 5', 'zip' => '80131', 'city' => 'Napoli', 'province' => 'NA', 'country' => 'Italy', 'onsite_contact' => 'Prof. Antonio Pisani'],
        ],
        'products' => [
            ['name' => 'Kidney Biopsy Pad', 'reference' => 'KBP-T', 'category' => 'Nefro', 'price' => null, 'production_days' => 5, 'kind' => 'manufactured', 'hs_code' => null],
            ['name' => 'Personale di supporto', 'reference' => 'PERS-T', 'category' => null, 'price' => 600, 'production_days' => null, 'kind' => 'service'],
        ],
        'lab_items' => [
            ['name' => 'EMIM-BF4, 99%', 'reference' => 'H-102-T', 'cas' => '143314-16-3', 'supplier_code' => 'IL-0006-UP-0250', 'package' => '250 mL', 'supplier' => 'IOLITEC', 'kind' => 'substance', 'use' => 'research', 'minimum' => 2.0, 'position' => 'Mobile sotto cappa', 'notes' => null],
        ],
    ]));
});

afterEach(fn () => @unlink($this->file));

it('imports the registers once, filling only what is missing, and never creates people', function () {
    $people = Partner::query()->where('account_type', AccountType::INDIVIDUAL)->count();

    $this->artisan('huvant:import-registers', ['file' => $this->file, '--dry-run' => true])->assertSuccessful();
    expect(Partner::query()->where('name', 'Bellco S.r.l.')->exists())->toBeFalse();

    $this->artisan('huvant:import-registers', ['file' => $this->file])->assertSuccessful();
    $this->artisan('huvant:import-registers', ['file' => $this->file])->assertSuccessful();

    $bellco = Partner::query()->where('name', 'Bellco S.r.l.')->sole();
    $federico = Partner::query()->where('name', 'Università Federico II')->sole();
    $pad = Product::query()->where('reference', 'KBP-T')->sole();
    $staff = Product::query()->where('reference', 'PERS-T')->sole();
    $reagent = Product::query()->where('reference', 'H-102-T')->sole();

    expect($bellco->huvant_sdi_code)->toBe('893R9T8')
        ->and($bellco->city)->toBe('Mirandola (MO)')
        ->and($federico->addresses()->where('name', 'Azienda Universitaria Federico II')->value('huvant_onsite_contact'))->toBe('Prof. Antonio Pisani')
        ->and($pad->tracking)->toBe(ProductTracking::LOT)
        ->and($pad->is_storable)->toBeTrue()
        ->and($pad->huvant_production_days)->toBe(5)
        ->and($staff->is_storable)->toBeFalse()
        ->and((float) $staff->price)->toBe(600.0)
        ->and($reagent->huvant_lab_use)->toBe(LabStock::RESEARCH)
        ->and($reagent->huvant_cas_number)->toBe('143314-16-3')
        ->and($reagent->uom->name)->toBe('mL')
        ->and((float) $reagent->huvant_package_quantity)->toBe(250.0)
        ->and(LabStock::minimum($reagent))->toBe(500.0)
        ->and(Partner::query()->where('account_type', AccountType::INDIVIDUAL)->count())->toBe($people)
        ->and(Product::query()->where('reference', 'H-102-T')->count())->toBe(1);
});
