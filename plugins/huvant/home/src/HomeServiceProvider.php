<?php

namespace Huvant\Home;

use Filament\Panel;
use Huvant\Home\Console\MiloBriefings;
use Illuminate\Console\Scheduling\Schedule;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class HomeServiceProvider extends PackageServiceProvider
{
    public static string $name = 'huvant-home';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasConfigFile('huvant-home')
            ->hasMigrations(['2026_10_02_000000_create_huvant_milo_briefings_table'])
            ->runsMigrations()
            ->hasInstallCommand(fn (InstallCommand $command) => $command->runsMigrations())
            ->hasUninstallCommand(function (UninstallCommand $command): void {});
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([MiloBriefings::class]);
        }

        // Milo's brief at 8:00 and 13:00 on working days.
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            if (! Package::isPluginInstalled(static::$name)) {
                return;
            }
            foreach ((array) config('huvant-home.times', []) as $i => $time) {
                $schedule->command(MiloBriefings::class, [$i === 0 ? 'morning' : 'midday'])
                    ->weekdays()->dailyAt($time)->timezone('Europe/Rome')
                    ->withoutOverlapping(60)->onOneServer()->runInBackground();
            }
        });
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(HomePlugin::make());
        });
    }
}
