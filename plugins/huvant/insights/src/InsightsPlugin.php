<?php

namespace Huvant\Insights;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Huvant\Insights\Filament\Pages\ManageEmployeeWork;
use Huvant\Insights\Filament\Pages\ProjectsOverview;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\PluginManager\Package;

class InsightsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'huvant-insights';
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
            // The person's work lives with the employee, not inside the projects.
            EmployeeResource::registerRecordPage('work', ManageEmployeeWork::class, '/{record}/work');
            $panel->pages([ProjectsOverview::class]);
            $panel->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style id="huvant-insights">'.file_get_contents(__DIR__.'/../resources/css/insights.css').'</style>',
            );
        });
    }

    public function boot(Panel $panel): void {}
}
