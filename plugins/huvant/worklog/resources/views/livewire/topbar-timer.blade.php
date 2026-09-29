<div class="hv-timer">
    @if ($running)
        <div class="hv-timer-running"
             x-data="{ start: {{ \Illuminate\Support\Carbon::parse($running->started_at)->getTimestamp() }} * 1000, now: Date.now(), asking: false }"
             x-init="setInterval(() => now = Date.now(), 1000)"
             x-on:keydown.escape="asking = false">
            <span class="hv-timer-dot" aria-hidden="true"></span>
            <span class="hv-timer-task" title="{{ $running->task_title }}">{{ $running->task_title }}</span>
            <span class="hv-timer-clock" x-text="(() => { const s = Math.max(0, Math.floor((now - start) / 1000)); return Math.floor(s / 3600) + ':' + String(Math.floor(s / 60) % 60).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0'); })()"></span>
            <button type="button" class="hv-timer-stop" title="Stop and log" aria-label="Stop and log"
                    x-on:click="asking = true; $nextTick(() => $refs.what.focus())">
                <x-filament::icon icon="heroicon-m-stop" class="h-4 w-4" />
            </button>
            <form class="hv-timer-ask" x-show="asking" x-cloak x-transition.opacity x-on:click.outside="asking = false" wire:submit="stop">
                <label for="hv-timer-what">What did you do?</label>
                <textarea id="hv-timer-what" x-ref="what" wire:model="description" rows="3" maxlength="255" required
                          placeholder="Required"
                          x-init="$wire.description = $wire.description || @js((string) ($running->note ?? ''))"
                          x-on:keydown.enter.prevent="if (! $event.shiftKey) $el.form.requestSubmit()"></textarea>
                <div class="hv-timer-ask-actions">
                    <button type="button" class="hv-timer-discard" wire:click="discard" wire:confirm="Discard the timer without logging any time?">Discard timer</button>
                    <x-filament::button type="submit" size="sm">Stop and log</x-filament::button>
                </div>
            </form>
        </div>
    @else
        <x-filament::dropdown placement="bottom-end" width="sm">
            <x-slot name="trigger">
                <button type="button" class="hv-timer-start" title="Start the timer on one of your tasks">
                    <x-filament::icon icon="heroicon-m-play" class="h-4 w-4" />
                    <span>Timer</span>
                </button>
            </x-slot>

            <div class="hv-timer-menu">
                @if ($tasks->isEmpty())
                    <p class="hv-timer-empty">
                        No tasks assigned to you.
                        <a href="{{ \Huvant\Worklog\Filament\Pages\MyWeek::getUrl() }}">Join a task</a>
                    </p>
                @else
                    <input type="text" wire:model="note" maxlength="255" placeholder="Note (optional)" class="hv-timer-note" />
                    <ul>
                        @foreach ($tasks as $task)
                            <li>
                                <button type="button" wire:click="start({{ $task->id }})" x-on:click="close()">
                                    <span class="hv-timer-menu-title">{{ $task->title }}</span>
                                    <span class="hv-timer-menu-project">{{ $task->project?->name ?? 'No project' }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </x-filament::dropdown>
    @endif
</div>
