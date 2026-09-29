<?php

namespace Huvant\Tasks;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Huvant\Tasks\Filament\Pages\ManageProjectBoard;
use Huvant\Tasks\Filament\Pages\ManageTaskWork;
use Webkul\PluginManager\Package;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Resources\TaskResource;

class TasksPlugin implements Plugin
{
    public function getId(): string
    {
        return 'huvant-tasks';
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
            ProjectResource::registerRecordPage('board', ManageProjectBoard::class, '/{record}/board');
            TaskResource::registerRecordPage('work', ManageTaskWork::class, '/{record}/work');
            $panel->discoverPages(in: __DIR__.'/Filament/Pages', for: 'Huvant\\Tasks\\Filament\\Pages');
            $panel->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style id="huvant-tasks">'.file_get_contents(__DIR__.'/../resources/css/tasks.css').'</style>',
            );
        });
    }

    public function boot(Panel $panel): void {}
}
