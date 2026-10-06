<?php

namespace Huvant\Orders\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Support\Enums\NavigationGroup;

/** Raw materials: what each product needs, the packages in the lab, the catalogue. */
class RawMaterials extends Cluster
{
    protected static ?string $slug = 'lab/raw-materials';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static ?int $navigationSort = 1;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Inventory;
    }

    public static function getNavigationLabel(): string
    {
        return 'Raw materials';
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return 'Raw materials';
    }
}
