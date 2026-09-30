<?php

namespace Huvant\Bridge\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Webkul\Project\Models\Project;

/** The Meeting Canvas colour of a project, kept the same in the ERP. */
class ProjectColorController
{
    public function update(Request $request, Project $project): JsonResponse
    {
        Gate::authorize('update', $project);
        $data = $request->validate(['color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/']]);

        $project->forceFill(['color' => strtoupper($data['color'])])->saveQuietly();

        return response()->json(['data' => ['id' => $project->getKey(), 'color' => $project->color, 'updated_at' => $project->updated_at?->toIso8601String()]]);
    }
}
