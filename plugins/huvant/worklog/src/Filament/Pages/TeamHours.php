<?php

namespace Huvant\Worklog\Filament\Pages;

use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Huvant\Worklog\Support\Worklog;
use Livewire\Attributes\Url;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Security\Models\User;
use Webkul\Timesheet\Models\Timesheet;

/** Administrators: who logged what, day by day, against the hours foreseen. */
class TeamHours extends Page
{
    protected string $view = 'huvant-worklog::filament.pages.team-hours';

    protected static ?string $slug = 'worklog/team';

    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    #[Url(as: 'settimana')]
    public string $week = '';

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return MyWeek::GROUP;
    }

    public static function getNavigationLabel(): string
    {
        return 'Ore del team';
    }

    public function getTitle(): string
    {
        return 'Ore del team';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && Worklog::isAdmin($user);
    }

    public function mount(): void
    {
        $this->week = $this->monday()->toDateString();
    }

    public function shiftWeek(int $weeks): void
    {
        $this->week = $this->monday()->addWeeks($weeks)->toDateString();
    }

    public function thisWeek(): void
    {
        $this->week = CarbonImmutable::today()->startOfWeek()->toDateString();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Esporta CSV')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('gray')
                ->action(fn (): StreamedResponse => $this->export()),
        ];
    }

    public function export(): StreamedResponse
    {
        $monday = $this->monday();
        $sunday = $monday->addDays(6);

        return response()->streamDownload(function () use ($monday, $sunday): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Data', 'Persona', 'Progetto', 'Task', 'Ore', 'Nota'], ';');
            Timesheet::query()->withoutGlobalScopes()
                ->leftJoin('users', 'users.id', '=', 'analytic_records.user_id')
                ->leftJoin('projects_projects', 'projects_projects.id', '=', 'analytic_records.project_id')
                ->leftJoin('projects_tasks', 'projects_tasks.id', '=', 'analytic_records.task_id')
                ->whereBetween('analytic_records.date', [$monday->toDateString(), $sunday->toDateString()])
                ->whereNotNull('analytic_records.project_id')
                ->orderBy('analytic_records.date')->orderBy('users.name')
                ->get(['analytic_records.date', 'users.name as person', 'projects_projects.name as project',
                    'projects_tasks.title as task', 'analytic_records.unit_amount', 'analytic_records.name'])
                ->each(fn ($row) => fputcsv($out, [
                    CarbonImmutable::parse($row->date)->toDateString(), $row->person, $row->project, $row->task,
                    number_format((float) $row->unit_amount, 2, ',', ''), $row->name,
                ], ';'));
            fclose($out);
        }, "ore-{$monday->toDateString()}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function getViewData(): array
    {
        $monday = $this->monday();

        return [
            'monday' => $monday,
            'data'   => Worklog::teamWeek($monday),
            'today'  => CarbonImmutable::today()->toDateString(),
        ];
    }

    private function monday(): CarbonImmutable
    {
        try {
            $day = $this->week !== '' ? CarbonImmutable::parse($this->week) : CarbonImmutable::today();
        } catch (\Throwable) {
            $day = CarbonImmutable::today();
        }

        return $day->startOfWeek();
    }
}
