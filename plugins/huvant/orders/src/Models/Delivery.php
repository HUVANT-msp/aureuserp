<?php

namespace Huvant\Orders\Models;

use Huvant\Orders\Enums\ShippingStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Inventory\Models\Operation;

/** An outgoing transfer (or the return of one) raised by an order. */
class Delivery extends Operation
{
    private const SHIPPING_FIELDS = [
        'huvant_order_id', 'huvant_delivery_note_number', 'huvant_carrier', 'huvant_tracking_number', 'huvant_shipping_cost',
        'huvant_packages', 'huvant_weight_kg', 'huvant_shipped_at', 'huvant_delivered_at', 'huvant_shipping_status',
    ];

    public function __construct(array $attributes = [])
    {
        $this->mergeFillable(self::SHIPPING_FIELDS);
        $this->mergeCasts([
            'huvant_shipping_status' => ShippingStatus::class,
            'huvant_shipped_at'      => 'date',
            'huvant_delivered_at'    => 'date',
            'huvant_shipping_cost'   => 'decimal:2',
            'huvant_weight_kg'       => 'decimal:2',
        ]);

        parent::__construct($attributes);
    }

    /** Chatter, activity log and attachments stay on the inventory transfer. */
    public function getMorphClass(): string
    {
        return (new Operation)->getMorphClass();
    }

    public function huvantOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'huvant_order_id');
    }

    public function isReturn(): bool
    {
        return $this->return_id !== null;
    }
}
