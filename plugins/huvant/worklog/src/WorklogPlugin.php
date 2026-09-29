<?php

namespace Huvant\Worklog;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Huvant\Worklog\Filament\Pages\MyWeek;
use Illuminate\Support\Facades\Blade;
use Webkul\PluginManager\Package;
use Webkul\Timesheet\Filament\Resources\TimesheetResource;

class WorklogPlugin implements Plugin
{
    public function getId(): string
    {
        return 'huvant-worklog';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel->when($panel->getId() == 'admin', function (Panel $panel): void {
            // One "Ore" menu: my hours, entries, the team (admins). The upstream
            // Timesheets list leaves the menu (its data is the same).
            $panel->navigationGroups([
                MyWeek::GROUP => NavigationGroup::make(MyWeek::GROUP)->icon('hvore-hours'),
            ]);
            TimesheetResource::$hiddenFromNavigation = true;
            $panel->discoverPages(in: __DIR__.'/Filament/Pages', for: 'Huvant\\Worklog\\Filament\\Pages');
            $panel->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style id="huvant-worklog">'.file_get_contents(__DIR__.'/../resources/css/worklog.css').'</style>',
            );
            // The running timer is always one click away, in the top bar.
            $panel->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => Blade::render('@livewire(\'huvant-worklog-topbar-timer\')'),
            );
        });
    }

    public function boot(Panel $panel): void {}
}
