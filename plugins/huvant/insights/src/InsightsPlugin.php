<?php

namespace Huvant\Insights;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Huvant\Insights\Filament\Pages\ManageEmployeeTasks;
use Huvant\Insights\Filament\Pages\ManageEmployeeWork;
use Huvant\Insights\Filament\Pages\ProjectsOverview;
use Huvant\Insights\Support\EmployeeProfile;
use Webkul\Employee\Filament\Clusters\Configurations as EmployeeConfigurations;
use Webkul\Employee\Filament\Clusters\Reportings as EmployeeReportings;
use Webkul\Employee\Filament\Resources\DepartmentResource;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\EditEmployee;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageResume;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageSkill;
use Webkul\PluginManager\Package;
use Webkul\Support\Filament\Pages\Profile;

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
            EmployeeResource::registerRecordPage('tasks', ManageEmployeeTasks::class, '/{record}/tasks');
            // People keep their own record from their profile: here it is read and worked with only.
            EmployeeResource::$profileManagedByEmployee = true;
            Profile::$extensions[EmployeeProfile::class] = EmployeeProfile::class;
            EmployeeResource::hideRecordPage(EditEmployee::class);
            EmployeeResource::hideRecordPage(ManageSkill::class);
            EmployeeResource::hideRecordPage(ManageResume::class);
            // Only the employees list in the menu for now (the other pages stay reachable).
            DepartmentResource::$hiddenFromNavigation = true;
            EmployeeReportings::$hiddenFromNavigation = true;
            EmployeeConfigurations::$hiddenFromNavigation = true;
            $panel->pages([ProjectsOverview::class]);
            $panel->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style id="huvant-insights">'.file_get_contents(__DIR__.'/../resources/css/insights.css').'</style>',
            );
        });
    }

    public function boot(Panel $panel): void {}
}
