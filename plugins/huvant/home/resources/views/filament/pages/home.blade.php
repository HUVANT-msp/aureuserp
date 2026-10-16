@php
    use Huvant\Home\Support\Home;
    use Huvant\Tasks\Support\Board;
    $calendarUrl = rescue(fn () => \Huvant\Calendar\Filament\Pages\CalendarPage::getUrl(['view' => 'calendar']), null, false);
    $open = collect($tasks)->flatten(1)->count();
@endphp
<x-filament-panels::page>
    <div class="hv-home">
        <div class="hv-home-top">
            {{-- Coming up: one widget for the week --}}
            @php
                $next = collect($upcoming)->flatMap(fn ($day) => collect($day['items'])->map(fn ($item) => $item + ['day' => $day['date']]))->take(6);
            @endphp
            <section class="hv-home-card hv-home-upcoming" aria-label="In programma">
                <header class="hv-home-head">
                    <h2>In programma</h2>
                    @if ($calendarUrl)<a href="{{ $calendarUrl }}" class="hv-home-link">Calendario</a>@endif
                </header>
                <ol class="hv-home-week">
                    @foreach ($upcoming as $i => $day)
                        <li @class(['is-today' => $i === 0, 'is-weekend' => $day['date']->isWeekend()]) title="{{ $day['date']->locale('it')->translatedFormat('l j F') }}{{ in_array($day['presence'], ['office', 'weekend'], true) ? '' : ' · '.\Huvant\Calendar\Support\Calendar::PRESENCE[$day['presence']][0] }}">
                            <small>{{ $day['date']->locale('it')->translatedFormat('D') }}</small>
                            <b>{{ $day['date']->day }}</b>
                            <span class="hv-home-dots">
                                @foreach (array_slice($day['items'], 0, 3) as $item)<i style="background: {{ $item['color'] }}"></i>@endforeach
                            </span>
                            @if (! in_array($day['presence'], ['office', 'weekend'], true))
                                <em class="where-{{ $day['presence'] }}">{{ ['remote' => 'Da remoto', 'travel' => 'Trasferta', 'away' => 'Assente', 'leave' => 'Ferie'][$day['presence']] ?? '' }}</em>
                            @endif
                        </li>
                    @endforeach
                </ol>
                @if ($next->isEmpty())
                    <p class="hv-home-muted">Nessun impegno nei prossimi sette giorni.</p>
                @else
                    <ul class="hv-home-agenda">
                        @foreach ($next as $item)
                            <li>
                                <a href="{{ $calendarUrl ? \Huvant\Calendar\Filament\Pages\CalendarPage::getUrl(['view' => 'calendar', 'event' => $item['id'], 'week' => $item['day']->startOfWeek()->toDateString()]) : '#' }}" style="--ec: {{ $item['color'] }}">
                                    <span class="hv-home-when">{{ $item['day']->isToday() ? 'Oggi' : ($item['day']->isTomorrow() ? 'Domani' : $item['day']->locale('it')->translatedFormat('D j')) }} <b>{{ $item['time'] }}</b></span>
                                    <span class="hv-home-what">{{ $item['title'] }}</span>
                                    @if ($item['pending'])<small>rispondi</small>@endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            @if ($canLogTime)
            {{-- My time today: a gauge and two buttons --}}
            @php
                $pct = $today['expected'] > 0 ? min(1, $today['hours'] / $today['expected']) : 0;
                $arc = 2 * M_PI * 52 * 0.75;
            @endphp
            <section class="hv-home-card hv-home-time">
                <header class="hv-home-head">
                    <h2>Le mie ore oggi</h2>
                    <a href="{{ \Huvant\Worklog\Filament\Pages\MyWeek::getUrl() }}" class="hv-home-link">Le mie ore</a>
                </header>
                <div @class(['hv-gauge', 'is-running' => (bool) $today['running']]) role="img" aria-label="{{ \Huvant\Worklog\Support\Worklog::format($today['hours']) }} di {{ \Huvant\Worklog\Support\Worklog::format($today['expected']) }} ore oggi">
                    <svg viewBox="0 0 120 120" aria-hidden="true">
                        <circle class="hv-gauge-track" cx="60" cy="60" r="52" stroke-dasharray="{{ round($arc, 2) }} 999" />
                        <circle class="hv-gauge-fill" cx="60" cy="60" r="52" stroke-dasharray="{{ round($arc * $pct, 2) }} 999" />
                    </svg>
                    <div class="hv-gauge-text">
                        <b>{{ \Huvant\Worklog\Support\Worklog::format($today['hours']) }}</b>
                        <small>di {{ \Huvant\Worklog\Support\Worklog::format($today['expected']) }} h</small>
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
                            <x-filament::icon icon="heroicon-m-stop" class="h-4 w-4" />Ferma
                        </button>
                    @else
                        <div class="hv-home-btn is-start" x-data="{ open: false }" x-on:click.outside="open = false">
                            <button type="button" x-on:click="open = ! open" :aria-expanded="open" @disabled($today['tasks']->isEmpty())>
                                <x-filament::icon icon="heroicon-m-play" class="h-4 w-4" />Avvia timer
                            </button>
                            <ul class="hv-home-pick" x-show="open" x-cloak x-transition.opacity>
                                @foreach ($today['tasks'] as $task)
                                    <li><button type="button" wire:click="startTimer({{ $task->id }})" x-on:click="open = false">{{ \Illuminate\Support\Str::limit($task->title, 80) }}<small>{{ $task->project?->name }}</small></button></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <button type="button" class="hv-home-btn" wire:click="mountAction('logTime')">
                        <x-filament::icon icon="heroicon-m-pencil-square" class="h-4 w-4" />Registra ore
                    </button>
                </div>
            </section>
            @endif
        </div>

        <div class="hv-home-grid">
            <div class="hv-home-col">
            {{-- My tasks --}}
            <section class="hv-home-card hv-home-tasks">
                <header class="hv-home-head">
                    <h2>I miei task <span>{{ $open }}</span></h2>
                </header>
                @if ($open === 0)
                    <p class="hv-home-muted">Nessun task assegnato. Assegnati un task da una bacheca di progetto per iniziare.</p>
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
            @if ($worklogAlerts)
                <section class="hv-home-card hv-home-hours-alerts">
                    <header class="hv-home-head">
                        <h2>Ore da verificare <span>{{ count($worklogAlerts) }}</span></h2>
                        <a href="{{ \Huvant\Worklog\Filament\Pages\TeamHours::getUrl() }}" class="hv-home-link">Vista settimanale</a>
                    </header>
                    <ul class="hv-hours-alert-list">
                        @foreach ($worklogAlerts as $alert)
                            <li class="is-{{ $alert['status'] }}">
                                <span class="hv-hours-alert-icon">
                                    <x-filament::icon :icon="$alert['status'] === 'justified' ? 'heroicon-m-document-check' : 'heroicon-m-exclamation-triangle'" class="h-4 w-4" />
                                </span>
                                <span class="hv-home-grow">
                                    <b>{{ $alert['name'] }}</b>
                                    <small>{{ \Carbon\CarbonImmutable::parse($alert['date'])->locale('it')->translatedFormat('D j M') }} · {{ \Huvant\Worklog\Support\Worklog::format($alert['hours']) }} / {{ \Huvant\Worklog\Support\Worklog::format($alert['expected']) }} h{{ $alert['reason'] ? ' · '.$alert['reason'] : '' }}</small>
                                </span>
                                <span class="hv-hours-alert-state">{{ $alert['status'] === 'justified' ? 'Giustificato' : 'Mancante' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
            {{-- Waiting on you --}}
            @if ($waiting['count'] > 0)
                <section class="hv-home-card hv-home-waiting">
                    <header class="hv-home-head"><h2>In attesa di tuo riscontro <span>{{ $waiting['count'] }}</span></h2></header>
                    @foreach ($waiting['invites'] as $invite)
                        <div class="hv-home-wait">
                            <x-filament::icon icon="heroicon-m-envelope" class="h-4 w-4" />
                            <span class="hv-home-grow"><b>{{ $invite->title }}</b><small>{{ $invite->starts_at->locale('it')->translatedFormat('D j M, H:i') }}{{ $invite->organizer ? ' · da '.$invite->organizer->name : '' }}</small></span>
                            <span class="hv-home-reply">
                                <button type="button" wire:click="respondInvite({{ $invite->id }}, 'accepted')">Sì</button>
                                <button type="button" wire:click="respondInvite({{ $invite->id }}, 'tentative')">Forse</button>
                                <button type="button" wire:click="respondInvite({{ $invite->id }}, 'declined')">No</button>
                            </span>
                        </div>
                    @endforeach
                    @foreach ($waiting['assigned'] as $task)
                        <button type="button" class="hv-home-wait" x-on:click="$dispatch('huvant-open-task', { taskId: {{ $task->id }} })">
                            <x-filament::icon icon="heroicon-m-user-plus" class="h-4 w-4" />
                            <span class="hv-home-grow"><b>{{ $task->title }}</b><small>Assegnato a te{{ $task->project ? ' · '.$task->project->name : '' }}</small></span>
                        </button>
                    @endforeach
                    @foreach ($waiting['comments'] as $comment)
                        <button type="button" class="hv-home-wait" x-on:click="$dispatch('huvant-open-task', { taskId: {{ $comment->task_id }} })">
                            <x-filament::icon icon="heroicon-m-chat-bubble-left-ellipsis" class="h-4 w-4" />
                            <span class="hv-home-grow"><b>{{ $comment->author ?? 'Qualcuno' }} su {{ $comment->task }}</b><small>{{ \Illuminate\Support\Str::limit(strip_tags((string) $comment->body), 90) }}</small></span>
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
                                {{ $brief->error ? 'Ultimo tentativo' : 'Generato' }} {{ \Carbon\Carbon::parse($brief->generated_at)->isToday() ? 'oggi alle '.\Carbon\Carbon::parse($brief->generated_at)->format('H:i') : \Carbon\Carbon::parse($brief->generated_at)->locale('it')->translatedFormat('D j M, H:i') }}
                            @else
                                Il tuo brief viene generato alle 8:00 e alle 13:00
                            @endif
                        </p>
                    </div>
                    <button type="button" class="hv-milo-refresh" wire:click="refreshBrief" title="Richiedi un nuovo brief a Milo" aria-label="Richiedi un nuovo brief a Milo">
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
                    <p class="hv-home-muted">{{ $brief->error }} Riprova tra qualche istante con il pulsante di aggiornamento.</p>
                @else
                    <p class="hv-home-muted">Nessun brief disponibile. Usa il pulsante di aggiornamento per richiederne uno.</p>
                @endif
                <a class="hv-milo-ask" href="/riunioni/milo">Chiedi a Milo <x-filament::icon icon="heroicon-m-arrow-right" class="h-4 w-4" /></a>
            </section>

            </div>

        </div>
    </div>
    @if ($canLogTime)
    <x-filament::modal id="hv-home-stop" width="md">
        <x-slot name="heading">A cosa hai lavorato?</x-slot>
        <form class="hv-home-stop" wire:submit="stopTimer">
            <textarea wire:model="stopNote" rows="3" maxlength="255" required placeholder="Descrizione dell'attività svolta (obbligatoria)"></textarea>
            <div>
                <button type="button" class="hv-home-discard" wire:click="discardTimer" wire:confirm="Vuoi annullare il timer senza registrare le ore?">Scarta timer</button>
                <x-filament::button type="submit">Salva e registra</x-filament::button>
            </div>
        </form>
    </x-filament::modal>
    @endif
    @livewire('huvant-task-panel')
</x-filament-panels::page>
