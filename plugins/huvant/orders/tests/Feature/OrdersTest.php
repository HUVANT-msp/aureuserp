<?php

use Huvant\Orders\Enums\ClosingState;
use Huvant\Orders\Enums\Fulfilment;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Enums\ProductionStatus;
use Huvant\Orders\Enums\SupplyType;
use Huvant\Orders\Filament\Resources\OrderResource\Pages\CreateOrder;
use Huvant\Orders\Filament\Resources\OrderResource\Pages\EditOrder;
use Huvant\Orders\Filament\Resources\OrderResource\Pages\ListOrders;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Support\ErpSetup;
use Huvant\Orders\Support\Orders;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\EditPartner;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ManageAddresses;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\EditProduct;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Facades\Manufacturing;
use Webkul\Manufacturing\Settings\OperationSettings;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Product\Filament\Resources\ProductResource;
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

    $this->admin = ManufacturingHelper::actingAsAdmin();
    $this->warehouse = InventoryHelper::warehouse();
    $this->customer = Partner::withoutEvents(fn () => Partner::create([
        'account_type' => AccountType::COMPANY->value,
        'name'         => 'Nipro Corporation',
    ]));

    // A pad made in the lab from two components, and a service with no bill of materials.
    $this->pad = ManufacturingHelper::product(['name' => 'High Grade Brain Pad', 'reference' => 'HGBP', 'price' => 450, 'huvant_role' => 'product']);
    $this->silicone = ManufacturingHelper::product(['name' => 'Silicone']);
    $this->mould = ManufacturingHelper::product(['name' => 'Mould insert']);
    $bom = ManufacturingHelper::bom($this->pad);
    ManufacturingHelper::bomLine($bom, $this->silicone, 0.5);
    ManufacturingHelper::bomLine($bom, $this->mould, 1);
    $this->support = ManufacturingHelper::product(['name' => 'Support staff', 'reference' => 'PERS', 'is_storable' => false]);
});

function offerFor(Partner $customer, array $lines, array $attributes = []): Order
{
    $order = Orders::open(array_merge(['partner_id' => $customer->id, 'expected_delivery_date' => today()->addDays(20)], $attributes));

    foreach ($lines as $sort => [$product, $quantity, $price, $discount]) {
        $order->lines()->create([
            'sort' => $sort, 'product_id' => $product->id, 'quantity' => $quantity, 'unit_price' => $price, 'discount' => $discount,
        ]);
    }

    return $order->refresh();
}

it('numbers offers and orders with their date and a counter that starts again every year', function () {
    $first = Orders::open(['partner_id' => $this->customer->id, 'offer_date' => '2026-03-02']);
    $second = Orders::open(['partner_id' => $this->customer->id, 'offer_date' => '2026-10-05']);
    $nextYear = Orders::open(['partner_id' => $this->customer->id, 'offer_date' => '2027-01-08']);

    expect($first->name)->toBe('OFF-20260302-001')
        ->and($second->name)->toBe('OFF-20261005-002')
        ->and($nextYear->name)->toBe('OFF-20270108-001')
        ->and($first->order_number)->toBeNull()
        ->and($first->state)->toBe(OrderState::Draft)
        ->and($first->closing_state)->toBe(ClosingState::Open);

    $second->lines()->create(['product_id' => $this->support->id, 'quantity' => 1]);
    Carbon::setTestNow('2026-10-07 10:00');
    Orders::confirm($second->refresh());
    Carbon::setTestNow();

    expect($second->order_number)->toBe('ORD-20261007-001')
        ->and($second->name)->toBe('OFF-20261005-002');
});

it('totals the lines with their discount, then VAT', function () {
    $order = offerFor($this->customer, [[$this->pad, 3, 450, 10], [$this->support, 2, 600, 0]], ['vat_rate' => 22]);

    expect($order->untaxedAmount())->toBe(2415.0)
        ->and($order->taxAmount())->toBe(531.3)
        ->and($order->totalAmount())->toBe(2946.3);
});

it('raises a draft manufacturing order for each line with a bill of materials when confirmed', function () {
    $order = offerFor($this->customer, [[$this->pad, 3, 450, 0], [$this->support, 1, 600, 0]]);

    Orders::confirm($order);

    $padLine = $order->lines()->where('product_id', $this->pad->id)->first();
    $mo = $padLine->manufacturingOrder;

    expect($order->refresh()->state)->toBe(OrderState::Confirmed)
        ->and($order->confirmed_at)->not->toBeNull()
        ->and($order->lines()->where('product_id', $this->support->id)->value('manufacturing_order_id'))->toBeNull()
        ->and($mo->state)->toBe(ManufacturingOrderState::DRAFT)
        ->and((float) $mo->quantity)->toBe(3.0)
        ->and($mo->origin)->toBe($order->order_number)
        ->and($mo->rawMaterialMoves->pluck('product_id')->sort()->values()->all())->toBe(collect([$this->silicone->id, $this->mould->id])->sort()->values()->all())
        ->and((float) $mo->rawMaterialMoves->firstWhere('product_id', $this->silicone->id)->product_uom_qty)->toBe(1.5)
        ->and(Orders::productionStatus($order))->toBe(ProductionStatus::NotTakenOn);

    // The lab takes the work on by confirming the manufacturing order.
    Manufacturing::confirmManufacturingOrder($mo->refresh());
    expect(Orders::productionStatus($order))->toBe(ProductionStatus::ToStart);
});

it('reports production as late once the deadline has passed', function () {
    $order = offerFor($this->customer, [[$this->pad, 1, 450, 0]], ['expected_delivery_date' => today()->subDay()]);
    Orders::confirm($order);

    expect(Orders::productionStatus($order))->toBe(ProductionStatus::Late);
});

it('needs a customer and a product before confirming, except production for stock', function () {
    $empty = offerFor($this->customer, []);
    expect(fn () => Orders::confirm($empty))->toThrow(RuntimeException::class, 'Add at least one product');

    $noCustomer = Orders::open(['fulfilment' => Fulfilment::Courier]);
    $noCustomer->lines()->create(['product_id' => $this->pad->id, 'quantity' => 1]);
    expect(fn () => Orders::confirm($noCustomer->refresh()))->toThrow(RuntimeException::class, 'Choose the customer');

    $forStock = Orders::open(['fulfilment' => Fulfilment::Stock]);
    $forStock->lines()->create(['product_id' => $this->pad->id, 'quantity' => 4]);
    Orders::confirm($forStock->refresh());

    expect($forStock->state)->toBe(OrderState::Confirmed)
        ->and(Orders::manufacturingOrders($forStock))->toHaveCount(1);
});

it('cancels the unfinished manufacturing orders with the order', function () {
    $order = offerFor($this->customer, [[$this->pad, 2, 450, 0]]);
    Orders::confirm($order);

    Orders::cancel($order);

    expect($order->refresh()->state)->toBe(OrderState::Cancelled)
        ->and(Orders::manufacturingOrders($order)->first()->state)->toBe(ManufacturingOrderState::CANCEL)
        ->and(Orders::productionStatus($order))->toBe(ProductionStatus::None);
});

it('moves offers through sent, on hold, rejected and expired', function () {
    $offer = offerFor($this->customer, [[$this->pad, 1, 450, 0]]);
    Orders::send($offer);
    Orders::hold($offer);
    expect($offer->state)->toBe(OrderState::Pending)
        ->and($offer->state->isVisibleToProduction())->toBeTrue()
        ->and(fn () => Orders::send($offer))->toThrow(RuntimeException::class);

    Orders::reject($offer);
    expect($offer->state)->toBe(OrderState::Rejected)
        ->and(fn () => Orders::confirm($offer))->toThrow(RuntimeException::class);

    $stale = offerFor($this->customer, [[$this->pad, 1, 450, 0]], ['validity_date' => Carbon::parse('2026-09-01')]);
    Orders::send($stale);
    expect(Orders::expireOverdue(Carbon::parse('2026-10-05')))->toBe(1)
        ->and($stale->refresh()->state)->toBe(OrderState::Expired);
});

it('closes confirmed orders as closed or contested', function () {
    $order = offerFor($this->customer, [[$this->support, 1, 600, 0]]);
    expect(fn () => Orders::close($order))->toThrow(RuntimeException::class);

    Orders::confirm($order);
    Orders::close($order, ClosingState::Contested);

    expect($order->refresh()->closing_state)->toBe(ClosingState::Contested);
});

it('prints the delivery note reason and knows what comes back', function () {
    expect(SupplyType::Loan->deliveryNoteReason())->toContain('da restituire')
        ->and(SupplyType::Loan->isReturnable())->toBeTrue()
        ->and(SupplyType::OnApproval->isReturnable())->toBeTrue()
        ->and(SupplyType::Sale->isReturnable())->toBeFalse();
});

it('shows prices to administrators only', function () {
    $colleague = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true]));

    expect(Orders::canSeePrices($this->admin))->toBeTrue()
        ->and(Orders::canSeePrices($colleague))->toBeFalse();

    $order = offerFor($this->customer, [[$this->pad, 1, 450, 0]]);

    $this->get(route('huvant.orders.offer', $order))->assertOk()->assertHeader('content-type', 'application/pdf');
    $this->actingAs($colleague)->get(route('huvant.orders.offer', $order))->assertForbidden();
});

it('creates an offer from the form and confirms it from the order page', function () {
    Livewire::test(ListOrders::class)->assertOk();

    Livewire::test(CreateOrder::class)
        ->fillForm([
            'partner_id'  => $this->customer->id,
            'supply_type' => SupplyType::Sale->value,
            'fulfilment'  => Fulfilment::Courier->value,
            'offer_date'  => today()->toDateString(),
            'vat_rate'    => 0,
            'event'       => 'ERA Congress',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $order = Order::query()->latest('id')->firstOrFail();
    $order->lines()->create(['product_id' => $this->pad->id, 'quantity' => 2, 'unit_price' => 450]);

    Livewire::test(EditOrder::class, ['record' => $order->getRouteKey()])
        ->assertOk()
        ->callAction('confirm');

    expect($order->refresh()->state)->toBe(OrderState::Confirmed)
        ->and($order->event)->toBe('ERA Congress')
        ->and(Orders::manufacturingOrders($order))->toHaveCount(1);
});

it('keeps e-invoicing, event and customs data on contacts and products', function () {
    $this->customer->update(['huvant_sdi_code' => 'T9K4ZHO', 'huvant_pec' => 'huvant@postecert.it']);
    $this->pad->update(['huvant_hs_code' => '9023.00', 'huvant_production_days' => 10]);

    expect($this->customer->refresh()->huvant_sdi_code)->toBe('T9K4ZHO')
        ->and($this->pad->refresh()->huvant_production_days)->toBe(10)
        ->and(DB::table('products_products')->where('id', $this->pad->id)->value('huvant_hs_code'))->toBe('9023.00');
});

it('edits the e-invoicing fields on the company and the production fields on the product', function () {
    Livewire::test(EditPartner::class, ['record' => $this->customer->getRouteKey()])
        ->assertOk()
        ->fillForm(['huvant_sdi_code' => 'M5UXCR1', 'huvant_customs_code' => '21515141'])
        ->call('save')
        ->assertHasNoFormErrors();

    Livewire::test(ManageAddresses::class, ['record' => $this->customer->getRouteKey()])->assertOk();

    Livewire::test(EditProduct::class, ['record' => $this->pad->getRouteKey()])
        ->assertOk()
        ->fillForm(['huvant_hs_code' => '9023.00', 'huvant_production_days' => 5])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->customer->refresh()->huvant_sdi_code)->toBe('M5UXCR1')
        ->and($this->customer->huvant_customs_code)->toBe('21515141')
        ->and($this->pad->refresh()->huvant_production_days)->toBe(5);
});

it('turns off work orders, work centres and by-products: production is one order per product', function () {
    ErpSetup::atomicProduction();

    $settings = app(OperationSettings::class);

    expect($settings->enable_work_orders)->toBeFalse()
        ->and($settings->enable_work_order_dependencies)->toBeFalse()
        ->and($settings->enable_byproducts)->toBeFalse();
});

it('hides product prices and costs from colleagues who are not administrators', function () {
    $colleague = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true]));

    expect(ProductResource::canSeePrices())->toBeTrue();
    $this->actingAs($colleague);
    expect(ProductResource::canSeePrices())->toBeFalse();

    // The product form follows the rule; checked as administrator with the rule saying no.
    $this->actingAs($this->admin);
    $rule = ProductResource::$pricesVisibleUsing;
    ProductResource::$pricesVisibleUsing = fn (): bool => false;

    try {
        Livewire::test(EditProduct::class, ['record' => $this->pad->getRouteKey()])
            ->assertOk()
            ->assertFormFieldIsHidden('price')
            ->assertFormFieldIsHidden('cost')
            ->assertFormFieldIsVisible('uom_id');
    } finally {
        ProductResource::$pricesVisibleUsing = $rule;
    }
});

it('saves a line left without discount or price, and never half an offer', function () {
    Livewire::test(CreateOrder::class)
        ->fillForm([
            'partner_id'  => $this->customer->id,
            'supply_type' => SupplyType::Sale->value,
            'fulfilment'  => Fulfilment::Courier->value,
            'offer_date'  => today()->toDateString(),
            'vat_rate'    => 22,
            'lines'       => [['product_id' => $this->pad->id, 'quantity' => 2, 'unit_price' => 400, 'discount' => null]],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $line = Order::query()->latest('id')->firstOrFail()->lines()->sole();

    expect((float) $line->discount)->toBe(0.0)
        ->and((float) $line->unit_price)->toBe(400.0);
});
