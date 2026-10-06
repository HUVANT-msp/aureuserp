<?php

use Huvant\Orders\Enums\Fulfilment;
use Huvant\Orders\Enums\ReturnStatus;
use Huvant\Orders\Enums\ShippingStatus;
use Huvant\Orders\Enums\SupplyType;
use Huvant\Orders\Filament\Resources\OrderResource\Pages\EditOrder;
use Huvant\Orders\Filament\Resources\OrderResource\RelationManagers\DeliveriesRelationManager;
use Huvant\Orders\Models\Delivery;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Support\Orders;
use Huvant\Orders\Support\Shipping;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;
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
    $this->warehouse = Shipping::warehouse(ManufacturingHelper::company()->id);
    $this->customer = Partner::create(['account_type' => AccountType::COMPANY->value, 'name' => 'Università Federico II']);
    $this->pad = InventoryHelper::lotTrackedProduct(['name' => 'Kidney Biopsy Pad', 'reference' => 'KBP', 'huvant_hs_code' => '9023.00']);
    $this->support = InventoryHelper::product(['name' => 'Support staff', 'is_storable' => false]);

    // One pad came back from a loan, two are new: the shipment mixes both lots.
    InventoryHelper::stockUp($this->pad, $this->warehouse->lotStockLocation, 1, InventoryHelper::lot($this->pad, 'L-RETURNED')->id);
    InventoryHelper::stockUp($this->pad, $this->warehouse->lotStockLocation, 2, InventoryHelper::lot($this->pad, 'L-NEW')->id);
});

function confirmedOrder(Partner $customer, array $lines, array $attributes = []): Order
{
    $order = Orders::open(array_merge(['partner_id' => $customer->id, 'company_id' => ManufacturingHelper::company()->id], $attributes));

    foreach ($lines as [$product, $quantity]) {
        $order->lines()->create(['product_id' => $product->id, 'quantity' => $quantity]);
    }

    return Orders::confirm($order->refresh());
}

function shipOut(Delivery $delivery): Delivery
{
    $operation = Operation::query()->findOrFail($delivery->id);
    Inventory::reserveTransfer($operation);
    Inventory::completeTransfer($operation->refresh());

    return $delivery->refresh();
}

it('opens a delivery for the goods of a confirmed order, not for services', function () {
    $order = confirmedOrder($this->customer, [[$this->pad, 3], [$this->support, 1]]);

    $delivery = $order->deliveries()->sole();

    expect($delivery->origin)->toBe($order->order_number)
        ->and($delivery->partner_id)->toBe($this->customer->id)
        ->and($delivery->huvant_shipping_status)->toBe(ShippingStatus::ToShip)
        ->and($delivery->moves()->pluck('product_id')->all())->toBe([$this->pad->id])
        ->and((float) $delivery->moves()->sole()->product_uom_qty)->toBe(3.0);
});

it('does not ship production for stock', function () {
    $order = confirmedOrder($this->customer, [[$this->pad, 3]], ['fulfilment' => Fulfilment::Stock, 'partner_id' => null]);

    expect($order->deliveries()->count())->toBe(0);
});

it('numbers the delivery note when the transfer is validated and lists every lot shipped', function () {
    Carbon::setTestNow('2026-10-09 09:00');
    $order = confirmedOrder($this->customer, [[$this->pad, 3]]);

    $delivery = shipOut($order->deliveries()->sole());
    Carbon::setTestNow();

    expect($delivery->state)->toBe(OperationState::DONE)
        ->and($delivery->huvant_delivery_note_number)->toBe('DDT-20261009-001')
        ->and($delivery->huvant_shipped_at->toDateString())->toBe('2026-10-09')
        ->and($delivery->huvant_shipping_status)->toBe(ShippingStatus::InTransit)
        ->and(collect(Shipping::lotsByProduct($delivery)[$this->pad->id])->sort()->values()->all())->toBe(['L-NEW', 'L-RETURNED']);

    Shipping::markDelivered($delivery, Carbon::parse('2026-10-11'));
    expect($delivery->refresh()->huvant_shipping_status)->toBe(ShippingStatus::Delivered)
        ->and($delivery->huvant_delivered_at->toDateString())->toBe('2026-10-11');

    $this->get(route('huvant.orders.delivery-note', $delivery->id))->assertOk()->assertHeader('content-type', 'application/pdf');
});

it('brings loaned goods back to stock with their lots and tracks the return date', function () {
    $order = confirmedOrder($this->customer, [[$this->pad, 3]], [
        'supply_type' => SupplyType::Loan, 'expected_return_date' => today()->addDays(10),
    ]);
    expect(Shipping::returnStatus($order))->toBe(ReturnStatus::NotShipped);

    $delivery = shipOut($order->deliveries()->sole());
    expect(Shipping::returnStatus($order))->toBe(ReturnStatus::Out);

    $order->update(['expected_return_date' => today()->subDay()]);
    expect(Shipping::returnStatus($order))->toBe(ReturnStatus::Overdue);

    $return = Shipping::registerReturn($delivery);
    expect($return->huvant_order_id)->toBe($order->id)
        ->and($return->isReturn())->toBeTrue()
        ->and(fn () => Shipping::registerReturn($delivery))->toThrow(RuntimeException::class);

    shipOut($return);

    expect($delivery->refresh()->huvant_shipping_status)->toBe(ShippingStatus::Returned)
        ->and(Shipping::returnStatus($order))->toBe(ReturnStatus::Returned)
        ->and(InventoryHelper::onHand($this->pad, $this->warehouse->lotStockLocation))->toBe(3.0);
});

it('cancels the open delivery with the order', function () {
    $order = confirmedOrder($this->customer, [[$this->pad, 1]]);

    Orders::cancel($order);

    expect($order->deliveries()->sole()->state)->toBe(OperationState::CANCELED);
});

it('shows the shipping of an order and edits its courier details', function () {
    $order = confirmedOrder($this->customer, [[$this->pad, 2]]);
    $delivery = shipOut($order->deliveries()->sole());

    Livewire::test(DeliveriesRelationManager::class, ['ownerRecord' => $order, 'pageClass' => EditOrder::class])
        ->assertOk()
        ->assertCanSeeTableRecords([$delivery])
        ->mountTableAction('edit', $delivery)
        ->set('mountedActions.0.data', [
            'huvant_carrier'         => 'DHL', 'huvant_tracking_number' => '1234567890', 'huvant_packages' => 1,
            'huvant_shipping_status' => ShippingStatus::InTransit->value,
        ])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($delivery->refresh()->huvant_carrier)->toBe('DHL')
        ->and($delivery->huvant_tracking_number)->toBe('1234567890');
});
