<?php

namespace Huvant\Meetings;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class MeetingsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'huvant-meetings';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile('huvant-meetings')
            ->hasViews()
            ->hasInstallCommand(function (InstallCommand $command): void {})
            ->hasUninstallCommand(function (UninstallCommand $command): void {});
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(MeetingsPlugin::make());
        });
    }
}
