<?php

namespace Huvant\Orders\Models;

use Huvant\Orders\Enums\UnitStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

/** One finished piece, identified by its code, with the material lots it was made from. */
class ProductUnit extends Model
{
    protected $table = 'huvant_product_units';

    protected $fillable = ['product_id', 'order_id', 'order_line_id', 'code', 'production_date', 'expiry_date', 'status', 'status_since', 'notes', 'quality_rating', 'creator_id'];

    /** @var array<string, mixed> */
    protected $attributes = ['status' => 'in_lab'];

    protected function casts(): array
    {
        return [
            'production_date' => 'date',
            'expiry_date'     => 'date',
            'status'          => UnitStatus::class,
            'status_since'    => 'date',
            'quality_rating'  => 'integer',
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderLine(): BelongsTo
    {
        return $this->belongsTo(OrderLine::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ProductUnitMaterial::class);
    }
}
