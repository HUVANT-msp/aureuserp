<?php

namespace Huvant\Tasks\Support;

use Carbon\CarbonImmutable;
use Huvant\Teams\Support\ProjectTeams;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use RuntimeException;
use Webkul\PluginManager\Package;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;
use Webkul\Security\Models\User;

/**
 * The task board behind the Kanban, timeline and list views. Every query runs
 * through the Task model, so the project-team visibility rules apply.
 */
class Board
{
    public const DEFAULT_FILTERS = [
        'projects'  => [],
        'assignee'  => 'all',   // all | me | none | <user id>
        'due'       => 'all',   // all | overdue | week | none
        'search'    => '',
        'cancelled' => false,
        'subtasks'  => false,
    ];

    /** Stage names are shared by every project (To Do, In Progress, ...). */
    private const STAGE_LABELS = [
        'to do'       => 'To do',
        'todo'        => 'To do',
        'in progress' => 'In progress',
        'done'        => 'Done',
        'cancelled'   => 'Cancelled',
        'canceled'    => 'Cancelled',
    ];

    public static function stageLabel(string $name): string
    {
        return self::STAGE_LABELS[Str::lower(trim($name))] ?? $name;
    }

    public static function stageKind(string $name): string
    {
        $name = Str::lower($name);

        return match (true) {
            Str::contains($name, ['done', 'fatt', 'complet', 'chius'])  => 'done',
            Str::contains($name, ['cancel', 'annull'])                  => 'cancelled',
            Str::contains($name, ['progress', 'corso', 'doing'])        => 'doing',
            default                                                     => 'todo',
        };
    }

    public static function normalize(array $filters): array
    {
        $filters = array_merge(self::DEFAULT_FILTERS, array_intersect_key($filters, self::DEFAULT_FILTERS));
        $filters['projects'] = array_values(array_filter(array_map('intval', (array) $filters['projects'])));
        $filters['search'] = trim((string) $filters['search']);
        $filters['cancelled'] = (bool) $filters['cancelled'];
        $filters['subtasks'] = (bool) $filters['subtasks'];

        return $filters;
    }

    public static function query(User $user, array $filters): Builder
    {
        $filters = static::normalize($filters);
        $today = CarbonImmutable::today();

        return Task::query()
            ->with(['project:id,name,color', 'stage:id,name,sort', 'users:id,name', 'subTasks:id,parent_id,state,title'])
            ->when(! $filters['subtasks'], fn (Builder $q) => $q->whereNull('parent_id'))
            ->when($filters['projects'], fn (Builder $q, array $ids) => $q->whereIn('project_id', $ids))
            ->when(! $filters['cancelled'], fn (Builder $q) => $q->where('state', '!=', TaskState::CANCELLED->value))
            ->when($filters['search'] !== '', fn (Builder $q) => $q->where('title', 'like', '%'.$filters['search'].'%'))
            ->when($filters['assignee'] === 'me', fn (Builder $q) => $q->whereHas('users', fn ($u) => $u->whereKey($user->getKey())))
            ->when($filters['assignee'] === 'none', fn (Builder $q) => $q->whereDoesntHave('users'))
            ->when(ctype_digit((string) $filters['assignee']), fn (Builder $q) => $q->whereHas('users', fn ($u) => $u->whereKey((int) $filters['assignee'])))
            ->when($filters['due'] === 'overdue', fn (Builder $q) => $q->whereDate('deadline', '<', $today)->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value]))
            ->when($filters['due'] === 'week', fn (Builder $q) => $q->whereBetween('deadline', [$today->startOfDay(), $today->addDays(7)->endOfDay()]))
            ->when($filters['due'] === 'none', fn (Builder $q) => $q->whereNull('deadline'));
    }

    /** @return list<array{key: string, label: string, kind: string, tasks: Collection}> */
    public static function columns(User $user, array $filters, int $perColumn = 200): array
    {
        $filters = static::normalize($filters);
        $tasks = static::query($user, $filters)->orderByDesc('priority')->orderBy('sort')->orderByDesc('id')->limit(1500)->get();

        $stageNames = TaskStage::query()
            ->when($filters['projects'], fn (Builder $q, array $ids) => $q->whereIn('project_id', $ids))
            ->orderBy('sort')->orderBy('id')->pluck('name')
            ->merge($tasks->pluck('stage.name')->filter())
            ->unique(fn (string $name): string => Str::lower(trim($name)))
            ->reject(fn (string $name): bool => ! $filters['cancelled'] && static::stageKind($name) === 'cancelled')
            ->values();

        $grouped = $tasks->groupBy(fn (Task $task): string => Str::lower(trim((string) $task->stage?->name)));
        $columns = $stageNames->map(fn (string $name): array => [
            'key'   => Str::lower(trim($name)),
            'name'  => $name,
            'label' => static::stageLabel($name),
            'kind'  => static::stageKind($name),
            'tasks' => ($grouped->get(Str::lower(trim($name))) ?? collect())->take($perColumn)->values(),
            'count' => ($grouped->get(Str::lower(trim($name))) ?? collect())->count(),
        ])->all();

        $unstaged = $grouped->get('') ?? collect();
        if ($unstaged->isNotEmpty()) {
            array_unshift($columns, ['key' => '', 'name' => '', 'label' => 'No stage', 'kind' => 'todo', 'tasks' => $unstaged->values(), 'count' => $unstaged->count()]);
        }

        return $columns;
    }

    /** Move a task to the stage with this name in its own project; the state follows the stage. */
    public static function move(User $user, Task $task, string $stageName): void
    {
        if (! Gate::forUser($user)->allows('update', $task)) {
            throw new RuntimeException('You cannot change this task.');
        }
        $stage = TaskStage::query()->where('project_id', $task->project_id)->get()
            ->first(fn (TaskStage $stage): bool => Str::lower(trim($stage->name)) === Str::lower(trim($stageName)));
        if (! $stage) {
            throw new RuntimeException('This project has no "'.static::stageLabel($stageName).'" stage.');
        }

        $state = match (static::stageKind($stage->name)) {
            'done'      => TaskState::DONE,
            'cancelled' => TaskState::CANCELLED,
            default     => in_array($task->state, [TaskState::DONE, TaskState::CANCELLED], true) ? TaskState::IN_PROGRESS : $task->state,
        };
        $task->forceFill(['stage_id' => $stage->getKey(), 'state' => $state])->save();
    }

    public static function setAssignees(User $user, Task $task, array $userIds): void
    {
        if (! Gate::forUser($user)->allows('update', $task)) {
            throw new RuntimeException('You cannot change this task.');
        }
        $task->users()->sync(array_values(array_unique(array_map('intval', $userIds))));
        $task->touch(); // pivot changes do not fire model events: let the Minutes sync know.
    }

    /** People who can work on the task: members of the project's teams (everyone active otherwise). */
    public static function assignableUsers(?Task $task): Collection
    {
        $query = User::query()->where('is_active', true)->orderBy('name');
        if ($task?->project_id && class_exists(ProjectTeams::class) && Package::isPluginInstalled('huvant-teams')) {
            $teamIds = DB::table(ProjectTeams::PIVOT)->where('project_id', $task->project_id)->pluck('team_id');
            if ($teamIds->isNotEmpty()) {
                $query->whereIn('id', DB::table('user_team')->whereIn('team_id', $teamIds)->pluck('user_id'));
            }
        }

        return $query->get(['id', 'name']);
    }

    public static function visibleProjects(): Collection
    {
        return Project::query()->orderBy('name')->get(['id', 'name', 'color']);
    }

    public static function isOverdue(Task $task): bool
    {
        return $task->deadline && $task->deadline->lt(CarbonImmutable::today())
            && ! in_array($task->state, [TaskState::DONE, TaskState::CANCELLED], true);
    }

    public static function startDate(Task $task): CarbonImmutable
    {
        $start = $task->getAttribute('huvant_start_date');

        return CarbonImmutable::parse($start ?: $task->created_at)->startOfDay();
    }

    public static function dueLabel(Task $task): ?string
    {
        if (! $task->deadline) {
            return null;
        }
        $days = (int) CarbonImmutable::today()->diffInDays($task->deadline->copy()->startOfDay(), false);

        return match (true) {
            $days === 0  => 'Today',
            $days === 1  => 'Tomorrow',
            $days === -1 => 'Yesterday',
            $days < 0    => abs($days).'d ago',
            $days < 7    => 'In '.$days.'d',
            default      => $task->deadline->format('M j'),
        };
    }
}
