<?php

namespace Huvant\Worklog;

use Filament\Panel;
use Huvant\Worklog\Livewire\TopbarTimer;
use Livewire\Livewire;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class WorklogServiceProvider extends PackageServiceProvider
{
    public static string $name = 'huvant-worklog';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasDependencies(['projects', 'timesheets'])
            ->hasMigrations(['2026_09_30_100000_create_huvant_work_timers_table'])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->startWith(function (InstallCommand $command): void {
                        foreach (['projects', 'timesheets'] as $plugin) {
                            if (! Package::isPluginInstalled($plugin)) {
                                $command->call("{$plugin}:install");
                            }
                        }
                    })
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {});
    }

    public function packageBooted(): void
    {
        Livewire::component('huvant-worklog-topbar-timer', TopbarTimer::class);
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(WorklogPlugin::make());
        });
    }
}
