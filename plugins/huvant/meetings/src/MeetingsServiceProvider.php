<?php

namespace Huvant\Meetings;

use BladeUI\Icons\Factory as IconFactory;
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
        // Launcher icon "huvant-meetings" (resources/svg/meetings.svg), same style as the app's own set.
        $this->callAfterResolving(IconFactory::class, function (IconFactory $factory): void {
            $factory->add('huvant-meetings', ['path' => __DIR__.'/../resources/svg', 'prefix' => 'huvant']);
        });

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(MeetingsPlugin::make());
        });
    }
}
