<?php

namespace Huvant\Insights;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class InsightsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'huvant-insights';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasDependencies(['projects', 'employees'])
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->startWith(function (InstallCommand $command): void {
                    foreach (['projects', 'employees', 'timesheets'] as $plugin) {
                        if (! Package::isPluginInstalled($plugin)) {
                            $command->call("{$plugin}:install");
                        }
                    }
                });
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {});
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(InsightsPlugin::make());
        });
    }
}
