<?php

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Policies\UserPolicy;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-bridge');

    if (! Route::has('admin.api.v1.huvant.users.index') || ! Route::has('admin.api.v1.huvant.users.show')) {
        require base_path('plugins/huvant/bridge/routes/api.php');
    }
});

function authenticateActiveBridgeUser(array $permissions): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);
    $user->forceFill(['is_active' => true])->saveQuietly();

    return $user;
}

it('registers the core user policy while the bridge is installed', function () {
    expect(Gate::getPolicyFor(User::class))->toBeInstanceOf(UserPolicy::class);
});

it('requires authentication to list bridge users', function () {
    $this->getJson(route('admin.api.v1.huvant.users.index'))->assertUnauthorized();
});

it('rejects bridge requests from an inactive authenticated user', function () {
    $user = authenticateActiveBridgeUser(['view_any_security_user']);
    $user->forceFill(['is_active' => false])->saveQuietly();

    $this->getJson(route('admin.api.v1.huvant.users.index'))->assertForbidden();
});

it('shows a user without exposing password fields', function () {
    $actor = authenticateActiveBridgeUser(['view_security_user']);
    $actor->forceFill(['resource_permission' => PermissionType::GLOBAL])->saveQuietly();
    $target = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $actor->default_company_id,
    ]));

    $this->getJson(route('admin.api.v1.huvant.users.show', $target))
        ->assertOk()
        ->assertJsonPath('data.id', $target->id)
        ->assertJsonMissingPath('data.password')
        ->assertJsonMissingPath('data.remember_token');
});

it('prevents a non-super-admin from assigning roles or global access', function () {
    $actor = authenticateActiveBridgeUser(['create_security_user']);
    $companyId = $actor->default_company_id;
    $customRole = Role::query()->firstOrCreate([
        'name'       => 'Bridge custom operator',
        'guard_name' => 'web',
    ]);
    $actor->roles()->sync([$customRole->id]);

    $this->postJson(route('admin.api.v1.huvant.users.store'), [
        'name'                  => 'Bridge User',
        'email'                 => 'bridge-user@huvant.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'default_company_id'    => $companyId,
        'allowed_company_ids'   => [$companyId],
        'resource_permission'   => PermissionType::GLOBAL->value,
        'role_ids'              => [1],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['resource_permission', 'role_ids']);
});

it('lets the Admin system role assign roles and global access', function () {
    $actor = authenticateActiveBridgeUser(['create_security_user']);
    $companyId = $actor->default_company_id;
    $adminRole = Role::query()->firstOrCreate([
        'name'       => 'Admin',
        'guard_name' => 'web',
    ]);
    $assignableRole = Role::query()->firstOrCreate([
        'name'       => 'Bridge assigned role',
        'guard_name' => 'web',
    ]);
    $actor->roles()->sync([$adminRole->id]);

    $this->postJson(route('admin.api.v1.huvant.users.store'), [
        'name'                  => 'Admin-created Bridge User',
        'email'                 => 'admin-created-bridge-user@huvant.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'default_company_id'    => $companyId,
        'allowed_company_ids'   => [$companyId],
        'resource_permission'   => PermissionType::GLOBAL->value,
        'role_ids'              => [$assignableRole->id],
    ])->assertCreated()
        ->assertJsonPath('data.resource_permission', PermissionType::GLOBAL->value)
        ->assertJsonPath('data.role_ids.0', $assignableRole->id);
});

it('prevents assigning a company unavailable to the acting user', function () {
    $actor = authenticateActiveBridgeUser(['create_security_user']);
    $otherCompany = Company::factory()->create();
    $actor->allowedCompanies()->detach($otherCompany->id);

    $this->postJson(route('admin.api.v1.huvant.users.store'), [
        'name'                  => 'Bridge User',
        'email'                 => 'other-company-user@huvant.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'default_company_id'    => $otherCompany->id,
        'allowed_company_ids'   => [$actor->default_company_id, $otherCompany->id],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['allowed_company_ids']);
});

it('uses individual access when a non-super-admin omits resource permission', function () {
    $actor = authenticateActiveBridgeUser(['create_security_user']);

    $this->postJson(route('admin.api.v1.huvant.users.store'), [
        'name'                  => 'Restricted Bridge User',
        'email'                 => 'restricted-bridge-user@huvant.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'default_company_id'    => $actor->default_company_id,
        'allowed_company_ids'   => [$actor->default_company_id],
    ])->assertCreated()
        ->assertJsonPath('data.resource_permission', PermissionType::INDIVIDUAL->value);
});

it('prevents non-super-admin self escalation and default-company-only escalation', function () {
    $actor = authenticateActiveBridgeUser(['update_security_user']);
    $actor->forceFill(['resource_permission' => PermissionType::GLOBAL])->saveQuietly();
    $otherCompany = Company::factory()->create();
    $actor->allowedCompanies()->detach($otherCompany->id);

    expect(Gate::forUser($actor)->allows('update', $actor))->toBeTrue();

    $this->patchJson(route('admin.api.v1.huvant.users.update', $actor), [
        'resource_permission' => PermissionType::GROUP->value,
        'default_company_id'  => $otherCompany->id,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['resource_permission', 'default_company_id']);
});

it('does not let a non-super-admin update a user from another company', function () {
    $actor = authenticateActiveBridgeUser(['update_security_user']);
    $actor->forceFill(['resource_permission' => PermissionType::GLOBAL])->saveQuietly();
    $otherCompany = Company::factory()->create();
    $actor->allowedCompanies()->detach($otherCompany->id);
    $target = User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $otherCompany->id,
    ]));

    $this->patchJson(route('admin.api.v1.huvant.users.update', $target), [
        'name' => 'Cross-company update',
    ])->assertForbidden();
});
