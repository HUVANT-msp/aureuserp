<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar;
use Webkul\Security\Models\Permission;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-bridge');
});

// A real installation seeds roles and permissions for the "web" guard only
// (the shared test helper creates both guards, which hid this in other tests).
function webOnlyUserWith(array $permissionNames): User
{
    $role = Role::query()->create(['name' => 'web-only-'.uniqid(), 'guard_name' => 'web']);

    foreach ($permissionNames as $name) {
        $role->givePermissionTo(Permission::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']));
    }

    $user = User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true]));
    $user->assignRole($role);
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    return $user->fresh();
}

it('grants a token request the permissions the user holds on the web guard', function () {
    $user = webOnlyUserWith(['view_any_security_user']);
    Sanctum::actingAs($user, ['*']);

    expect(Auth::getDefaultDriver())->toBe('sanctum')
        ->and(Gate::forUser($user)->allows('view_any_security_user'))->toBeTrue()
        ->and(Gate::forUser($user)->allows('viewAny', User::class))->toBeTrue();
});

it('still denies a token request what the user does not hold on the web guard', function () {
    $user = webOnlyUserWith(['view_any_security_user']);
    Sanctum::actingAs($user, ['*']);

    expect(Gate::forUser($user)->allows('create_security_user'))->toBeFalse()
        ->and(Gate::forUser($user)->allows('create', User::class))->toBeFalse();
});
