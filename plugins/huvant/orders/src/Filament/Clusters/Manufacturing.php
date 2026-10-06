<?php

namespace Huvant\Orders\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Webkul\Support\Enums\NavigationGroup;

class Manufacturing extends Cluster
{
    protected static ?string $slug = 'manufacturing';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?int $navigationSort = 1;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Manufacturing;
    }

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::manufacturing.manufacturing');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('huvant-orders::manufacturing.manufacturing');
    }
}
