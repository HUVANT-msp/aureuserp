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
    @livewire('huvant-task-panel')
</x-filament-panels::page>
