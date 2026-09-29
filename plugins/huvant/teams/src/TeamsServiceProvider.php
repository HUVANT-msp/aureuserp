<?php

namespace Huvant\Teams;

use Filament\Panel;
use Huvant\Teams\Scopes\TeamProjectScope;
use Huvant\Teams\Support\ProjectTeams;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Project\Models\Milestone;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\Timesheet as ProjectTimesheet;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\Team;
use Webkul\Timesheet\Models\Timesheet;

class TeamsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'huvant-teams';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasDependencies(['projects'])
            ->hasMigrations(['2026_09_30_000000_create_huvant_project_teams_table'])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->startWith(function (InstallCommand $command): void {
                        if (! Package::isPluginInstalled('projects')) {
                            $command->call('projects:install');
                        }
                    })
                    ->runsMigrations()
                    ->endWith(function (): void {
                        // The Minutes integration must keep seeing every project.
                        $permission = Permission::query()->firstOrCreate([
                            'name' => ProjectTeams::VIEW_ALL_PERMISSION, 'guard_name' => 'web',
                        ]);
                        Role::query()->where('name', 'Huvant Integration')->get()
                            ->each(fn (Role $role) => $role->givePermissionTo($permission));
                    });
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {});
    }

    public function packageBooted(): void
    {
        if (! $this->package->isInstalled()) {
            return;
        }

        Project::resolveRelationUsing('teams', fn (Project $project) => $project->belongsToMany(
            Team::class, ProjectTeams::PIVOT, 'project_id', 'team_id'
        )->withTimestamps());

        foreach ([Project::class, Task::class, Milestone::class, ProjectTimesheet::class, Timesheet::class] as $model) {
            $model::addGlobalScope('huvant_project_teams', new TeamProjectScope);
        }
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(TeamsPlugin::make());
        });
    }
}
