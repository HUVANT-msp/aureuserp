<?php

use Filament\Facades\Filament;
use Huvant\Tasks\Filament\Pages\ManageProjectBoard;
use Huvant\Tasks\Filament\Pages\ManageTaskWork;
use Huvant\Tasks\Livewire\TaskBoard;
use Huvant\Tasks\Livewire\TaskPanel;
use Huvant\Tasks\Support\Board;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-worklog');
    TestBootstrapHelper::ensurePluginInstalled('huvant-tasks');
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function boardAdmin(): User
{
    return User::query()->whereHas('roles', fn ($query) => $query->where('name', 'Admin'))->first()
        ?? tap(User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true])))->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
}

/** A project with the usual four stages; returns [project, stages by name]. */
function boardProject(): array
{
    $project = Project::withoutEvents(fn (): Project => Project::factory()->create());
    $stages = [];
    foreach (['To Do', 'In Progress', 'Done', 'Cancelled'] as $i => $name) {
        $stages[$name] = DB::table('projects_task_stages')->insertGetId([
            'name' => $name, 'sort' => $i + 1, 'is_active' => true, 'project_id' => $project->getKey(), 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    return [$project, $stages];
}

// Upstream TaskFactory writes a non-existent "visibility" column: insert directly.
function boardTask(Project $project, int $stageId, array $extra = []): int
{
    return DB::table('projects_tasks')->insertGetId(array_merge([
        'title'     => 'Task '.uniqid(), 'state' => 'in_progress', 'project_id' => $project->getKey(), 'stage_id' => $stageId,
        'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
    ], $extra));
}

it('groups tasks by stage with Italian labels, hiding cancelled ones', function () {
    $admin = boardAdmin();
    [$project, $stages] = boardProject();
    boardTask($project, $stages['To Do']);
    boardTask($project, $stages['Done'], ['state' => 'done']);
    boardTask($project, $stages['Cancelled'], ['state' => 'cancelled']);
    $this->actingAs($admin);

    $columns = collect(Board::columns($admin, ['projects' => [$project->getKey()]]));

    expect($columns->pluck('label')->all())->toBe(['Da fare', 'In corso', 'Completati'])
        ->and($columns->firstWhere('label', 'Da fare')['count'])->toBe(1)
        ->and(collect(Board::columns($admin, ['projects' => [$project->getKey()], 'cancelled' => true]))->pluck('label'))->toContain('Annullati');
});

it('moves a task between stages and keeps the state in step', function () {
    $admin = boardAdmin();
    [$project, $stages] = boardProject();
    $id = boardTask($project, $stages['To Do']);
    $this->actingAs($admin);

    Board::move($admin, Task::query()->findOrFail($id), 'Done');
    $task = Task::query()->findOrFail($id);
    expect($task->stage_id)->toBe($stages['Done'])->and($task->state)->toBe(TaskState::DONE);

    Board::move($admin, $task, 'In Progress');
    expect(Task::query()->findOrFail($id)->state)->toBe(TaskState::IN_PROGRESS);

    $stranger = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true]));
    expect(fn () => Board::move($stranger, Task::query()->findOrFail($id), 'Done'))->toThrow(RuntimeException::class);
});

it('filters by assignee, deadline and search', function () {
    $admin = boardAdmin();
    [$project, $stages] = boardProject();
    $mine = boardTask($project, $stages['To Do'], ['title' => 'Preparare offerta', 'deadline' => now()->subDays(2)]);
    $other = boardTask($project, $stages['To Do'], ['title' => 'Altro lavoro']);
    DB::table('projects_task_users')->insert(['task_id' => $mine, 'user_id' => $admin->getKey()]);
    $this->actingAs($admin);

    $ids = fn (array $f) => Board::query($admin, ['projects' => [$project->getKey()], ...$f])->pluck('id')->all();
    expect($ids(['assignee' => 'me']))->toBe([$mine])
        ->and($ids(['assignee' => 'none']))->toBe([$other])
        ->and($ids(['due' => 'overdue']))->toBe([$mine])
        ->and($ids(['search' => 'offerta']))->toBe([$mine]);
});

it('renders every view, the panel and the project and task tabs', function () {
    $admin = boardAdmin();
    [$project, $stages] = boardProject();
    $id = boardTask($project, $stages['To Do'], ['title' => 'Montare il banco prova', 'deadline' => now()->addDays(3)]);
    $this->actingAs($admin);

    foreach (['kanban', 'timeline', 'list'] as $view) {
        Livewire::test(TaskBoard::class, ['projectId' => $project->getKey()])->call('setView', $view)->assertOk()->assertSee('Montare il banco prova');
    }
    Livewire::test(TaskBoard::class, ['projectId' => $project->getKey()])->call('moveTask', $id, 'In Progress')->assertOk();
    expect(Task::query()->findOrFail($id)->stage_id)->toBe($stages['In Progress']);

    Livewire::test(TaskPanel::class)
        ->call('open', $id)->assertOk()->assertSee('Montare il banco prova')->assertSee('Aggiungimi al task')
        ->call('joinTask')
        ->set('newSubtask', 'Ordinare i sensori')->call('addSubtask')
        ->assertSee('Ordinare i sensori')
        ->set('entry', ['date' => now()->toDateString(), 'hours' => '1:30', 'description' => ''])->call('logTime')
        ->set('entry', ['date' => now()->toDateString(), 'hours' => '1:30', 'description' => 'Cablaggio'])->call('logTime')
        ->assertSee('Cablaggio');

    expect(Worklog::isAssignee($admin, $id))->toBeTrue()
        ->and((float) DB::table('analytic_records')->where('task_id', $id)->sum('unit_amount'))->toBe(1.5)
        ->and(Task::query()->where('parent_id', $id)->value('title'))->toBe('Ordinare i sensori');

    $this->get(ManageProjectBoard::getUrl(['record' => $project->getKey()]))->assertOk();
    $this->get(ManageTaskWork::getUrl(['record' => $id]))->assertOk()->assertSee('Cablaggio');
});
