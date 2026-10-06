<?php

namespace Huvant\Orders\Models;

use Huvant\Orders\Enums\ManufacturingSource;
use Huvant\Orders\Enums\ProductionTaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

/** One physical piece requested by one offer line. */
class ManufacturingEntry extends Model
{
    protected $table = 'huvant_manufacturing_entries';

    protected $fillable = [
        'order_id', 'order_line_id', 'product_id', 'position', 'source',
        'product_unit_id', 'managed_at', 'managed_by',
    ];

    protected function casts(): array
    {
        return [
            'position'   => 'integer',
            'source'     => ManufacturingSource::class,
            'managed_at' => 'datetime',
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

    public function productUnit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class);
    }

    public function productionTask(): HasOne
    {
        return $this->hasOne(ProductionTask::class, 'manufacturing_entry_id');
    }

    public function managedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    public function isManaged(): bool
    {
        return $this->source !== null;
    }

    public function isComplete(): bool
    {
        return match ($this->source) {
            ManufacturingSource::Stock => $this->product_unit_id !== null,
            ManufacturingSource::Production => $this->product_unit_id !== null
                && $this->productionTask?->status === ProductionTaskStatus::Completed,
            default => false,
        };
    }
}
