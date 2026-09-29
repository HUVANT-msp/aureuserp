@php
    use Huvant\Tasks\Filament\Pages\ManageProjectNotes as Notes;
@endphp
<x-filament-panels::page>
    @if ($error)
        <p class="hv-notes-muted">Meetings are not reachable right now. Try again in a moment.</p>
    @else
        <div class="hv-notes">
            <div class="hv-notes-top">
                <div class="hv-notes-kpis">
                    <span class="kpi-open"><b>{{ $counts['open_point'] }}</b> open {{ $counts['open_point'] === 1 ? 'question' : 'questions' }}</span>
                    <span class="kpi-risk"><b>{{ $counts['risk'] }}</b> open {{ $counts['risk'] === 1 ? 'risk' : 'risks' }}</span>
                    <span class="kpi-decision"><b>{{ $counts['decision'] }}</b> {{ $counts['decision'] === 1 ? 'decision' : 'decisions' }}</span>
                </div>
                <div class="hv-notes-filters">
                    <div class="hv-notes-search">
                        <x-filament::icon icon="heroicon-m-magnifying-glass" class="h-4 w-4" />
                        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search notes" aria-label="Search notes" />
                    </div>
                    <select wire:model.live="meeting" aria-label="Meeting">
                        <option value="">All meetings</option>
                        @foreach ($meetings as $m)
                            <option value="{{ $m['id'] }}">{{ $m['title'] }}{{ $m['date'] ? ' · '.\Carbon\Carbon::parse($m['date'])->format('j M') : '' }}</option>
                        @endforeach
                    </select>
                    <label class="hv-notes-check"><input type="checkbox" wire:model.live="showClosed" /> Show closed</label>
                </div>
            </div>

            <div class="hv-notes-grid">
                <div class="hv-notes-main">
                    @foreach ($sections as $kind => $section)
                        @continue($section['open']->isEmpty() && $section['closed']->isEmpty() && in_array($kind, ['idea', 'key_point'], true))
                        <section class="hv-notes-section kind-{{ $kind }}">
                            <header>
                                <h2>{{ $section['title'] }}</h2>
                                <span>{{ $section['open']->count() }}</span>
                                @if ($section['closed']->isNotEmpty())<small>{{ $section['closed']->count() }} closed</small>@endif
                            </header>
                            @if ($section['open']->isEmpty() && (! $showClosed || $section['closed']->isEmpty()))
                                <p class="hv-notes-muted">{{ $section['empty'] }}</p>
                            @endif
                            <ul>
                                @foreach ($showClosed ? $section['open']->concat($section['closed']) : $section['open'] as $note)
                                    @php $closed = $note['status'] !== 'open'; @endphp
                                    <li @class(['hv-note', 'is-closed' => $closed]) wire:key="{{ $note['key'] }}">
                                        <div class="hv-note-body">
                                            <p>{{ $note['text'] }}</p>
                                            <small>
                                                <a href="{{ $note['url'] }}">{{ $note['meeting'] }}</a>
                                                @if ($note['date']) · {{ \Carbon\Carbon::parse($note['date'])->format('j M Y') }}@endif
                                                @if ($note['owner']) · {{ $note['owner'] }}@endif
                                                @if ($note['due']) · due {{ $note['due'] }}@endif
                                            </small>
                                            @if ($closed)
                                                <p class="hv-note-outcome">
                                                    <b>{{ Notes::ACTIONS[$note['status']][1] ?? ucfirst($note['status']) }}</b>
                                                    @if ($note['status_by']) by {{ $note['status_by'] }}@endif
                                                    @if ($note['status_at']) · {{ \Carbon\Carbon::parse($note['status_at'])->format('j M') }}@endif
                                                    @if ($note['status_note'])<span>“{{ $note['status_note'] }}”</span>@endif
                                                </p>
                                            @endif
                                        </div>
                                        <div class="hv-note-actions">
                                            @if ($closed)
                                                <button type="button" wire:click="setState('{{ $note['key'] }}', '{{ $note['kind'] }}', 'open')">
                                                    <x-filament::icon icon="heroicon-m-arrow-path" class="h-4 w-4" />Reopen
                                                </button>
                                            @else
                                                @foreach ($note['actions'] as $action)
                                                    <button type="button" @class(['is-primary' => $loop->first])
                                                            wire:click="askState('{{ $note['key'] }}', '{{ $note['kind'] }}', '{{ $action }}')">
                                                        <x-filament::icon :icon="Notes::ACTIONS[$action][2] ?? 'heroicon-m-check'" class="h-4 w-4" />{{ Notes::ACTIONS[$action][0] ?? ucfirst($action) }}
                                                    </button>
                                                @endforeach
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>

                <aside class="hv-notes-side">
                    <h3>Meetings</h3>
                    <p class="hv-notes-muted small">Notes come from these meetings. A meeting in the wrong project can be moved, with all its notes.</p>
                    @forelse ($meetings as $m)
                        <div class="hv-notes-meeting" x-data="{ moving: false }">
                            <div>
                                <x-filament::icon :icon="$m['source'] === 'canvas' ? 'heroicon-m-presentation-chart-bar' : 'heroicon-m-document-text'" class="h-4 w-4" />
                                <span class="hv-notes-grow"><b>{{ $m['title'] }}</b><small>{{ $m['date'] ? \Carbon\Carbon::parse($m['date'])->format('j M Y') : '' }}{{ $m['classified'] ? '' : ' · not filed yet' }}</small></span>
                                <button type="button" x-on:click="moving = ! moving" :aria-expanded="moving">Move</button>
                            </div>
                            <select x-show="moving" x-cloak aria-label="Move {{ $m['title'] }} to project"
                                    x-on:change="if ($event.target.value) $wire.moveMeeting('{{ $m['source'] }}', '{{ $m['id'] }}', Number($event.target.value))">
                                <option value="">Move to project…</option>
                                @foreach ($projects as $project)
                                    @continue($project->id === $this->record->id)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @empty
                        <p class="hv-notes-muted">No meetings for this project yet.</p>
                    @endforelse
                </aside>
            </div>
        </div>

        <x-filament::modal id="hv-note-state" width="md">
            <x-slot name="heading">{{ Notes::ACTIONS[$noteStatus][0] ?? 'Update' }}</x-slot>
            <form class="hv-note-form" wire:submit="confirmState">
                <label>How was it settled? <span>(optional)</span>
                    <textarea wire:model="noteText" rows="3" maxlength="2000" placeholder="e.g. budget approved by the client on 3 Oct"></textarea>
                </label>
                <div><x-filament::button type="submit">{{ Notes::ACTIONS[$noteStatus][0] ?? 'Save' }}</x-filament::button></div>
            </form>
        </x-filament::modal>
    @endif
</x-filament-panels::page>
