<?php

use Huvant\Teams\Support\ProjectTeams;
use Huvant\Worklog\Filament\Pages\MyWeek;
use Huvant\Worklog\Filament\Pages\TeamHours;
use Huvant\Worklog\Livewire\TopbarTimer;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Timesheet\Models\Timesheet;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-worklog');
    if (class_exists(ProjectTeams::class)) {
        ProjectTeams::forget();
    }
});

function worker(): User
{
    return User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true]));
}

// Upstream TaskFactory writes a non-existent "visibility" column: insert directly.
function workTask(?User $assignee = null): int
{
    $project = Project::withoutEvents(fn (): Project => Project::factory()->create());
    $id = DB::table('projects_tasks')->insertGetId([
        'title'      => 'Task '.uniqid(), 'state' => 'in_progress', 'project_id' => $project->getKey(),
        'created_at' => now(), 'updated_at' => now(),
    ]);
    if ($assignee) {
        DB::table('projects_task_users')->insert(['task_id' => $id, 'user_id' => $assignee->getKey()]);
    }

    return $id;
}

it('lets a person log time only on tasks they are assigned to', function () {
    $user = worker();
    $mine = workTask($user);
    $other = workTask();

    expect(Worklog::isAssignee($user, $mine))->toBeTrue()
        ->and(fn () => Worklog::start($user, $other))->toThrow(RuntimeException::class)
        ->and(fn () => Worklog::setDayTotal($user, $other, '2026-09-28', 2))->toThrow(RuntimeException::class);

    Worklog::join($user, $other);
    expect(Worklog::isAssignee($user, $other))->toBeTrue();
});

it('turns a stopped timer into a timesheet entry on the task', function () {
    $user = worker();
    $task = workTask($user);

    Carbon::setTestNow('2026-09-28 09:00:00');
    Worklog::start($user, $task, 'Analisi');
    Carbon::setTestNow('2026-09-28 10:30:00');
    $entry = Worklog::stop($user);
    Carbon::setTestNow();

    expect($entry)->not->toBeNull()
        ->and((float) $entry->unit_amount)->toBe(1.5)
        ->and($entry->name)->toBe('Analisi')
        ->and($entry->task_id)->toBe($task)
        ->and(Worklog::running($user))->toBeNull()
        ->and((float) DB::table('projects_tasks')->where('id', $task)->value('effective_hours'))->toBe(1.5);
});

it('drops a timer stopped within a minute and replaces a running one on start', function () {
    $user = worker();
    [$a, $b] = [workTask($user), workTask($user)];

    Carbon::setTestNow('2026-09-28 09:00:00');
    Worklog::start($user, $a);
    Carbon::setTestNow('2026-09-28 09:00:30');
    expect(Worklog::stop($user))->toBeNull();

    Worklog::start($user, $a);
    Carbon::setTestNow('2026-09-28 10:00:30');
    Worklog::start($user, $b);
    Carbon::setTestNow();

    expect((int) Worklog::running($user)->task_id)->toBe($b)
        ->and((float) Timesheet::query()->where('task_id', $a)->sum('unit_amount'))->toBe(1.0);
});

it('sets the daily total from the weekly grid around timer entries', function () {
    $user = worker();
    $task = workTask($user);
    $day = '2026-09-28';

    Carbon::setTestNow("{$day} 09:00:00");
    Worklog::start($user, $task);
    Carbon::setTestNow("{$day} 10:00:00");
    Worklog::stop($user);
    Carbon::setTestNow();

    Worklog::setDayTotal($user, $task, $day, 3);
    $total = fn (): float => (float) Timesheet::query()->where('task_id', $task)->whereDate('date', $day)->sum('unit_amount');
    expect($total())->toBe(3.0);

    Worklog::setDayTotal($user, $task, $day, 1);
    expect($total())->toBe(1.0)
        ->and(Timesheet::query()->where('task_id', $task)->where('name', Worklog::WEEKLY_ENTRY)->exists())->toBeFalse()
        ->and(fn () => Worklog::setDayTotal($user, $task, $day, 0.5))->toThrow(RuntimeException::class);

    $week = Worklog::week($user, Carbon::parse($day)->toImmutable()->startOfWeek());
    expect($week['rows'][0]['hours'][$day])->toBe(1.0)
        ->and($week['totals'][$day])->toBe(1.0);
});

it('reads expected hours from the work calendar, eight on weekdays otherwise', function () {
    $user = worker();

    expect(Worklog::expectedHours($user, '2026-09-28'))->toBe(8.0)
        ->and(Worklog::expectedHours($user, '2026-10-04'))->toBe(0.0)
        ->and(Worklog::parse('1:30'))->toBe(1.5)
        ->and(Worklog::parse('2,25'))->toBe(2.25)
        ->and(Worklog::format(1.75))->toBe('1:45');
});

it('renders the week, the team overview and the top bar timer', function () {
    Filament\Facades\Filament::setCurrentPanel(Filament\Facades\Filament::getPanel('admin'));
    $admin = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'Admin'))->first()
        ?? tap(worker())->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
    $task = workTask($admin);
    $this->actingAs($admin);

    Livewire\Livewire::test(TopbarTimer::class)->assertOk()->assertSee('Timer')
        ->call('start', $task)->assertSee('hv-timer-running', false);
    Livewire\Livewire::test(MyWeek::class)->assertOk()->assertSee('Aggiungimi a un task')
        ->call('saveCell', $task, now()->toDateString(), '1:30')->assertOk()
        ->set('joining', true)->assertOk();
    Livewire\Livewire::test(TeamHours::class)->assertOk()->assertSee('Ore per progetto')
        ->call('shiftWeek', -1)->assertOk();

    expect((float) Timesheet::query()->where('task_id', $task)->where('name', Worklog::WEEKLY_ENTRY)->sum('unit_amount'))->toBe(1.5);
});
