<?php

namespace Huvant\Tasks;

use Filament\Panel;
use Huvant\Tasks\Livewire\TaskBoard;
use Huvant\Tasks\Livewire\TaskPanel;
use Livewire\Livewire;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class TasksServiceProvider extends PackageServiceProvider
{
    public static string $name = 'huvant-tasks';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasDependencies(['projects'])
            ->hasMigrations(['2026_10_01_000000_add_huvant_start_date_to_projects_tasks'])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->startWith(function (InstallCommand $command): void {
                        if (! Package::isPluginInstalled('projects')) {
                            $command->call('projects:install');
                        }
                    })
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {});
    }

    public function packageBooted(): void
    {
        Livewire::component('huvant-task-board', TaskBoard::class);
        Livewire::component('huvant-task-panel', TaskPanel::class);
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(TasksPlugin::make());
        });
    }
}
