<?php

namespace Huvant\Orders\Models;

use Huvant\Orders\Enums\ClosingState;
use Huvant\Orders\Enums\Fulfilment;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Enums\SupplyType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Manufacturing\Models\Order as ManufacturingOrder;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'huvant_orders';

    protected $fillable = [
        'name', 'order_number', 'state', 'supply_type', 'fulfilment',
        'partner_id', 'contact_id', 'delivery_address_id', 'hand_delivery_user_id',
        'event', 'subject', 'offer_date', 'validity_date', 'expected_delivery_date', 'confirmed_at',
        'rental_starts_on', 'rental_ends_on', 'expected_return_date',
        'payment_terms', 'vat_rate', 'notes', 'closing_state', 'company_id', 'creator_id',
    ];

    /** @var array<string, mixed> */
    protected $attributes = [
        'state'         => 'draft',
        'supply_type'   => 'sale',
        'fulfilment'    => 'courier',
        'closing_state' => 'open',
        'vat_rate'      => 22,
    ];

    protected function casts(): array
    {
        return [
            'state'                  => OrderState::class,
            'supply_type'            => SupplyType::class,
            'fulfilment'             => Fulfilment::class,
            'closing_state'          => ClosingState::class,
            'offer_date'             => 'date',
            'validity_date'          => 'date',
            'expected_delivery_date' => 'date',
            'confirmed_at'           => 'datetime',
            'rental_starts_on'       => 'date',
            'rental_ends_on'         => 'date',
            'expected_return_date'   => 'date',
            'vat_rate'               => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            $order->creator_id ??= Auth::id();
            $order->company_id ??= current_company_id();
            $order->offer_date ??= today();
        });
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'contact_id');
    }

    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'delivery_address_id');
    }

    public function handDeliveryUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hand_delivery_user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class)->orderBy('sort')->orderBy('id');
    }

    public function manufacturingOrders(): HasManyThrough
    {
        return $this->hasManyThrough(ManufacturingOrder::class, OrderLine::class, 'order_id', 'id', 'id', 'manufacturing_order_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'huvant_order_id');
    }

    public function untaxedAmount(): float
    {
        return round($this->lines->sum(fn (OrderLine $line): float => $line->subtotal()), 2);
    }

    public function taxAmount(): float
    {
        return round($this->untaxedAmount() * (float) $this->vat_rate / 100, 2);
    }

    public function totalAmount(): float
    {
        return $this->untaxedAmount() + $this->taxAmount();
    }
}
