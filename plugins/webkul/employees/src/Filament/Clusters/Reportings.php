<?php

namespace Webkul\Employee\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Panel;
use Webkul\Support\Enums\NavigationGroup;

class Reportings extends Cluster
{
    /** Another plugin can take this out of the menu; its pages stay reachable. */
    public static bool $hiddenFromNavigation = false;

    public static function shouldRegisterNavigation(): bool
    {
        return ! static::$hiddenFromNavigation && parent::shouldRegisterNavigation();
    }

    protected static ?int $navigationSort = 3;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'employees/reportings';
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/reportings.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Employee;
    }
}
