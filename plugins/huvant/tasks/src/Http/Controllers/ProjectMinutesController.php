<?php

namespace Huvant\Tasks\Http\Controllers;

use Huvant\Bridge\Support\MinutesApi;
use Huvant\Tasks\Filament\Pages\ManageProjectMeetings;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Project\Models\Project;

class ProjectMinutesController
{
    /** The minutes PDF of one of a project's meetings, for whoever can see the project. */
    public function show(int $project, string $meeting): Response
    {
        abort_unless(auth()->check(), 403);
        $model = Project::query()->find($project); // project visibility applies
        abort_unless($model, 404);
        try {
            $found = collect(ManageProjectMeetings::meetingsOf($model))->first(fn ($m) => $m['source'] === 'minutes' && $m['id'] === $meeting);
        } catch (\Throwable $e) {
            report($e);
            abort(502, 'The minutes are not reachable right now.');
        }
        abort_unless($found && $found['pdf'], 404);

        try {
            $pdf = MinutesApi::send('minutes-pdf', ['meeting_id' => $meeting], 30);
        } catch (\Throwable $e) {
            report($e);
            abort(502, 'The minutes are not reachable right now.');
        }
        $name = preg_replace('/[^\w\s.-]+/u', '', 'Minutes '.$found['title'].' '.($found['date'] ?? '')) ?: 'Minutes';

        return response($pdf->body(), 200, [
            'Content-Type'           => 'application/pdf',
            'Content-Disposition'    => 'attachment; filename="'.trim(mb_substr($name, 0, 120)).'.pdf"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
