<?php

namespace Huvant\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Manufacturing\Models\Order as ManufacturingOrder;
use Webkul\Product\Models\Product;

class OrderLine extends Model
{
    protected $table = 'huvant_order_lines';

    protected $fillable = [
        'order_id', 'sort', 'product_id', 'description', 'quantity', 'unit_price', 'discount', 'manufacturing_order_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity'   => 'decimal:4',
            'unit_price' => 'decimal:4',
            'discount'   => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function manufacturingOrder(): BelongsTo
    {
        return $this->belongsTo(ManufacturingOrder::class, 'manufacturing_order_id');
    }

    /** Discount included, VAT excluded. */
    public function subtotal(): float
    {
        return round((float) $this->quantity * (float) $this->unit_price * (1 - (float) $this->discount / 100), 2);
    }
}
