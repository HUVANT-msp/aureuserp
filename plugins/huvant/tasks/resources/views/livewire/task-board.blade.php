@php
    use Huvant\Tasks\Support\Board;
    use Huvant\Tasks\Support\Palette;
    use Huvant\Tasks\Support\TaskWork;
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
@endphp
<div class="hv-board" x-data="{ dragging: null, over: null, filtersOpen: false }">
    {{-- Toolbar: views and filters --}}
    <div class="hv-board-toolbar">
        <div class="hv-seg" role="tablist" aria-label="View">
            @foreach (['kanban' => ['Kanban', 'heroicon-m-view-columns'], 'timeline' => ['Timeline', 'heroicon-m-chart-bar'], 'list' => ['List', 'heroicon-m-list-bullet']] as $key => [$label, $icon])
                <button type="button" role="tab" aria-selected="{{ $view === $key ? 'true' : 'false' }}" wire:click="setView('{{ $key }}')">
                    <x-filament::icon :icon="$icon" class="h-4 w-4" />{{ $label }}
                </button>
            @endforeach
        </div>

        <div class="hv-board-search">
            <x-filament::icon icon="heroicon-m-magnifying-glass" class="h-4 w-4" />
            <input type="search" placeholder="Search tasks" wire:model.live.debounce.300ms="filters.search" aria-label="Search tasks" />
        </div>

        <div class="hv-board-quick">
            @if ($projectId || $assigneeId)
                {{ $this->newTaskAction }}
            @endif
            @unless ($assigneeId)
            <select wire:model.live="filters.assignee" aria-label="Assignee">
                <option value="all">Everyone</option>
                <option value="me">Only mine</option>
                <option value="none">Unassigned</option>
                <optgroup label="Person">
                    @foreach ($people as $person)
                        <option value="{{ $person->id }}">{{ $person->name }}</option>
                    @endforeach
                </optgroup>
            </select>
            @endunless
            <select wire:model.live="filters.due" aria-label="Deadline">
                <option value="all">Any deadline</option>
                <option value="overdue">Overdue</option>
                <option value="week">Within 7 days</option>
                <option value="none">No deadline</option>
            </select>
            <button type="button" class="hv-chip-btn" x-on:click="filtersOpen = ! filtersOpen" :aria-expanded="filtersOpen">
                <x-filament::icon icon="heroicon-m-funnel" class="h-4 w-4" />
                Filters @if ($active)<span class="hv-count">{{ $active }}</span>@endif
            </button>
            @if ($active)
                <button type="button" class="hv-link-btn" wire:click="resetFilters">Reset</button>
            @endif
        </div>
    </div>

    <div class="hv-board-filters" x-show="filtersOpen" x-cloak>
        @if ($projects->isNotEmpty())
            <fieldset>
                <legend>Projects</legend>
                <div class="hv-project-chips">
                    @foreach ($projects as $project)
                        <label class="hv-project-chip" style="--pc: {{ Palette::project($project->id, $project->color) }}">
                            <input type="checkbox" value="{{ $project->id }}" wire:model.live="filters.projects" />
                            <span class="hv-dot"></span>{{ $project->name }}
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @endif
        <fieldset class="hv-toggles">
            <legend>Also show</legend>
            <label><input type="checkbox" wire:model.live="filters.subtasks" /> Subtasks</label>
            <label><input type="checkbox" wire:model.live="filters.cancelled" /> Cancelled</label>
        </fieldset>
    </div>

    {{-- Kanban --}}
    @if ($view === 'kanban')
        <div class="hv-kanban" wire:loading.class="is-loading" wire:target="filters,setView,resetFilters">
            @foreach ($columns as $column)
                <section class="hv-col hv-col-{{ $column['kind'] }}" wire:key="col-{{ $column['key'] }}"
                         :class="{ 'is-over': over === @js($column['key']) }"
                         x-on:dragover.prevent="over = @js($column['key'])"
                         x-on:dragleave.self="over = null"
                         x-on:drop.prevent="
                            if (dragging && @js($column['name']) !== '') {
                                const card = document.querySelector('[data-task=\'' + dragging + '\']');
                                if (card) $el.querySelector('.hv-col-list').prepend(card);
                                $wire.moveTask(dragging, @js($column['name']));
                            }
                            dragging = null; over = null">
                    <header>
                        <span class="hv-col-mark"></span>
                        <h3>{{ $column['label'] }}</h3>
                        <span class="hv-col-count">{{ $column['count'] }}</span>
                        @if ($projectId && $column['name'] !== '')
                            {{ ($this->addTaskAction)(['stage' => $column['name']]) }}
                        @endif
                    </header>
                    <div class="hv-col-list">
                        @forelse ($column['tasks'] as $task)
                            @php
                                $done = $task->subTasks->where('state', \Webkul\Project\Enums\TaskState::DONE)->count();
                                $overdue = Board::isOverdue($task);
                            @endphp
                            <article class="hv-card" data-task="{{ $task->id }}" wire:key="card-{{ $task->id }}"
                                     style="--pc: {{ Palette::project($task->project_id, $task->project?->color) }}"
                                     draggable="true" tabindex="0" role="button"
                                     aria-label="{{ $task->title }}"
                                     x-on:dragstart="dragging = {{ $task->id }}; $event.dataTransfer.effectAllowed = 'move'; $event.dataTransfer.setData('text/plain', '{{ $task->id }}')"
                                     x-on:dragend="dragging = null; over = null"
                                     :class="{ 'is-dragging': dragging === {{ $task->id }} }"
                                     x-on:click="$dispatch('huvant-open-task', { taskId: {{ $task->id }} })"
                                     x-on:keydown.enter="$dispatch('huvant-open-task', { taskId: {{ $task->id }} })">
                                @unless ($projectId)
                                    <p class="hv-card-project"><span class="hv-dot"></span>{{ $task->project?->name ?? 'No project' }}</p>
                                @endunless
                                <h4>
                                    @if ($task->priority)<x-filament::icon icon="heroicon-s-star" class="hv-star h-4 w-4" />@endif
                                    {{ $task->title }}
                                </h4>
                                @if ($task->parent_id)
                                    <p class="hv-card-parent">Subtask</p>
                                @endif
                                <footer>
                                    <span class="hv-avatars">
                                        @foreach ($task->users->take(3) as $person)
                                            <span class="hv-avatar" style="--ac: {{ Palette::person($person->id) }}" title="{{ $person->name }}">{{ Palette::initials($person->name) }}</span>
                                        @endforeach
                                        @if ($task->users->count() > 3)<span class="hv-avatar more">+{{ $task->users->count() - 3 }}</span>@endif
                                    </span>
                                    @if ($task->subTasks->isNotEmpty())
                                        <span class="hv-meta" title="Subtasks done"><x-filament::icon icon="heroicon-m-check-circle" class="h-3.5 w-3.5" />{{ $done }}/{{ $task->subTasks->count() }}</span>
                                    @endif
                                    @if ((float) $task->total_hours_spent > 0)
                                        <span class="hv-meta" title="Time logged"><x-filament::icon icon="heroicon-m-clock" class="h-3.5 w-3.5" />{{ TaskWork::hours((float) $task->total_hours_spent) }}</span>
                                    @endif
                                    @if ($label = Board::dueLabel($task))
                                        <span @class(['hv-due', 'is-overdue' => $overdue])><x-filament::icon icon="heroicon-m-calendar" class="h-3.5 w-3.5" />{{ $label }}</span>
                                    @endif
                                </footer>
                            </article>
                        @empty
                            <p class="hv-col-empty">Drop a task here</p>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>
    @endif

    {{-- Timeline --}}
    @if ($view === 'timeline')
        @php
            $dayW = ['4' => 40, '6' => 28, '12' => 14, '26' => 7][(string) $weeks] ?? 28;
            $todayOffset = $start->diffInDays($today, false);
        @endphp
        <div class="hv-timeline-tools">
            <div class="hv-weeknav">
                <x-filament::icon-button icon="heroicon-m-chevron-left" color="gray" wire:click="shiftTimeline(-{{ max(1, intdiv($weeks, 2)) }})" label="Back" />
                <span>{{ $start->day }} {{ $months[$start->month - 1] }} – {{ $end->day }} {{ $months[$end->month - 1] }} {{ $end->year }}</span>
                <x-filament::icon-button icon="heroicon-m-chevron-right" color="gray" wire:click="shiftTimeline({{ max(1, intdiv($weeks, 2)) }})" label="Forward" />
            </div>
            <div class="hv-seg small">
                @foreach ([4 => '4 weeks', 6 => '6 weeks', 12 => '3 months', 26 => '6 months'] as $w => $label)
                    <button type="button" aria-selected="{{ $weeks === $w ? 'true' : 'false' }}" wire:click="zoom({{ $w }})">{{ $label }}</button>
                @endforeach
            </div>
        </div>
        <div class="hv-timeline" style="--day: {{ $dayW }}px; --days: {{ $days }}">
            <div class="hv-tl-head">
                <div class="hv-tl-label">Task</div>
                <div class="hv-tl-scale">
                    @for ($i = 0; $i < $days; $i++)
                        @php $d = $start->addDays($i); @endphp
                        <span @class(['hv-tl-day', 'is-weekend' => $d->isWeekend(), 'is-today' => $d->isSameDay($today), 'is-monday' => $d->isMonday()])>
                            @if ($d->isMonday() || $i === 0)<b>{{ $d->day }} {{ $months[$d->month - 1] }}</b>@elseif ($dayW >= 28){{ $d->day }}@endif
                        </span>
                    @endfor
                </div>
            </div>
            @forelse ($rows as $projectName => $tasks)
                @php $color = Palette::project($tasks->first()->project_id, $tasks->first()->project?->color); @endphp
                <div class="hv-tl-group" style="--pc: {{ $color }}"><span class="hv-dot"></span>{{ $projectName }} <small>{{ $tasks->count() }}</small></div>
                @foreach ($tasks as $task)
                    @php
                        $from = Board::startDate($task);
                        $to = $task->deadline ? \Carbon\CarbonImmutable::parse($task->deadline)->startOfDay() : null;
                        $a = max(0, $start->diffInDays($from, false));
                        $b = $to ? min($days - 1, $start->diffInDays($to, false)) : min($days - 1, $a + 2);
                        $b = max($a, $b);
                        $state = $task->state?->value;
                    @endphp
                    <div class="hv-tl-row" wire:key="tl-{{ $task->id }}" style="--pc: {{ $color }}">
                        <button type="button" class="hv-tl-label" x-on:click="$dispatch('huvant-open-task', { taskId: {{ $task->id }} })" title="{{ $task->title }}">
                            {{ $task->title }}
                        </button>
                        <div class="hv-tl-track">
                            @if ($todayOffset >= 0 && $todayOffset < $days)<span class="hv-tl-today" style="left: calc({{ $todayOffset }} * var(--day) + var(--day) / 2)"></span>@endif
                            <button type="button"
                                    @class(['hv-tl-bar', 'is-done' => $state === 'done', 'is-overdue' => Board::isOverdue($task), 'is-open' => ! $to, 'is-clipped-start' => $from->lt($start)])
                                    style="left: calc({{ $a }} * var(--day)); width: calc({{ $b - $a + 1 }} * var(--day))"
                                    title="{{ $task->title }} · {{ $from->format('d/m') }} → {{ $to ? $to->format('d/m') : 'no deadline' }}"
                                    x-on:click="$dispatch('huvant-open-task', { taskId: {{ $task->id }} })">
                                <span>{{ $task->title }}</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            @empty
                <p class="hv-empty">No tasks in this period.</p>
            @endforelse
        </div>
        <p class="hv-hint">Bars run from the planned start (or creation) to the deadline; dashed means no deadline. Open a task to set its dates.</p>
    @endif

    {{-- List --}}
    @if ($view === 'list')
        <div class="hv-list-wrap">
            <table class="hv-list">
                <thead>
                    <tr>
                        @foreach (['title' => 'Task', 'project' => 'Project', 'stage' => 'Stage', 'people' => 'People', 'deadline' => 'Deadline', 'hours' => 'Hours'] as $key => $label)
                            <th scope="col">
                                @if ($key !== 'people')
                                    <button type="button" wire:click="sortBy('{{ $key }}')">{{ $label }}
                                        @if ($sort === $key)<x-filament::icon :icon="$sortDesc ? 'heroicon-m-chevron-down' : 'heroicon-m-chevron-up'" class="h-3.5 w-3.5" />@endif
                                    </button>
                                @else
                                    {{ $label }}
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr wire:key="row-{{ $task->id }}" style="--pc: {{ Palette::project($task->project_id, $task->project?->color) }}"
                            tabindex="0" x-on:click="$dispatch('huvant-open-task', { taskId: {{ $task->id }} })" x-on:keydown.enter="$dispatch('huvant-open-task', { taskId: {{ $task->id }} })">
                            <td class="hv-list-title">
                                @if ($task->priority)<x-filament::icon icon="heroicon-s-star" class="hv-star h-4 w-4" />@endif
                                {{ $task->title }}
                                @if ($task->subTasks->isNotEmpty())<small>{{ $task->subTasks->where('state', \Webkul\Project\Enums\TaskState::DONE)->count() }}/{{ $task->subTasks->count() }}</small>@endif
                            </td>
                            <td><span class="hv-dot"></span>{{ $task->project?->name ?? '—' }}</td>
                            <td><span class="hv-stage hv-stage-{{ Board::stageKind((string) $task->stage?->name) }}">{{ $task->stage ? Board::stageLabel($task->stage->name) : '—' }}</span></td>
                            <td>
                                <span class="hv-avatars">
                                    @foreach ($task->users->take(4) as $person)
                                        <span class="hv-avatar" style="--ac: {{ Palette::person($person->id) }}" title="{{ $person->name }}">{{ Palette::initials($person->name) }}</span>
                                    @endforeach
                                </span>
                            </td>
                            <td>@if ($label = Board::dueLabel($task))<span @class(['hv-due', 'is-overdue' => Board::isOverdue($task)])>{{ $label }}</span>@else<span class="hv-muted">—</span>@endif</td>
                            <td class="hv-num">{{ (float) $task->total_hours_spent > 0 ? TaskWork::hours((float) $task->total_hours_spent) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="hv-empty">No tasks match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <x-filament-actions::modals />
</div>
