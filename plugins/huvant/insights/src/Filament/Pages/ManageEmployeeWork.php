<?php

namespace Huvant\Insights\Filament\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Huvant\Insights\Support\Insights;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Attributes\Url;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Security\Models\User;

/** The employee's work: hours against the calendar, projects, tasks, recent entries. */
class ManageEmployeeWork extends Page
{
    use InteractsWithRecord;

    protected static string $resource = EmployeeResource::class;

    protected string $view = 'huvant-insights::filament.pages.employee-work';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    #[Url(as: 'giorni')]
    public int $days = 14;

    /** Administrators, the person's manager or coach, and the person. */
    public static function canAccess(array $parameters = []): bool
    {
        $viewer = auth()->user();
        $person = ($parameters['record'] ?? null)?->user;

        return $viewer instanceof User && (! $person ? Insights::isManager($viewer) : Insights::canSeePerson($viewer, $person));
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        abort_unless(static::canAccess(['record' => $this->record]), 403);
        $this->days = in_array($this->days, [7, 14, 30], true) ? $this->days : 14;
    }

    public function setDays(int $days): void
    {
        $this->days = in_array($days, [7, 14, 30], true) ? $days : 14;
    }

    public static function getNavigationLabel(): string
    {
        return 'Lavoro';
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    protected function getViewData(): array
    {
        $person = $this->record->user;

        return ['person' => $person, 'data' => $person ? Insights::person($person, $this->days) : null];
    }
}
