@php
    use Huvant\Worklog\Support\Worklog;
@endphp
<x-filament-panels::page>
    @if (! $person)
        <p class="hv-ins-muted">This employee is not linked to a user, so no task can be assigned to them.</p>
    @else
        <div class="hv-ins hv-load">
            <div class="hv-ins-kpis">
                <div class="hv-load-level is-{{ $load['level'] }}"><span>Workload</span><b>{{ $load['levelLabel'] }}</b></div>
                <div><span>Open tasks</span><b>{{ $load['open'] }}@if ($load['doing'])<small>{{ $load['doing'] }} in progress</small>@endif</b></div>
                <div @class(['is-short' => $load['overdue'] > 0])><span>Overdue</span><b>{{ $load['overdue'] }}</b></div>
                <div><span>Due in 7 days</span><b>{{ $load['dueSoon'] }}@if ($load['noDeadline'])<small>{{ $load['noDeadline'] }} without deadline</small>@endif</b></div>
                <div><span>Planned hours left</span><b>{{ Worklog::format($load['plannedLeft']) }}</b></div>
                <div @class(['is-short' => $load['weekHours'] < $load['weekTarget'] * 0.8])><span>Logged this week</span><b>{{ Worklog::format($load['weekHours']) }}<small>/ {{ Worklog::format($load['weekTarget']) }}</small></b></div>
            </div>
        </div>

        @livewire('huvant-task-board', ['assigneeId' => (int) $person->getKey()])
        @livewire('huvant-task-panel')
    @endif
</x-filament-panels::page>
