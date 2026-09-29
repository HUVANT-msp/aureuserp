<?php

use Huvant\Teams\Support\ProjectTeams;
use Illuminate\Support\Facades\DB;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\Scopes\OwnershipScope;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-teams');
    ProjectTeams::forget();
});

function teamMember(): User
{
    $user = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true]));
    $team = Team::query()->create(['name' => 'Team '.uniqid()]);
    $team->users()->attach($user->getKey());

    return $user->setRelation('huvantTeam', $team);
}

// Upstream TaskFactory writes a non-existent "visibility" column: insert directly.
function taskIn(?int $projectId, ?int $creatorId = null): int
{
    return DB::table('projects_tasks')->insertGetId([
        'title'      => 'Task '.uniqid(), 'state' => 'in_progress', 'project_id' => $projectId,
        'creator_id' => $creatorId, 'created_at' => now(), 'updated_at' => now(),
    ]);
}

function projectFor(?Team $team): Project
{
    $project = Project::withoutEvents(fn (): Project => Project::factory()->create());
    if ($team) {
        ProjectTeams::syncProjectTeams($project->getKey(), [$team->getKey()]);
    }

    return $project;
}

it('shows a member only the projects and tasks of their teams', function () {
    $member = teamMember();
    $mine = projectFor($member->getRelation('huvantTeam'));
    $other = projectFor(Team::query()->create(['name' => 'Altri']));
    $unassigned = projectFor(null);
    $myTask = taskIn($mine->getKey());
    $hiddenTask = taskIn($other->getKey());

    $this->actingAs($member);

    expect(Project::query()->withoutGlobalScope(OwnershipScope::class)->pluck('id')->all())
        ->toContain($mine->getKey())
        ->not->toContain($other->getKey())
        ->not->toContain($unassigned->getKey())
        ->and(Task::query()->withoutGlobalScope(OwnershipScope::class)->pluck('id')->all())
        ->toContain($myTask)
        ->not->toContain($hiddenTask);
});

it('keeps a task outside any project visible to its creator only', function () {
    $member = teamMember();
    $stranger = teamMember();
    $private = taskIn(null, $member->getKey());

    $this->actingAs($member);
    expect(Task::query()->withoutGlobalScope(OwnershipScope::class)->whereKey($private)->exists())->toBeTrue();

    ProjectTeams::forget();
    $this->actingAs($stranger);
    expect(Task::query()->withoutGlobalScope(OwnershipScope::class)->whereKey($private)->exists())->toBeFalse();
});

it('lets administrators and the integration account see every project', function () {
    $unassigned = projectFor(null);

    $admin = User::query()->whereHas('roles', fn ($query) => $query->where('name', 'Admin'))->first()
        ?? tap(User::withoutEvents(fn (): User => User::factory()->create()))->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
    $this->actingAs($admin);
    expect(ProjectTeams::bypasses($admin))->toBeTrue()
        ->and(Project::query()->withoutGlobalScope(OwnershipScope::class)->whereKey($unassigned->getKey())->exists())->toBeTrue();

    ProjectTeams::forget();
    $integration = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true]));
    $integration->givePermissionTo(Permission::query()->firstOrCreate([
        'name' => ProjectTeams::VIEW_ALL_PERMISSION, 'guard_name' => 'web',
    ]));
    expect(ProjectTeams::bypasses($integration->fresh()))->toBeTrue();
});
