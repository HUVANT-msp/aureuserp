<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-bridge');
    TestBootstrapHelper::ensurePluginInstalled('projects');

    if (! Route::has('admin.api.v1.huvant.projects.color.update') || ! Route::has('admin.api.v1.huvant.externals.store')) {
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

it('files a structured external as a contact under its company, tagged Esterno', function () {
    $admin = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->first()
        ?? tap(User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true])))->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
    $admin->forceFill(['is_active' => true])->saveQuietly();
    Sanctum::actingAs($admin);
    $body = ['first_name' => 'Marco', 'last_name' => 'Neri', 'company' => 'Acme Medical', 'email' => 'M.Neri@acme.example'];

    $id = $this->postJson(route('admin.api.v1.huvant.externals.store'), $body)->assertCreated()
        ->assertJsonPath('data.name', 'Marco Neri')->json('data.id');
    // The same e-mail updates the same contact; the company is reused.
    $this->postJson(route('admin.api.v1.huvant.externals.store'), [...$body, 'last_name' => 'Neri Bianchi'])->assertOk()->assertJsonPath('data.id', $id);
    $this->putJson(route('admin.api.v1.huvant.externals.update', $id), [...$body, 'company' => 'acme medical'])->assertOk();

    $partner = Partner::query()->with(['tags', 'parent'])->find($id);
    expect($partner->email)->toBe('m.neri@acme.example')
        ->and($partner->parent->name)->toBe('Acme Medical')
        ->and($partner->tags->pluck('name')->all())->toBe(['Esterno'])
        ->and(Partner::query()->where('name', 'Acme Medical')->count())->toBe(1);

    $this->postJson(route('admin.api.v1.huvant.externals.store'), [...$body, 'company' => ''])->assertUnprocessable();
});
