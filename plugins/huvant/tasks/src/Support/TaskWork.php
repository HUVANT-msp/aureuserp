<?php

namespace Huvant\Tasks\Support;

use Illuminate\Support\Collection;
use Webkul\Project\Models\Task;
use Webkul\Timesheet\Models\Timesheet;

/** Who worked on a task (and its subtasks), how long, and what they declared. */
class TaskWork
{
    /** @return array{total: float, people: list<array{id:int,name:string,hours:float,share:float,entries:int}>, entries: Collection} */
    public static function summary(Task $task, int $limit = 200): array
    {
        $taskIds = $task->subTasks()->pluck('id')->push($task->getKey())->all();
        $entries = Timesheet::query()
            ->whereIn('task_id', $taskIds)
            ->with(['user:id,name', 'task:id,title,parent_id'])
            ->orderByDesc('date')->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'date', 'name', 'unit_amount', 'user_id', 'task_id']);

        $total = round((float) Timesheet::query()->whereIn('task_id', $taskIds)->sum('unit_amount'), 2);
        $people = Timesheet::query()->whereIn('task_id', $taskIds)->whereNotNull('user_id')
            ->selectRaw('user_id, SUM(unit_amount) as hours, COUNT(*) as entries')
            ->groupBy('user_id')->orderByDesc('hours')->with('user:id,name')->get()
            ->map(fn ($row): array => [
                'id'      => (int) $row->user_id,
                'name'    => $row->user?->name ?? '—',
                'hours'   => round((float) $row->hours, 2),
                'share'   => $total > 0 ? round((float) $row->hours / $total * 100, 1) : 0.0,
                'entries' => (int) $row->entries,
            ])->all();

        return ['total' => $total, 'people' => $people, 'entries' => $entries];
    }

    public static function hours(float $hours): string
    {
        $minutes = (int) round($hours * 60);

        return sprintf('%d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
