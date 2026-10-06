<?php

namespace Huvant\Orders\Models;

use Huvant\Orders\Enums\UnitStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Models\Product;

/** One finished piece, identified by its code, with the material lots it was made from. */
class ProductUnit extends Model
{
    protected $table = 'huvant_product_units';

    protected $fillable = ['product_id', 'code', 'production_date', 'expiry_date', 'status', 'status_since', 'notes', 'creator_id'];

    /** @var array<string, mixed> */
    protected $attributes = ['status' => 'in_lab'];

    protected function casts(): array
    {
        return [
            'production_date' => 'date',
            'expiry_date'     => 'date',
            'status'          => UnitStatus::class,
            'status_since'    => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(fn (ProductUnit $unit) => $unit->creator_id ??= Auth::id());
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ProductUnitMaterial::class);
    }
}
