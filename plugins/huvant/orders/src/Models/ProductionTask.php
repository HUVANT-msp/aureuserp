<?php

namespace Huvant\Orders\Models;

use Huvant\Orders\Enums\ProductionTaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

class ProductionTask extends Model
{
    protected $table = 'huvant_production_tasks';

    protected $fillable = [
        'order_id', 'order_line_id', 'product_id', 'quantity', 'completed_quantity',
        'status', 'completed_at', 'managed_by', 'notes',
    ];

    /** @var array<string, mixed> */
    protected $attributes = ['status' => 'pending', 'completed_quantity' => 0];

    protected function casts(): array
    {
        return [
            'quantity'           => 'integer',
            'completed_quantity' => 'integer',
            'status'             => ProductionTaskStatus::class,
            'completed_at'       => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderLine(): BelongsTo
    {
        return $this->belongsTo(OrderLine::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function managedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    public function remaining(): int
    {
        return max(0, $this->quantity - $this->completed_quantity);
    }
}
