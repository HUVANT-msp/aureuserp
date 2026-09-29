@php
    use Huvant\Tasks\Support\Board;
    use Huvant\Tasks\Support\Palette;
    use Huvant\Tasks\Support\TaskWork;
@endphp
<div>
    <x-filament::modal id="huvant-task-panel" slide-over width="2xl" :close-by-clicking-away="true">
        @if ($task)
            @php $color = Palette::project($task->project_id, $task->project?->color); @endphp
            <x-slot name="heading">
                <div class="hv-panel-head" style="--pc: {{ $color }}">
                    <p class="hv-panel-project"><span class="hv-dot"></span>{{ $task->project?->name ?? 'Senza progetto' }}
                        @if ($task->parent)<span class="hv-muted"> · sottotask di «{{ $task->parent->title }}»</span>@endif
                    </p>
                    <h2>{{ $task->title }}</h2>
                </div>
            </x-slot>

            <div class="hv-panel" style="--pc: {{ $color }}">
                {{-- Quick actions --}}
                <div class="hv-panel-actions">
                    @if ($isAssignee)
                        @if ($running && (int) $running->task_id === $task->id)
                            <span class="hv-running"><span class="hv-pulse"></span>Timer in corso su questo task</span>
                        @else
                            <x-filament::button size="sm" icon="heroicon-m-play" wire:click="startTimer">Avvia timer</x-filament::button>
                        @endif
                    @else
                        <x-filament::button size="sm" color="gray" icon="heroicon-m-user-plus" wire:click="joinTask" :disabled="! $canEdit">Aggiungimi al task</x-filament::button>
                    @endif
                    @if ($docsUrl = rescue(fn () => \Webkul\Project\Filament\Resources\TaskResource::getUrl('documents', ['record' => $task]), null, false))
                        <x-filament::button size="sm" color="gray" icon="heroicon-m-paper-clip" tag="a" :href="$docsUrl">Documenti</x-filament::button>
                    @endif
                    <x-filament::button size="sm" color="gray" icon="heroicon-m-arrow-top-right-on-square" tag="a" :href="\Webkul\Project\Filament\Resources\TaskResource::getUrl('view', ['record' => $task])">Scheda completa</x-filament::button>
                </div>

                {{-- Properties --}}
                <dl class="hv-props">
                    <dt>Stato</dt>
                    <dd>
                        <div class="hv-stage-picker" role="radiogroup" aria-label="Stato">
                            @foreach ($stages as $stage)
                                <button type="button" role="radio" aria-checked="{{ $task->stage_id === $stage->id ? 'true' : 'false' }}"
                                        class="hv-stage hv-stage-{{ Board::stageKind($stage->name) }}" wire:click="setStage({{ $stage->id }})" @disabled(! $canEdit)>
                                    {{ Board::stageLabel($stage->name) }}
                                </button>
                            @endforeach
                        </div>
                    </dd>

                    <dt>Persone</dt>
                    <dd class="hv-people">
                        @forelse ($task->users as $person)
                            <span class="hv-person">
                                <span class="hv-avatar" style="--ac: {{ Palette::person($person->id) }}">{{ Palette::initials($person->name) }}</span>
                                {{ $person->name }}
                                @if ($canEdit)
                                    <button type="button" wire:click="unassign({{ $person->id }})" aria-label="Togli {{ $person->name }}">
                                        <x-filament::icon icon="heroicon-m-x-mark" class="h-3.5 w-3.5" />
                                    </button>
                                @endif
                            </span>
                        @empty
                            <span class="hv-muted">Nessuno assegnato</span>
                        @endforelse
                        @if ($canEdit && $candidates->isNotEmpty())
                            <select class="hv-add-person" wire:change="assign($event.target.value)" aria-label="Aggiungi persona">
                                <option value="">+ Aggiungi</option>
                                @foreach ($candidates as $candidate)
                                    <option value="{{ $candidate->id }}">{{ $candidate->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </dd>

                    <dt>Date</dt>
                    <dd class="hv-dates">
                        <label>Inizio
                            <input type="date" value="{{ $task->huvant_start_date ? \Carbon\Carbon::parse($task->huvant_start_date)->toDateString() : '' }}"
                                   wire:change="setDate('huvant_start_date', $event.target.value)" @disabled(! $canEdit) />
                        </label>
                        <label>Scadenza
                            <input type="date" value="{{ $task->deadline?->toDateString() }}"
                                   wire:change="setDate('deadline', $event.target.value)" @disabled(! $canEdit) @class(['is-overdue' => Board::isOverdue($task)]) />
                        </label>
                        <button type="button" @class(['hv-prio', 'is-on' => $task->priority]) wire:click="togglePriority" @disabled(! $canEdit)
                                aria-pressed="{{ $task->priority ? 'true' : 'false' }}">
                            <x-filament::icon :icon="$task->priority ? 'heroicon-s-star' : 'heroicon-o-star'" class="h-4 w-4" />Priorità
                        </button>
                    </dd>
                </dl>

                @if (filled($task->description))
                    <section class="hv-section">
                        <h3>Descrizione</h3>
                        <div class="hv-desc fi-prose">{!! str($task->description)->sanitizeHtml() !!}</div>
                    </section>
                @endif

                {{-- Subtasks --}}
                <section class="hv-section">
                    <h3>Sottotask <small>{{ $task->subTasks->where('state', \Webkul\Project\Enums\TaskState::DONE)->count() }}/{{ $task->subTasks->count() }}</small></h3>
                    <ul class="hv-subtasks">
                        @foreach ($task->subTasks as $sub)
                            @php $isDone = $sub->state === \Webkul\Project\Enums\TaskState::DONE; @endphp
                            <li wire:key="sub-{{ $sub->id }}" @class(['is-done' => $isDone])>
                                <button type="button" class="hv-check" wire:click="toggleSubtask({{ $sub->id }})" @disabled(! $canEdit)
                                        aria-pressed="{{ $isDone ? 'true' : 'false' }}" aria-label="Completato">
                                    @if ($isDone)<x-filament::icon icon="heroicon-m-check" class="h-3.5 w-3.5" />@endif
                                </button>
                                <button type="button" class="hv-sub-title" wire:click="open({{ $sub->id }})">{{ $sub->title }}</button>
                                <span class="hv-avatars">
                                    @foreach ($sub->users->take(3) as $person)
                                        <span class="hv-avatar small" style="--ac: {{ Palette::person($person->id) }}" title="{{ $person->name }}">{{ Palette::initials($person->name) }}</span>
                                    @endforeach
                                </span>
                                @if ((float) $sub->total_hours_spent > 0)<span class="hv-meta">{{ TaskWork::hours((float) $sub->total_hours_spent) }}</span>@endif
                            </li>
                        @endforeach
                    </ul>
                    @if ($canEdit)
                        <form class="hv-inline-form" wire:submit="addSubtask">
                            <input type="text" wire:model="newSubtask" placeholder="Nuovo sottotask…" maxlength="255" aria-label="Nuovo sottotask" />
                            <x-filament::button type="submit" size="sm" color="gray">Aggiungi</x-filament::button>
                        </form>
                    @endif
                </section>

                {{-- Work: who, how long, what --}}
                <section class="hv-section">
                    <h3>Lavoro <small>{{ TaskWork::hours($work['total']) }} h in totale</small></h3>
                    @include('huvant-tasks::partials.work-people', ['work' => $work])

                    @if ($isAssignee)
                        <form class="hv-entry-form" wire:submit="logTime">
                            <input type="date" wire:model="entry.date" max="{{ now()->toDateString() }}" aria-label="Giorno" />
                            <input type="text" wire:model="entry.hours" placeholder="1:30" inputmode="decimal" aria-label="Ore" class="hv-hours" />
                            <input type="text" wire:model="entry.description" placeholder="Cosa hai fatto? (obbligatorio)" maxlength="255" aria-label="Descrizione" required class="hv-grow" />
                            <x-filament::button type="submit" size="sm">Registra</x-filament::button>
                        </form>
                    @else
                        <p class="hv-muted small">Per registrare ore su questo task aggiungiti come assegnatario.</p>
                    @endif

                    @include('huvant-tasks::partials.work-entries', ['entries' => $work['entries'], 'task' => $task])
                </section>
            </div>
        @else
            <x-slot name="heading">Task</x-slot>
            <p class="hv-muted">Caricamento…</p>
        @endif
    </x-filament::modal>
</div>
