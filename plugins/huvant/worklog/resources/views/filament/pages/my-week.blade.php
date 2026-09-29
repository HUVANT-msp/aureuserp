@php
    use Huvant\Worklog\Support\Worklog;
    $fmt = fn (float $h): string => $h > 0 ? Worklog::format($h) : '';
    $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $longDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $clock = fn (int $m): string => sprintf('%d:%02d', intdiv($m, 60), $m % 60);
@endphp
<x-filament-panels::page>
    <div class="hv-wl">
        <div class="hv-wl-toolbar">
            <div class="hv-wl-weeknav">
                <x-filament::icon-button icon="heroicon-m-chevron-left" wire:click="shiftWeek(-1)" label="Previous week" color="gray" />
                <span class="hv-wl-range">{{ Worklog::weekLabel($monday) }}</span>
                <x-filament::icon-button icon="heroicon-m-chevron-right" wire:click="shiftWeek(1)" label="Next week" color="gray" />
                @if ($monday->toDateString() !== \Carbon\CarbonImmutable::today()->startOfWeek()->toDateString())
                    <x-filament::button size="sm" color="gray" wire:click="thisWeek">Today</x-filament::button>
                @endif
            </div>
            <div class="hv-wl-seg" role="tablist" aria-label="View">
                <button type="button" role="tab" aria-selected="{{ $tab === 'week' ? 'true' : 'false' }}" wire:click="setTab('week')">
                    <x-filament::icon icon="heroicon-m-table-cells" class="h-4 w-4" />Week
                </button>
                <button type="button" role="tab" aria-selected="{{ $tab === 'timeline' ? 'true' : 'false' }}" wire:click="setTab('timeline')">
                    <x-filament::icon icon="heroicon-m-chart-bar" class="h-4 w-4" />Timeline
                </button>
            </div>
            @if ($tab === 'week')
                <x-filament::button size="sm" color="gray" icon="heroicon-m-user-plus" wire:click="$toggle('joining')">Join a task</x-filament::button>
            @endif
        </div>

        @if ($tab === 'week')
            @if ($joining)
                <div class="hv-wl-join">
                    <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                        <x-filament::input type="search" wire:model.live.debounce.300ms="search" placeholder="Search the tasks of your projects" autofocus />
                    </x-filament::input.wrapper>
                    <ul>
                        @forelse ($joinable as $task)
                            <li>
                                <span><strong>{{ $task->title }}</strong> <small>{{ $task->project?->name ?? 'No project' }}</small></span>
                                <x-filament::button size="xs" wire:click="join({{ $task->id }})">Join</x-filament::button>
                            </li>
                        @empty
                            <li class="hv-wl-muted">No tasks found.</li>
                        @endforelse
                    </ul>
                </div>
            @endif

            <div class="hv-wl-tablewrap">
                <table class="hv-wl-table">
                    <thead>
                        <tr>
                            <th class="hv-wl-taskcol">Task</th>
                            @foreach ($grid['days'] as $i => $day)
                                <th @class(['hv-wl-today' => $day === $today, 'hv-wl-weekend' => $i >= 5])>
                                    {{ $dayNames[$i] }} <span>{{ \Carbon\CarbonImmutable::parse($day)->format('j') }}</span>
                                </th>
                            @endforeach
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($grid['rows'] as $row)
                            <tr wire:key="row-{{ $row['id'] }}">
                                <td class="hv-wl-taskcol">
                                    <div class="hv-wl-task">
                                        <button type="button" class="hv-wl-play" wire:click="start({{ $row['id'] }})"
                                                title="Start the timer" @disabled($running)>
                                            <x-filament::icon :icon="$running && $running->task_id == $row['id'] ? 'heroicon-m-clock' : 'heroicon-m-play'" class="h-4 w-4" />
                                        </button>
                                        <a href="{{ \Webkul\Project\Filament\Resources\TaskResource::getUrl('view', ['record' => $row['id']]) }}">
                                            <span class="hv-wl-title">{{ $row['title'] }}</span>
                                            <span class="hv-wl-project">{{ $row['project'] }}</span>
                                        </a>
                                    </div>
                                </td>
                                @foreach ($grid['days'] as $i => $day)
                                    <td @class(['hv-wl-today' => $day === $today, 'hv-wl-weekend' => $i >= 5])>
                                        @if ($day <= $today)
                                            <button type="button" class="hv-wl-cell" wire:click="openDay({{ $row['id'] }}, '{{ $day }}')"
                                                    aria-label="{{ $row['title'] }}, {{ $longDays[$i] }}: {{ $fmt($row['hours'][$day]) ?: 'no time' }}">
                                                {{ $fmt($row['hours'][$day]) ?: '+' }}
                                            </button>
                                        @else
                                            <span class="hv-wl-muted">·</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="hv-wl-total">{{ Worklog::format(array_sum($row['hours'])) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="hv-wl-emptyrow">
                                    You are not assigned to any open task. Use “Join a task” to start logging time.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="hv-wl-taskcol">Total</th>
                            @foreach ($grid['days'] as $i => $day)
                                @php
                                    $logged = $grid['totals'][$day];
                                    $expected = $grid['expected'][$day];
                                @endphp
                                <td @class([
                                    'hv-wl-weekend' => $i >= 5,
                                    'hv-wl-short' => $expected > 0 && $day < $today && $logged < $expected,
                                ]) title="Expected {{ Worklog::format($expected) }} h">
                                    {{ Worklog::format($logged) }}<small>/ {{ Worklog::format($expected) }}</small>
                                </td>
                            @endforeach
                            <td class="hv-wl-total">
                                {{ Worklog::format(array_sum($grid['totals'])) }}<small>/ {{ Worklog::format(array_sum($grid['expected'])) }}</small>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <p class="hv-wl-hint">Click a day to see and add entries: each entry says what you did.</p>
        @else
            @php
                $span = max(60, $timeline['to'] - $timeline['from']);
                $pos = fn (int $m): string => round(($m - $timeline['from']) / $span * 100, 3).'%';
            @endphp
            @if ($timeline['projects'])
                <div class="hv-tl-summary">
                    <div class="hv-tl-stack" role="img" aria-label="Hours by project">
                        @foreach ($timeline['projects'] as $project)
                            <span style="flex: {{ $project['hours'] }}; background: {{ $project['color'] }}" title="{{ $project['name'] }} · {{ Worklog::format($project['hours']) }} h"></span>
                        @endforeach
                    </div>
                    <ul class="hv-tl-legend">
                        @foreach ($timeline['projects'] as $project)
                            <li><span class="hv-tl-swatch" style="background: {{ $project['color'] }}"></span>{{ $project['name'] }} <b>{{ Worklog::format($project['hours']) }}</b></li>
                        @endforeach
                        <li class="hv-tl-total">Total <b>{{ Worklog::format($timeline['total']) }}</b></li>
                    </ul>
                </div>
            @endif

            <div class="hv-gantt">
                <div class="hv-gantt-row hv-gantt-head">
                    <span class="hv-gantt-day"></span>
                    <div class="hv-gantt-track">
                        @for ($m = $timeline['from']; $m <= $timeline['to']; $m += 60)
                            <span class="hv-gantt-hour" style="left: {{ $pos($m) }}">{{ intdiv($m, 60) }}</span>
                        @endfor
                    </div>
                    <span class="hv-gantt-sum">Hours</span>
                </div>
                @foreach ($timeline['days'] as $i => $day)
                    <div @class(['hv-gantt-row', 'is-today' => $day['date'] === $today, 'is-weekend' => $i >= 5])>
                        <span class="hv-gantt-day">{{ $dayNames[$i] }} <b>{{ \Carbon\CarbonImmutable::parse($day['date'])->format('j') }}</b></span>
                        <div class="hv-gantt-track">
                            @for ($m = $timeline['from']; $m <= $timeline['to']; $m += 60)
                                <span class="hv-gantt-grid" style="left: {{ $pos($m) }}"></span>
                            @endfor
                            @foreach ($day['blocks'] as $block)
                                <span class="hv-gantt-block" style="left: {{ $pos($block['from']) }}; width: {{ round(($block['to'] - $block['from']) / $span * 100, 3) }}%; --pc: {{ $block['color'] }}"
                                      title="{{ $clock($block['from']) }}–{{ $clock($block['to']) }} · {{ $block['project'] }} · {{ $block['task'] }} — {{ $block['description'] }}">
                                    <span>{{ $block['task'] }}</span>
                                </span>
                            @endforeach
                        </div>
                        <span class="hv-gantt-sum">{{ $day['total'] > 0 ? Worklog::format($day['total']) : '' }}</span>
                    </div>
                    @if ($day['loose'])
                        <div class="hv-gantt-loose">
                            <span class="hv-gantt-day"></span>
                            <div>
                                @foreach ($day['loose'] as $item)
                                    <span class="hv-gantt-chip" style="--pc: {{ $item['color'] }}" title="{{ $item['project'] }} · {{ $item['task'] }} — {{ $item['description'] }}">
                                        <b>{{ Worklog::format($item['hours']) }}</b> {{ $item['task'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <p class="hv-wl-hint">Blocks come from the timer or from entries with a start time; the other entries sit under their day.</p>
        @endif
    </div>

    <x-filament::modal id="hv-day-entries" width="xl">
        <x-slot name="heading">
            {{ $cellTitle ?? 'Time entries' }}
        </x-slot>
        <x-slot name="description">
            @if ($cellDate)
                {{ ucfirst($longDays[\Carbon\CarbonImmutable::parse($cellDate)->dayOfWeekIso - 1]) }} {{ \Carbon\CarbonImmutable::parse($cellDate)->format('d/m/Y') }}
            @endif
        </x-slot>

        <div class="hv-day">
            @if ($edits)
                <ul class="hv-day-list">
                    @foreach ($edits as $id => $edit)
                        <li wire:key="edit-{{ $id }}">
                            <input type="text" wire:model="edits.{{ $id }}.hours" class="hv-day-hours" aria-label="Hours" inputmode="decimal" />
                            <input type="text" wire:model="edits.{{ $id }}.description" class="hv-day-desc" aria-label="Description" maxlength="255" />
                            <x-filament::icon-button icon="heroicon-m-check" color="gray" wire:click="saveEntry({{ $id }})" label="Save" />
                            <x-filament::icon-button icon="heroicon-m-trash" color="danger" wire:click="deleteEntry({{ $id }})" wire:confirm="Delete this entry?" label="Delete" />
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="hv-wl-muted">No time logged on this day.</p>
            @endif

            <form class="hv-day-new" wire:submit="addEntry">
                <label>From <input type="time" wire:model="entry.from" aria-label="Start time (optional)" /></label>
                <label>Hours <input type="text" wire:model="entry.hours" placeholder="1:30" inputmode="decimal" required /></label>
                <label class="hv-grow">What did you do <input type="text" wire:model="entry.description" placeholder="Required" maxlength="255" required /></label>
                <x-filament::button type="submit">Add</x-filament::button>
            </form>
        </div>
    </x-filament::modal>
</x-filament-panels::page>
