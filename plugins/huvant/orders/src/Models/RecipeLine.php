<?php

namespace Huvant\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Models\Product;

/** One raw material of a recipe, with the quantity one piece takes, in the material's own unit. */
class RecipeLine extends Model
{
    protected $table = 'huvant_recipe_lines';

    protected $fillable = ['product_id', 'material_id', 'quantity', 'sort'];

    protected function casts(): array
    {
        return ['quantity' => 'decimal:4'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'material_id')->withTrashed();
    }
}
