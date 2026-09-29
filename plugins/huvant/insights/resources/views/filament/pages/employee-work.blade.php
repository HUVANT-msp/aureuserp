@php
    use Huvant\Worklog\Support\Worklog;
    $dayNames = ['L', 'M', 'M', 'G', 'V', 'S', 'D'];
    $projectColor = fn ($id, $color) => class_exists(\Huvant\Tasks\Support\Palette::class) ? \Huvant\Tasks\Support\Palette::project($id ? (int) $id : null, $color) : '#0075de';
@endphp
<x-filament-panels::page>
    @if (! $person)
        <p class="hv-ins-muted">Questo dipendente non è collegato a un utente: non ci sono ore o task da mostrare.</p>
    @else
        <div class="hv-ins">
            <div class="hv-ins-bar">
                <div class="hv-ins-seg" role="tablist" aria-label="Periodo">
                    @foreach ([7 => '7 giorni', 14 => '14 giorni', 30 => '30 giorni'] as $d => $label)
                        <button type="button" role="tab" aria-selected="{{ $days === $d ? 'true' : 'false' }}" wire:click="setDays({{ $d }})">{{ $label }}</button>
                    @endforeach
                </div>
                @if ($data['running'])
                    <span class="hv-ins-live"><span></span>Ora su «{{ $data['running']->task_title }}»</span>
                @endif
            </div>

            <div class="hv-ins-kpis">
                <div><span>Oggi</span><b>{{ Worklog::format($data['today']) }}</b></div>
                <div @class(['is-short' => $data['weekHours'] < $data['weekTarget'] * 0.8])><span>Questa settimana</span><b>{{ Worklog::format($data['weekHours']) }}<small>/ {{ Worklog::format($data['weekTarget']) }}</small></b></div>
                <div><span>Ultimi {{ $days }} giorni</span><b>{{ Worklog::format($data['total']) }}<small>/ {{ Worklog::format($data['expected']) }}</small></b></div>
                <div @class(['is-short' => $data['overdue'] > 0])><span>Task aperti</span><b>{{ $data['openCount'] }}@if ($data['overdue'])<small>{{ $data['overdue'] }} scaduti</small>@endif</b></div>
                <div><span>Completati nel periodo</span><b>{{ $data['completed'] }}</b></div>
            </div>

            <section class="hv-ins-card">
                <h3>Ore giorno per giorno</h3>
                @php $max = max(1, collect($data['series'])->max(fn ($d) => max($d['hours'], $d['expected']))); @endphp
                <div class="hv-ins-bars" style="--n: {{ count($data['series']) }}">
                    @foreach ($data['series'] as $day)
                        @php $d = \Carbon\CarbonImmutable::parse($day['date']); @endphp
                        <div @class(['hv-ins-daybar', 'is-weekend' => $d->isWeekend(), 'is-missing' => $day['expected'] > 0 && $day['hours'] == 0 && $d->isPast() && ! $d->isToday()])
                             title="{{ $d->format('d/m') }}: {{ Worklog::format($day['hours']) }} su {{ Worklog::format($day['expected']) }}">
                            <div class="hv-ins-daycol">
                                @if ($day['expected'] > 0)<span class="hv-ins-target" style="bottom: {{ round($day['expected'] / $max * 100, 1) }}%"></span>@endif
                                <span class="hv-ins-fill" style="height: {{ round($day['hours'] / $max * 100, 1) }}%"></span>
                            </div>
                            <small>{{ $dayNames[$d->dayOfWeekIso - 1] }}<br>{{ $d->day }}</small>
                        </div>
                    @endforeach
                </div>
            </section>

            <div class="hv-ins-two">
                <section class="hv-ins-card">
                    <h3>Su quali progetti</h3>
                    @if ($data['projects']->isEmpty())
                        <p class="hv-ins-muted">Nessuna ora nel periodo.</p>
                    @else
                        <div class="hv-ins-stack">
                            @foreach ($data['projects'] as $p)<span style="flex: {{ $p['hours'] }}; background: {{ $p['color'] }}" title="{{ $p['name'] }}"></span>@endforeach
                        </div>
                        <ul class="hv-ins-list">
                            @foreach ($data['projects'] as $p)
                                <li><span class="hv-ins-dot" style="--pc: {{ $p['color'] }}"></span><span class="hv-ins-grow">{{ $p['name'] }}</span><b>{{ Worklog::format($p['hours']) }}</b></li>
                            @endforeach
                        </ul>
                    @endif
                </section>
                <section class="hv-ins-card">
                    <h3>Task aperti</h3>
                    @forelse ($data['openTasks'] as $task)
                        <a class="hv-ins-task" href="{{ \Webkul\Project\Filament\Resources\TaskResource::getUrl('view', ['record' => $task]) }}" style="--pc: {{ $projectColor($task->project_id, $task->project?->color) }}">
                            <span class="hv-ins-grow">{{ $task->title }}<small>{{ $task->project?->name }}</small></span>
                            @if ($task->deadline)<span @class(['hv-ins-due', 'is-late' => $task->deadline->isPast()])>{{ $task->deadline->format('d/m') }}</span>@endif
                        </a>
                    @empty
                        <p class="hv-ins-muted">Nessun task aperto.</p>
                    @endforelse
                </section>
            </div>

            <section class="hv-ins-card">
                <h3>Ultime registrazioni</h3>
                @forelse ($data['entries'] as $e)
                    <div class="hv-ins-entry" style="--pc: {{ $projectColor($e->project_id, $e->color) }}">
                        <span class="hv-ins-date">{{ \Carbon\Carbon::parse($e->date)->format('d/m') }}</span>
                        <span class="hv-ins-grow"><b>{{ $e->name }}</b><small>{{ $e->project }} · {{ $e->task }}</small></span>
                        <span class="hv-ins-num">{{ Worklog::format((float) $e->unit_amount) }}</span>
                    </div>
                @empty
                    <p class="hv-ins-muted">Nessuna registrazione.</p>
                @endforelse
            </section>
        </div>
    @endif
</x-filament-panels::page>
