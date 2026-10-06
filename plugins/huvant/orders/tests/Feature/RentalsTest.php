<?php

use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\SupplyType;
use Huvant\Orders\Filament\Pages\RentalCalendar;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Support\Orders;
use Huvant\Orders\Support\Rentals;
use Huvant\Orders\Support\Shipping;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\Product;
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
    $this->warehouse = Shipping::warehouse(ManufacturingHelper::company()->id);

    // Two torsos in the warehouse: the rental item is counted from its pieces in stock.
    $this->torso = Product::query()->findOrFail(ManufacturingHelper::product(['name' => 'Renal biopsy torso', 'reference' => 'BBRN', 'huvant_role' => ItemRole::Rental->value])->id);
    InventoryHelper::stockUp($this->torso, $this->warehouse->lotStockLocation, 2);
    $this->staff = ManufacturingHelper::product(['name' => 'Support staff', 'reference' => 'PERS', 'huvant_role' => ItemRole::Service->value]);
});

function rentalOrder(Partner $customer, $product, int $units, ?string $from, ?string $until): Order
{
    $order = Orders::open([
        'partner_id'       => $customer->id, 'supply_type' => SupplyType::Loan, 'company_id' => ManufacturingHelper::company()->id,
        'rental_starts_on' => $from, 'rental_ends_on' => $until,
    ]);
    $order->lines()->create(['product_id' => $product->id, 'quantity' => $units]);

    return $order->refresh();
}

it('makes a rental item a stocked piece, counted as units', function () {
    expect($this->torso->is_storable)->toBeTrue()
        ->and($this->torso->tracking->value)->toBe('qty')
        ->and(Rentals::units($this->torso))->toBe(2)
        ->and(Rentals::items()->pluck('id')->all())->toBe([$this->torso->id]);
});

it('needs a rental period before confirming a rental', function () {
    $order = rentalOrder($this->customer, $this->torso, 1, null, null);

    expect(fn () => Orders::confirm($order))->toThrow(RuntimeException::class, 'rental period');
});

it('counts pieces booked per item and day, from orders on hold and confirmed', function () {
    Orders::confirm(rentalOrder($this->customer, $this->torso, 1, '2026-11-10', '2026-11-12'));
    Orders::hold(rentalOrder($this->customer, $this->torso, 1, '2026-11-12', '2026-11-14'));
    rentalOrder($this->customer, $this->torso, 2, '2026-11-01', '2026-11-30'); // a draft takes nothing

    $occupancy = Rentals::occupancy(Carbon::parse('2026-11-01'), Carbon::parse('2026-11-30'))[$this->torso->id];

    expect($occupancy['2026-11-10'])->toBe(1)
        ->and($occupancy['2026-11-12'])->toBe(2)
        ->and($occupancy['2026-11-14'])->toBe(1)
        ->and($occupancy)->not->toHaveKey('2026-11-15');
});

it('refuses to overbook an item unless asked to', function () {
    Orders::confirm(rentalOrder($this->customer, $this->torso, 2, '2026-11-10', '2026-11-12'));
    $second = rentalOrder($this->customer, $this->torso, 1, '2026-11-12', '2026-11-13');

    expect(Rentals::conflicts($second))->toBe(['Renal biopsy torso: 1 requested, 2 of 2 already booked in that period'])
        ->and(fn () => Orders::confirm($second))->toThrow(RuntimeException::class, 'Not enough units');

    $later = rentalOrder($this->customer, $this->torso, 2, '2026-11-13', '2026-11-14');
    expect(Rentals::conflicts($later))->toBe([]);

    Orders::confirm($second, allowOverbooking: true);
    expect($second->refresh()->order_number)->not->toBeNull();
});

it('still counts the pieces that are out with a customer, until they are back', function () {
    $order = Orders::confirm(rentalOrder($this->customer, $this->torso, 2, today()->toDateString(), today()->addDays(3)->toDateString()));
    $delivery = $order->deliveries()->sole();

    $operation = Operation::query()->findOrFail($delivery->id);
    Inventory::reserveTransfer($operation);
    Inventory::completeTransfer($operation->refresh());

    expect(Rentals::units($this->torso))->toBe(2);

    $return = Shipping::registerReturn($delivery->refresh());
    $operation = Operation::query()->findOrFail($return->id);
    Inventory::reserveTransfer($operation);
    Inventory::completeTransfer($operation->refresh());

    expect(Rentals::units($this->torso))->toBe(2);
});

it('shows the month with its bookings', function () {
    Orders::confirm(rentalOrder($this->customer, $this->torso, 1, today()->toDateString(), today()->addDay()->toDateString()));

    Livewire::test(RentalCalendar::class)
        ->assertOk()
        ->assertSee('Renal biopsy torso')
        ->assertSee('AB Medica')
        ->call('shiftMonth', 1)
        ->assertOk();
});
