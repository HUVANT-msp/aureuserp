<?php

use Huvant\Teams\Support\ProjectTeams;
use Huvant\Worklog\Filament\Pages\MyWeek;
use Huvant\Worklog\Filament\Pages\TeamHours;
use Huvant\Worklog\Filament\Pages\TimeEntries;
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
        ->and(fn () => Worklog::addEntry($user, $other, '2026-09-28', 2, 'Analisi'))->toThrow(RuntimeException::class);

    Worklog::join($user, $other);
    expect(Worklog::isAssignee($user, $other))->toBeTrue();
});

it('asks what was done before a timer becomes an entry, and records when', function () {
    $user = worker();
    $task = workTask($user);

    Carbon::setTestNow('2026-09-28 09:00:00');
    Worklog::start($user, $task);
    Carbon::setTestNow('2026-09-28 10:30:00');
    expect(fn () => Worklog::stop($user, '  '))->toThrow(RuntimeException::class)
        ->and(Worklog::running($user))->not->toBeNull();

    $entry = Worklog::stop($user, 'Analisi dei requisiti');
    Carbon::setTestNow();

    $span = DB::table(Worklog::SPANS)->where('timesheet_id', $entry->getKey())->first();
    expect((float) $entry->unit_amount)->toBe(1.5)
        ->and($entry->name)->toBe('Analisi dei requisiti')
        ->and(Worklog::running($user))->toBeNull()
        ->and(Carbon::parse($span->started_at)->format('H:i'))->toBe('09:00')
        ->and(Carbon::parse($span->ended_at)->format('H:i'))->toBe('10:30')
        ->and((float) DB::table('projects_tasks')->where('id', $task)->value('effective_hours'))->toBe(1.5);
});

it('uses the start note as description, drops a short timer and never runs two', function () {
    $user = worker();
    [$a, $b] = [workTask($user), workTask($user)];

    Carbon::setTestNow('2026-09-28 09:00:00');
    Worklog::start($user, $a, 'Revisione');
    Carbon::setTestNow('2026-09-28 09:00:30');
    expect(Worklog::stop($user))->toBeNull();

    Worklog::start($user, $a, 'Revisione');
    expect(fn () => Worklog::start($user, $b))->toThrow(RuntimeException::class);
    Carbon::setTestNow('2026-09-28 10:00:00');
    expect(Worklog::stop($user)->name)->toBe('Revisione');

    Worklog::start($user, $b);
    Worklog::discard($user);
    Carbon::setTestNow();
    expect(Worklog::running($user))->toBeNull()
        ->and(Timesheet::query()->where('task_id', $b)->exists())->toBeFalse();
});

it('adds, corrects and removes entries, each with a description', function () {
    $user = worker();
    $colleague = worker();
    $task = workTask($user);
    $day = '2026-09-28';

    expect(fn () => Worklog::addEntry($user, $task, $day, 1, ''))->toThrow(RuntimeException::class)
        ->and(fn () => Worklog::addEntry($user, $task, now()->addDay()->toDateString(), 1, 'Domani'))->toThrow(RuntimeException::class);

    $entry = Worklog::addEntry($user, $task, $day, 2, 'Prove al banco', '14:00');
    expect(Carbon::parse(DB::table(Worklog::SPANS)->where('timesheet_id', $entry->getKey())->value('ended_at'))->format('H:i'))->toBe('16:00');

    Worklog::updateEntry($user, $entry, 1.5, 'Prove al banco e report');
    expect((float) $entry->fresh()->unit_amount)->toBe(1.5)
        ->and(fn () => Worklog::updateEntry($colleague, $entry, 3, 'No'))->toThrow(RuntimeException::class)
        ->and(Worklog::entriesOn($user, $task, $day))->toHaveCount(1);

    $week = Worklog::week($user, Carbon::parse($day)->toImmutable()->startOfWeek());
    $timeline = Worklog::timeline($user, Carbon::parse($day)->toImmutable()->startOfWeek());
    expect($week['totals'][$day])->toBe(1.5)
        ->and($timeline['days'][0]['blocks'][0]['from'])->toBe(14 * 60)
        ->and($timeline['days'][0]['blocks'][0]['to'])->toBe(15 * 60 + 30)
        ->and($timeline['total'])->toBe(1.5);

    Worklog::deleteEntry($user, $entry);
    expect(Timesheet::query()->whereKey($entry->getKey())->exists())->toBeFalse();
});

it('reads expected hours from the work calendar, eight on weekdays otherwise', function () {
    $user = worker();

    expect(Worklog::expectedHours($user, '2026-09-28'))->toBe(8.0)
        ->and(Worklog::expectedHours($user, '2026-10-04'))->toBe(0.0)
        ->and(Worklog::parse('1:30'))->toBe(1.5)
        ->and(Worklog::parse('2,25'))->toBe(2.25)
        ->and(Worklog::format(1.75))->toBe('1:45');
});

it('renders my hours, the entries, the team overview and the top bar timer', function () {
    Filament\Facades\Filament::setCurrentPanel(Filament\Facades\Filament::getPanel('admin'));
    $admin = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'Admin'))->first()
        ?? tap(worker())->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
    $task = workTask($admin);
    $this->actingAs($admin);

    $timer = Livewire\Livewire::test(TopbarTimer::class)->assertOk()->assertSee('Timer')
        ->call('start', $task)->assertSee('hv-timer-running', false);
    Carbon::setTestNow(now()->addHour());
    $timer->set('description', '')->call('stop')->assertSee('hv-timer-running', false)
        ->call('discard')->assertDontSee('hv-timer-running', false);
    Carbon::setTestNow();

    Livewire\Livewire::test(MyWeek::class)->assertOk()->assertSee('Join a task')
        ->call('openDay', $task, now()->toDateString())
        ->set('entry', ['from' => '9:00', 'hours' => '1:30', 'description' => 'Montaggio'])->call('addEntry')
        ->assertCount('edits', 1)
        ->call('setTab', 'timeline')->assertOk()->assertSee('Montaggio', false)
        ->set('joining', true)->assertOk();
    Livewire\Livewire::test(TimeEntries::class)->assertOk()->assertSee('Montaggio');
    Livewire\Livewire::test(TeamHours::class)->assertOk()->assertSee('Hours by project')
        ->call('shiftWeek', -1)->assertOk();

    expect((float) Timesheet::query()->where('task_id', $task)->sum('unit_amount'))->toBe(1.5);
});
