<?php

namespace Huvant\Orders;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Huvant\Orders\Support\Orders;
use Huvant\Orders\Support\RecordFields;
use Webkul\PluginManager\Package;
use Webkul\Product\Filament\Resources\ProductResource;

class OrdersPlugin implements Plugin
{
    public function getId(): string
    {
        return 'huvant-orders';
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
            $panel->discoverResources(in: __DIR__.'/Filament/Resources', for: 'Huvant\\Orders\\Filament\\Resources');
            $panel->discoverPages(in: __DIR__.'/Filament/Pages', for: 'Huvant\\Orders\\Filament\\Pages');
            RecordFields::register();
            ProductResource::$pricesVisibleUsing = fn (): bool => Orders::canSeePrices();
        });
    }

    public function boot(Panel $panel): void {}
}
