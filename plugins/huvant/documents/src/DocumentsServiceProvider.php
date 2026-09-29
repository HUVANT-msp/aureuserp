<?php

namespace Huvant\Documents;

use Filament\Panel;
use Huvant\Documents\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class DocumentsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'huvant-documents';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasDependencies(['projects'])
            ->hasMigrations(['2026_09_30_200000_create_huvant_documents_tables'])
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
        // Private file downloads, on the ERP session (the controller checks sign-in and access).
        Route::middleware('web')->get('admin/huvant/documents/{document}', [DocumentController::class, 'show'])
            ->whereNumber('document')
            ->name('huvant.documents.show');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(DocumentsPlugin::make());
        });
    }
}
