<?php

namespace Huvant\Bridge\Http\Controllers\API\V1;

use Huvant\Bridge\Http\Requests\StoreNoteRequest;
use Huvant\Bridge\Http\Resources\V1\NoteResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;

class NoteController
{
    public function project(StoreNoteRequest $request, Project $project)
    {
        return $this->store($request, $project);
    }

    public function task(StoreNoteRequest $request, Task $task)
    {
        return $this->store($request, $task);
    }

    private function store(StoreNoteRequest $request, Model $record)
    {
        Gate::authorize('update', $record);

        $data = $request->validated();
        $message = $record->addMessage([
            'type'        => 'comment',
            'subject'     => $data['subject'] ?? null,
            'body'        => $data['body'],
            'is_internal' => $data['is_internal'] ?? true,
            'company_id'  => $record->getAttribute('company_id'),
        ]);

        return (new NoteResource($message))
            ->additional(['message' => 'Note created successfully.'])
            ->response()
            ->setStatusCode(201);
    }
}
