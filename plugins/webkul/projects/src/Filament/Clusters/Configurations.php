<?php

namespace Webkul\Project\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class Configurations extends Cluster
{
    /** Another plugin can take this out of the menu; its pages stay reachable. */
    public static bool $hiddenFromNavigation = false;

    /** Cluster the configuration resources move to (e.g. the project settings), if any. */
    public static ?string $movedTo = null;

    public static function shouldRegisterNavigation(): bool
    {
        return ! static::$hiddenFromNavigation && parent::shouldRegisterNavigation();
    }

    protected static ?string $slug = 'project/configurations';

    protected static ?int $navigationSort = 0;

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }
}
