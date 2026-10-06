<?php

namespace Huvant\Orders\Filament\Clusters\RawMaterials\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Huvant\Orders\Filament\Clusters\RawMaterials;
use Huvant\Orders\Models\RecipeLine;
use Huvant\Orders\Support\LabInventory;
use Webkul\Product\Models\Product;

/**
 * One histogram per product: a column for each raw material of its recipe, with what is left for
 * production (all packages together) against the minimum to keep.
 */
class MaterialsByProduct extends Page
{
    protected string $view = 'huvant-orders::filament.lab.materials-by-product';

    protected static ?string $cluster = RawMaterials::class;

    protected static ?string $slug = 'by-product';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::lab.by_product');
    }

    public function getTitle(): string
    {
        return __('huvant-orders::lab.raw_materials_by_product');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $products = LabInventory::finishedProducts()->map(function (Product $product): array {
            $columns = LabInventory::recipe($product)
                ->filter(fn (RecipeLine $line): bool => $line->material !== null)
                ->map(function (RecipeLine $line): array {
                    $material = $line->material;
                    $total = LabInventory::total($material);
                    $minimum = (float) $material->huvant_min_quantity;

                    return [
                        'name'     => $material->name,
                        'unit'     => (string) $material->huvant_package_unit,
                        'total'    => $total,
                        'minimum'  => $minimum,
                        'level'    => LabInventory::level($material, $total),
                        'per_unit' => (float) $line->quantity,
                        'pieces'   => (float) $line->quantity > 0 ? (int) floor($total / (float) $line->quantity) : null,
                    ];
                })
                ->values();

            // Each column on its own scale: the minimum sits at two thirds of the plot when stock is short.
            $columns = $columns->map(function (array $column): array {
                $scale = max($column['total'], $column['minimum'] * 1.5, 0.0001);
                $column['height'] = round(min(1, $column['total'] / $scale) * 100, 1);
                $column['min_at'] = $column['minimum'] > 0 ? round(min(1, $column['minimum'] / $scale) * 100, 1) : null;

                return $column;
            });

            return [
                'product' => $product,
                'columns' => $columns,
                'pieces'  => $columns->pluck('pieces')->filter(fn ($pieces) => $pieces !== null)->min(),
            ];
        });

        return [
            'withRecipe'    => $products->filter(fn (array $item): bool => $item['columns']->isNotEmpty())->values(),
            'withoutRecipe' => $products->filter(fn (array $item): bool => $item['columns']->isEmpty())->pluck('product'),
        ];
    }
}
