<?php

namespace Webkul\Manufacturing\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Products extends Cluster
{
    /** Another plugin can replace this cluster in the menu without removing its routes. */
    public static bool $hiddenFromNavigation = false;

    public static function shouldRegisterNavigation(): bool
    {
        return ! static::$hiddenFromNavigation && parent::shouldRegisterNavigation();
    }

    protected static ?string $slug = 'manufacturing/products';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/products.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Manufacturing;
    }
}
