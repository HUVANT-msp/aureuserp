<?php

namespace Huvant\Calendar;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Huvant\Calendar\Filament\Pages\CalendarPage;
use Webkul\PluginManager\Package;

class CalendarPlugin implements Plugin
{
    public function getId(): string
    {
        return 'huvant-calendar';
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
            $panel->navigationGroups([
                CalendarPage::GROUP => NavigationGroup::make(CalendarPage::GROUP)->icon('hvcal-calendar'),
            ]);
            $panel->pages([CalendarPage::class]);
            $panel->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style id="huvant-calendar">'.file_get_contents(__DIR__.'/../resources/css/calendar.css').'</style>',
            );
        });
    }

    public function boot(Panel $panel): void {}
}
