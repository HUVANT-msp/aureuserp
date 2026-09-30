<?php

namespace Huvant\Tasks\Http\Controllers;

use Huvant\Bridge\Support\MinutesApi;
use Huvant\Tasks\Support\TaskOrigin;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Project\Models\Task;

class TaskMinutesController
{
    /** The minutes PDF of the meeting a task came from, for whoever can see the task. */
    public function show(int $task): Response
    {
        abort_unless(auth()->check(), 403);
        $model = Task::query()->find($task); // project-team visibility applies
        abort_unless($model, 404);
        $origin = TaskOrigin::for($model);
        abort_unless($origin && ($origin['pdf'] ?? false), 404);

        try {
            $pdf = MinutesApi::send('minutes-pdf', ['meeting_id' => $origin['meeting_id']], 30);
        } catch (\Throwable $e) {
            report($e);
            abort(502, 'The minutes are not reachable right now.');
        }
        $name = preg_replace('/[^\w\s.-]+/u', '', 'Minutes '.$origin['title'].' '.($origin['date'] ?? '')) ?: 'Minutes';

        return response($pdf->body(), 200, [
            'Content-Type'           => 'application/pdf',
            'Content-Disposition'    => 'attachment; filename="'.trim(mb_substr($name, 0, 120)).'.pdf"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
