<?php

namespace Huvant\Tasks\Livewire;

use Carbon\CarbonImmutable;
use Filament\Notifications\Notification;
use Huvant\Tasks\Support\Board;
use Huvant\Tasks\Support\TaskWork;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use RuntimeException;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Models\User;

/** The side sheet that opens on a task, with what one actually does on it. */
class TaskPanel extends Component
{
    public ?int $taskId = null;

    public string $newSubtask = '';

    public ?string $addPerson = null;

    public array $entry = ['date' => '', 'hours' => '', 'description' => ''];

    #[On('huvant-open-task')]
    public function open(int $taskId): void
    {
        $this->taskId = $taskId;
        $this->reset('newSubtask', 'addPerson');
        $this->entry = ['date' => CarbonImmutable::today()->toDateString(), 'hours' => '', 'description' => ''];
        if (! $this->task()) {
            $this->taskId = null;
            Notification::make()->warning()->title('Task non trovato o non visibile.')->send();

            return;
        }
        $this->dispatch('open-modal', id: 'huvant-task-panel');
    }

    public function setStage(int $stageId): void
    {
        $this->attempt(function (): void {
            $stage = TaskStage::query()->where('project_id', $this->task()->project_id)->findOrFail($stageId);
            Board::move($this->user(), $this->task(), $stage->name);
        });
    }

    public function setDate(string $field, ?string $value): void
    {
        if (! in_array($field, ['deadline', 'huvant_start_date'], true)) {
            return;
        }
        $this->attempt(function () use ($field, $value): void {
            $task = $this->editableTask();
            $date = $value ? CarbonImmutable::parse($value) : null;
            if ($field === 'deadline' && $date && $task->huvant_start_date && $date->lt(CarbonImmutable::parse($task->huvant_start_date))) {
                throw new RuntimeException('La scadenza è prima dell\'inizio.');
            }
            $task->forceFill([$field => $field === 'deadline' ? $date?->setTime(18, 0) : $date?->toDateString()])->save();
        });
    }

    public function togglePriority(): void
    {
        $this->attempt(fn () => ($task = $this->editableTask())->forceFill(['priority' => ! $task->priority])->save());
    }

    public function assign(int $userId): void
    {
        $this->attempt(fn () => Board::setAssignees($this->user(), $this->task(), [...$this->task()->users->pluck('id')->all(), $userId]));
        $this->addPerson = null;
    }

    public function unassign(int $userId): void
    {
        $this->attempt(fn () => Board::setAssignees($this->user(), $this->task(), $this->task()->users->pluck('id')->reject(fn ($id) => (int) $id === $userId)->all()));
    }

    public function addSubtask(): void
    {
        $title = trim($this->newSubtask);
        if ($title === '') {
            return;
        }
        $this->attempt(function () use ($title): void {
            $parent = $this->editableTask();
            $stage = TaskStage::query()->where('project_id', $parent->project_id)->orderBy('sort')->get()
                ->first(fn (TaskStage $s): bool => Board::stageKind($s->name) === 'todo');
            Task::query()->create([
                'title'      => mb_substr($title, 0, 255),
                'project_id' => $parent->project_id,
                'parent_id'  => $parent->getKey(),
                'stage_id'   => $stage?->getKey() ?? $parent->stage_id,
                'state'      => TaskState::IN_PROGRESS,
                'is_active'  => true,
                'creator_id' => $this->user()->getKey(),
            ]);
            $this->newSubtask = '';
        });
    }

    public function toggleSubtask(int $subtaskId): void
    {
        $this->attempt(function () use ($subtaskId): void {
            $sub = $this->task()->subTasks()->with('stage')->findOrFail($subtaskId);
            $done = $sub->state === TaskState::DONE;
            $target = TaskStage::query()->where('project_id', $sub->project_id)->orderBy('sort')->get()
                ->first(fn (TaskStage $s): bool => Board::stageKind($s->name) === ($done ? 'todo' : 'done'));
            if ($target) {
                Board::move($this->user(), $sub, $target->name);
            } else {
                $sub->forceFill(['state' => $done ? TaskState::IN_PROGRESS : TaskState::DONE])->save();
            }
        });
    }

    public function startTimer(): void
    {
        $this->attempt(fn () => Worklog::start($this->user(), $this->taskId), 'Timer avviato');
        $this->dispatch('huvant-worklog-changed');
    }

    public function logTime(): void
    {
        $ok = $this->attempt(fn () => Worklog::addEntry(
            $this->user(), $this->taskId, (string) $this->entry['date'], Worklog::parse((string) $this->entry['hours']), (string) $this->entry['description']
        ), 'Ore registrate');
        if ($ok) {
            $this->entry = ['date' => $this->entry['date'], 'hours' => '', 'description' => ''];
            $this->dispatch('huvant-worklog-changed');
        }
    }

    public function joinTask(): void
    {
        $this->assign($this->user()->getKey());
    }

    public function render(): View
    {
        $task = $this->taskId ? $this->task() : null;

        return view('huvant-tasks::livewire.task-panel', $task ? [
            'task'       => $task,
            'stages'     => TaskStage::query()->where('project_id', $task->project_id)->orderBy('sort')->get(['id', 'name']),
            'work'       => TaskWork::summary($task),
            'candidates' => Board::assignableUsers($task)->reject(fn (User $u) => $task->users->contains('id', $u->id))->values(),
            'canEdit'    => Gate::allows('update', $task),
            'isAssignee' => $task->users->contains('id', $this->user()->getKey()),
            'running'    => class_exists(Worklog::class) ? Worklog::running($this->user()) : null,
        ] : ['task' => null]);
    }

    private function task(): ?Task
    {
        return $this->taskId ? Task::query()
            ->with(['project:id,name,color', 'stage:id,name', 'users:id,name', 'subTasks' => fn ($q) => $q->with(['users:id,name', 'stage:id,name'])->orderBy('id'), 'parent:id,title'])
            ->find($this->taskId) : null;
    }

    private function editableTask(): Task
    {
        $task = $this->task();
        if (! $task || ! Gate::allows('update', $task)) {
            throw new RuntimeException('Non puoi modificare questo task.');
        }

        return $task;
    }

    private function attempt(callable $callback, ?string $success = null): bool
    {
        try {
            $callback();
            if ($success) {
                Notification::make()->success()->title($success)->send();
            }
            $this->dispatch('huvant-task-changed', taskId: $this->taskId);

            return true;
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        } catch (ModelNotFoundException) {
            Notification::make()->danger()->title('Elemento non trovato.')->send();
        }

        return false;
    }

    private function user(): User
    {
        return auth()->user();
    }
}
