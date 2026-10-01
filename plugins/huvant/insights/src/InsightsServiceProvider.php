<?php

namespace Huvant\Insights;

use Filament\Panel;
use Huvant\Insights\Console\SendInvites;
use Huvant\Insights\Support\EmployeeProfile;
use Huvant\Insights\Support\Onboarding;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Security\Models\User;

class InsightsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'huvant-insights';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasConfigFile('huvant-insights')
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

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([SendInvites::class]);
        }

        // Invitations to new colleagues set a password through the reset link: give them 3 days.
        config(['auth.passwords.users.expire' => max((int) config('auth.passwords.users.expire', 60), 4320)]);

        // Name and e-mail changed anywhere (profile, users) reach the employee record.
        User::saved(function (User $user): void {
            if (! Package::isPluginInstalled(static::$name)) {
                return;
            }
            // A new user is a new employee and an internal contact, however it was added.
            if ($user->wasRecentlyCreated && $user->partner_id) {
                Onboarding::onboard($user);
            }
            if ($user->wasChanged(['name', 'email'])) {
                EmployeeProfile::syncIdentity($user);
            }
        });
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(InsightsPlugin::make());
        });
    }
}
