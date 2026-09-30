<?php

namespace Huvant\Insights\Filament\Pages;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Huvant\Insights\Support\Insights;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Security\Models\User;

/** The person's tasks, to see and change: how busy they are, and on what. */
class ManageEmployeeTasks extends Page
{
    use InteractsWithRecord;

    protected static string $resource = EmployeeResource::class;

    protected string $view = 'huvant-insights::filament.pages.employee-tasks';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';

    /** Administrators and the person's manager or coach. */
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
    }

    public static function getNavigationLabel(): string
    {
        return 'Tasks';
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    protected function getViewData(): array
    {
        $person = $this->record->user;

        return ['person' => $person, 'load' => $person ? Insights::workload($person) : null];
    }
}
