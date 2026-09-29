<?php

namespace Huvant\Bridge;

use Huvant\Bridge\Http\Middleware\EnsureActiveUser;
use Huvant\Bridge\Http\Middleware\EnsureBridgeCompanyBoundary;
use Huvant\Bridge\Http\Middleware\EnsureIdempotentBridgeRequest;
use Huvant\Bridge\Http\Middleware\TouchTaskAfterPivotUpdate;
use Huvant\Bridge\Observers\BridgeWebhookObserver;
use Huvant\Bridge\Observers\TaskStageCompanyObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Models\User;
use Webkul\Security\Policies\UserPolicy;

class BridgeServiceProvider extends PackageServiceProvider
{
    public static string $name = 'huvant-bridge';

    private const DEPENDENCIES = ['employees', 'projects', 'timesheets'];

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile('huvant-bridge')
            ->hasRoute('api')
            ->hasDependencies(self::DEPENDENCIES)
            ->hasMigrations(['2026_09_29_000000_create_huvant_bridge_idempotency_keys_table'])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->startWith(function (InstallCommand $command): void {
                        foreach (self::DEPENDENCIES as $dependency) {
                            if (! Package::isPluginInstalled($dependency)) {
                                $command->call("{$dependency}:install");
                            }
                        }
                    })
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {});
    }

    public function packageBooted(): void
    {
        if (! $this->package->isInstalled()) {
            return;
        }

        Gate::policy(User::class, UserPolicy::class);

        // Roles and permissions are defined for the "web" guard only, while User
        // declares ['web', 'sanctum']: on token requests spatie/permission looks
        // them up for "sanctum", finds nothing and every REST endpoint (core ones
        // included) answers 403. Honour exactly the permissions the user already
        // holds on "web"; anything else still falls through to the policies.
        Gate::before(function ($user, string $ability): ?bool {
            if (! $user instanceof User || Auth::getDefaultDriver() === 'web') {
                return null;
            }

            return $user->checkPermissionTo($ability, 'web') ? true : null;
        });

        foreach ([Task::class, Project::class, User::class, Partner::class] as $model) {
            $model::observe(BridgeWebhookObserver::class);
        }

        TaskStage::observe(TaskStageCompanyObserver::class);

        foreach (Route::getRoutes() as $route) {
            if (! preg_match('#^admin/api/v1/(projects/(tasks|projects|task-stages)|partners/partners)(/\{[^}]+\}(/(restore|force))?)?$#', $route->uri())) {
                continue;
            }

            if (array_intersect($route->methods(), ['POST', 'PUT', 'PATCH', 'DELETE']) === []) {
                continue;
            }

            $middleware = [
                EnsureActiveUser::class,
                EnsureBridgeCompanyBoundary::class,
            ];

            if (array_intersect($route->methods(), ['POST', 'PUT', 'PATCH']) !== []) {
                $middleware[] = EnsureIdempotentBridgeRequest::class;
            }

            if (preg_match('#^admin/api/v1/projects/tasks/\{[^}]+\}$#', $route->uri())
                && array_intersect($route->methods(), ['PUT', 'PATCH']) !== []) {
                $middleware[] = TouchTaskAfterPivotUpdate::class;
            }

            $route->middleware($middleware);
        }
    }

    public function packageRegistered(): void {}
}
