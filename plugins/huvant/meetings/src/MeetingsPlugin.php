<?php

namespace Huvant\Meetings;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Huvant\Meetings\Filament\Pages\CanvasPage;
use Huvant\Meetings\Filament\Pages\MiloPage;
use Huvant\Meetings\Filament\Pages\MinutesPage;
use Huvant\Meetings\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;
use Webkul\PluginManager\Package;

class MeetingsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'huvant-meetings';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel->when($panel->getId() == 'admin', function (Panel $panel): void {
            // The app launcher only lists navigation groups that have an icon.
            // Registered before the panel's own groups, so it comes first.
            $panel->navigationGroups([
                'Meetings' => NavigationGroup::make('Meetings')->icon('huvant-meetings'),
                'Milo'     => NavigationGroup::make('Milo')->icon('huvant-milo'),
            ]);
            // Minutes, Canvas and Milo are ERP pages: the ERP top bar stays on top, the tool fills the page.
            $panel->pages([MinutesPage::class, CanvasPage::class, MiloPage::class]);
            // An ERP page opened inside the tool frame (a link, the sign-in page) takes the whole window.
            $panel->renderHook(
                PanelsRenderHook::HEAD_START,
                fn (): string => '<script>if (window.self !== window.top) { try { if (window.top.location.origin === window.location.origin) window.top.location.replace(window.location.href); } catch (e) {} }</script>',
            );
            // Huvant look (light/dark) for every page of the panel, sign-in included.
            $panel->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style id="huvant-theme">'.file_get_contents(__DIR__.'/../resources/css/huvant.css').'</style>'
                    .'<style id="huvant-motion">'.file_get_contents(__DIR__.'/../resources/css/motion.css').'</style>',
            );
            $panel->authenticatedRoutes(function (): void {
                Route::get('huvant/sso', [SessionController::class, 'sso'])->name('huvant.sso');
                Route::get('huvant/navigation', [SessionController::class, 'navigation'])->name('huvant.navigation');
                Route::get('huvant/logout', [SessionController::class, 'logout'])->name('huvant.logout');
            });
        });
    }

    public function boot(Panel $panel): void {}
}
