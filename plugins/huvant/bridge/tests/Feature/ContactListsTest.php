<?php

use Filament\Facades\Filament;
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
    if (! \Illuminate\Support\Facades\Route::has('filament.admin.resources.contact.contacts.view')) {
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
