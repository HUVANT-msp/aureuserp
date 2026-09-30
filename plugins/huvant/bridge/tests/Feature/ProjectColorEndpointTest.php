<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-bridge');
    TestBootstrapHelper::ensurePluginInstalled('projects');

    if (! Route::has('admin.api.v1.huvant.projects.color.update')) {
        require base_path('plugins/huvant/bridge/routes/api.php');
    }
});

it('sets the Meeting Canvas colour of a project', function () {
    $admin = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->first()
        ?? tap(User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true])))->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
    $admin->forceFill(['is_active' => true])->saveQuietly();
    $project = Project::withoutEvents(fn (): Project => Project::factory()->create());
    Sanctum::actingAs($admin);

    $this->patchJson(route('admin.api.v1.huvant.projects.color.update', $project), ['color' => '#be185d'])
        ->assertOk()->assertJsonPath('data.color', '#BE185D');
    expect($project->fresh()->color)->toBe('#BE185D');

    $this->patchJson(route('admin.api.v1.huvant.projects.color.update', $project), ['color' => 'red'])->assertUnprocessable();
});
