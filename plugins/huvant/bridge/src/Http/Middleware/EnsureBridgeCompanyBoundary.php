<?php

namespace Huvant\Bridge\Http\Middleware;

use Closure;
use Huvant\Bridge\Support\BridgeAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Milestone;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\ProjectStage;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Scopes\CompanyScope;

class EnsureBridgeCompanyBoundary
{
    public function handle(Request $request, Closure $next): Response
    {
        $actor = $request->user();
        $resource = $this->resource($request);

        if (! $actor instanceof User || $resource === null) {
            return $next($request);
        }

        $isStore = str_ends_with((string) $request->route()?->getName(), '.store');
        $target = $isStore ? null : $this->target($request, $resource);

        if (! $isStore && $target === null) {
            return $next($request);
        }

        $allowedCompanyIds = BridgeAuthorization::companyIds($actor);
        $isSuperAdmin = BridgeAuthorization::isSuperAdmin($actor);

        if ($target !== null && ! $isSuperAdmin && ! $this->companyIsAllowed($this->modelCompanyId($target), $allowedCompanyIds)) {
            return $this->forbidden();
        }

        $isUpdate = str_ends_with((string) $request->route()?->getName(), '.update');
        if (! $isStore && ! $isUpdate) {
            return $next($request);
        }

        $error = match ($resource) {
            'project'    => $this->guardProject($request, $actor, $target, $allowedCompanyIds, $isSuperAdmin),
            'partner'    => $this->guardPartner($request, $actor, $target, $allowedCompanyIds, $isSuperAdmin),
            'task-stage' => $this->guardTaskStage($request, $target, $allowedCompanyIds, $isSuperAdmin),
            'task'       => $this->guardTask($request, $target, $allowedCompanyIds, $isSuperAdmin),
        };

        return $error ?? $next($request);
    }

    private function guardProject(
        Request $request,
        User $actor,
        ?Project $project,
        Collection $allowedCompanyIds,
        bool $isSuperAdmin,
    ): ?Response {
        $companyId = $this->effectiveCompanyId($request, $actor, $project);

        if ($companyId === null) {
            return $this->invalid('company_id', 'A company is required for this project.');
        }

        if (! $isSuperAdmin && ! $this->companyIsAllowed($companyId, $allowedCompanyIds)) {
            return $this->forbidden();
        }

        if ($project !== null
            && $request->exists('company_id')
            && $this->modelCompanyId($project) !== $companyId) {
            return $this->invalid('company_id', 'An existing project cannot be moved to another company.');
        }

        if ($project === null && ! $request->exists('company_id')) {
            $request->merge(['company_id' => $companyId]);
        }

        $stageId = $this->effectiveValue($request, $project, 'stage_id');
        if ($stageId !== null) {
            $stage = ProjectStage::withoutGlobalScope(CompanyScope::class)->find($stageId);

            // Stages without a company are shared by every company (the installer
            // seeds them that way); only another company's stage is foreign.
            if (! $stage || ($stage->company_id !== null && (int) $stage->company_id !== $companyId)) {
                return $this->invalid('stage_id', 'The selected project stage does not belong to the project company.');
            }
        }

        $partnerId = $this->effectiveValue($request, $project, 'partner_id');
        if ($partnerId !== null && ! $this->partnerBelongsToCompany($partnerId, $companyId)) {
            return $this->invalid('partner_id', 'The selected partner does not belong to the project company.');
        }

        $userId = $this->effectiveValue($request, $project, 'user_id');
        if ($userId !== null && ! $this->userBelongsToCompany($userId, $companyId)) {
            return $this->invalid('user_id', 'The selected user is not available in the project company.');
        }

        return null;
    }

    private function guardPartner(
        Request $request,
        User $actor,
        ?Partner $partner,
        Collection $allowedCompanyIds,
        bool $isSuperAdmin,
    ): ?Response {
        $companyId = $this->effectiveCompanyId($request, $actor, $partner);

        if ($companyId === null) {
            return $this->invalid('company_id', 'A company is required for this partner.');
        }

        if (! $isSuperAdmin && ! $this->companyIsAllowed($companyId, $allowedCompanyIds)) {
            return $this->forbidden();
        }

        if ($partner === null && ! $request->exists('company_id')) {
            $request->merge(['company_id' => $companyId]);
        }

        $parentId = $this->effectiveValue($request, $partner, 'parent_id');
        if ($parentId !== null && ! $this->partnerBelongsToCompany($parentId, $companyId)) {
            return $this->invalid('parent_id', 'The selected parent partner does not belong to the partner company.');
        }

        $userId = $this->effectiveValue($request, $partner, 'user_id');
        if ($userId !== null && ! $this->userBelongsToCompany($userId, $companyId)) {
            return $this->invalid('user_id', 'The selected user is not available in the partner company.');
        }

        return null;
    }

    private function guardTaskStage(
        Request $request,
        ?TaskStage $taskStage,
        Collection $allowedCompanyIds,
        bool $isSuperAdmin,
    ): ?Response {
        $projectId = $this->effectiveValue($request, $taskStage, 'project_id');
        $project = $this->project($projectId);

        if (! $project) {
            return $this->invalid('project_id', 'A valid project is required for this task stage.');
        }

        $companyId = $this->integerOrNull($project->company_id);
        if ($companyId === null) {
            return $this->invalid('project_id', 'The selected project has no company.');
        }

        if (! $isSuperAdmin && ! $this->companyIsAllowed($companyId, $allowedCompanyIds)) {
            return $this->forbidden();
        }

        if ($taskStage !== null && $this->modelCompanyId($taskStage) !== $companyId) {
            return $this->invalid('project_id', 'A task stage cannot be moved to a project in another company.');
        }

        return null;
    }

    private function guardTask(
        Request $request,
        ?Task $task,
        Collection $allowedCompanyIds,
        bool $isSuperAdmin,
    ): ?Response {
        $projectId = $this->effectiveValue($request, $task, 'project_id');
        $project = $this->project($projectId);

        if (! $project) {
            return $this->invalid('project_id', 'A valid project is required for this task.');
        }

        $companyId = $this->integerOrNull($project->company_id);
        if ($companyId === null) {
            return $this->invalid('project_id', 'The selected project has no company.');
        }

        if (! $isSuperAdmin && ! $this->companyIsAllowed($companyId, $allowedCompanyIds)) {
            return $this->forbidden();
        }

        if ($task !== null && $this->modelCompanyId($task) !== $companyId) {
            return $this->invalid('project_id', 'A task cannot be moved to a project in another company.');
        }

        $stageId = $this->effectiveValue($request, $task, 'stage_id');
        $stage = TaskStage::withoutGlobalScope(CompanyScope::class)->find($stageId);
        if (! $stage || (int) $stage->project_id !== (int) $project->getKey() || $this->modelCompanyId($stage) !== $companyId) {
            return $this->invalid('stage_id', 'The selected task stage does not belong to the selected project.');
        }

        $milestoneId = $this->effectiveValue($request, $task, 'milestone_id');
        if ($milestoneId !== null && ! Milestone::withoutGlobalScope(CompanyScope::class)
            ->whereKey($milestoneId)
            ->where('project_id', $project->getKey())
            ->exists()) {
            return $this->invalid('milestone_id', 'The selected milestone does not belong to the selected project.');
        }

        $partnerId = $request->exists('partner_id')
            ? $request->input('partner_id')
            : ($request->exists('project_id') ? $project->partner_id : $task?->partner_id);
        if ($partnerId !== null && ! $this->partnerBelongsToCompany($partnerId, $companyId)) {
            return $this->invalid('partner_id', 'The selected partner does not belong to the task company.');
        }

        $parentId = $this->effectiveValue($request, $task, 'parent_id');
        if ($parentId !== null) {
            $parent = Task::withoutGlobalScope(CompanyScope::class)->find($parentId);

            if (! $parent
                || ($task !== null && (int) $parent->getKey() === (int) $task->getKey())
                || (int) $parent->project_id !== (int) $project->getKey()
                || $this->modelCompanyId($parent) !== $companyId) {
                return $this->invalid('parent_id', 'The selected parent task does not belong to the selected project.');
            }
        }

        if ($request->exists('users') && is_array($request->input('users'))) {
            foreach ($request->input('users') as $userId) {
                if (! $this->userBelongsToCompany($userId, $companyId)) {
                    return $this->invalid('users', 'Every assigned user must be available in the task company.');
                }
            }
        }

        return null;
    }

    private function effectiveCompanyId(Request $request, User $actor, ?Model $target): ?int
    {
        if ($request->exists('company_id')) {
            return $this->integerOrNull($request->input('company_id'));
        }

        return $target === null
            ? $this->integerOrNull($actor->default_company_id)
            : $this->modelCompanyId($target);
    }

    private function effectiveValue(Request $request, ?Model $target, string $field): mixed
    {
        return $request->exists($field) ? $request->input($field) : $target?->getAttribute($field);
    }

    private function target(Request $request, string $resource): ?Model
    {
        $parameter = match ($resource) {
            'project'    => 'project',
            'task'       => 'task',
            'task-stage' => 'task_stage',
            'partner'    => 'partner',
        };
        $id = $request->route($parameter) ?? $request->route('id');
        $id = $id instanceof Model ? $id->getKey() : $id;

        if (! is_numeric($id)) {
            return null;
        }

        $model = match ($resource) {
            'project'    => Project::class,
            'task'       => Task::class,
            'task-stage' => TaskStage::class,
            'partner'    => Partner::class,
        };

        return $model::withoutGlobalScope(CompanyScope::class)->withTrashed()->find((int) $id);
    }

    private function resource(Request $request): ?string
    {
        $name = (string) $request->route()?->getName();

        return match (true) {
            str_starts_with($name, 'admin.api.v1.projects.tasks.')        => 'task',
            str_starts_with($name, 'admin.api.v1.projects.projects.')     => 'project',
            str_starts_with($name, 'admin.api.v1.projects.task-stages.')  => 'task-stage',
            str_starts_with($name, 'admin.api.v1.partners.partners.')     => 'partner',
            default                                                       => null,
        };
    }

    private function project(mixed $id): ?Project
    {
        if (! is_numeric($id)) {
            return null;
        }

        return Project::withoutGlobalScope(CompanyScope::class)->find((int) $id);
    }

    private function modelCompanyId(Model $model): ?int
    {
        $companyId = $this->integerOrNull($model->getAttribute('company_id'));

        if ($companyId !== null || (! ($model instanceof Task) && ! ($model instanceof TaskStage))) {
            return $companyId;
        }

        return $this->integerOrNull($this->project($model->getAttribute('project_id'))?->company_id);
    }

    private function partnerBelongsToCompany(mixed $partnerId, int $companyId): bool
    {
        if (! is_numeric($partnerId)) {
            return false;
        }

        return Partner::withoutGlobalScope(CompanyScope::class)
            ->whereKey((int) $partnerId)
            ->where(fn ($query) => $query->where('company_id', $companyId)->orWhereNull('company_id'))
            ->exists();
    }

    private function userBelongsToCompany(mixed $userId, int $companyId): bool
    {
        if (! is_numeric($userId)) {
            return false;
        }

        return User::query()
            ->whereKey((int) $userId)
            ->where(function ($query) use ($companyId): void {
                $query->where('default_company_id', $companyId)
                    ->orWhereHas('allowedCompanies', fn ($query) => $query->where('companies.id', $companyId));
            })
            ->exists();
    }

    private function companyIsAllowed(?int $companyId, Collection $allowedCompanyIds): bool
    {
        return $companyId !== null && $allowedCompanyIds->contains($companyId);
    }

    private function integerOrNull(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function forbidden(): JsonResponse
    {
        return response()->json(['message' => 'You are not allowed to mutate resources in this company.'], 403);
    }

    private function invalid(string $field, string $message): JsonResponse
    {
        return response()->json([
            'message' => 'The given data was invalid.',
            'errors'  => [$field => [$message]],
        ], 422);
    }
}
