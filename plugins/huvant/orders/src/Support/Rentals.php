<?php

namespace Huvant\Orders\Support;

use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Models\OrderLine;
use Huvant\Orders\Models\RentalCategory;
use Illuminate\Support\Collection;

/**
 * Rentals are booked by category, not by single item: a category has a number of units, and every
 * order on hold or confirmed takes as many as its lines ask for, for the whole rental period.
 */
class Rentals
{
    /** Orders whose units are taken: on hold or confirmed and still open. */
    private const BOOKING_STATES = [OrderState::Pending, OrderState::Confirmed];

    /**
     * Booked lines that overlap the period, with their order and category.
     *
     * @return Collection<int, OrderLine>
     */
    public static function bookings(CarbonInterface $from, CarbonInterface $until, ?int $exceptOrderId = null): Collection
    {
        return OrderLine::query()
            ->with(['order.partner', 'product'])
            ->whereHas('product', fn ($query) => $query->whereNotNull('huvant_rental_category_id'))
            ->whereHas('order', fn ($query) => $query
                ->whereIn('state', self::BOOKING_STATES)
                ->when($exceptOrderId, fn ($query) => $query->whereKeyNot($exceptOrderId))
                ->whereNotNull('rental_starts_on')
                ->whereDate('rental_starts_on', '<=', $until->toDateString())
                ->whereDate('rental_ends_on', '>=', $from->toDateString()))
            ->get();
    }

    /**
     * Units taken per category and day.
     *
     * @return array<int, array<string, int>> category id => [Y-m-d => units]
     */
    public static function occupancy(CarbonInterface $from, CarbonInterface $until, ?int $exceptOrderId = null): array
    {
        $occupancy = [];

        foreach (static::bookings($from, $until, $exceptOrderId) as $line) {
            $start = $line->order->rental_starts_on->max($from);
            $end = $line->order->rental_ends_on->min($until);

            foreach (CarbonPeriod::create($start, $end) as $day) {
                $categoryId = $line->product->huvant_rental_category_id;
                $occupancy[$categoryId][$day->toDateString()] = ($occupancy[$categoryId][$day->toDateString()] ?? 0) + (int) ceil((float) $line->quantity);
            }
        }

        return $occupancy;
    }

    /**
     * Categories the order would overbook: what it asks for on top of the other bookings exceeds the units.
     *
     * @return array<int, string> human readable conflicts
     */
    public static function conflicts(Order $order): array
    {
        if (! $order->rental_starts_on || ! $order->rental_ends_on) {
            return [];
        }

        $requested = $order->lines()->with('product')->get()
            ->filter(fn (OrderLine $line): bool => (bool) $line->product?->huvant_rental_category_id)
            ->groupBy(fn (OrderLine $line): int => $line->product->huvant_rental_category_id)
            ->map(fn (Collection $lines): int => (int) ceil($lines->sum(fn (OrderLine $line): float => (float) $line->quantity)));

        if ($requested->isEmpty()) {
            return [];
        }

        $occupancy = static::occupancy($order->rental_starts_on, $order->rental_ends_on, $order->id);
        $categories = RentalCategory::query()->whereIn('id', $requested->keys())->get()->keyBy('id');
        $conflicts = [];

        foreach ($requested as $categoryId => $units) {
            $category = $categories[$categoryId];
            $peak = max([0, ...array_values($occupancy[$categoryId] ?? [])]);

            if ($peak + $units > $category->units) {
                $conflicts[] = sprintf('%s: %d requested, %d of %d already booked in that period', $category->name, $units, $peak, $category->units);
            }
        }

        return $conflicts;
    }

    /** True when the order rents something, so it needs a rental period. */
    public static function hasRentals(Order $order): bool
    {
        return $order->lines()->whereHas('product', fn ($query) => $query->whereNotNull('huvant_rental_category_id'))->exists();
    }
}
