<?php

namespace Huvant\Orders\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Support\Enums\NavigationGroup;

/** Finished products: pieces in the lab, stock per product, recipes, pieces out and sold. */
class FinishedProducts extends Cluster
{
    protected static ?string $slug = 'lab/finished-products';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 2;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Inventory;
    }

    public static function getNavigationLabel(): string
    {
        return 'Finished products';
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return 'Finished products';
    }
}
