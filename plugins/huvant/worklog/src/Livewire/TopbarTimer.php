<?php

namespace Huvant\Worklog\Livewire;

use Filament\Notifications\Notification;
use Huvant\Worklog\Support\Worklog;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use RuntimeException;
use Webkul\Security\Models\User;

class TopbarTimer extends Component
{
    public string $note = '';

    public function start(int $taskId): void
    {
        try {
            Worklog::start($this->user(), $taskId, $this->note);
            $this->note = '';
        } catch (RuntimeException $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        }
        $this->dispatch('huvant-worklog-changed');
    }

    public function stop(): void
    {
        $entry = Worklog::stop($this->user());
        Notification::make()->success()
            ->title($entry ? 'Registrate '.Worklog::format((float) $entry->unit_amount).' h' : 'Timer fermato (meno di un minuto, non registrato)')
            ->send();
        $this->dispatch('huvant-worklog-changed');
    }

    #[On('huvant-worklog-changed')]
    public function refreshTimer(): void {}

    public function render(): View
    {
        $user = $this->user();

        return view('huvant-worklog::livewire.topbar-timer', [
            'running' => $user ? Worklog::running($user) : null,
            'tasks'   => $user ? Worklog::assignedOpenTasks($user) : collect(),
        ]);
    }

    private function user(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
