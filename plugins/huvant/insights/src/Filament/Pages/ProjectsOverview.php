<?php

namespace Huvant\Insights\Filament\Pages;

use Filament\Pages\Page;
use Huvant\Insights\Support\Insights;
use Livewire\Attributes\Url;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

/** Which projects are moving: hours in the period and trend, who works on them, what got done. */
class ProjectsOverview extends Page
{
    protected string $view = 'huvant-insights::filament.pages.projects-overview';

    protected static ?string $slug = 'project/overview';

    protected static ?int $navigationSort = 0;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-fire';

    #[Url(as: 'days')]
    public int $days = 7;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }

    public static function getNavigationLabel(): string
    {
        return 'Overview';
    }

    public function getTitle(): string
    {
        return 'Projects overview';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && Insights::isManager($user);
    }

    public function setDays(int $days): void
    {
        $this->days = in_array($days, [7, 14, 30], true) ? $days : 7;
    }

    protected function getViewData(): array
    {
        return ['data' => Insights::projects(in_array($this->days, [7, 14, 30], true) ? $this->days : 7)];
    }
}
