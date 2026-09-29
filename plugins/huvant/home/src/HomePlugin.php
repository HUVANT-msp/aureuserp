<?php

namespace Huvant\Home;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Huvant\Home\Filament\Pages\HomePage;
use Webkul\PluginManager\Package;

class HomePlugin implements Plugin
{
    public function getId(): string
    {
        return 'huvant-home';
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
            $panel->pages([HomePage::class]);
            $panel->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<style id="huvant-home">'.file_get_contents(__DIR__.'/../resources/css/home.css').'</style>',
            );
        });
    }

    public function boot(Panel $panel): void {}
}
