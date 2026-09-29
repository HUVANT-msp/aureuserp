<?php

namespace Huvant\Tasks\Filament\Pages;

use Filament\Pages\Page;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

/** Every visible task: Kanban, timeline or list, with filters. */
class TaskBoardPage extends Page
{
    protected string $view = 'huvant-tasks::filament.pages.board';

    protected static ?string $slug = 'project/board';

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }

    public static function getNavigationLabel(): string
    {
        return 'Bacheca';
    }

    public function getTitle(): string
    {
        return 'Bacheca task';
    }

    public static function canAccess(): bool
    {
        return auth()->user() instanceof User;
    }
}
