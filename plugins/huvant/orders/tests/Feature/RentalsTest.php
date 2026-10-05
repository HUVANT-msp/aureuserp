<?php

use Huvant\Orders\Enums\SupplyType;
use Huvant\Orders\Filament\Pages\RentalCalendar;
use Huvant\Orders\Filament\Resources\RentalCategoryResource\Pages\ManageRentalCategories;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Models\RentalCategory;
use Huvant\Orders\Support\Orders;
use Huvant\Orders\Support\Rentals;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

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

    $this->customer = Partner::create(['account_type' => AccountType::COMPANY->value, 'name' => 'AB Medica']);
    $this->torsos = RentalCategory::create(['name' => 'Renal biopsy torso', 'units' => 4]);
    // Rental items are services: they do not leave stock through a sale.
    $this->torsoRental = ManufacturingHelper::product(['name' => 'Renal biopsy torso rental', 'reference' => 'BBRN', 'is_storable' => false, 'huvant_rental_category_id' => $this->torsos->id]);
    $this->staff = ManufacturingHelper::product(['name' => 'Support staff', 'reference' => 'PERS', 'is_storable' => false]);
});

function rentalOrder(Partner $customer, $product, int $units, ?string $from, ?string $until): Order
{
    $order = Orders::open([
        'partner_id'       => $customer->id, 'supply_type' => SupplyType::Loan,
        'rental_starts_on' => $from, 'rental_ends_on' => $until,
    ]);
    $order->lines()->create(['product_id' => $product->id, 'quantity' => $units]);

    return $order->refresh();
}

it('needs a rental period before confirming a rental', function () {
    $order = rentalOrder($this->customer, $this->torsoRental, 1, null, null);

    expect(fn () => Orders::confirm($order))->toThrow(RuntimeException::class, 'rental period');
});

it('counts units booked per category and day, from orders on hold and confirmed', function () {
    Orders::confirm(rentalOrder($this->customer, $this->torsoRental, 3, '2026-11-10', '2026-11-12'));
    Orders::hold(rentalOrder($this->customer, $this->torsoRental, 1, '2026-11-12', '2026-11-14'));
    rentalOrder($this->customer, $this->torsoRental, 4, '2026-11-01', '2026-11-30'); // a draft takes nothing

    $occupancy = Rentals::occupancy(Carbon::parse('2026-11-01'), Carbon::parse('2026-11-30'))[$this->torsos->id];

    expect($occupancy['2026-11-10'])->toBe(3)
        ->and($occupancy['2026-11-12'])->toBe(4)
        ->and($occupancy['2026-11-14'])->toBe(1)
        ->and($occupancy)->not->toHaveKey('2026-11-15');
});

it('refuses to overbook a category unless asked to', function () {
    Orders::confirm(rentalOrder($this->customer, $this->torsoRental, 3, '2026-11-10', '2026-11-12'));
    $second = rentalOrder($this->customer, $this->torsoRental, 2, '2026-11-12', '2026-11-13');

    expect(Rentals::conflicts($second))->toBe(['Renal biopsy torso: 2 requested, 3 of 4 already booked in that period'])
        ->and(fn () => Orders::confirm($second))->toThrow(RuntimeException::class, 'Not enough units');

    $fits = rentalOrder($this->customer, $this->torsoRental, 1, '2026-11-12', '2026-11-13');
    expect(Rentals::conflicts($fits))->toBe([]);

    Orders::confirm($second, allowOverbooking: true);
    expect($second->refresh()->order_number)->not->toBeNull();
});

it('shows the month with its bookings and manages the categories', function () {
    Orders::confirm(rentalOrder($this->customer, $this->torsoRental, 2, today()->toDateString(), today()->addDay()->toDateString()));

    Livewire::test(RentalCalendar::class)
        ->assertOk()
        ->assertSee('Renal biopsy torso')
        ->assertSee('AB Medica')
        ->call('shiftMonth', 1)
        ->assertOk();

    Livewire::test(ManageRentalCategories::class)
        ->assertOk()
        ->callAction('create', data: ['name' => 'Oculus station', 'units' => 2])
        ->assertHasNoActionErrors();

    expect(RentalCategory::query()->where('name', 'Oculus station')->value('units'))->toBe(2);
});
