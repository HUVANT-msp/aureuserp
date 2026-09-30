@php
    use Huvant\Home\Support\Home;
    use Huvant\Tasks\Support\Board;
    $calendarUrl = rescue(fn () => \Huvant\Calendar\Filament\Pages\CalendarPage::getUrl(['view' => 'agenda']), null, false);
    $open = collect($tasks)->flatten(1)->count();
@endphp
<x-filament-panels::page>
    <div class="hv-home">
        <div class="hv-home-top">
            {{-- Coming up: one widget for the week --}}
            @php
                $next = collect($upcoming)->flatMap(fn ($day) => collect($day['items'])->map(fn ($item) => $item + ['day' => $day['date']]))->take(6);
            @endphp
            <section class="hv-home-card hv-home-upcoming" aria-label="Coming up">
                <header class="hv-home-head">
                    <h2>Coming up</h2>
                    @if ($calendarUrl)<a href="{{ $calendarUrl }}" class="hv-home-link">Calendar</a>@endif
                </header>
                <ol class="hv-home-week">
                    @foreach ($upcoming as $i => $day)
                        <li @class(['is-today' => $i === 0, 'is-weekend' => $day['date']->isWeekend()]) title="{{ $day['date']->format('l j F') }}{{ in_array($day['presence'], ['office', 'weekend'], true) ? '' : ' · '.\Huvant\Calendar\Support\Calendar::PRESENCE[$day['presence']][0] }}">
                            <small>{{ $day['date']->format('D') }}</small>
                            <b>{{ $day['date']->day }}</b>
                            <span class="hv-home-dots">
                                @foreach (array_slice($day['items'], 0, 3) as $item)<i style="background: {{ $item['color'] }}"></i>@endforeach
                            </span>
                            @if (! in_array($day['presence'], ['office', 'weekend'], true))
                                <em class="where-{{ $day['presence'] }}">{{ ['remote' => 'Remote', 'travel' => 'Travel', 'away' => 'Away', 'leave' => 'Leave'][$day['presence']] ?? '' }}</em>
                            @endif
                        </li>
                    @endforeach
                </ol>
                @if ($next->isEmpty())
                    <p class="hv-home-muted">Nothing planned in the next seven days.</p>
                @else
                    <ul class="hv-home-agenda">
                        @foreach ($next as $item)
                            <li>
                                <a href="{{ $calendarUrl ? \Huvant\Calendar\Filament\Pages\CalendarPage::getUrl(['view' => 'agenda', 'event' => $item['id'], 'week' => $item['day']->startOfWeek()->toDateString()]) : '#' }}" style="--ec: {{ $item['color'] }}">
                                    <span class="hv-home-when">{{ $item['day']->isToday() ? 'Today' : ($item['day']->isTomorrow() ? 'Tomorrow' : $item['day']->format('D j')) }} <b>{{ $item['time'] }}</b></span>
                                    <span class="hv-home-what">{{ $item['title'] }}</span>
                                    @if ($item['pending'])<small>reply</small>@endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            {{-- My time today: a gauge and two buttons --}}
            @php
                $pct = $today['expected'] > 0 ? min(1, $today['hours'] / $today['expected']) : 0;
                $arc = 2 * M_PI * 52 * 0.75;
            @endphp
            <section class="hv-home-card hv-home-time">
                <header class="hv-home-head">
                    <h2>My time today</h2>
                    <a href="{{ \Huvant\Worklog\Filament\Pages\MyWeek::getUrl() }}" class="hv-home-link">My time</a>
                </header>
                <div @class(['hv-gauge', 'is-running' => (bool) $today['running']]) role="img" aria-label="{{ \Huvant\Worklog\Support\Worklog::format($today['hours']) }} of {{ \Huvant\Worklog\Support\Worklog::format($today['expected']) }} hours today">
                    <svg viewBox="0 0 120 120" aria-hidden="true">
                        <circle class="hv-gauge-track" cx="60" cy="60" r="52" stroke-dasharray="{{ round($arc, 2) }} 999" />
                        <circle class="hv-gauge-fill" cx="60" cy="60" r="52" stroke-dasharray="{{ round($arc * $pct, 2) }} 999" />
                    </svg>
                    <div class="hv-gauge-text">
                        <b>{{ \Huvant\Worklog\Support\Worklog::format($today['hours']) }}</b>
                        <small>of {{ \Huvant\Worklog\Support\Worklog::format($today['expected']) }} h</small>
                    </div>
                </div>
                @if ($today['running'])
                    <p class="hv-home-running" x-data="{ start: {{ \Carbon\Carbon::parse($today['running']->started_at)->getTimestamp() }} * 1000, now: Date.now() }" x-init="setInterval(() => now = Date.now(), 1000)">
                        <span></span><em>{{ \Illuminate\Support\Str::limit($today['running']->task_title, 40) }}</em>
                        <b x-text="(() => { const s = Math.max(0, Math.floor((now - start) / 1000)); return Math.floor(s / 3600) + ':' + String(Math.floor(s / 60) % 60).padStart(2, '0') })()"></b>
                    </p>
                @endif
                <div class="hv-home-time-actions">
                    @if ($today['running'])
                        <button type="button" class="hv-home-btn is-stop" x-on:click="$dispatch('open-modal', { id: 'hv-home-stop' })">
                            <x-filament::icon icon="heroicon-m-stop" class="h-4 w-4" />Stop
                        </button>
                    @else
                        <div class="hv-home-btn is-start" x-data="{ open: false }" x-on:click.outside="open = false">
                            <button type="button" x-on:click="open = ! open" :aria-expanded="open" @disabled($today['tasks']->isEmpty())>
                                <x-filament::icon icon="heroicon-m-play" class="h-4 w-4" />Start timer
                            </button>
                            <ul class="hv-home-pick" x-show="open" x-cloak x-transition.opacity>
                                @foreach ($today['tasks'] as $task)
                                    <li><button type="button" wire:click="startTimer({{ $task->id }})" x-on:click="open = false">{{ \Illuminate\Support\Str::limit($task->title, 80) }}<small>{{ $task->project?->name }}</small></button></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <button type="button" class="hv-home-btn" wire:click="mountAction('logTime')">
                        <x-filament::icon icon="heroicon-m-pencil-square" class="h-4 w-4" />Log time
                    </button>
                </div>
            </section>
        </div>

        <div class="hv-home-grid">
            <div class="hv-home-col">
            {{-- My tasks --}}
            <section class="hv-home-card hv-home-tasks">
                <header class="hv-home-head">
                    <h2>My tasks <span>{{ $open }}</span></h2>
                </header>
                @if ($open === 0)
                    <p class="hv-home-muted">Nothing assigned to you. Join a task from a project board to start.</p>
                @endif
                @foreach (Home::BUCKETS as $key => [$label, $tone])
                    @continue(empty($tasks[$key]))
                    <div class="hv-home-bucket tone-{{ $tone }}">
                        <h3>{{ $label }} <span>{{ count($tasks[$key]) }}</span></h3>
                        <ul>
                            @foreach ($tasks[$key] as $row)
                                @php
                                    $task = $row['task'];
                                    $stage = (string) $task->stage?->name;
                                @endphp
                                <li>
                                    <button type="button" x-on:click="$dispatch('huvant-open-task', { taskId: {{ $task->id }} })" title="{{ $task->title }}{{ $task->project ? ' · '.$task->project->name : '' }}">
                                        <span class="hv-home-title">{{ $task->title }}</span>
                                        @if ($due = Home::dueLabel($row['days'], $task->deadline ? \Carbon\CarbonImmutable::parse($task->deadline) : null))
                                            <span class="hv-home-due">{{ $due }}</span>
                                        @endif
                                        <span class="hv-home-stage stage-{{ class_exists(Board::class) ? Board::stageKind($stage) : 'todo' }}">{{ class_exists(Board::class) && $stage !== '' ? Board::stageLabel($stage) : ($stage ?: '—') }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </section>

            </div>

            <div class="hv-home-col">
            {{-- Waiting on you --}}
            @if ($waiting['count'] > 0)
                <section class="hv-home-card hv-home-waiting">
                    <header class="hv-home-head"><h2>Waiting on you <span>{{ $waiting['count'] }}</span></h2></header>
                    @foreach ($waiting['invites'] as $invite)
                        <div class="hv-home-wait">
                            <x-filament::icon icon="heroicon-m-envelope" class="h-4 w-4" />
                            <span class="hv-home-grow"><b>{{ $invite->title }}</b><small>{{ $invite->starts_at->format('D j M, H:i') }}{{ $invite->organizer ? ' · from '.$invite->organizer->name : '' }}</small></span>
                            <span class="hv-home-reply">
                                <button type="button" wire:click="respondInvite({{ $invite->id }}, 'accepted')">Yes</button>
                                <button type="button" wire:click="respondInvite({{ $invite->id }}, 'tentative')">Maybe</button>
                                <button type="button" wire:click="respondInvite({{ $invite->id }}, 'declined')">No</button>
                            </span>
                        </div>
                    @endforeach
                    @foreach ($waiting['assigned'] as $task)
                        <button type="button" class="hv-home-wait" x-on:click="$dispatch('huvant-open-task', { taskId: {{ $task->id }} })">
                            <x-filament::icon icon="heroicon-m-user-plus" class="h-4 w-4" />
                            <span class="hv-home-grow"><b>{{ $task->title }}</b><small>Assigned to you{{ $task->project ? ' · '.$task->project->name : '' }}</small></span>
                        </button>
                    @endforeach
                    @foreach ($waiting['comments'] as $comment)
                        <button type="button" class="hv-home-wait" x-on:click="$dispatch('huvant-open-task', { taskId: {{ $comment->task_id }} })">
                            <x-filament::icon icon="heroicon-m-chat-bubble-left-ellipsis" class="h-4 w-4" />
                            <span class="hv-home-grow"><b>{{ $comment->author ?? 'Someone' }} on {{ $comment->task }}</b><small>{{ \Illuminate\Support\Str::limit(strip_tags((string) $comment->body), 90) }}</small></span>
                        </button>
                    @endforeach
                </section>
            @endif

            {{-- Milo's brief --}}
            <section class="hv-home-card hv-milo" wire:poll.30s.visible>
                <header class="hv-milo-head">
                    <span class="hv-milo-orb" aria-hidden="true"><i></i></span>
                    <div>
                        <h2>Milo</h2>
                        <p>
                            @if ($brief)
                                {{ $brief->error ? 'Last attempt' : 'Written' }} {{ \Carbon\Carbon::parse($brief->generated_at)->isToday() ? 'today at '.\Carbon\Carbon::parse($brief->generated_at)->format('H:i') : \Carbon\Carbon::parse($brief->generated_at)->format('D j M, H:i') }}
                            @else
                                Your brief arrives at 8:00 and 13:00
                            @endif
                        </p>
                    </div>
                    <button type="button" class="hv-milo-refresh" wire:click="refreshBrief" title="Ask Milo for a fresh brief" aria-label="Ask Milo for a fresh brief">
                        <x-filament::icon icon="heroicon-m-arrow-path" class="h-4 w-4" />
                    </button>
                </header>

                @if ($brief && ! $brief->error)
                    <p class="hv-milo-headline">{{ $brief->headline }}</p>
                    <p class="hv-milo-summary">{{ $brief->summary }}</p>
                    @if ($brief->focus)
                        <ol class="hv-milo-focus">
                            @foreach ($brief->focus as $item)
                                <li><b>{{ $item['title'] ?? '' }}</b><span>{{ $item['why'] ?? '' }}</span></li>
                            @endforeach
                        </ol>
                    @endif
                    @if ($brief->heads_up)
                        <ul class="hv-milo-heads">
                            @foreach ($brief->heads_up as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    @endif
                @elseif ($brief && $brief->error)
                    <p class="hv-home-muted">{{ $brief->error }} Try the refresh button in a moment.</p>
                @else
                    <p class="hv-home-muted">No brief yet. Use the refresh button to get one now.</p>
                @endif
                <a class="hv-milo-ask" href="/riunioni/milo">Ask Milo <x-filament::icon icon="heroicon-m-arrow-right" class="h-4 w-4" /></a>
            </section>

            </div>

        </div>
    </div>
    <x-filament::modal id="hv-home-stop" width="md">
        <x-slot name="heading">What did you do?</x-slot>
        <form class="hv-home-stop" wire:submit="stopTimer">
            <textarea wire:model="stopNote" rows="3" maxlength="255" required placeholder="Required"></textarea>
            <div>
                <button type="button" class="hv-home-discard" wire:click="discardTimer" wire:confirm="Discard the timer without logging any time?">Discard timer</button>
                <x-filament::button type="submit">Stop and log</x-filament::button>
            </div>
        </form>
    </x-filament::modal>
    @livewire('huvant-task-panel')
</x-filament-panels::page>
