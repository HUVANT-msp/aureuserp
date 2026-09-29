<?php

use Filament\Facades\Filament;
use Huvant\Insights\Filament\Pages\ManageEmployeeWork;
use Huvant\Insights\Filament\Pages\ProjectsOverview;
use Huvant\Insights\Support\Insights;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-worklog');
    TestBootstrapHelper::ensurePluginInstalled('huvant-insights');
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function insightsAdmin(): User
{
    return User::query()->whereHas('roles', fn ($query) => $query->where('name', 'Admin'))->first()
        ?? tap(User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true])))->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
}

function insightsUser(string $name): User
{
    return User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true, 'name' => $name]));
}

function employeeFor(User $user, ?Employee $manager = null): Employee
{
    return Employee::withoutEvents(fn (): Employee => Employee::factory()->create(['user_id' => $user->getKey(), 'name' => $user->name, 'parent_id' => $manager?->getKey()]));
}

function hoursOn(Project $project, User $user, string $date, float $hours): void
{
    DB::table('analytic_records')->insert([
        'type'    => 'project', 'name' => 'Lavoro', 'date' => $date, 'amount' => 0, 'unit_amount' => $hours,
        'user_id' => $user->getKey(), 'project_id' => $project->getKey(), 'created_at' => now(), 'updated_at' => now(),
    ]);
}

it('ranks projects by recent hours with trend, people and completed tasks', function () {
    $admin = insightsAdmin();
    [$anna, $bruno] = [insightsUser('Anna Rossi'), insightsUser('Bruno Verdi')];
    $hot = Project::withoutEvents(fn () => Project::factory()->create(['name' => 'Progetto caldo']));
    $quiet = Project::withoutEvents(fn () => Project::factory()->create(['name' => 'Progetto tranquillo']));

    hoursOn($hot, $anna, now()->toDateString(), 5);
    hoursOn($hot, $bruno, now()->subDay()->toDateString(), 2);
    hoursOn($hot, $anna, now()->subDays(10)->toDateString(), 3.5);
    hoursOn($quiet, $bruno, now()->toDateString(), 1);
    $done = DB::table('projects_tasks')->insertGetId(['title' => 'Chiuso', 'state' => 'done', 'project_id' => $hot->getKey(), 'created_at' => now(), 'updated_at' => now()]);
    DB::table('projects_task_users')->insert(['task_id' => $done, 'user_id' => $bruno->getKey()]);
    $this->actingAs($admin);

    $data = Insights::projects(7);
    $first = $data['projects'][0];

    expect($first['name'])->toBe('Progetto caldo')
        ->and($first['hours'])->toBe(7.0)
        ->and($first['trend'])->toBe(100.0)
        ->and($first['done'])->toBe(1)
        ->and(array_column($first['people'], 'name'))->toBe(['Anna Rossi', 'Bruno Verdi'])
        ->and($first['people'][1]['done'])->toBe(1)
        ->and($data['projects'][1]['name'])->toBe('Progetto tranquillo')
        ->and($data['leaders'][0]['name'])->toBe('Anna Rossi');
});

it('shows a person to administrators, their manager and themselves only', function () {
    $admin = insightsAdmin();
    [$boss, $worker, $other] = [insightsUser('Capo'), insightsUser('Operatore'), insightsUser('Estraneo')];
    $bossEmployee = employeeFor($boss);
    employeeFor($worker, $bossEmployee);
    employeeFor($other);

    expect(Insights::canSeePerson($admin, $worker))->toBeTrue()
        ->and(Insights::canSeePerson($boss, $worker))->toBeTrue()
        ->and(Insights::canSeePerson($worker, $worker))->toBeTrue()
        ->and(Insights::canSeePerson($other, $worker))->toBeFalse()
        ->and(Insights::isManager($boss))->toBeTrue()
        ->and(Insights::isManager($other))->toBeFalse();
});

it('renders the overview and the employee work tab', function () {
    $admin = insightsAdmin();
    $worker = insightsUser('Carla Neri');
    $employee = employeeFor($worker);
    $project = Project::withoutEvents(fn () => Project::factory()->create(['name' => 'Banco prova']));
    hoursOn($project, $worker, now()->toDateString(), 3);
    $this->actingAs($admin);

    Livewire::test(ProjectsOverview::class)->assertOk()->assertSee('Banco prova')->assertSee('Carla Neri')->call('setDays', 30)->assertOk();
    Livewire::test(ManageEmployeeWork::class, ['record' => $employee->getKey()])->assertOk()->assertSee('Banco prova')->assertSee('3:00');
    $this->get(ManageEmployeeWork::getUrl(['record' => $employee->getKey()]))->assertOk();

    $this->actingAs(insightsUser('Curioso'));
    expect(ManageEmployeeWork::canAccess(['record' => $employee]))->toBeFalse();
    Livewire::test(ManageEmployeeWork::class, ['record' => $employee->getKey()])->assertForbidden();
});
