<?php

namespace Huvant\Orders\Filament\Clusters\FinishedProducts\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Huvant\Orders\Filament\Clusters\FinishedProducts;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource;
use Huvant\Orders\Models\ProductUnit;
use Huvant\Orders\Support\LabInventory;
use Webkul\Product\Models\Product;

/** The products as cards; each opens its pieces in stock. */
class StockByProduct extends Page
{
    protected string $view = 'huvant-orders::filament.lab.stock-by-product';

    protected static ?string $cluster = FinishedProducts::class;

    protected static ?string $slug = 'stock';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::lab.stock');
    }

    public function getTitle(): string
    {
        return __('huvant-orders::lab.stock_by_product');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return [
            'products' => LabInventory::finishedProducts()->map(fn (Product $product): array => [
                'product' => $product,
                'prefix'  => LabInventory::prefix($product),
                'counts'  => LabInventory::counts($product),
                'last'    => ProductUnit::query()->where('product_id', $product->id)->max('production_date'),
                'url'     => RecipeResource::getUrl('stock', ['record' => $product]),
            ]),
        ];
    }
}
