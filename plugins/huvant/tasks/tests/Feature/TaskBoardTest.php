<?php

use Filament\Facades\Filament;
use Huvant\Tasks\Filament\Pages\ManageProjectBoard;
use Huvant\Tasks\Filament\Pages\ManageProjectNotes;
use Huvant\Tasks\Filament\Pages\ManageTaskWork;
use Huvant\Tasks\Filament\Pages\TaskBoardPage;
use Huvant\Tasks\Livewire\TaskBoard;
use Huvant\Tasks\Livewire\TaskPanel;
use Huvant\Tasks\Support\Board;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Clusters\Configurations\Resources\TaskStageResource;
use Webkul\Project\Filament\Clusters\PluginSettings;
use Webkul\Project\Filament\Resources\TaskResource;
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

it('groups tasks by stage, hiding cancelled ones', function () {
    $admin = boardAdmin();
    [$project, $stages] = boardProject();
    boardTask($project, $stages['To Do']);
    boardTask($project, $stages['Done'], ['state' => 'done']);
    boardTask($project, $stages['Cancelled'], ['state' => 'cancelled']);
    $this->actingAs($admin);

    $columns = collect(Board::columns($admin, ['projects' => [$project->getKey()]]));

    expect($columns->pluck('label')->all())->toBe(['To do', 'In progress', 'Done'])
        ->and($columns->firstWhere('label', 'To do')['count'])->toBe(1)
        ->and(collect(Board::columns($admin, ['projects' => [$project->getKey()], 'cancelled' => true]))->pluck('label'))->toContain('Cancelled');
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
        ->call('open', $id)->assertOk()->assertSee('Montare il banco prova')->assertSee('Join this task')
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

it('creates tasks from the board with the standard form, in the chosen stage', function () {
    $admin = boardAdmin();
    [$project, $stages] = boardProject();
    $this->actingAs($admin);

    Livewire::test(TaskBoard::class, ['projectId' => $project->getKey()])
        ->mountAction('addTask', ['stage' => 'In Progress'])
        ->assertActionDataSet(['stage_id' => $stages['In Progress'], 'project_id' => $project->getKey()])
        ->setActionData(['title' => 'Nuovo dal kanban'])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    $task = Task::query()->where('title', 'Nuovo dal kanban')->firstOrFail();
    expect($task->project_id)->toBe($project->getKey())->and($task->stage_id)->toBe($stages['In Progress']);
});

it('keeps tasks inside projects: no global task list or board in the menu, configuration under settings', function () {
    expect(TaskResource::shouldRegisterNavigation())->toBeFalse()
        ->and(TaskBoardPage::shouldRegisterNavigation())->toBeFalse()
        ->and(TaskStageResource::getCluster())->toBe(PluginSettings::class);
});

it('shows the project notes from the meetings and changes their state', function () {
    $admin = boardAdmin();
    [$project] = boardProject();
    config(['huvant-bridge.webhook.url' => 'http://minutes.test/api/v1/erp/webhook', 'huvant-bridge.webhook.secret' => 'notes-secret']);
    $note = fn (string $kind, string $text, string $status = 'open', array $actions = ['resolved']) => [
        'key'  => 'minutes:m1:'.md5($text), 'kind' => $kind, 'text' => $text, 'owner' => 'Anna', 'due' => null, 'meeting' => 'Planning Atlas',
        'date' => '2026-09-28', 'url' => '/riunioni/?meeting=m1', 'status' => $status, 'status_note' => null, 'status_by' => null, 'status_at' => null, 'actions' => $actions,
    ];
    Http::fake([
        'minutes.test/api/v1/erp/project-notes' => Http::response(['notes' => [
            $note('open_point', 'Chi approva il budget?'),
            $note('risk', 'Fornitore in ritardo', 'open', ['addressed', 'accepted']),
            $note('open_point', 'Data del kick-off', 'resolved'),
        ], 'meetings' => [['source' => 'minutes', 'id' => 'm1', 'title' => 'Planning Atlas', 'date' => '2026-09-28', 'classified' => true]]]),
        'minutes.test/api/v1/erp/project-notes/*' => Http::response(['ok' => true]),
    ]);
    $this->actingAs($admin);

    $page = Livewire::test(ManageProjectNotes::class, ['record' => $project->getKey()])->assertOk()
        ->assertSee('Open questions')->assertSee('Chi approva il budget?')->assertSee('Mark addressed')->assertDontSee('Data del kick-off')
        ->set('showClosed', true)->assertSee('Data del kick-off')
        ->call('askState', 'minutes:m1:x', 'open_point', 'resolved')->set('noteText', 'Approvato dal cliente')->call('confirmState')
        ->call('moveMeeting', 'minutes', 'm1', $project->getKey());

    Http::assertSent(fn ($request) => str_ends_with($request->url(), '/project-notes/state')
        && $request['status'] === 'resolved' && $request['note'] === 'Approvato dal cliente' && $request['email'] === $admin->email);
    Http::assertSent(fn ($request) => str_ends_with($request->url(), '/project-notes/move') && $request['meeting_id'] === 'm1');
});

it('shows the meeting minutes as an attachment and downloads the PDF through the ERP', function () {
    $admin = boardAdmin();
    [$project, $stages] = boardProject();
    $id = boardTask($project, $stages['To Do'], ['title' => 'Contattare l\'Agenzia', 'description' => '<p><strong>Obiettivo:</strong> Avere i documenti.</p>']);
    config(['huvant-bridge.webhook.url' => 'http://minutes.test/api/v1/erp/webhook', 'huvant-bridge.webhook.secret' => 'pdf-secret']);
    Http::fake([
        'minutes.test/api/v1/erp/task-origin' => Http::response(['origin' => [
            'source' => 'minutes', 'meeting_id' => 'm1', 'title' => 'Planning Atlas', 'date' => '2026-09-28', 'pdf' => true, 'url' => '/riunioni/?meeting=m1',
        ]]),
        'minutes.test/api/v1/erp/minutes-pdf' => Http::response('%PDF-1.4 minutes', 200, ['Content-Type' => 'application/pdf']),
    ]);
    $this->actingAs($admin);

    Livewire::test(TaskPanel::class)->call('open', $id)->assertSee('Meeting minutes')->assertSee('Planning Atlas')->assertSee('Obiettivo:', false);

    $response = $this->get(route('huvant.tasks.minutes', ['task' => $id]));
    $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
    expect($response->getContent())->toBe('%PDF-1.4 minutes')
        ->and($response->headers->get('Content-Disposition'))->toContain('attachment; filename="Minutes Planning Atlas 2026-09-28.pdf"');

    $this->get(route('huvant.tasks.minutes', ['task' => 999999]))->assertNotFound();
});
