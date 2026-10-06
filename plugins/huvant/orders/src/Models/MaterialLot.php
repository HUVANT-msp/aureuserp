<?php

namespace Huvant\Orders\Models;

use Huvant\Orders\Enums\LabArea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Models\Product;

/** A package of a raw material in the lab: its lot, expiry, and how much of it is left. */
class MaterialLot extends Model
{
    protected $table = 'huvant_material_lots';

    protected $fillable = [
        'material_id', 'lot_number', 'expiry_date', 'received_on', 'area',
        'initial_quantity', 'remaining_quantity', 'finished_at', 'notes', 'creator_id',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date'        => 'date',
            'received_on'        => 'date',
            'area'               => LabArea::class,
            'initial_quantity'   => 'decimal:4',
            'remaining_quantity' => 'decimal:4',
            'finished_at'        => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn (MaterialLot $lot) => $lot->creator_id ??= Auth::id());
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'material_id')->withTrashed();
    }

    /** Still in the lab: not marked finished, something left. */
    public function scopeInLab(Builder $query): Builder
    {
        return $query->whereNull('finished_at')->where('remaining_quantity', '>', 0);
    }

    public function remainingShare(): float
    {
        return (float) $this->initial_quantity > 0 ? max(0, min(1, (float) $this->remaining_quantity / (float) $this->initial_quantity)) : 0;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->endOfDay()->isPast();
    }
}
