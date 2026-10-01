<?php

use Filament\Facades\Filament;
use Huvant\Insights\Filament\Pages\ManageEmployeeTasks;
use Huvant\Insights\Support\EmployeeProfile;
use Huvant\Insights\Support\Insights;
use Huvant\Tasks\Livewire\TaskBoard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Webkul\Employee\Filament\Clusters\Configurations as EmployeeConfigurations;
use Webkul\Employee\Filament\Clusters\Reportings;
use Webkul\Employee\Filament\Resources\DepartmentResource;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ViewEmployee;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Pages\Profile;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-worklog');
    TestBootstrapHelper::ensurePluginInstalled('huvant-tasks');
    TestBootstrapHelper::ensurePluginInstalled('huvant-insights');
    if (! Route::has('filament.admin.resources.employees.employees.tasks')) {
        // Installed during this run: boot again so the panel registers the pages.
        $this->refreshApplication();
        $this->setUpTraits();
    }
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function tasksAdmin(): User
{
    return User::query()->whereHas('roles', fn ($query) => $query->where('name', 'Admin'))->first()
        ?? tap(User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true])))->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
}

function taskFor(Project $project, User $user, string $title, array $extra = []): int
{
    $id = DB::table('projects_tasks')->insertGetId(array_merge([
        'title' => $title, 'state' => 'in_progress', 'project_id' => $project->getKey(), 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
    ], $extra));
    DB::table('projects_task_users')->insert(['task_id' => $id, 'user_id' => $user->getKey()]);

    return $id;
}

it('shows an employee\'s tasks and how busy they are, without edit, skills or resume', function () {
    $this->actingAs(tasksAdmin());
    $anna = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true, 'name' => 'Anna Rossi']));
    $bruno = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true, 'name' => 'Bruno Verdi']));
    $employee = Employee::withoutEvents(fn (): Employee => Employee::factory()->create(['user_id' => $anna->getKey(), 'name' => 'Anna Rossi']));
    $project = Project::withoutEvents(fn () => Project::factory()->create(['name' => 'Atlas']));
    taskFor($project, $anna, 'Preparare offerta', ['deadline' => now()->subDays(2)]);
    taskFor($project, $anna, 'Chiamare fornitore', ['deadline' => now()->addDays(3)]);
    taskFor($project, $anna, 'Già fatto', ['state' => 'done']);
    taskFor($project, $bruno, 'Task di Bruno');

    $load = Insights::workload($anna);
    expect([$load['open'], $load['overdue'], $load['dueSoon'], $load['level']])->toBe([2, 1, 1, 'light']);

    Livewire::test(ManageEmployeeTasks::class, ['record' => $employee->getKey()])->assertOk()
        ->assertSee('Workload')->assertSee('Light')->assertSee('Overdue');
    Livewire::test(TaskBoard::class, ['assigneeId' => $anna->getKey()])->set('view', 'list')
        ->assertSee('Preparare offerta')->assertSee('Chiamare fornitore')->assertDontSee('Task di Bruno')
        ->assertDontSeeHtml('aria-label="Assignee"');

    Livewire::test(ViewEmployee::class, ['record' => $employee->getKey()])->assertOk()
        ->assertSeeHtml(EmployeeResource::getUrl('tasks', ['record' => $employee]))
        ->assertSeeHtml(EmployeeResource::getUrl('work', ['record' => $employee]))
        ->assertDontSeeHtml('href="'.EmployeeResource::getUrl('edit', ['record' => $employee]).'"')
        ->assertDontSeeHtml('href="'.EmployeeResource::getUrl('skills', ['record' => $employee]).'"');
    expect(EmployeeResource::getRelations())->toBe([])
        ->and(DepartmentResource::shouldRegisterNavigation())->toBeFalse()
        ->and(Reportings::shouldRegisterNavigation())->toBeFalse()
        ->and(EmployeeConfigurations::shouldRegisterNavigation())->toBeFalse();
});

it('keeps the employee record from the person\'s own profile', function () {
    $anna = User::factory()->create(['is_active' => true, 'name' => 'Anna Rossi', 'email' => 'anna@huvant.test']);
    // Every new user is an employee already (Onboarding).
    $employee = Employee::withoutGlobalScopes()->where('user_id', $anna->getKey())->firstOrFail();
    $this->actingAs($anna);

    expect(Profile::$extensions)->toContain(EmployeeProfile::class);
    Livewire::test(Profile::class)->assertOk()->assertSee('Work details')
        ->set('profileData.name', 'Anna Maria Rossi')
        ->set('profileData.employee.job_title', 'Biomedical engineer')
        ->set('profileData.employee.mobile_phone', '+39 333 1234567')
        ->call('updateProfile')->assertHasNoErrors();

    $employee->refresh();
    expect([$employee->name, $employee->job_title, $employee->mobile_phone, $employee->work_email])
        ->toBe(['Anna Maria Rossi', 'Biomedical engineer', '+39 333 1234567', 'anna@huvant.test'])
        ->and(Partner::withoutGlobalScopes()->find($employee->partner_id)?->job_title)->toBe('Biomedical engineer');

    // Changed elsewhere (users administration), the employee follows too.
    $anna->refresh()->forceFill(['email' => 'anna.rossi@huvant.test'])->save();
    expect($employee->refresh()->work_email)->toBe('anna.rossi@huvant.test');
});
