<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Webkul\Contact\Filament\Clusters\Configurations;
use Webkul\Contact\Filament\Resources\PartnerResource\Pages\ListPartners;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\Partner\Models\Tag;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('contacts');
    if (! Route::has('filament.admin.resources.contact.contacts.view')) {
        // Installed during this run: boot again so the panel registers the contact routes.
        $this->refreshApplication();
        $this->setUpTraits();
    }
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('organises contacts as Internal, External and Companies only', function () {
    $admin = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->first()
        ?? tap(User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true])))->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
    $this->actingAs($admin);
    $team = Tag::query()->firstOrCreate(['name' => 'Team Huvant']);
    $ext = Tag::query()->firstOrCreate(['name' => 'Esterno']);
    $company = Partner::query()->create(['account_type' => AccountType::COMPANY, 'name' => 'Acme Medical']);
    $anna = Partner::query()->create(['account_type' => AccountType::INDIVIDUAL, 'name' => 'Anna Team']);
    $anna->tags()->attach($team);
    $marco = Partner::query()->create(['account_type' => AccountType::INDIVIDUAL, 'name' => 'Marco Esterno', 'parent_id' => $company->getKey()]);
    $marco->tags()->attach($ext);

    $page = Livewire::test(ListPartners::class)->assertOk()
        ->assertSee('Internal')->assertSee('External')->assertSee('Companies')
        ->assertDontSee('Individuals')->assertDontSee('Employees')->assertDontSee('Archived')
        ->assertCanSeeTableRecords([$anna])->assertCanNotSeeTableRecords([$marco, $company]);
    $page->call('loadView', 'external')->assertCanSeeTableRecords([$marco])->assertCanNotSeeTableRecords([$anna, $company]);
    $page->call('loadView', 'companies')->assertCanSeeTableRecords([$company])->assertCanNotSeeTableRecords([$anna, $marco]);
    expect(Configurations::shouldRegisterNavigation())->toBeFalse();
});

it('gives the addresses of the colleagues picked, comma separated, ready to copy', function () {
    $admin = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->first()
        ?? tap(User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true])))->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
    $this->actingAs($admin);
    $team = Tag::query()->firstOrCreate(['name' => 'Team Huvant']);
    $ext = Tag::query()->firstOrCreate(['name' => 'Esterno']);
    $person = function (string $name, ?string $email, Tag $tag): void {
        Partner::query()->create(['account_type' => AccountType::INDIVIDUAL, 'name' => $name, 'email' => $email])->tags()->attach($tag);
    };

    $person('Anna Rossi', 'anna.rossi@huvant.com', $team);
    $person('Bruno Verdi', 'bruno.verdi@huvant.com', $team);
    $person('Carla Senzamail', null, $team);
    $person('Marco Esterno', 'marco@cliente.it', $ext);

    $options = ListPartners::internalEmails();
    $anna = Partner::query()->where('name', 'Anna Rossi')->value('id');
    $bruno = Partner::query()->where('name', 'Bruno Verdi')->value('id');

    expect($options->values()->all())->toContain('Anna Rossi <anna.rossi@huvant.com>', 'Bruno Verdi <bruno.verdi@huvant.com>')
        ->and($options->implode(' '))->not->toContain('Senzamail')->not->toContain('marco@cliente.it')
        ->and(ListPartners::emailLine([$bruno, $anna]))->toBe('anna.rossi@huvant.com, bruno.verdi@huvant.com');

    Livewire::test(ListPartners::class)
        ->mountAction('broadcast')
        ->assertSee('Bruno Verdi')
        ->fillForm(['recipients' => [$anna, $bruno]])
        ->assertSchemaStateSet(['emails' => 'anna.rossi@huvant.com, bruno.verdi@huvant.com']);
});
