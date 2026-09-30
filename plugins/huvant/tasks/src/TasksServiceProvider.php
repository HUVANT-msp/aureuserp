<?php

namespace Huvant\Tasks;

use Filament\Panel;
use Huvant\Tasks\Http\Controllers\TaskMinutesController;
use Huvant\Tasks\Livewire\TaskBoard;
use Huvant\Tasks\Livewire\TaskPanel;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Project\Filament\Clusters\Configurations;
use Webkul\Project\Filament\Clusters\PluginSettings;

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

        // The minutes of the meeting a task came from, downloaded through the ERP session.
        Route::middleware('web')
            ->get('admin/huvant/tasks/{task}/minutes.pdf', [TaskMinutesController::class, 'show'])
            ->whereNumber('task')
            ->name('huvant.tasks.minutes');
    }

    public function packageRegistered(): void
    {
        // Project configuration (stages, tags, milestones, activity plans) lives under the
        // project Settings. Set before any panel registers its resources to clusters.
        Configurations::$movedTo = PluginSettings::class;

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(TasksPlugin::make());
        });
    }
}
