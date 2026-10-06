<?php

namespace Huvant\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Models\Product;

/** How much of which material lot went into a finished piece. */
class ProductUnitMaterial extends Model
{
    protected $table = 'huvant_product_unit_materials';

    protected $fillable = ['product_unit_id', 'material_id', 'material_lot_id', 'lot_number', 'quantity'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:4'];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'material_id')->withTrashed();
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(MaterialLot::class, 'material_lot_id');
    }
}
