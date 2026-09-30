<?php

use Filament\Facades\Filament;
use Huvant\Meetings\Filament\Pages\CanvasPage;
use Huvant\Meetings\Filament\Pages\EmbeddedToolPage;
use Huvant\Meetings\Filament\Pages\MiloPage;
use Huvant\Meetings\Filament\Pages\MinutesPage;
use Livewire\Livewire;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('huvant-meetings');
    if (! \Illuminate\Support\Facades\Route::has('filament.admin.pages.meetings')) {
        // Installed during this run: boot again so the panel registers the pages.
        $this->refreshApplication();
        $this->setUpTraits();
    }
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $admin = User::query()->whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->first()
        ?? tap(User::withoutEvents(fn (): User => User::factory()->create(['is_active' => true])))->assignRole(Role::query()->where('name', 'Admin')->firstOrFail());
    $this->actingAs($admin);
});

it('shows Minutes, Canvas and Milo under the ERP top bar', function () {
    Livewire::test(MinutesPage::class)->assertOk()->assertSeeHtml('src="/riunioni/"')->assertSeeHtml('allow="microphone');
    Livewire::test(CanvasPage::class)->assertOk()->assertSeeHtml('src="/riunioni/canvas"');
    Livewire::test(MiloPage::class)->assertOk()->assertSeeHtml('src="/riunioni/milo"');
    expect(MinutesPage::getNavigationGroup())->toBe('Meetings')
        ->and(CanvasPage::getNavigationGroup())->toBe('Meetings')
        ->and(MiloPage::getNavigationGroup())->toBe('Milo');
});

it('opens the tool where the link pointed, and only inside the tool', function () {
    Livewire::withQueryParams(['p' => '/riunioni/?meeting=abc'])->test(MinutesPage::class)->assertSeeHtml('src="/riunioni/?meeting=abc"');
    Livewire::withQueryParams(['p' => '/riunioni/canvas?meeting_id=x'])->test(CanvasPage::class)->assertSeeHtml('src="/riunioni/canvas?meeting_id=x"');
    // Another tool's path, or anything outside /riunioni, falls back to the tool's home.
    Livewire::withQueryParams(['p' => '/riunioni/milo'])->test(MinutesPage::class)->assertSeeHtml('src="/riunioni/"');
    Livewire::withQueryParams(['p' => '/admin/users'])->test(MinutesPage::class)->assertSeeHtml('src="/riunioni/"');
    Livewire::withQueryParams(['p' => '//evil.example/riunioni/'])->test(MinutesPage::class)->assertSeeHtml('src="/riunioni/"');

    expect(EmbeddedToolPage::toolFor('/riunioni/admin/voice-profiles'))->toBe('canvas')
        ->and(EmbeddedToolPage::toolFor('/riunioni/milo'))->toBe('milo')
        ->and(EmbeddedToolPage::toolFor('/riunioni/contacts'))->toBe('minutes');
});
