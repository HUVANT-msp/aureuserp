<?php

namespace Huvant\Insights\Support;

use Carbon\CarbonImmutable;
use Huvant\Tasks\Support\Palette;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\User;

/**
 * Where time goes and what gets done: per person (the employee's Lavoro tab)
 * and per project (the projects overview). A task counts as completed on the
 * day it was last updated while done.
 */
class Insights
{
    public static function isManagerOf(User $viewer, User $person): bool
    {
        if (! Schema::hasTable('employees_employees')) {
            return false;
        }
        $mine = DB::table('employees_employees')->where('user_id', $viewer->getKey())->whereNull('deleted_at')->value('id');
        if (! $mine) {
            return false;
        }

        return DB::table('employees_employees')->where('user_id', $person->getKey())->whereNull('deleted_at')
            ->where(fn ($q) => $q->where('parent_id', $mine)->orWhere('coach_id', $mine))->exists();
    }

    public static function isManager(User $user): bool
    {
        if (Worklog::isAdmin($user)) {
            return true;
        }
        if (! Schema::hasTable('employees_employees')) {
            return false;
        }
        $mine = DB::table('employees_employees')->where('user_id', $user->getKey())->whereNull('deleted_at')->value('id');

        return $mine && DB::table('employees_employees')->whereNull('deleted_at')
            ->where(fn ($q) => $q->where('parent_id', $mine)->orWhere('coach_id', $mine))->exists();
    }

    public static function canSeePerson(User $viewer, User $person): bool
    {
        return $viewer->is($person) || Worklog::isAdmin($viewer) || static::isManagerOf($viewer, $person);
    }

    /** One person's work: hours against the calendar, projects, tasks, what they declared. */
    public static function person(User $person, int $days = 14): array
    {
        $today = CarbonImmutable::today();
        $from = $today->subDays($days - 1);
        $monday = $today->startOfWeek();

        $daily = static::hoursByDay($from, $today, $person->getKey());
        $series = [];
        for ($d = $from; $d->lte($today); $d = $d->addDay()) {
            $key = $d->toDateString();
            $series[] = ['date' => $key, 'hours' => $daily[$key] ?? 0.0, 'expected' => Worklog::expectedHours($person, $key)];
        }
        $week = collect($series)->filter(fn ($d) => $d['date'] >= $monday->toDateString());

        $projects = DB::table('analytic_records')
            ->leftJoin('projects_projects', 'projects_projects.id', '=', 'analytic_records.project_id')
            ->where('analytic_records.user_id', $person->getKey())
            ->whereBetween('analytic_records.date', [$from->toDateString(), $today->toDateString()])
            ->selectRaw("analytic_records.project_id, COALESCE(projects_projects.name, 'No project') as name, projects_projects.color, SUM(analytic_records.unit_amount) as hours")
            ->groupBy('analytic_records.project_id', 'projects_projects.name', 'projects_projects.color')
            ->orderByDesc('hours')->get()
            ->map(fn ($r) => ['id' => $r->project_id ? (int) $r->project_id : null, 'name' => $r->name, 'color' => static::color($r->project_id, $r->color), 'hours' => round((float) $r->hours, 2)]);

        $assigned = Task::query()->withoutGlobalScopes()->whereNull('deleted_at')
            ->whereExists(fn ($q) => $q->selectRaw('1')->from('projects_task_users')->whereColumn('projects_task_users.task_id', 'projects_tasks.id')->where('projects_task_users.user_id', $person->getKey()));
        $open = (clone $assigned)->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value]);

        return [
            'days'       => $days,
            'series'     => $series,
            'total'      => round(collect($series)->sum('hours'), 2),
            'expected'   => round(collect($series)->sum('expected'), 2),
            'weekHours'  => round($week->sum('hours'), 2),
            'weekTarget' => round($week->sum('expected'), 2),
            'today'      => $daily[$today->toDateString()] ?? 0.0,
            'running'    => Worklog::running($person),
            'projects'   => $projects,
            'openTasks'  => (clone $open)->with('project:id,name,color')->orderByRaw('deadline IS NULL')->orderBy('deadline')->limit(12)->get(),
            'openCount'  => (clone $open)->count(),
            'overdue'    => (clone $open)->whereDate('deadline', '<', $today)->count(),
            'completed'  => (clone $assigned)->where('state', TaskState::DONE->value)->whereBetween('updated_at', [$from->startOfDay(), $today->endOfDay()])->count(),
            'entries'    => DB::table('analytic_records')
                ->leftJoin('projects_tasks', 'projects_tasks.id', '=', 'analytic_records.task_id')
                ->leftJoin('projects_projects', 'projects_projects.id', '=', 'analytic_records.project_id')
                ->where('analytic_records.user_id', $person->getKey())
                ->orderByDesc('analytic_records.date')->orderByDesc('analytic_records.id')->limit(15)
                ->get(['analytic_records.date', 'analytic_records.name', 'analytic_records.unit_amount', 'projects_tasks.title as task', 'projects_projects.name as project', 'analytic_records.project_id', 'projects_projects.color']),
        ];
    }

    /**
     * Projects ranked by hours in the period, with the trend against the period
     * before, the daily curve, who put the time in and who closed tasks.
     */
    public static function projects(int $days = 7): array
    {
        $today = CarbonImmutable::today();
        $from = $today->subDays($days - 1);
        $prevFrom = $from->subDays($days);
        $prevTo = $from->subDay();
        $visible = Project::query()->pluck('name', 'id');

        $hours = DB::table('analytic_records')->whereNotNull('project_id')
            ->whereBetween('date', [$from->toDateString(), $today->toDateString()])
            ->selectRaw('project_id, DATE(date) as day, user_id, SUM(unit_amount) as hours')
            ->groupBy('project_id', DB::raw('DATE(date)'), 'user_id')->get()
            ->filter(fn ($r) => $visible->has($r->project_id));
        $previous = DB::table('analytic_records')->whereNotNull('project_id')
            ->whereBetween('date', [$prevFrom->toDateString(), $prevTo->toDateString()])
            ->selectRaw('project_id, SUM(unit_amount) as hours')->groupBy('project_id')->pluck('hours', 'project_id');

        $completed = DB::table('projects_tasks')
            ->join('projects_task_users', 'projects_task_users.task_id', '=', 'projects_tasks.id')
            ->whereNull('projects_tasks.deleted_at')->where('projects_tasks.state', TaskState::DONE->value)
            ->whereBetween('projects_tasks.updated_at', [$from->startOfDay(), $today->endOfDay()])
            ->selectRaw('projects_tasks.project_id, projects_task_users.user_id, COUNT(DISTINCT projects_tasks.id) as done')
            ->groupBy('projects_tasks.project_id', 'projects_task_users.user_id')->get()
            ->filter(fn ($r) => $visible->has($r->project_id));

        $taskStats = DB::table('projects_tasks')->whereNull('deleted_at')->whereNull('parent_id')
            ->selectRaw("project_id, SUM(state = 'done') as done, SUM(state NOT IN ('done','cancelled')) as open, SUM(state NOT IN ('done','cancelled') AND deadline < ?) as overdue", [$today->toDateString()])
            ->groupBy('project_id')->get()->keyBy('project_id');

        $names = User::query()->whereIn('id', $hours->pluck('user_id')->merge($completed->pluck('user_id'))->filter()->unique())->pluck('name', 'id');
        $colors = Project::query()->pluck('color', 'id');

        $ids = $hours->pluck('project_id')->merge($completed->pluck('project_id'))->unique()->merge($visible->keys())->unique();
        $rows = $ids->map(function ($projectId) use ($hours, $previous, $completed, $taskStats, $names, $visible, $colors, $from, $today) {
            $mine = $hours->where('project_id', $projectId);
            $total = round((float) $mine->sum('hours'), 2);
            $before = round((float) ($previous[$projectId] ?? 0), 2);
            $curve = [];
            for ($d = $from; $d->lte($today); $d = $d->addDay()) {
                $curve[] = round((float) $mine->where('day', $d->toDateString())->sum('hours'), 2);
            }
            $done = $completed->where('project_id', $projectId);
            $people = $mine->groupBy('user_id')->map(fn ($r, $uid) => [
                'id'    => (int) $uid,
                'name'  => $names[$uid] ?? '—',
                'hours' => round((float) $r->sum('hours'), 2),
                'done'  => (int) $done->where('user_id', $uid)->sum('done'),
            ]);
            foreach ($done as $row) {
                if (! $people->has($row->user_id)) {
                    $people->put($row->user_id, ['id' => (int) $row->user_id, 'name' => $names[$row->user_id] ?? '—', 'hours' => 0.0, 'done' => (int) $row->done]);
                }
            }
            $stats = $taskStats->get($projectId);
            $closed = (int) ($stats->done ?? 0);
            $openCount = (int) ($stats->open ?? 0);

            return [
                'id'       => (int) $projectId,
                'name'     => $visible[$projectId],
                'color'    => static::color($projectId, $colors[$projectId] ?? null),
                'hours'    => $total,
                'previous' => $before,
                'trend'    => $before > 0 ? round(($total - $before) / $before * 100) : ($total > 0 ? null : 0),
                'curve'    => $curve,
                'people'   => $people->sortByDesc(fn ($p) => $p['hours'] * 10 + $p['done'])->values()->all(),
                'done'     => (int) $done->pluck('done')->sum(),
                'open'     => $openCount,
                'overdue'  => (int) ($stats->overdue ?? 0),
                'progress' => $closed + $openCount > 0 ? round($closed / ($closed + $openCount) * 100) : 0,
            ];
        })->filter(fn ($r) => $r['name'] !== null)->sortByDesc(fn ($r) => [$r['hours'], $r['done']])->values();

        $leaders = $hours->groupBy('user_id')->map(fn ($r, $uid) => ['id' => (int) $uid, 'name' => $names[$uid] ?? '—', 'hours' => round((float) $r->sum('hours'), 2), 'done' => (int) $completed->where('user_id', $uid)->sum('done')]);
        foreach ($completed->groupBy('user_id') as $uid => $r) {
            if (! $leaders->has($uid)) {
                $leaders->put($uid, ['id' => (int) $uid, 'name' => $names[$uid] ?? '—', 'hours' => 0.0, 'done' => (int) $r->sum('done')]);
            }
        }

        return [
            'days'     => $days,
            'from'     => $from,
            'to'       => $today,
            'projects' => $rows->all(),
            'active'   => $rows->where('hours', '>', 0)->count(),
            'hours'    => round($rows->sum('hours'), 2),
            'done'     => $rows->sum('done'),
            'leaders'  => $leaders->sortByDesc(fn ($p) => $p['hours'] * 10 + $p['done'])->values()->take(8)->all(),
        ];
    }

    /** SVG polyline points for a small curve. */
    public static function sparkline(array $values, int $width = 120, int $height = 28): string
    {
        $n = count($values);
        if ($n === 0) {
            return '';
        }
        $max = max(max($values), 0.01);
        $step = $n > 1 ? $width / ($n - 1) : 0;

        return collect($values)->map(fn ($v, $i) => round($i * $step, 1).','.round($height - 2 - ($v / $max) * ($height - 4), 1))->implode(' ');
    }

    private static function hoursByDay(CarbonImmutable $from, CarbonImmutable $to, int $userId): array
    {
        return DB::table('analytic_records')->where('user_id', $userId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('DATE(date) as day, SUM(unit_amount) as hours')->groupBy(DB::raw('DATE(date)'))
            ->pluck('hours', 'day')->map(fn ($h) => round((float) $h, 2))->all();
    }

    private static function color($projectId, ?string $color): string
    {
        return class_exists(Palette::class)
            ? Palette::project($projectId ? (int) $projectId : null, $color)
            : '#0075de';
    }
}
