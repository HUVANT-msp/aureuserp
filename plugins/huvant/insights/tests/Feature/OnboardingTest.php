<?php

use Filament\Facades\Filament;
use Huvant\Insights\Support\Onboarding;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ListEmployees;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-worklog');
    TestBootstrapHelper::ensurePluginInstalled('huvant-tasks');
    TestBootstrapHelper::ensurePluginInstalled('huvant-insights');
    if (! Route::has('filament.admin.resources.employees.employees.tasks')) {
        $this->refreshApplication();
        $this->setUpTraits();
    }
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Role::query()->firstOrCreate(['name' => 'Team Huvant', 'guard_name' => 'web']);
});

function onboardingAdmin(): User
{
    return User::query()->whereHas('roles', fn ($query) => $query->where('name', 'Admin'))->first()
        ?? tap(User::factory()->create(['is_active' => true]))->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
}

it('makes every new user an employee and an internal contact, joining what was added by hand', function () {
    $this->actingAs(onboardingAdmin());
    // Added by hand before: a contact and an employee for the same person.
    $handContact = Partner::query()->create(['account_type' => 'individual', 'name' => 'Enrico Milani']);
    $handEmployee = Employee::query()->create(['name' => 'Enrico Milani', 'job_title' => 'Designer']);

    $user = User::factory()->create(['name' => 'Enrico Milani', 'email' => 'e.milani@huvant.test', 'is_active' => true]);

    $employee = Employee::withoutGlobalScopes()->where('user_id', $user->getKey())->first();
    expect($employee->getKey())->toBe($handEmployee->getKey())
        ->and($employee->job_title)->toBe('Designer')
        ->and($employee->partner_id)->toBe($user->refresh()->partner_id)
        ->and($employee->work_email)->toBe('e.milani@huvant.test')
        ->and(Partner::withoutGlobalScopes()->find($user->partner_id)->tags->pluck('name')->all())->toContain(Onboarding::TEAM_TAG)
        ->and(Partner::withTrashed()->find($handContact->getKey())->trashed())->toBeTrue()
        ->and(Employee::withoutGlobalScopes()->whereRaw('LOWER(name) = ?', ['enrico milani'])->whereNull('deleted_at')->count())->toBe(1);

    // A brand-new person gets a new employee record.
    $other = User::factory()->create(['name' => 'Ariele Mairani', 'email' => 'a.mairani@huvant.test', 'is_active' => true]);
    expect(Employee::withoutGlobalScopes()->where('user_id', $other->getKey())->value('name'))->toBe('Ariele Mairani');
});

it('adds a colleague from Employees in one step and invites them', function () {
    $admin = onboardingAdmin();
    $this->actingAs($admin);
    config(['huvant-bridge.webhook.url' => 'http://minutes.test/api/v1/erp/webhook', 'huvant-bridge.webhook.secret' => 'welcome-secret', 'huvant-insights.send_invites' => true]);
    Http::fake(['minutes.test/api/v1/erp/welcome-email' => Http::response(['sent' => true])]);
    $manager = Employee::withoutGlobalScopes()->where('user_id', $admin->getKey())->first() ?? Onboarding::onboard($admin);

    Livewire::test(ListEmployees::class)->assertOk()->assertActionExists('newEmployee')
        ->callAction('newEmployee', ['name' => 'Giulia Nuova', 'email' => 'G.Nuova@huvant.test', 'job_title' => 'Engineer', 'parent_id' => $manager->getKey(), 'access' => 'team'])
        ->assertHasNoActionErrors();

    $user = User::query()->where('email', 'g.nuova@huvant.test')->firstOrFail();
    $employee = Employee::withoutGlobalScopes()->where('user_id', $user->getKey())->firstOrFail();
    expect($user->roles->pluck('name')->all())->toBe(['Team Huvant'])
        ->and([$employee->name, $employee->job_title, $employee->parent_id])->toBe(['Giulia Nuova', 'Engineer', $manager->getKey()])
        ->and(Partner::withoutGlobalScopes()->find($user->partner_id)->tags->pluck('name')->all())->toContain('Team Huvant');
    Http::assertSent(fn ($request) => str_ends_with($request->url(), '/welcome-email')
        && $request['email'] === 'g.nuova@huvant.test' && str_contains($request['url'], '/password-reset/'));

    // The same e-mail cannot be added twice.
    Livewire::test(ListEmployees::class)
        ->callAction('newEmployee', ['name' => 'Altra', 'email' => 'g.nuova@huvant.test', 'access' => 'team'])
        ->assertHasActionErrors(['email']);
});

it('sends no invitation while invitations are off, and the command sends it later', function () {
    $this->actingAs(onboardingAdmin());
    config(['huvant-bridge.webhook.url' => 'http://minutes.test/api/v1/erp/webhook', 'huvant-bridge.webhook.secret' => 'welcome-secret', 'huvant-insights.send_invites' => false]);
    Http::fake(['minutes.test/api/v1/erp/welcome-email' => Http::response(['sent' => true])]);

    Livewire::test(ListEmployees::class)
        ->callAction('newEmployee', ['name' => 'Paolo Quieto', 'email' => 'p.quieto@huvant.test', 'access' => 'team'])
        ->assertHasNoActionErrors();
    expect(User::query()->where('email', 'p.quieto@huvant.test')->exists())->toBeTrue();
    Http::assertNothingSent();

    $this->artisan('huvant:invite', ['emails' => ['p.quieto@huvant.test']])->assertSuccessful();
    Http::assertSent(fn ($request) => $request['email'] === 'p.quieto@huvant.test');
});
