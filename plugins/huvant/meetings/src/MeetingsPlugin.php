<?php

namespace Huvant\Meetings;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Huvant\Meetings\Http\Controllers\SessionController;
use Huvant\Meetings\Support\MeetingsSso;
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
            // Minutes and Canvas live on the same domain under /riunioni.
            $panel->navigationItems([
                NavigationItem::make('Minutes')
                    ->url(MeetingsSso::HOME)
                    ->icon('heroicon-o-document-text')
                    ->group('Meetings')
                    ->sort(1),
                NavigationItem::make('Canvas')
                    ->url(MeetingsSso::HOME.'canvas')
                    ->icon('heroicon-o-microphone')
                    ->group('Meetings')
                    ->sort(2),
                // Milo lives on its own page, outside Minutes and Canvas.
                NavigationItem::make('Ask Milo')
                    ->url(MeetingsSso::HOME.'milo')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->group('Milo')
                    ->sort(1),
            ]);
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
