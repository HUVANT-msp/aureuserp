<?php

namespace Huvant\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Manufacturing\Models\Order as ManufacturingOrder;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

class OrderLine extends Model
{
    protected $table = 'huvant_order_lines';

    protected $fillable = [
        'order_id', 'sort', 'product_id', 'description', 'quantity', 'stock_quantity', 'production_quantity',
        'manufacturing_managed_at', 'manufacturing_managed_by', 'unit_price', 'discount', 'manufacturing_order_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity'                 => 'decimal:4',
            'stock_quantity'           => 'integer',
            'production_quantity'      => 'integer',
            'manufacturing_managed_at' => 'datetime',
            'unit_price'               => 'decimal:4',
            'discount'                 => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        // An empty price or discount on the form means none.
        static::saving(function (OrderLine $line): void {
            $line->unit_price ??= 0;
            $line->discount ??= 0;
        });
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

    public function manufacturingManagedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manufacturing_managed_by');
    }

    public function productionTasks(): HasMany
    {
        return $this->hasMany(ProductionTask::class);
    }

    public function manufacturingEntries(): HasMany
    {
        return $this->hasMany(ManufacturingEntry::class)->orderBy('position');
    }

    /** Discount included, VAT excluded. */
    public function subtotal(): float
    {
        return round((float) $this->quantity * (float) $this->unit_price * (1 - (float) $this->discount / 100), 2);
    }
}
