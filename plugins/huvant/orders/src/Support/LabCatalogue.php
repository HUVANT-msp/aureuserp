<?php

namespace Huvant\Orders\Support;

use Huvant\Orders\Enums\ItemRole;
use Webkul\Product\Models\Category;
use Webkul\Support\Models\UOM;

/**
 * What the ERP needs on a product that the lab forms do not ask: role, unit of measure, category.
 * Raw materials and finished products are created through these defaults.
 */
class LabCatalogue
{
    /** @param  array<string, mixed>  $data */
    public static function materialDefaults(array $data): array
    {
        return static::defaults($data, ItemRole::Material, 'Lab');
    }

    /** @param  array<string, mixed>  $data */
    public static function productDefaults(array $data): array
    {
        return static::defaults($data, ItemRole::Product, 'Products');
    }

    /** @param  array<string, mixed>  $data */
    protected static function defaults(array $data, ItemRole $role, string $category): array
    {
        $units = UOM::query()->where('name', 'Units')->value('id') ?? UOM::query()->orderBy('id')->value('id');

        return array_merge([
            'uom_id'      => $units,
            'uom_po_id'   => $units,
            'category_id' => Category::query()->firstOrCreate(['name' => $category])->id,
            'price'       => 0,
        ], $data, ['huvant_role' => $role->value]);
    }
}
