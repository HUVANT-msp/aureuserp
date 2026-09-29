<?php

namespace Huvant\Worklog\Support;

use Carbon\CarbonImmutable;
use Huvant\Tasks\Support\Palette;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Timesheet\Models\Timesheet;

/**
 * Time is logged only on tasks the person is assigned to (or co-assigned):
 * to work on a task, join it first, then record the time spent.
 */
class Worklog
{
    public const TIMERS = 'huvant_work_timers';

    public const TIMER_ENTRY = 'Timer';

    /** Start and end time of an entry, when known (timer, or a start time given by hand). */
    public const SPANS = 'huvant_time_spans';

    private const OPEN_STATES = ['in_progress', 'change_requested', 'approved'];

    public static function isAdmin(User $user): bool
    {
        return $user->roles()->get()->contains(fn (Role $role): bool => $role->isSystemRole());
    }

    public static function isAssignee(User $user, int $taskId): bool
    {
        return DB::table('projects_task_users')->where('task_id', $taskId)->where('user_id', $user->getKey())->exists();
    }

    /** Open tasks the person is assigned to, within the projects they can see. */
    public static function assignedOpenTasks(User $user): Collection
    {
        return Task::query()
            ->whereIn('state', self::OPEN_STATES)
            ->whereExists(fn ($q) => $q->selectRaw('1')->from('projects_task_users')
                ->whereColumn('projects_task_users.task_id', 'projects_tasks.id')
                ->where('projects_task_users.user_id', $user->getKey()))
            ->with('project:id,name')
            ->orderBy('title')
            ->get(['projects_tasks.id', 'projects_tasks.title', 'projects_tasks.project_id']);
    }

    /** Visible open tasks the person could join as co-assignee. */
    public static function joinableTasks(User $user, string $search = '', int $limit = 20): Collection
    {
        return Task::query()
            ->whereIn('state', self::OPEN_STATES)
            ->whereNotExists(fn ($q) => $q->selectRaw('1')->from('projects_task_users')
                ->whereColumn('projects_task_users.task_id', 'projects_tasks.id')
                ->where('projects_task_users.user_id', $user->getKey()))
            ->when($search !== '', fn ($q) => $q->where('projects_tasks.title', 'like', '%'.$search.'%'))
            ->with('project:id,name')
            ->orderBy('title')
            ->limit($limit)
            ->get(['projects_tasks.id', 'projects_tasks.title', 'projects_tasks.project_id']);
    }

    public static function join(User $user, int $taskId): void
    {
        // The team scope applies: only tasks of the person's projects can be joined.
        $task = Task::query()->findOrFail($taskId);
        $task->users()->syncWithoutDetaching([$user->getKey()]);
    }

    public static function running(User $user): ?object
    {
        return DB::table(self::TIMERS)
            ->join('projects_tasks', 'projects_tasks.id', '=', self::TIMERS.'.task_id')
            ->where(self::TIMERS.'.user_id', $user->getKey())
            ->first([self::TIMERS.'.*', 'projects_tasks.title as task_title']);
    }

    public static function start(User $user, int $taskId, ?string $note = null): void
    {
        static::assertAssignee($user, $taskId);
        DB::transaction(function () use ($user, $taskId, $note): void {
            if ($running = static::running($user)) {
                throw new RuntimeException('A timer is already running on "'.$running->task_title.'": stop it and describe what you did.');
            }
            DB::table(self::TIMERS)->insert([
                'user_id'    => $user->getKey(),
                'task_id'    => $taskId,
                'started_at' => now(),
                'note'       => $note ? mb_substr(trim($note), 0, 255) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    /**
     * Stop the running timer and log its time with what was done (required).
     * Under a minute nothing is logged; without a description the timer keeps running.
     */
    public static function stop(User $user, ?string $description = null): ?Timesheet
    {
        return DB::transaction(function () use ($user, $description): ?Timesheet {
            $timer = DB::table(self::TIMERS)->where('user_id', $user->getKey())->lockForUpdate()->first();
            if (! $timer) {
                return null;
            }
            $started = CarbonImmutable::parse($timer->started_at);
            $ended = CarbonImmutable::now();
            $hours = round($started->diffInSeconds($ended) / 3600, 2);
            if ($hours < 1 / 60) {
                DB::table(self::TIMERS)->where('id', $timer->id)->delete();

                return null;
            }
            $description = static::requireDescription($description ?? $timer->note);
            DB::table(self::TIMERS)->where('id', $timer->id)->delete();

            return static::log($user, (int) $timer->task_id, $started->toDateString(), $hours, $description, $started, $ended);
        });
    }

    /** Throw the running timer away without logging anything. */
    public static function discard(User $user): void
    {
        DB::table(self::TIMERS)->where('user_id', $user->getKey())->delete();
    }

    /** One declared piece of work: how long, on which day, and what was done (required). */
    public static function addEntry(User $user, int $taskId, string $date, float $hours, string $description, ?string $from = null): Timesheet
    {
        static::assertAssignee($user, $taskId);
        $description = static::requireDescription($description);
        $hours = round($hours, 2);
        if ($hours <= 0 || $hours > 24) {
            throw new RuntimeException('Enter between 1 minute and 24 hours.');
        }
        $day = CarbonImmutable::parse($date)->startOfDay();
        if ($day->isAfter(CarbonImmutable::today())) {
            throw new RuntimeException('Time cannot be logged in the future.');
        }

        $startedAt = null;
        if ($from !== null && trim($from) !== '') {
            if (! preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/', trim($from), $m)) {
                throw new RuntimeException('Write the start time like 9:30.');
            }
            $startedAt = $day->setTime((int) $m[1], (int) $m[2]);
        }

        return static::log($user, $taskId, $day->toDateString(), $hours, $description, $startedAt, $startedAt?->addMinutes((int) round($hours * 60)));
    }

    /** The person's own entry (administrators may correct anyone's). */
    public static function updateEntry(User $user, Timesheet $entry, float $hours, string $description): void
    {
        static::assertOwnEntry($user, $entry);
        $hours = round($hours, 2);
        if ($hours <= 0 || $hours > 24) {
            throw new RuntimeException('Enter between 1 minute and 24 hours.');
        }
        $entry->forceFill(['unit_amount' => $hours, 'name' => static::requireDescription($description)])->save();
        $span = DB::table(self::SPANS)->where('timesheet_id', $entry->getKey())->first();
        if ($span) {
            DB::table(self::SPANS)->where('id', $span->id)->update([
                'ended_at' => CarbonImmutable::parse($span->started_at)->addMinutes((int) round($hours * 60)), 'updated_at' => now(),
            ]);
        }
    }

    public static function deleteEntry(User $user, Timesheet $entry): void
    {
        static::assertOwnEntry($user, $entry);
        $entry->delete();
    }

    public static function canEditEntry(User $user, Timesheet $entry): bool
    {
        return (int) $entry->user_id === (int) $user->getKey() || static::isAdmin($user);
    }

    /** @return Collection<int, Timesheet> the person's entries on a task and day */
    public static function entriesOn(User $user, int $taskId, string $date): Collection
    {
        return Timesheet::query()->where('user_id', $user->getKey())->where('task_id', $taskId)
            ->whereDate('date', $date)->orderBy('id')->get(['id', 'name', 'unit_amount', 'date', 'user_id', 'task_id']);
    }

    public static function requireDescription(?string $description): string
    {
        $description = trim(preg_replace('/\s+/u', ' ', (string) $description) ?? '');
        if (mb_strlen($description) < 3) {
            throw new RuntimeException('Describe what you did: a description is required.');
        }

        return mb_substr($description, 0, 255);
    }

    /** @return array{days: list<string>, rows: list<array>, totals: array<string, float>, expected: array<string, float>} */
    public static function week(User $user, CarbonImmutable $monday): array
    {
        $days = collect(range(0, 6))->map(fn (int $i): string => $monday->addDays($i)->toDateString())->all();
        $hours = static::hoursByTaskAndDay($user, $days[0], $days[6]);

        $tasks = static::assignedOpenTasks($user)->keyBy('id');
        $missing = array_diff(array_keys($hours), $tasks->keys()->all());
        if ($missing) {
            // Tasks closed since, but with time this week, stay in the week.
            Task::query()->whereIn('projects_tasks.id', $missing)->with('project:id,name')
                ->get(['projects_tasks.id', 'projects_tasks.title', 'projects_tasks.project_id'])
                ->each(fn (Task $task) => $tasks->put($task->getKey(), $task));
        }

        $rows = $tasks->sortBy(fn (Task $task): string => ($task->project?->name ?? '~').' '.$task->title)
            ->map(fn (Task $task): array => [
                'id'      => $task->getKey(),
                'title'   => $task->title,
                'project' => $task->project?->name ?? 'No project',
                'hours'   => collect($days)->mapWithKeys(fn (string $day): array => [$day => $hours[$task->getKey()][$day] ?? 0.0])->all(),
            ])->values()->all();

        return [
            'days'     => $days,
            'rows'     => $rows,
            'totals'   => collect($days)->mapWithKeys(fn (string $day): array => [$day => round(collect($rows)->sum(fn (array $row) => $row['hours'][$day]), 2)])->all(),
            'expected' => collect($days)->mapWithKeys(fn (string $day): array => [$day => static::expectedHours($user, $day)])->all(),
        ];
    }

    /**
     * The person's week on a clock: timed entries as blocks between their start
     * and end, the others (no time of day) listed apart; hours per project.
     */
    public static function timeline(User $user, CarbonImmutable $monday): array
    {
        $sunday = $monday->addDays(6);
        $entries = Timesheet::query()->withoutGlobalScopes()
            ->leftJoin(self::SPANS, self::SPANS.'.timesheet_id', '=', 'analytic_records.id')
            ->leftJoin('projects_tasks', 'projects_tasks.id', '=', 'analytic_records.task_id')
            ->leftJoin('projects_projects', 'projects_projects.id', '=', 'analytic_records.project_id')
            ->where('analytic_records.user_id', $user->getKey())
            ->whereBetween('analytic_records.date', [$monday->toDateString(), $sunday->toDateString()])
            ->orderBy(self::SPANS.'.started_at')->orderBy('analytic_records.id')
            ->get([
                'analytic_records.id', 'analytic_records.date', 'analytic_records.unit_amount', 'analytic_records.name',
                'analytic_records.project_id', 'analytic_records.task_id', 'projects_tasks.title as task_title',
                'projects_projects.name as project_name', 'projects_projects.color as project_color',
                self::SPANS.'.started_at', self::SPANS.'.ended_at',
            ]);

        $color = fn ($row): string => class_exists(Palette::class)
            ? Palette::project($row->project_id ? (int) $row->project_id : null, $row->project_color)
            : '#0075de';
        $minuteOf = fn (string $at): int => (int) CarbonImmutable::parse($at)->format('G') * 60 + (int) CarbonImmutable::parse($at)->format('i');

        $first = 8 * 60;
        $last = 19 * 60;
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $monday->addDays($i)->toDateString();
            $rows = $entries->filter(fn ($row): bool => CarbonImmutable::parse($row->date)->toDateString() === $date);
            $blocks = [];
            $loose = [];
            foreach ($rows as $row) {
                $item = [
                    'id'          => (int) $row->id,
                    'hours'       => round((float) $row->unit_amount, 2),
                    'description' => (string) $row->name,
                    'task'        => $row->task_title ?? '—',
                    'project'     => $row->project_name ?? 'No project',
                    'color'       => $color($row),
                ];
                if ($row->started_at && $row->ended_at) {
                    $from = $minuteOf($row->started_at);
                    $to = CarbonImmutable::parse($row->ended_at)->isSameDay(CarbonImmutable::parse($row->started_at)) ? $minuteOf($row->ended_at) : 24 * 60;
                    $first = min($first, intdiv($from, 60) * 60);
                    $last = max($last, (int) ceil($to / 60) * 60);
                    $blocks[] = $item + ['from' => $from, 'to' => max($to, $from + 10)];
                } else {
                    $loose[] = $item;
                }
            }
            $days[] = [
                'date'   => $date,
                'blocks' => $blocks,
                'loose'  => $loose,
                'total'  => round($rows->sum('unit_amount'), 2),
            ];
        }

        $projects = $entries->groupBy(fn ($row) => $row->project_id ?? 0)->map(fn ($rows) => [
            'name'  => $rows->first()->project_name ?? 'No project',
            'color' => $color($rows->first()),
            'hours' => round($rows->sum('unit_amount'), 2),
        ])->sortByDesc('hours')->values()->all();

        return ['days' => $days, 'from' => $first, 'to' => min(24 * 60, $last), 'projects' => $projects, 'total' => round($entries->sum('unit_amount'), 2)];
    }

    /** Working hours foreseen by the person's work calendar (employee record); 8 h Mon–Fri otherwise. */
    public static function expectedHours(User $user, string $date): float
    {
        $weekday = strtolower(CarbonImmutable::parse($date)->englishDayOfWeek);
        $calendarId = static::hasEmployees()
            ? DB::table('employees_employees')->where('user_id', $user->getKey())->whereNull('deleted_at')->value('calendar_id')
            : null;
        if (! $calendarId) {
            return in_array($weekday, ['saturday', 'sunday'], true) ? 0.0 : 8.0;
        }

        return round((float) DB::table('calendar_attendances')
            ->where('calendar_id', $calendarId)->where('day_of_week', $weekday)->where('day_period', '!=', 'lunch')
            ->get(['hour_from', 'hour_to'])
            ->sum(fn ($slot): float => max(0, (float) $slot->hour_to - (float) $slot->hour_from)), 2);
    }

    /** Administrators' overview: every employee's week, and hours per project. */
    public static function teamWeek(CarbonImmutable $monday): array
    {
        $days = collect(range(0, 6))->map(fn (int $i): string => $monday->addDays($i)->toDateString())->all();
        $people = User::query()
            ->where('is_active', true)
            ->when(static::hasEmployees(), fn ($q) => $q->whereIn('id', DB::table('employees_employees')->whereNull('deleted_at')->whereNotNull('user_id')->pluck('user_id')))
            ->orderBy('name')->get(['id', 'name']);

        $logged = Timesheet::query()->withoutGlobalScopes()
            ->whereBetween('date', [$days[0], $days[6]])->whereNotNull('user_id')
            ->selectRaw('user_id, DATE(date) as day, SUM(unit_amount) as hours')
            ->groupBy('user_id', DB::raw('DATE(date)'))->get()
            ->groupBy('user_id');

        $rows = $people->map(function (User $person) use ($days, $logged): array {
            $byDay = collect($logged->get($person->getKey(), []))->mapWithKeys(fn ($row): array => [(string) $row->day => round((float) $row->hours, 2)]);
            $cells = collect($days)->map(fn (string $day): array => [
                'day'      => $day,
                'hours'    => $byDay[$day] ?? 0.0,
                'expected' => static::expectedHours($person, $day),
            ])->all();

            return [
                'name'     => $person->name,
                'cells'    => $cells,
                'total'    => round(collect($cells)->sum('hours'), 2),
                'expected' => round(collect($cells)->sum('expected'), 2),
            ];
        })->all();

        $projects = Timesheet::query()->withoutGlobalScopes()
            ->leftJoin('projects_projects', 'projects_projects.id', '=', 'analytic_records.project_id')
            ->whereBetween('analytic_records.date', [$days[0], $days[6]])
            ->selectRaw("COALESCE(projects_projects.name, 'No project') as project, SUM(analytic_records.unit_amount) as hours")
            ->groupBy('project')->orderByDesc('hours')->get()
            ->map(fn ($row): array => ['project' => $row->project, 'hours' => round((float) $row->hours, 2)])->all();

        return ['days' => $days, 'rows' => $rows, 'projects' => $projects];
    }

    /** "28 set – 4 ott 2026", independent of the panel locale. */
    public static function weekLabel(CarbonImmutable $monday): string
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $sunday = $monday->addDays(6);
        $day = fn (CarbonImmutable $d): string => $d->day.' '.$months[$d->month - 1];

        return $day($monday).($monday->year !== $sunday->year ? ' '.$monday->year : '').' – '.$day($sunday).' '.$sunday->year;
    }

    public static function format(float $hours): string
    {
        $minutes = (int) round($hours * 60);

        return sprintf('%d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    /** Accepts "1.5", "1,5" or "1:30". */
    public static function parse(string $value): float
    {
        $value = trim($value);
        if ($value === '') {
            return 0.0;
        }
        if (preg_match('/^(\d{1,2}):([0-5]\d)$/', $value, $m)) {
            return (int) $m[1] + (int) $m[2] / 60;
        }
        if (! is_numeric($normalized = str_replace(',', '.', $value))) {
            throw new RuntimeException('Write hours like 1.5 or 1:30.');
        }

        return (float) $normalized;
    }

    /** @return array<int, array<string, float>> task id => day => hours */
    private static function hoursByTaskAndDay(User $user, string $from, string $to): array
    {
        $result = [];
        Timesheet::query()
            ->where('user_id', $user->getKey())->whereNotNull('task_id')->whereBetween('date', [$from, $to])
            ->selectRaw('task_id, DATE(date) as day, SUM(unit_amount) as hours')
            ->groupBy('task_id', DB::raw('DATE(date)'))->get()
            ->each(function ($row) use (&$result): void {
                $result[(int) $row->task_id][(string) $row->day] = round((float) $row->hours, 2);
            });

        return $result;
    }

    private static function hasEmployees(): bool
    {
        static $has;

        return $has ??= Schema::hasTable('employees_employees');
    }

    private static function assertOwnEntry(User $user, Timesheet $entry): void
    {
        if (! static::canEditEntry($user, $entry)) {
            throw new RuntimeException('You can only change your own entries.');
        }
    }

    private static function assertAssignee(User $user, int $taskId): void
    {
        if (! static::isAssignee($user, $taskId)) {
            throw new RuntimeException('You can only log time on tasks you are assigned to: join the task first.');
        }
    }

    private static function log(User $user, int $taskId, string $date, float $hours, string $description, ?CarbonImmutable $startedAt = null, ?CarbonImmutable $endedAt = null): Timesheet
    {
        $task = Task::query()->withoutGlobalScopes()->findOrFail($taskId);
        $entry = new Timesheet;
        $entry->forceFill([
            'type'        => 'project',
            'name'        => $description,
            'date'        => $date,
            'unit_amount' => $hours,
            'amount'      => 0,
            'user_id'     => $user->getKey(),
            'creator_id'  => $user->getKey(),
            'project_id'  => $task->project_id,
            'task_id'     => $task->getKey(),
            'partner_id'  => $task->partner_id,
            'company_id'  => $task->company_id ?? $user->default_company_id,
        ]);
        $entry->save();
        if ($startedAt && $endedAt) {
            DB::table(self::SPANS)->insert([
                'timesheet_id' => $entry->getKey(), 'started_at' => $startedAt, 'ended_at' => $endedAt,
                'created_at'   => now(), 'updated_at' => now(),
            ]);
        }

        return $entry;
    }
}
