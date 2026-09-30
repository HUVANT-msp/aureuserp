@php
    use Huvant\Tasks\Filament\Pages\ManageProjectMeetings as Page;
@endphp
<x-filament-panels::page>
    @if ($error)
        <p class="hv-notes-muted">Meetings are not reachable right now. Try again in a moment.</p>
    @else
        <div class="hv-meet">
            <div class="hv-meet-top">
                <p class="hv-meet-count"><b>{{ $total }}</b> {{ $total === 1 ? 'meeting' : 'meetings' }}</p>
                <div class="hv-notes-search">
                    <x-filament::icon icon="heroicon-m-magnifying-glass" class="h-4 w-4" />
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search meetings" aria-label="Search meetings" />
                </div>
            </div>

            @if ($meetings->isEmpty())
                <p class="hv-notes-muted">{{ $total ? 'No meeting matches.' : 'No meetings for this project yet.' }}</p>
            @else
                <ul class="hv-meet-list">
                    @foreach ($meetings as $m)
                        @php
                            [$label, $tone] = Page::STATES[$m['state']] ?? ['—', 'processing'];
                            $date = $m['date'] ? \Carbon\Carbon::parse($m['date']) : null;
                            $pdf = $m['source'] === 'minutes' && $m['pdf'];
                            $href = $pdf
                                ? route('huvant.projects.minutes', ['project' => $this->record->getKey(), 'meeting' => $m['id']])
                                : ($m['source'] === 'minutes' ? '/riunioni/?meeting='.$m['id'] : '/riunioni/canvas?meeting_id='.$m['id']);
                        @endphp
                        <li>
                            <a class="hv-meet-row" href="{{ $href }}" @if (! $pdf) target="_blank" rel="noopener" @endif
                               title="{{ $pdf ? 'Download the minutes (PDF)' : 'Open in Meetings' }}">
                                <span class="hv-meet-date">
                                    @if ($date)
                                        <b>{{ $date->format('j') }}</b><small>{{ $date->format('M Y') }}</small>
                                    @else
                                        <small>No date</small>
                                    @endif
                                </span>
                                <span class="hv-meet-main">
                                    <b>{{ $m['title'] }}</b>
                                    <small>
                                        {{ $m['participants'] ? $m['participants'].' '.($m['participants'] === 1 ? 'participant' : 'participants') : 'No participants listed' }}
                                        @if ($m['duration_minutes']) · {{ $m['duration_minutes'] }} min @endif
                                        @if ($m['recorded_in_canvas']) · Meeting Canvas @endif
                                    </small>
                                </span>
                                <span class="hv-meet-state tone-{{ $tone }}">{{ $label }}</span>
                                <span class="hv-meet-action">
                                    @if ($pdf)
                                        <x-filament::icon icon="heroicon-m-arrow-down-tray" class="h-4 w-4" /> PDF
                                    @else
                                        <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="h-4 w-4" /> Open
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</x-filament-panels::page>
