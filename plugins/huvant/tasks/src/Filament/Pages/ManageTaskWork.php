<?php

namespace Huvant\Tasks\Filament\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Huvant\Tasks\Support\TaskWork;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Project\Filament\Resources\TaskResource;

/** Who worked on the task, how long and what they did, subtask by subtask. */
class ManageTaskWork extends Page
{
    use InteractsWithRecord;

    protected static string $resource = TaskResource::class;

    protected string $view = 'huvant-tasks::filament.pages.task-work';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public static function getNavigationLabel(): string
    {
        return 'Work';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Work on this task';
    }

    protected function getViewData(): array
    {
        $task = $this->record->load(['subTasks' => fn ($q) => $q->with(['users:id,name', 'stage:id,name'])->orderBy('id'), 'users:id,name']);

        return [
            'task' => $task,
            'work' => TaskWork::summary($task, 500),
            'subs' => $task->subTasks->map(fn ($sub) => ['task' => $sub, 'work' => TaskWork::summary($sub, 0)]),
        ];
    }
}
