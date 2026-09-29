<?php

namespace Huvant\Home\Support;

use Carbon\CarbonImmutable;
use Huvant\Calendar\Models\Event;
use Huvant\Calendar\Support\Calendar;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\User;

/** One's own day: tasks by urgency, what is coming up, and Milo's brief. */
class Home
{
    public const TABLE = 'huvant_milo_briefings';

    /** Urgency buckets, most urgent first: key => [label, tone]. */
    public const BUCKETS = [
        'overdue' => ['Overdue', 'danger'],
        'soon'    => ['Due in the next 3 days', 'warning'],
        'later'   => ['Coming up', 'neutral'],
        'none'    => ['No deadline', 'muted'],
    ];

    /** Open tasks assigned to the person (visible to them), grouped by urgency. */
    public static function myTasks(User $user, ?CarbonImmutable $today = null): array
    {
        $today ??= CarbonImmutable::today();
        $tasks = Task::query()
            ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value])
            ->whereHas('users', fn ($q) => $q->whereKey($user->getKey()))
            ->with(['project:id,name,color', 'stage:id,name'])
            ->orderByRaw('deadline IS NULL')->orderBy('deadline')->orderByDesc('priority')
            ->limit(200)->get();

        $buckets = array_fill_keys(array_keys(self::BUCKETS), []);
        foreach ($tasks as $task) {
            $days = $task->deadline ? (int) $today->diffInDays(CarbonImmutable::parse($task->deadline)->startOfDay(), false) : null;
            $key = match (true) {
                $days === null => 'none',
                $days < 0      => 'overdue',
                $days <= 3     => 'soon',
                default        => 'later',
            };
            $buckets[$key][] = ['task' => $task, 'days' => $days];
        }

        return $buckets;
    }

    public static function dueLabel(?int $days, ?CarbonImmutable $deadline = null): ?string
    {
        return match (true) {
            $days === null => null,
            $days < -1     => abs($days).' days late',
            $days === -1   => '1 day late',
            $days === 0    => 'Due today',
            $days === 1    => 'Due tomorrow',
            $days <= 6     => 'In '.$days.' days',
            default        => $deadline?->format('M j'),
        };
    }

    /** The person's next appointments and where they will be, day by day. */
    public static function upcoming(User $user, int $days = 7, ?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now();
        $from = $now->startOfDay();
        $to = $from->addDays($days);
        if (! class_exists(Event::class)) {
            return [];
        }
        $events = Event::query()->with(['attendees'])
            ->where('starts_at', '<', $to)->where('ends_at', '>', $from)
            ->whereHas('attendees', fn ($q) => $q->where('user_id', $user->getKey())->where('response', '!=', 'declined'))
            ->orderBy('starts_at')->get();
        $board = Calendar::board(collect([$user]), $from, $days, $now);
        $cells = $board['rows'][0]['cells'] ?? [];

        $out = [];
        foreach ($cells as $i => $cell) {
            $day = $from->addDays($i);
            $items = $events->filter(fn (Event $e) => ! Calendar::kind($e->kind)[3] && $e->starts_at->lt($day->addDay()) && $e->ends_at->gt($day))
                ->filter(fn (Event $e) => ! $day->isSameDay($now) || $e->all_day || $e->ends_at->gt($now))
                ->map(fn (Event $e) => [
                    'id'       => $e->id,
                    'title'    => $e->title,
                    'time'     => $e->all_day ? 'All day' : $e->starts_at->format('H:i'),
                    'color'    => Calendar::kind($e->kind)[2],
                    'pending'  => $e->attendees->firstWhere('user_id', $user->getKey())?->response === 'pending',
                ])->values()->all();
            $out[] = ['date' => $day, 'presence' => $cell['presence'], 'items' => $items];
        }

        return $out;
    }

    /** Today's time: hours against the calendar, the running timer, today's entries. */
    public static function today(User $user, ?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now();
        $entries = DB::table('analytic_records')
            ->leftJoin('projects_tasks', 'projects_tasks.id', '=', 'analytic_records.task_id')
            ->where('analytic_records.user_id', $user->getKey())->whereDate('analytic_records.date', $now->toDateString())
            ->orderByDesc('analytic_records.id')
            ->get(['analytic_records.id', 'analytic_records.unit_amount', 'analytic_records.name', 'projects_tasks.title as task']);

        return [
            'hours'    => round((float) $entries->sum('unit_amount'), 2),
            'expected' => Worklog::expectedHours($user, $now->toDateString()),
            'running'  => Worklog::running($user),
            'entries'  => $entries->take(5)->all(),
            'tasks'    => Worklog::assignedOpenTasks($user),
        ];
    }

    /** What is waiting for the person: invitations, tasks just assigned, comments on their tasks. */
    public static function waiting(User $user, ?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now();
        $invites = class_exists(Calendar::class) ? Calendar::pendingFor($user)->take(5) : collect();

        $assigned = Task::query()
            ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value])
            ->whereIn('projects_tasks.id', DB::table('projects_task_users')->where('user_id', $user->getKey())
                ->where('created_at', '>=', $now->subDays(3))->pluck('task_id'))
            ->where(fn ($q) => $q->whereNull('creator_id')->orWhere('creator_id', '!=', $user->getKey()))
            ->with('project:id,name')->latest('updated_at')->limit(5)->get(['projects_tasks.id', 'title', 'project_id']);

        $myTasks = DB::table('projects_task_users')->where('user_id', $user->getKey())->pluck('task_id');
        $comments = DB::table('chatter_messages')
            ->join('projects_tasks', 'projects_tasks.id', '=', 'chatter_messages.messageable_id')
            ->leftJoin('users', 'users.id', '=', 'chatter_messages.causer_id')
            ->where('chatter_messages.messageable_type', Task::class)
            ->whereIn('chatter_messages.messageable_id', $myTasks)
            ->whereIn('chatter_messages.type', ['comment', 'note'])
            ->where(fn ($q) => $q->whereNull('chatter_messages.causer_id')->orWhere('chatter_messages.causer_id', '!=', $user->getKey()))
            ->where('chatter_messages.created_at', '>=', $now->subDays(7))
            ->orderByDesc('chatter_messages.created_at')->limit(5)
            ->get(['chatter_messages.id', 'chatter_messages.body', 'chatter_messages.created_at', 'projects_tasks.id as task_id', 'projects_tasks.title as task', 'users.name as author']);

        return ['invites' => $invites, 'assigned' => $assigned, 'comments' => $comments, 'count' => $invites->count() + $assigned->count() + $comments->count()];
    }

    /** The person's latest meetings, decisions and actions, from the Minutes (cached 10 minutes). */
    public static function meetings(User $user, ?CarbonImmutable $today = null): ?array
    {
        $today ??= CarbonImmutable::today();

        return cache()->remember('huvant-home:meetings:'.$user->getKey().':'.$today->toDateString(), now()->addMinutes(10), function () use ($user, $today) {
            $url = static::minutesUrl('/erp/my-meetings');
            $secret = (string) config('huvant-bridge.webhook.secret');
            if (! $url || $secret === '') {
                return null;
            }
            $body = json_encode(['email' => $user->email, 'today' => $today->toDateString()], JSON_THROW_ON_ERROR);
            $timestamp = (string) now()->timestamp;
            try {
                $response = Http::timeout(4)->withHeaders([
                    'X-Huvant-Timestamp' => $timestamp,
                    'X-Huvant-Signature' => 'sha256='.hash_hmac('sha256', $timestamp.'.'.$body, $secret),
                ])->withBody($body, 'application/json')->post($url)->throw();

                return (array) ($response->json('meetings') ?? []);
            } catch (\Throwable $e) {
                report($e);

                return null;
            }
        });
    }

    /** What Milo is told: open tasks, the next events and the hours. */
    public static function context(User $user, string $slot, ?CarbonImmutable $now = null): array
    {
        $now ??= CarbonImmutable::now();
        $tasks = collect(static::myTasks($user, $now->startOfDay()))->flatten(1)->map(fn (array $row) => [
            'title'    => mb_substr($row['task']->title, 0, 500),
            'project'  => $row['task']->project?->name,
            'stage'    => $row['task']->stage?->name,
            'deadline' => $row['task']->deadline?->toDateString(),
            'priority' => (bool) $row['task']->priority,
        ])->take(40)->values()->all();

        $events = class_exists(Event::class) ? Event::query()
            ->where('starts_at', '<', $now->addDays(3))->where('ends_at', '>', $now)
            ->whereHas('attendees', fn ($q) => $q->where('user_id', $user->getKey())->where('response', '!=', 'declined'))
            ->orderBy('starts_at')->limit(20)->get()
            ->map(fn (Event $e) => [
                'title'     => $e->private ? 'Private appointment' : $e->title,
                'kind'      => $e->kind,
                'starts_at' => $e->starts_at->toIso8601String(),
                'ends_at'   => $e->ends_at->toIso8601String(),
                'all_day'   => (bool) $e->all_day,
                'location'  => $e->private ? null : $e->location,
            ])->all() : [];

        $monday = $now->startOfWeek();
        $hours = fn (string $from, string $to): float => round((float) DB::table('analytic_records')->where('user_id', $user->getKey())->whereBetween('date', [$from, $to])->sum('unit_amount'), 2);
        $expected = 0.0;
        for ($d = $monday; $d->lte($now->startOfDay()); $d = $d->addDay()) {
            $expected += Worklog::expectedHours($user, $d->toDateString());
        }

        return [
            'email'               => $user->email,
            'name'                => $user->name,
            'now'                 => $now->toIso8601String(),
            'slot'                => $slot,
            'language'            => (string) config('huvant-home.language', 'en'),
            'tasks'               => $tasks,
            'events'              => $events,
            'hours_yesterday'     => $hours($now->subDay()->toDateString(), $now->subDay()->toDateString()),
            'hours_week'          => $hours($monday->toDateString(), $now->toDateString()),
            'hours_week_expected' => round($expected, 2),
        ];
    }

    /** Ask Milo (Minutes API, signed like the bridge webhook) and keep the brief. */
    public static function generate(User $user, string $slot = 'manual'): object
    {
        $url = static::miloUrl();
        $secret = (string) config('huvant-bridge.webhook.secret');
        if (! $url || $secret === '') {
            throw new RuntimeException('Milo is not connected.');
        }
        $body = json_encode(static::context($user, $slot), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $timestamp = (string) now()->timestamp;
        $row = ['user_id' => $user->getKey(), 'slot' => $slot, 'generated_at' => now(), 'created_at' => now(), 'updated_at' => now()];

        try {
            $response = Http::timeout((int) config('huvant-home.timeout', 90))
                ->withHeaders([
                    'X-Huvant-Timestamp' => $timestamp,
                    'X-Huvant-Signature' => 'sha256='.hash_hmac('sha256', $timestamp.'.'.$body, $secret),
                ])
                ->withBody($body, 'application/json')->post($url);
            $response->throw();
            $brief = $response->json();
            $row += [
                'headline' => mb_substr((string) ($brief['headline'] ?? ''), 0, 255),
                'summary'  => (string) ($brief['summary'] ?? ''),
                'focus'    => json_encode(array_slice((array) ($brief['focus'] ?? []), 0, 4)),
                'heads_up' => json_encode(array_slice((array) ($brief['heads_up'] ?? []), 0, 3)),
            ];
        } catch (\Throwable $e) {
            report($e);
            $row['error'] = 'Milo could not write the brief this time.';
        }
        DB::table(self::TABLE)->insert($row);
        // Keep the last few briefs per person.
        $keep = DB::table(self::TABLE)->where('user_id', $user->getKey())->orderByDesc('id')->limit(6)->pluck('id');
        DB::table(self::TABLE)->where('user_id', $user->getKey())->whereNotIn('id', $keep)->delete();

        return static::latest($user);
    }

    public static function latest(User $user): ?object
    {
        $row = DB::table(self::TABLE)->where('user_id', $user->getKey())->whereNull('error')->orderByDesc('id')->first()
            ?? DB::table(self::TABLE)->where('user_id', $user->getKey())->orderByDesc('id')->first();
        if ($row) {
            $row->focus = json_decode((string) $row->focus, true) ?: [];
            $row->heads_up = json_decode((string) $row->heads_up, true) ?: [];
        }

        return $row;
    }

    /** People who get a brief: active users with an employee record. */
    public static function recipients(): Collection
    {
        return Calendar::people();
    }

    private static function miloUrl(): ?string
    {
        $url = config('huvant-home.milo_url');

        return is_string($url) && $url !== '' ? $url : static::minutesUrl('/erp/milo-briefing');
    }

    /** Minutes API endpoints sit next to the bridge webhook (/api/v1/erp/...). */
    private static function minutesUrl(string $path): ?string
    {
        $webhook = config('huvant-bridge.webhook.url');

        return is_string($webhook) && str_contains($webhook, '/erp/webhook') ? str_replace('/erp/webhook', $path, $webhook) : null;
    }
}
