<?php

namespace Huvant\Tasks\Livewire;

use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Huvant\Tasks\Support\Board;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use RuntimeException;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Models\User;

class TaskBoard extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions, InteractsWithSchemas;

    /** Set when the board lives inside a project: the project filter is fixed. */
    public ?int $projectId = null;

    /** Set when the board shows one person's work (employee page): the assignee filter is fixed. */
    public ?int $assigneeId = null;

    #[Url(as: 'view')]
    public string $view = 'kanban';

    #[Url(as: 'filters')]
    public array $filters = Board::DEFAULT_FILTERS;

    #[Url(as: 'from')]
    public string $from = '';

    public int $weeks = 6;

    public string $sort = 'deadline';

    public bool $sortDesc = false;

    public function mount(?int $projectId = null, ?int $assigneeId = null): void
    {
        $this->projectId = $projectId;
        $this->assigneeId = $assigneeId;
        $this->filters = Board::normalize($this->filters);
        if (! in_array($this->view, ['kanban', 'timeline', 'list'], true)) {
            $this->view = 'kanban';
        }
    }

    public function setView(string $view): void
    {
        $this->view = in_array($view, ['kanban', 'timeline', 'list'], true) ? $view : 'kanban';
    }

    public function resetFilters(): void
    {
        $this->filters = Board::DEFAULT_FILTERS;
    }

    /** "New task": the standard task form, as a side panel. */
    public function newTaskAction(): Action
    {
        return $this->taskFormAction('newTask')->label('New task')->icon('heroicon-m-plus');
    }

    /** "+" on a Kanban column: the same form, already in that stage. */
    public function addTaskAction(): Action
    {
        return $this->taskFormAction('addTask')->label('Add a task here')->icon('heroicon-m-plus')->iconButton()->size('sm')->color('gray');
    }

    private function taskFormAction(string $name): CreateAction
    {
        return CreateAction::make($name)
            ->model(Task::class)
            ->modalHeading('New task')
            ->slideOver()
            ->modalWidth(Width::FourExtraLarge)
            ->schema(fn (Schema $schema): Schema => TaskResource::form($schema))
            ->fillForm(function (array $arguments): array {
                $projectId = $this->projectId;
                $stageId = null;
                if ($projectId && ($arguments['stage'] ?? '') !== '') {
                    $stageId = TaskStage::query()->where('project_id', $projectId)->get()
                        ->first(fn (TaskStage $stage): bool => Str::lower(trim($stage->name)) === Str::lower(trim((string) $arguments['stage'])))?->getKey();
                }

                return array_filter([
                    'users'      => $this->assigneeId ? [$this->assigneeId] : null,
                    'project_id' => $projectId,
                    'stage_id'   => $stageId ?? ($projectId ? TaskResource::getDefaultStageId($projectId) : null),
                    'state'      => TaskState::IN_PROGRESS,
                ], fn ($value) => $value !== null);
            })
            ->successNotificationTitle('Task created')
            ->after(fn () => $this->dispatch('huvant-task-changed'));
    }

    public function moveTask(int $taskId, string $stageName): void
    {
        $task = Task::query()->find($taskId);
        if (! $task) {
            return;
        }
        try {
            Board::move($this->user(), $task, $stageName);
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
        $this->dispatch('huvant-task-changed', taskId: $taskId);
    }

    public function openTask(int $taskId): void
    {
        $this->dispatch('huvant-open-task', taskId: $taskId);
    }

    public function shiftTimeline(int $weeks): void
    {
        $this->from = $this->timelineStart()->addWeeks($weeks)->toDateString();
    }

    public function zoom(int $weeks): void
    {
        $this->weeks = in_array($weeks, [4, 6, 12, 26], true) ? $weeks : 6;
    }

    public function sortBy(string $column): void
    {
        $this->sortDesc = $this->sort === $column ? ! $this->sortDesc : false;
        $this->sort = $column;
    }

    #[On('huvant-task-changed')]
    public function refreshBoard(): void {}

    public function render(): View
    {
        $user = $this->user();
        $filters = Board::normalize($this->filters);
        if ($this->projectId) {
            $filters['projects'] = [$this->projectId];
        }
        if ($this->assigneeId) {
            $filters['assignee'] = (string) $this->assigneeId;
        }

        $data = [
            'projects'  => $this->projectId ? collect() : Board::visibleProjects(),
            'people'    => User::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'active'    => $this->activeFilterCount(),
            'today'     => CarbonImmutable::today(),
        ];

        if ($this->view === 'kanban') {
            $data['columns'] = Board::columns($user, $filters);
        } elseif ($this->view === 'timeline') {
            $start = $this->timelineStart();
            $end = $start->addWeeks($this->weeks)->subDay();
            $data['start'] = $start;
            $data['end'] = $end;
            $data['days'] = (int) $start->diffInDays($end) + 1;
            $data['rows'] = Board::query($user, $filters)
                ->where(fn ($q) => $q->whereNull('deadline')->orWhereDate('deadline', '>=', $start))
                ->where(fn ($q) => $q->whereNull('huvant_start_date')->orWhereDate('huvant_start_date', '<=', $end))
                ->whereDate('created_at', '<=', $end)
                ->orderBy('deadline')->limit(400)->get()
                ->groupBy(fn (Task $task): string => $task->project?->name ?? 'No project')
                ->sortKeys();
        } else {
            $tasks = Board::query($user, $filters)->limit(800)->get();
            $key = match ($this->sort) {
                'title'   => fn (Task $t) => mb_strtolower($t->title),
                'project' => fn (Task $t) => mb_strtolower($t->project?->name ?? '~'),
                'stage'   => fn (Task $t) => $t->stage?->sort ?? 99,
                'hours'   => fn (Task $t) => (float) $t->total_hours_spent,
                default   => fn (Task $t) => $t->deadline?->timestamp ?? PHP_INT_MAX,
            };
            $data['tasks'] = $tasks->sortBy($key, SORT_REGULAR, $this->sortDesc)->values();
        }

        return view('huvant-tasks::livewire.task-board', $data);
    }

    private function timelineStart(): CarbonImmutable
    {
        try {
            $day = $this->from !== '' ? CarbonImmutable::parse($this->from) : CarbonImmutable::today()->subWeek();
        } catch (\Throwable) {
            $day = CarbonImmutable::today()->subWeek();
        }

        return $day->startOfWeek();
    }

    private function activeFilterCount(): int
    {
        $f = Board::normalize($this->filters);

        return (int) (! $this->projectId && $f['projects']) + (int) (! $this->assigneeId && $f['assignee'] !== 'all') + (int) ($f['due'] !== 'all')
            + (int) ($f['search'] !== '') + (int) $f['cancelled'] + (int) $f['subtasks'];
    }

    private function user(): User
    {
        return auth()->user();
    }
}
