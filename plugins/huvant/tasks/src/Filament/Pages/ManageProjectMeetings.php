<?php

namespace Huvant\Tasks\Filament\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Huvant\Bridge\Support\MinutesApi;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Url;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Models\Project;

/** The meetings that concerned a project, each with its minutes PDF once final. */
class ManageProjectMeetings extends Page
{
    use InteractsWithRecord;

    /** Minutes state => [label, badge tone]. */
    public const STATES = [
        'final'      => ['Final', 'final'],
        'review'     => ['In review', 'review'],
        'processing' => ['Processing', 'processing'],
        'failed'     => ['Failed', 'failed'],
        'live'       => ['Live in Canvas', 'live'],
        'canvas'     => ['Canvas only', 'processing'],
    ];

    protected static string $resource = ProjectResource::class;

    protected string $view = 'huvant-tasks::filament.pages.project-meetings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    #[Url(as: 'q')]
    public string $search = '';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public static function getNavigationLabel(): string
    {
        return 'Meetings';
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    /** The project's meetings, as the Minutes files them (cached briefly; the PDF route re-checks). */
    public static function meetingsOf(Project $project): array
    {
        return Cache::remember('huvant:project-meetings:'.$project->getKey(), 60,
            fn (): array => MinutesApi::post('project-meetings', ['erp_project_id' => (int) $project->getKey()])['meetings'] ?? []);
    }

    protected function getViewData(): array
    {
        try {
            $meetings = collect(static::meetingsOf($this->record));
        } catch (\Throwable $e) {
            report($e);

            return ['error' => true, 'meetings' => collect()];
        }
        $search = mb_strtolower(trim($this->search));

        return [
            'error'    => false,
            'total'    => $meetings->count(),
            'meetings' => $meetings->when($search !== '', fn ($c) => $c->filter(fn ($m) => str_contains(mb_strtolower($m['title']), $search)))->values(),
        ];
    }
}
