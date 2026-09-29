<?php

namespace Huvant\Calendar;

use BladeUI\Icons\Factory as IconFactory;
use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class CalendarServiceProvider extends PackageServiceProvider
{
    public static string $name = 'huvant-calendar';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasMigrations(['2026_10_01_200000_create_huvant_calendar_tables'])
            ->runsMigrations()
            ->hasInstallCommand(fn (InstallCommand $command) => $command->runsMigrations())
            ->hasUninstallCommand(function (UninstallCommand $command): void {});
    }

    public function packageRegistered(): void
    {
        // Launcher icon "hvcal-calendar" for the Calendar group.
        $this->callAfterResolving(IconFactory::class, function (IconFactory $factory): void {
            $factory->add('huvant-calendar', ['path' => __DIR__.'/../resources/svg', 'prefix' => 'hvcal']);
        });

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(CalendarPlugin::make());
        });
    }
}
