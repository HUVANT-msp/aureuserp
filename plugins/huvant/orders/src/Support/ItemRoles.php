<?php

namespace Huvant\Orders\Support;

use Huvant\Orders\Enums\ItemRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Product\Enums\ProductType;
use Webkul\TableViews\Filament\Components\PresetView;

/** The role of an item sets how the ERP stocks it, so nobody has to know which inventory switches go together. */
class ItemRoles
{
    /** Called when a product is saved. */
    public static function apply(Model $product): void
    {
        $role = $product->huvant_role instanceof ItemRole ? $product->huvant_role : ItemRole::tryFrom((string) $product->huvant_role);

        if (! $role) {
            return;
        }

        // Scalar values: the base product model does not cast the inventory columns.
        $product->type = ($role === ItemRole::Service ? ProductType::SERVICE : ProductType::GOODS)->value;
        $product->is_storable = $role !== ItemRole::Service;
        $product->enable_sales = $role !== ItemRole::Material;

        // Products and materials are traced by lot (serial numbers stay if chosen); rentals are counted as pieces.
        $current = $product->tracking instanceof ProductTracking ? $product->tracking : ProductTracking::tryFrom((string) $product->tracking);
        $product->tracking = match ($role) {
            ItemRole::Product, ItemRole::Material => ($current === ProductTracking::SERIAL ? ProductTracking::SERIAL : ProductTracking::LOT)->value,
            default                               => ProductTracking::QTY->value,
        };

        if ($role === ItemRole::Material) {
            $product->use_expiration_date = true;
        }
    }

    /** The product list in tabs by role: products first, raw materials last. @return array<string, PresetView> */
    public static function presetViews(): array
    {
        $byRole = fn (ItemRole $role) => fn (Builder $query) => $query->where('huvant_role', $role->value);
        $isIt = app()->getLocale() === 'it';

        return [
            'products'  => PresetView::make($isIt ? 'Prodotti' : 'Products')->icon('heroicon-s-cube')->favorite()->setAsDefault()->modifyQueryUsing($byRole(ItemRole::Product)),
            'rentals'   => PresetView::make($isIt ? 'Noleggi' : 'Rentals')->icon('heroicon-s-arrow-path-rounded-square')->favorite()->modifyQueryUsing($byRole(ItemRole::Rental)),
            'services'  => PresetView::make($isIt ? 'Servizi' : 'Services')->icon('heroicon-s-sparkles')->favorite()->modifyQueryUsing($byRole(ItemRole::Service)),
            'materials' => PresetView::make($isIt ? 'Materie prime' : 'Raw materials')->icon('heroicon-s-beaker')->favorite()->modifyQueryUsing($byRole(ItemRole::Material)),
            'archived'  => PresetView::make($isIt ? 'Archiviati' : 'Archived')->icon('heroicon-s-archive-box')->favorite()->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }

    /** Restricts a product query to the given roles (items without a role yet still show). */
    public static function scope(Builder $query, array $roles): Builder
    {
        return $query->where(fn (Builder $query) => $query->whereIn('huvant_role', $roles)->orWhereNull('huvant_role'));
    }

    /**
     * Gives a role to the items that have none, from what the registers said about them: lab items
     * are raw materials, rentals were named "Noleggio …" or "(rental)", staff is a service.
     */
    public static function backfill(): void
    {
        $products = DB::table('products_products')->whereNull('huvant_role');

        (clone $products)->whereNotNull('huvant_lab_kind')->update(['huvant_role' => ItemRole::Material->value]);

        $rentals = (clone $products)->where(fn ($query) => $query->where('name', 'like', 'Noleggio%')->orWhere('name', 'like', '%(rental)%'));
        $rentals->update([
            'huvant_role' => ItemRole::Rental->value,
            'type'        => ProductType::GOODS->value,
            'is_storable' => true,
            'tracking'    => ProductTracking::QTY->value,
        ]);

        (clone $products)->where('type', ProductType::SERVICE->value)->update(['huvant_role' => ItemRole::Service->value]);
        (clone $products)->update([
            'huvant_role' => ItemRole::Product->value,
            'type'        => ProductType::GOODS->value,
            'is_storable' => true,
        ]);

        // Products are traced by lot, as when they are saved.
        DB::table('products_products')
            ->where('huvant_role', ItemRole::Product->value)
            ->where('tracking', ProductTracking::QTY->value)
            ->update(['tracking' => ProductTracking::LOT->value]);
    }
}
