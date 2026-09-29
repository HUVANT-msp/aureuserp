<div class="hv-timer">
    @if ($running)
        <div class="hv-timer-running"
             x-data="{ start: {{ \Illuminate\Support\Carbon::parse($running->started_at)->getTimestamp() }} * 1000, now: Date.now() }"
             x-init="setInterval(() => now = Date.now(), 1000)">
            <span class="hv-timer-dot" aria-hidden="true"></span>
            <span class="hv-timer-task" title="{{ $running->task_title }}">{{ $running->task_title }}</span>
            <span class="hv-timer-clock" x-text="(() => { const s = Math.max(0, Math.floor((now - start) / 1000)); return Math.floor(s / 3600) + ':' + String(Math.floor(s / 60) % 60).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0'); })()"></span>
            <button type="button" class="hv-timer-stop" wire:click="stop" title="Ferma e registra">
                <x-filament::icon icon="heroicon-m-stop" class="h-4 w-4" />
                <span class="sr-only">Ferma</span>
            </button>
        </div>
    @else
        <x-filament::dropdown placement="bottom-end" width="sm">
            <x-slot name="trigger">
                <button type="button" class="hv-timer-start" title="Avvia il timer su un tuo task">
                    <x-filament::icon icon="heroicon-m-play" class="h-4 w-4" />
                    <span>Timer</span>
                </button>
            </x-slot>

            <div class="hv-timer-menu">
                @if ($tasks->isEmpty())
                    <p class="hv-timer-empty">
                        Nessun task assegnato a te.
                        <a href="{{ \Huvant\Worklog\Filament\Pages\MyWeek::getUrl() }}">Aggiungiti a un task</a>
                    </p>
                @else
                    <input type="text" wire:model="note" maxlength="255" placeholder="Nota (facoltativa)" class="hv-timer-note" />
                    <ul>
                        @foreach ($tasks as $task)
                            <li>
                                <button type="button" wire:click="start({{ $task->id }})" x-on:click="close()">
                                    <span class="hv-timer-menu-title">{{ $task->title }}</span>
                                    <span class="hv-timer-menu-project">{{ $task->project?->name ?? 'Senza progetto' }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </x-filament::dropdown>
    @endif
</div>
