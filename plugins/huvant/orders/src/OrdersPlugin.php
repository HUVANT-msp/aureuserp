<?php

namespace Huvant\Orders;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Support\ItemRoles;
use Huvant\Orders\Support\Navigation;
use Huvant\Orders\Support\Orders;
use Huvant\Orders\Support\RecordFields;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\BillsOfMaterialResource\Schemas\BillOfMaterialForm;
use Webkul\PluginManager\Package;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Product\Filament\Resources\ProductResource\Pages\ListProducts;

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
            Navigation::hide();
            ProductResource::$pricesVisibleUsing = fn (): bool => Orders::canSeePrices();
            ListProducts::$presetViewsUsing = fn (): array => ItemRoles::presetViews();
            BillOfMaterialForm::$componentQueryUsing = fn (Builder $query) => ItemRoles::scope($query, ItemRole::components());
        });
    }

    public function boot(Panel $panel): void {}
}
