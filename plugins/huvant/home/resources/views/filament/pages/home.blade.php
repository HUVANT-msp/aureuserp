@php
    use Huvant\Home\Support\Home;
    use Huvant\Tasks\Support\Board;
    $calendarUrl = rescue(fn () => \Huvant\Calendar\Filament\Pages\CalendarPage::getUrl(['view' => 'agenda']), null, false);
    $open = collect($tasks)->flatten(1)->count();
@endphp
<x-filament-panels::page>
    <div class="hv-home">
        {{-- What is coming up --}}
        <section class="hv-home-strip" aria-label="Coming up">
            @foreach ($upcoming as $i => $day)
                <div @class(['hv-home-day', 'is-today' => $i === 0])>
                    <header>
                        <span>{{ $i === 0 ? 'Today' : ($i === 1 ? 'Tomorrow' : $day['date']->format('D j')) }}</span>
                        @if (! in_array($day['presence'], ['office', 'weekend'], true))
                            <em class="hv-home-where where-{{ $day['presence'] }}">{{ \Huvant\Calendar\Support\Calendar::PRESENCE[$day['presence']][0] }}</em>
                        @endif
                    </header>
                    @forelse ($day['items'] as $item)
                        <a class="hv-home-event" href="{{ $calendarUrl ? \Huvant\Calendar\Filament\Pages\CalendarPage::getUrl(['view' => 'agenda', 'event' => $item['id'], 'week' => $day['date']->startOfWeek()->toDateString()]) : '#' }}" style="--ec: {{ $item['color'] }}">
                            <b>{{ $item['time'] }}</b> {{ $item['title'] }}
                            @if ($item['pending'])<small>reply</small>@endif
                        </a>
                    @empty
                        <p class="hv-home-free">{{ $day['date']->isWeekend() ? '' : 'Nothing planned' }}</p>
                    @endforelse
                </div>
            @endforeach
        </section>

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

            {{-- From your meetings --}}
            <section class="hv-home-card">
                <header class="hv-home-head"><h2>From your meetings</h2></header>
                @if ($meetings === null)
                    <p class="hv-home-muted">Meetings are not reachable right now.</p>
                @elseif (empty($meetings))
                    <p class="hv-home-muted">No meetings in the last three weeks.</p>
                @else
                    @foreach ($meetings as $meeting)
                        <div class="hv-home-meeting">
                            <a href="{{ $meeting['url'] }}" class="hv-home-meeting-title">
                                <x-filament::icon :icon="($meeting['system'] ?? '') === 'canvas' ? 'heroicon-m-presentation-chart-bar' : 'heroicon-m-document-text'" class="h-4 w-4" />
                                {{ $meeting['title'] }}
                                <small>{{ $meeting['date'] ? \Carbon\Carbon::parse($meeting['date'])->format('D j M') : '' }}</small>
                                @if (($meeting['mine'] ?? 0) > 0)<em>{{ $meeting['mine'] }} for you</em>@endif
                            </a>
                            @if (! empty($meeting['items']))
                                <ul>
                                    @foreach ($meeting['items'] as $item)
                                        <li @class(['is-mine' => $item['mine']])>
                                            <span class="hv-home-kind kind-{{ $item['kind'] }}">{{ ['action' => 'To do', 'decision' => 'Decided', 'open_point' => 'Open'][$item['kind']] ?? $item['kind'] }}</span>
                                            <span class="hv-home-item">{{ $item['text'] }}@if ($item['owner'] && ! $item['mine']) <small>· {{ $item['owner'] }}</small>@endif</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                @endif
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

            {{-- My time today --}}
            <section class="hv-home-card">
                <header class="hv-home-head">
                    <h2>My time today</h2>
                    <a href="{{ \Huvant\Worklog\Filament\Pages\MyWeek::getUrl() }}" class="hv-home-link">My time</a>
                </header>
                @php $pct = $today['expected'] > 0 ? min(100, round($today['hours'] / $today['expected'] * 100)) : 0; @endphp
                <div class="hv-home-meter">
                    <b>{{ \Huvant\Worklog\Support\Worklog::format($today['hours']) }}</b><span>of {{ \Huvant\Worklog\Support\Worklog::format($today['expected']) }} h</span>
                    <i><span style="width: {{ $pct }}%"></span></i>
                </div>
                @if ($today['running'])
                    <p class="hv-home-running"><span></span>Timer on “{{ $today['running']->task_title }}” since {{ \Carbon\Carbon::parse($today['running']->started_at)->format('H:i') }} <small>(stop it from the top bar)</small></p>
                @elseif ($today['tasks']->isNotEmpty())
                    <label class="hv-home-start">
                        <x-filament::icon icon="heroicon-m-play" class="h-4 w-4" />
                        <select x-on:change="if ($event.target.value) { $wire.startTimer(Number($event.target.value)); $event.target.value = '' }" aria-label="Start the timer on a task">
                            <option value="">Start the timer on…</option>
                            @foreach ($today['tasks'] as $task)<option value="{{ $task->id }}">{{ \Illuminate\Support\Str::limit($task->title, 70) }}</option>@endforeach
                        </select>
                    </label>
                @endif
                @if ($today['entries'])
                    <ul class="hv-home-entries">
                        @foreach ($today['entries'] as $entry)
                            <li><span class="hv-home-grow">{{ $entry->name }}<small>{{ $entry->task }}</small></span><b>{{ \Huvant\Worklog\Support\Worklog::format((float) $entry->unit_amount) }}</b></li>
                        @endforeach
                    </ul>
                @endif
            </section>
            </div>

        </div>
    </div>
    @livewire('huvant-task-panel')
</x-filament-panels::page>
