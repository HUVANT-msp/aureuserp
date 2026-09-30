<?php

namespace Huvant\Tasks\Support;

use Huvant\Bridge\Support\MinutesApi;
use Webkul\Project\Models\Task;

/**
 * The meeting a task came from, asked to the Minutes (cached): its minutes
 * are shown as an attachment of the task instead of a line in the description.
 */
class TaskOrigin
{
    /** @return array{source: string, meeting_id: string, title: string, date: ?string, pdf: bool, url: ?string}|null */
    public static function for(Task $task): ?array
    {
        if (! class_exists(MinutesApi::class)) {
            return null;
        }

        return cache()->remember('huvant-task-origin:'.$task->getKey(), now()->addMinutes(10), function () use ($task): ?array {
            try {
                $origin = MinutesApi::post('task-origin', ['erp_task_id' => (int) $task->getKey()], 4)['origin'] ?? null;
            } catch (\Throwable $e) {
                report($e);

                return null;
            }

            return is_array($origin) ? $origin : null;
        });
    }

    public static function downloadUrl(Task $task): string
    {
        return route('huvant.tasks.minutes', ['task' => $task->getKey()]);
    }
}
