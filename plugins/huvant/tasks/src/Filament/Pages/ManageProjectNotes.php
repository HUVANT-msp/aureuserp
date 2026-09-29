<?php

namespace Huvant\Tasks\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Huvant\Bridge\Support\MinutesApi;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Models\Project;

/**
 * What the project's meetings produced besides tasks: open questions, risks,
 * decisions, ideas and key points, with what became of them.
 */
class ManageProjectNotes extends Page
{
    use InteractsWithRecord;

    /** Sections in reading order: kind => [title, empty text]. */
    public const SECTIONS = [
        'open_point' => ['Open questions', 'No open questions.'],
        'risk'       => ['Risks', 'No risks raised.'],
        'decision'   => ['Decisions', 'No decisions recorded.'],
        'idea'       => ['Ideas', 'No ideas yet.'],
        'key_point'  => ['Key points', 'No key points.'],
    ];

    /** Button labels for each state change. */
    public const ACTIONS = [
        'resolved'   => ['Resolve', 'Resolved', 'heroicon-m-check-circle'],
        'addressed'  => ['Mark addressed', 'Addressed', 'heroicon-m-shield-check'],
        'accepted'   => ['Accept risk', 'Accepted', 'heroicon-m-hand-thumb-up'],
        'superseded' => ['Superseded', 'Superseded', 'heroicon-m-arrow-uturn-right'],
        'archived'   => ['Archive', 'Archived', 'heroicon-m-archive-box'],
        'adopted'    => ['Adopt', 'Adopted', 'heroicon-m-light-bulb'],
    ];

    protected static string $resource = ProjectResource::class;

    protected string $view = 'huvant-tasks::filament.pages.project-notes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-light-bulb';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'closed')]
    public bool $showClosed = false;

    #[Url(as: 'meeting')]
    public string $meeting = '';

    public ?string $noteKey = null;

    public string $noteKind = '';

    public string $noteStatus = '';

    public string $noteText = '';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public static function getNavigationLabel(): string
    {
        return 'Notes';
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    /** Resolving and similar changes ask for an optional note first. */
    public function askState(string $key, string $kind, string $status): void
    {
        [$this->noteKey, $this->noteKind, $this->noteStatus, $this->noteText] = [$key, $kind, $status, ''];
        $this->dispatch('open-modal', id: 'hv-note-state');
    }

    public function confirmState(): void
    {
        if ($this->noteKey) {
            $this->setState($this->noteKey, $this->noteKind, $this->noteStatus, $this->noteText);
        }
        $this->dispatch('close-modal', id: 'hv-note-state');
    }

    public function setState(string $key, string $kind, string $status, ?string $note = null): void
    {
        $this->sendToMeetings('project-notes/state', [
            'key' => $key, 'kind' => $kind, 'status' => $status, 'note' => $note ?: null, 'email' => auth()->user()->email,
        ], $status === 'open' ? 'Reopened' : (self::ACTIONS[$status][1] ?? 'Saved'));
    }

    /** File a whole meeting (and all its notes) under another project. */
    public function moveMeeting(string $source, string $meetingId, int $projectId): void
    {
        if (! Project::query()->whereKey($projectId)->exists()) {
            return;
        }
        $this->sendToMeetings('project-notes/move', ['source' => $source, 'meeting_id' => $meetingId, 'erp_project_id' => $projectId], 'Meeting moved');
    }

    protected function getViewData(): array
    {
        try {
            $data = MinutesApi::post('project-notes', ['erp_project_id' => (int) $this->record->getKey()]);
        } catch (\Throwable $e) {
            report($e);

            return ['error' => true, 'sections' => [], 'meetings' => [], 'counts' => [], 'projects' => collect()];
        }
        $notes = collect($data['notes'] ?? []);
        $counts = [
            'open_point' => $notes->where('kind', 'open_point')->where('status', 'open')->count(),
            'risk'       => $notes->where('kind', 'risk')->where('status', 'open')->count(),
            'decision'   => $notes->where('kind', 'decision')->where('status', 'open')->count(),
        ];
        $search = mb_strtolower(trim($this->search));
        $notes = $notes
            ->when($search !== '', fn ($c) => $c->filter(fn ($n) => str_contains(mb_strtolower($n['text'].' '.($n['owner'] ?? '').' '.$n['meeting']), $search)))
            ->when($this->meeting !== '', fn ($c) => $c->filter(fn ($n) => explode(':', $n['key'])[1] === $this->meeting))
            ->map(fn ($n) => $n + ['section' => $n['kind'] === 'conclusion' ? 'key_point' : $n['kind']]);

        $sections = [];
        foreach (self::SECTIONS as $kind => [$title, $empty]) {
            $mine = $notes->where('section', $kind);
            $sections[$kind] = [
                'title'  => $title,
                'empty'  => $empty,
                'open'   => $mine->where('status', 'open')->values(),
                'closed' => $mine->where('status', '!=', 'open')->values(),
            ];
        }

        return [
            'error'    => false,
            'sections' => $sections,
            'meetings' => $data['meetings'] ?? [],
            'counts'   => $counts,
            'projects' => Project::query()->orderBy('name')->get(['id', 'name']),
        ];
    }

    private function sendToMeetings(string $path, array $payload, string $success): void
    {
        try {
            MinutesApi::post($path, $payload);
            Notification::make()->success()->title($success)->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()->danger()->title('Meetings could not be updated right now.')->send();
        }
    }
}
