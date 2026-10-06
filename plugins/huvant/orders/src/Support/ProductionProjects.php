<?php

namespace Huvant\Orders\Support;

use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Models\ProductionTask;
use Huvant\Orders\Models\ProductProject;
use Huvant\Teams\Support\ProjectTeams;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use Webkul\Product\Models\Product;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\ProjectStage;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/** Product-specific Lab projects and the standard tasks linked to production entries. */
class ProductionProjects
{
    private const STAGES = ['To Do', 'In Progress', 'Done', 'Cancelled'];

    public static function backfill(): void
    {
        if (! Schema::hasTable('huvant_product_projects') || ! Schema::hasTable('projects_projects')) {
            return;
        }

        Product::withoutGlobalScopes()
            ->where('huvant_role', ItemRole::Product->value)
            ->orderBy('id')
            ->each(fn (Product $product): ?Project => static::ensureForProduct($product));
    }

    public static function ensureForProduct(Product $product): ?Project
    {
        $role = $product->huvant_role instanceof ItemRole
            ? $product->huvant_role
            : ItemRole::tryFrom((string) $product->huvant_role);

        if (! Schema::hasTable('huvant_product_projects') || $role !== ItemRole::Product) {
            return null;
        }

        $mapping = ProductProject::query()->where('product_id', $product->getKey())->first();
        if ($mapping) {
            return Project::withoutGlobalScopes()->withTrashed()->find($mapping->project_id);
        }

        return DB::transaction(function () use ($product): Project {
            $mapping = ProductProject::query()->where('product_id', $product->getKey())->lockForUpdate()->first();
            if ($mapping && ($project = Project::withoutGlobalScopes()->withTrashed()->find($mapping->project_id))) {
                return $project;
            }

            $creatorId = $product->creator_id ?? Auth::id() ?? User::query()->value('id');
            $companyId = $product->company_id ?? Company::query()->value('id');
            $projectStageId = ProjectStage::withoutGlobalScopes()
                ->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $companyId))
                ->orderBy('sort')
                ->value('id');

            $project = Project::withoutGlobalScopes()->create([
                'name'                    => __('huvant-orders::manufacturing.production_project_name', ['product' => $product->name]),
                'tasks_label'             => __('huvant-orders::manufacturing.production_tasks'),
                'description'             => __('huvant-orders::manufacturing.production_project_description', ['product' => $product->name]),
                'visibility'              => 'internal',
                'color'                   => Project::nextColor(),
                'stage_id'                => $projectStageId,
                'company_id'              => $companyId,
                'creator_id'              => $creatorId,
                'allow_timesheets'        => true,
                'allow_milestones'        => false,
                'allow_task_dependencies' => false,
                'is_active'               => true,
            ]);

            foreach (self::STAGES as $sort => $name) {
                TaskStage::withoutGlobalScopes()->create([
                    'name'       => $name,
                    'is_active'  => true,
                    'sort'       => $sort + 1,
                    'project_id' => $project->getKey(),
                    'company_id' => $companyId,
                    'creator_id' => $creatorId,
                ]);
            }

            ProductProject::query()->create([
                'product_id' => $product->getKey(),
                'project_id' => $project->getKey(),
            ]);

            $team = static::ensureLabTeam();
            ProjectTeams::syncProjectTeams($project->getKey(), [$team->getKey()]);

            return $project;
        });
    }

    public static function createTask(ProductionTask $production, array $userIds, \DateTimeInterface $deadline): Task
    {
        $project = static::ensureForProduct($production->product)
            ?? throw new RuntimeException(__('huvant-orders::manufacturing.error_production_project'));
        $stage = static::stage($project, 'todo');
        $creatorId = Auth::id() ?? $production->managed_by ?? User::query()->value('id');

        $task = Task::withoutGlobalScopes()->create([
            'title'       => __('huvant-orders::manufacturing.production_task_title', [
                'product'  => $production->product->name,
                'order'    => $production->order->order_number,
                'position' => $production->manufacturingEntry?->position,
            ]),
            'description' => __('huvant-orders::manufacturing.production_task_description', [
                'order' => $production->order->order_number,
            ]),
            'state'       => TaskState::IN_PROGRESS,
            'stage_id'    => $stage?->getKey(),
            'project_id'  => $project->getKey(),
            'partner_id'  => $production->order->partner_id,
            'company_id'  => $production->order->company_id,
            'creator_id'  => $creatorId,
            'deadline'    => $deadline,
            'is_active'   => true,
        ]);

        $task->users()->sync(static::validLabUserIds($userIds));
        $production->update(['project_task_id' => $task->getKey()]);

        return $task;
    }

    /** @return Collection<int, User> */
    public static function labUsers(): Collection
    {
        $team = static::labTeam();

        return $team
            ? $team->users()->where('is_active', true)->orderBy('name')->get(['users.id', 'users.name'])
            : collect();
    }

    /** @return list<int> */
    public static function validLabUserIds(array $userIds): array
    {
        $selectedIds = collect($userIds)->map(fn ($id): int => (int) $id)->unique()->values();
        $administrators = User::query()
            ->with('roles')
            ->whereIn('id', $selectedIds)
            ->get()
            ->filter(fn (User $user): bool => $user->roles->contains(fn ($role): bool => $role->isSystemRole()))
            ->pluck('id');
        $allowed = static::labUsers()->pluck('id')
            ->merge($administrators)
            ->map(fn ($id): int => (int) $id)
            ->unique();
        $selected = $selectedIds->intersect($allowed)->values();

        if ($selected->isEmpty()) {
            throw new RuntimeException(__('huvant-orders::manufacturing.error_assignee_required'));
        }

        return $selected->all();
    }

    public static function syncTaskCompleted(ProductionTask $production): void
    {
        $task = $production->projectTask;
        if (! $task || $task->state === TaskState::DONE) {
            return;
        }

        $done = static::stage($task->project, 'done');
        $task->forceFill([
            'state'    => TaskState::DONE,
            'stage_id' => $done?->getKey() ?? $task->stage_id,
            'progress' => 100,
        ])->saveQuietly();
    }

    public static function syncTaskCancelled(ProductionTask $production): void
    {
        $task = $production->projectTask;
        if (! $task || $task->state === TaskState::CANCELLED) {
            return;
        }

        $cancelled = static::stage($task->project, 'cancelled');
        $task->forceFill([
            'state'    => TaskState::CANCELLED,
            'stage_id' => $cancelled?->getKey() ?? $task->stage_id,
        ])->saveQuietly();
    }

    public static function labTeam(): ?Team
    {
        return Team::query()->orderBy('id')->get()
            ->first(fn (Team $team): bool => preg_match('/\blab\b/u', Str::lower($team->name)) === 1);
    }

    public static function ensureLabTeam(): Team
    {
        return static::labTeam()
            ?? Team::withoutEvents(fn (): Team => Team::query()->create(['name' => 'Lab Team']));
    }

    private static function stage(Project $project, string $kind): ?TaskStage
    {
        return TaskStage::withoutGlobalScopes()->where('project_id', $project->getKey())->orderBy('sort')->get()
            ->first(function (TaskStage $stage) use ($kind): bool {
                $name = Str::lower($stage->name);

                return match ($kind) {
                    'done'      => Str::contains($name, ['done', 'fatt', 'complet']),
                    'cancelled' => Str::contains($name, ['cancel', 'annull']),
                    default     => Str::contains($name, ['to do', 'todo', 'da fare']),
                };
            });
    }
}
