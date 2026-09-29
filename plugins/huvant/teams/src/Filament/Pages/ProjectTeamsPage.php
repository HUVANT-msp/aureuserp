<?php

namespace Huvant\Teams\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Huvant\Teams\Support\ProjectTeams;
use Illuminate\Support\Facades\DB;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

/**
 * One place for administrators: teams, their members, and the teams working
 * on each project (a user sees only the projects of their teams).
 */
class ProjectTeamsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'huvant-teams::filament.pages.project-teams';

    protected static ?string $slug = 'project/teams';

    protected static ?int $navigationSort = 90;

    public ?array $data = [];

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }

    public static function getNavigationLabel(): string
    {
        return 'Team';
    }

    public function getTitle(): string
    {
        return 'Teams and projects';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && ProjectTeams::bypasses($user);
    }

    public function mount(): void
    {
        $this->form->fill([
            'teams' => Team::query()->orderBy('name')->get()->map(fn (Team $team): array => [
                'id'      => $team->getKey(),
                'name'    => $team->name,
                'members' => $team->users()->pluck('users.id')->map(fn ($id): string => (string) $id)->all(),
            ])->all(),
            'projects' => Project::query()->orderBy('name')->get()->mapWithKeys(fn (Project $project): array => [
                "p{$project->getKey()}" => DB::table(ProjectTeams::PIVOT)->where('project_id', $project->getKey())
                    ->pluck('team_id')->map(fn ($id): string => (string) $id)->all(),
            ])->all(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $users = User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all();
        $teams = Team::query()->orderBy('name')->pluck('name', 'id')->all();

        return $schema
            ->components([
                Section::make('Team')
                    ->description('Who belongs to each team.')
                    ->schema([
                        Repeater::make('teams')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('name')->label('Name')->required()->maxLength(255),
                                Select::make('members')->label('Members')->multiple()->searchable()->options($users),
                            ])
                            ->columns(2)
                            ->addActionLabel('New team')
                            ->defaultItems(0)
                            ->reorderable(false),
                    ]),
                Section::make('Projects')
                    ->description('The teams working on each project. A project without a team is visible to administrators only.')
                    ->schema(
                        Project::query()->orderBy('name')->get()->map(
                            fn (Project $project) => Select::make("projects.p{$project->getKey()}")
                                ->label($project->name)
                                ->multiple()
                                ->searchable()
                                ->options($teams)
                                ->placeholder('Administrators only')
                        )->all()
                    )
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [Action::make('save')->label('Save')->submit('save')];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        DB::transaction(function () use ($state): void {
            $keptTeamIds = [];
            foreach ($state['teams'] ?? [] as $row) {
                $team = isset($row['id']) ? Team::query()->find($row['id']) : null;
                $team ??= new Team;
                $team->name = trim($row['name']);
                $team->save();
                $team->users()->sync(array_map('intval', $row['members'] ?? []));
                $keptTeamIds[] = $team->getKey();
            }
            // Teams removed from the list are deleted, with their memberships and project links.
            Team::query()->whereNotIn('id', $keptTeamIds ?: [0])->get()->each(function (Team $team): void {
                $team->users()->detach();
                DB::table(ProjectTeams::PIVOT)->where('team_id', $team->getKey())->delete();
                $team->delete();
            });

            foreach ($state['projects'] ?? [] as $key => $teamIds) {
                ProjectTeams::syncProjectTeams((int) ltrim($key, 'p'), array_intersect(
                    array_map('intval', $teamIds ?? []), $keptTeamIds
                ));
            }
        });

        ProjectTeams::forget();
        $this->mount();

        Notification::make()->title('Teams saved')->success()->send();
    }
}
