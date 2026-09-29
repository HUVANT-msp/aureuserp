<?php

namespace Huvant\Bridge\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Project\Models\Task;
use Webkul\Support\Models\Scopes\CompanyScope;

class TouchTaskAfterPivotUpdate
{
    public function handle(Request $request, Closure $next): Response
    {
        $taskId = $request->route('task');
        $task = is_numeric($taskId)
            ? Task::withoutGlobalScope(CompanyScope::class)->find((int) $taskId)
            : null;
        $originalUpdatedAt = $task?->updated_at?->copy();

        $response = $next($request);

        if ($response->getStatusCode() >= 400
            || ! $task
            || ! ($request->exists('users') || $request->exists('tags'))) {
            return $response;
        }

        $task->refresh();

        if ($originalUpdatedAt !== null && $task->updated_at?->greaterThan($originalUpdatedAt)) {
            return $response;
        }

        $nextUpdatedAt = $task->freshTimestamp()->startOfSecond();

        if ($originalUpdatedAt !== null) {
            $minimumUpdatedAt = $originalUpdatedAt->copy()->startOfSecond()->addSecond();

            if ($nextUpdatedAt->lessThan($minimumUpdatedAt)) {
                $nextUpdatedAt = $minimumUpdatedAt;
            }
        }

        $task->forceFill([$task->getUpdatedAtColumn() => $nextUpdatedAt])->save();

        return $response;
    }
}
