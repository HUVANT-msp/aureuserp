<?php

namespace Huvant\Orders\Filament\Clusters\FinishedProducts\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Huvant\Orders\Enums\UnitStatus;
use Huvant\Orders\Filament\Clusters\FinishedProducts;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource;
use Huvant\Orders\Models\ProductUnit;
use Huvant\Orders\Settings\OrdersSettings;
use Huvant\Orders\Support\LabInventory;
use Webkul\Product\Models\Product;

/** Pieces in the lab per product, one square each, in rows of ten. */
class PiecesInLab extends Page
{
    protected string $view = 'huvant-orders::filament.lab.pieces-in-lab';

    protected static ?string $cluster = FinishedProducts::class;

    protected static ?string $slug = 'in-lab';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::lab.in_the_lab');
    }

    public function getTitle(): string
    {
        return __('huvant-orders::lab.finished_products_in_lab');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $soon = today()->addDays(app(OrdersSettings::class)->expiry_warning_days);

        return [
            'products' => LabInventory::finishedProducts()->map(function (Product $product) use ($soon): array {
                $units = ProductUnit::query()->where('product_id', $product->id)->where('status', UnitStatus::InLab)->get(['id', 'expiry_date']);
                $expiring = $units->filter(fn (ProductUnit $unit): bool => $unit->expiry_date !== null && $unit->expiry_date->lte($soon))->count();

                return [
                    'product'  => $product,
                    'count'    => $units->count(),
                    'expiring' => $expiring,
                    // Ten squares, more in rows of ten when there are more pieces.
                    'squares'  => max(10, (int) ceil($units->count() / 10) * 10),
                    'url'      => RecipeResource::getUrl('stock', ['record' => $product]),
                ];
            }),
        ];
    }
}
