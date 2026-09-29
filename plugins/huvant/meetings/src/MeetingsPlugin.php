<?php

namespace Huvant\Meetings;

use Filament\Contracts\Plugin;
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
            $panel->discoverPages(
                in: __DIR__.'/Filament/Pages',
                for: 'Huvant\\Meetings\\Filament\\Pages'
            );
        });
    }

    public function boot(Panel $panel): void {}
}
