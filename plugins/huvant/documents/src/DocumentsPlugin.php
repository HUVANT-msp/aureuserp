<?php

namespace Huvant\Documents;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Huvant\Documents\Filament\Pages\ManageProjectDocuments;
use Huvant\Documents\Filament\Pages\ManageTaskDocuments;
use Webkul\PluginManager\Package;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Resources\TaskResource;

class DocumentsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'huvant-documents';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel->when($panel->getId() == 'admin', function (Panel $panel): void {
            // A "Documenti" tab on every project and task (before the panel builds its routes).
            ProjectResource::registerRecordPage('documents', ManageProjectDocuments::class, '/{record}/documents');
            TaskResource::registerRecordPage('documents', ManageTaskDocuments::class, '/{record}/documents');

            $panel->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style id="huvant-documents">'.file_get_contents(__DIR__.'/../resources/css/documents.css').'</style>',
            );
        });
    }

    public function boot(Panel $panel): void {}
}
