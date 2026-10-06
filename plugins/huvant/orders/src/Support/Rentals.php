<?php

namespace Huvant\Orders\Support;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Enums\ShippingStatus;
use Huvant\Orders\Models\Delivery;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Models\OrderLine;
use Illuminate\Support\Collection;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Models\Move;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;

/**
 * Rentals are booked per item. How many pieces exist is what the warehouse says: the pieces in stock
 * plus those out with a customer and not back yet. Every order on hold or confirmed takes as many
 * as its lines ask for, for the whole rental period.
 */
class Rentals
{
    /** Orders whose pieces are taken: on hold or confirmed. */
    private const BOOKING_STATES = [OrderState::Pending, OrderState::Confirmed];

    /** @return Collection<int, Product> */
    public static function items(): Collection
    {
        return Product::query()->where('huvant_role', ItemRole::Rental->value)->orderBy('name')->get();
    }

    public static function isRental(?object $product): bool
    {
        $role = $product?->huvant_role;

        return ($role instanceof ItemRole ? $role : ItemRole::tryFrom((string) $role)) === ItemRole::Rental;
    }

    /** Pieces of the item the company owns: in its own locations, or lent out and not returned yet. */
    public static function units(Product $product): int
    {
        $inStock = (float) ProductQuantity::query()
            ->where('product_id', $product->id)
            ->whereHas('location', fn ($query) => $query->where('type', LocationType::INTERNAL))
            ->sum('quantity');

        $lentOut = (float) Move::query()
            ->where('product_id', $product->id)
            ->whereIn('operation_id', Delivery::query()
                ->whereNotNull('huvant_order_id')
                ->whereNull('return_id')
                ->where('state', OperationState::DONE)
                ->where(fn ($query) => $query->whereNull('huvant_shipping_status')->orWhere('huvant_shipping_status', '!=', ShippingStatus::Returned->value))
                ->select('id'))
            ->sum('quantity');

        return (int) round($inStock + $lentOut);
    }

    /**
     * Booked rental lines that overlap the period, with their order and item.
     *
     * @return Collection<int, OrderLine>
     */
    public static function bookings(CarbonInterface $from, CarbonInterface $until, ?int $exceptOrderId = null): Collection
    {
        return OrderLine::query()
            ->with(['order.partner', 'product'])
            ->whereHas('product', fn ($query) => $query->where('huvant_role', ItemRole::Rental->value))
            ->whereHas('order', fn ($query) => $query
                ->whereIn('state', self::BOOKING_STATES)
                ->when($exceptOrderId, fn ($query) => $query->whereKeyNot($exceptOrderId))
                ->whereNotNull('rental_starts_on')
                ->whereDate('rental_starts_on', '<=', $until->toDateString())
                ->whereDate('rental_ends_on', '>=', $from->toDateString()))
            ->get();
    }

    /**
     * Pieces taken per item and day.
     *
     * @return array<int, array<string, int>> product id => [Y-m-d => pieces]
     */
    public static function occupancy(CarbonInterface $from, CarbonInterface $until, ?int $exceptOrderId = null): array
    {
        $occupancy = [];

        foreach (static::bookings($from, $until, $exceptOrderId) as $line) {
            $start = $line->order->rental_starts_on->max($from);
            $end = $line->order->rental_ends_on->min($until);

            foreach (CarbonPeriod::create($start, $end) as $day) {
                $occupancy[$line->product_id][$day->toDateString()] = ($occupancy[$line->product_id][$day->toDateString()] ?? 0) + (int) ceil((float) $line->quantity);
            }
        }

        return $occupancy;
    }

    /**
     * Items the order would overbook: what it asks for on top of the other bookings exceeds the pieces.
     *
     * @return array<int, string> human readable conflicts
     */
    public static function conflicts(Order $order): array
    {
        if (! $order->rental_starts_on || ! $order->rental_ends_on) {
            return [];
        }

        $requested = $order->lines()->with('product')->get()
            ->filter(fn (OrderLine $line): bool => static::isRental($line->product))
            ->groupBy('product_id')
            ->map(fn (Collection $lines): int => (int) ceil($lines->sum(fn (OrderLine $line): float => (float) $line->quantity)));

        if ($requested->isEmpty()) {
            return [];
        }

        $occupancy = static::occupancy($order->rental_starts_on, $order->rental_ends_on, $order->id);
        $conflicts = [];

        foreach ($requested as $productId => $pieces) {
            $product = Product::query()->findOrFail($productId);
            $units = static::units($product);
            $peak = max([0, ...array_values($occupancy[$productId] ?? [])]);

            if ($peak + $pieces > $units) {
                $conflicts[] = sprintf('%s: %d requested, %d of %d already booked in that period', $product->name, $pieces, $peak, $units);
            }
        }

        return $conflicts;
    }

    /** True when the order rents something, so it needs a rental period. */
    public static function hasRentals(Order $order): bool
    {
        return $order->lines()->whereHas('product', fn ($query) => $query->where('huvant_role', ItemRole::Rental->value))->exists();
    }
}
