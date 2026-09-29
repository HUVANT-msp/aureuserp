<?php

namespace Huvant\Meetings;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Webkul\PluginManager\Package;

class MeetingsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'huvant-meetings';
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
            // The app launcher only lists navigation groups that have an icon.
            // Registered before the panel's own groups, so it comes first.
            $panel->navigationGroups([
                'Riunioni' => NavigationGroup::make('Riunioni')->icon('huvant-meetings'),
            ]);
            $panel->discoverPages(
                in: __DIR__.'/Filament/Pages',
                for: 'Huvant\\Meetings\\Filament\\Pages'
            );
        });
    }

    public function boot(Panel $panel): void {}
}
