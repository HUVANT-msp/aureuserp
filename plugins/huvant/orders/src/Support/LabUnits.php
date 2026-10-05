<?php

namespace Huvant\Orders\Support;

use RuntimeException;
use Webkul\Product\Models\Product;
use Webkul\Support\Enums\UOMType;
use Webkul\Support\Models\UOM;
use Webkul\Support\Models\UOMCategory;

/**
 * Lab quantities: every item is stocked in one main unit (g, mL, cm or units) but can be entered in
 * any unit of the same kind, or across weight and volume through the item's density (g/mL).
 * A package ("250 mL") is only a shortcut for loading.
 */
class LabUnits
{
    public const PACKAGE = 'package';

    /** Units offered to the lab, smallest first; the others in the ERP (oz, gal...) stay out of the way. */
    private const METRIC = ['mg', 'g', 'kg', 'mL', 'L', 'mm', 'cm', 'm', 'Units'];

    /** The ERP ships kg/g and L but not mg or mL: added on install. */
    public static function ensureUnits(): void
    {
        foreach (['Weight' => ['mg', 1_000_000.0], 'Volume' => ['mL', 1000.0]] as $categoryName => [$name, $factor]) {
            $category = UOMCategory::query()->where('name', $categoryName)->first();

            if ($category && ! UOM::query()->where('name', $name)->where('category_id', $category->id)->exists()) {
                UOM::query()->create([
                    'type'        => UOMType::SMALLER,
                    'name'        => $name,
                    'factor'      => $factor,
                    'rounding'    => 0.01,
                    'category_id' => $category->id,
                ]);
            }
        }
    }

    public static function unit(string $name, ?string $categoryName = null): ?UOM
    {
        return UOM::query()
            ->where('name', $name)
            ->when($categoryName, fn ($query) => $query->whereHas('category', fn ($query) => $query->where('name', $categoryName)))
            ->first();
    }

    /**
     * Units a quantity of this item can be entered in.
     *
     * @return array<int|string, string>
     */
    public static function options(Product $product, bool $withPackage = false): array
    {
        $categories = [$product->uom->category_id];

        if (static::density($product) && ($other = static::otherMassOrVolumeCategory($product))) {
            $categories[] = $other;
        }

        $options = UOM::query()
            ->whereIn('category_id', $categories)
            ->whereIn('name', self::METRIC)
            ->get()
            ->sortBy(fn (UOM $unit): int => array_search($unit->name, self::METRIC, true))
            ->mapWithKeys(fn (UOM $unit): array => [$unit->id => $unit->name])
            ->all();

        if ($withPackage && static::packageSize($product)) {
            $options[self::PACKAGE] = sprintf('Packages (%s)', static::describePackage($product));
        }

        return $options;
    }

    /** Converts a quantity entered in any unit (or in packages) into the item's main unit. */
    public static function toMain(Product $product, float $quantity, int|string $unit): float
    {
        if ($unit === self::PACKAGE) {
            $size = static::packageSize($product) ?? throw new RuntimeException('This item has no package size.');

            return static::toMain($product, $quantity * $size, (int) $product->huvant_package_uom_id);
        }

        $from = UOM::query()->findOrFail($unit);
        $to = $product->uom;

        if ($from->category_id === $to->category_id) {
            return (float) $from->computeQuantity($quantity, $to, round: false);
        }

        $density = static::density($product) ?? throw new RuntimeException("Set the density of {$product->name} to convert between weight and volume.");
        $weight = UOMCategory::query()->where('name', 'Weight')->value('id');
        $volume = UOMCategory::query()->where('name', 'Volume')->value('id');

        // Through the reference units: 1 L of a liquid at density d g/mL weighs d kg.
        $reference = $quantity / (float) $from->factor;

        $converted = match (true) {
            $from->category_id === $volume && $to->category_id === $weight => $reference * $density,
            $from->category_id === $weight && $to->category_id === $volume => $reference / $density,
            default                                                        => throw new RuntimeException("{$from->name} cannot be converted to {$to->name}."),
        };

        return $converted * (float) $to->factor;
    }

    public static function format(Product $product, float $quantity): string
    {
        return rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',').' '.$product->uom->name;
    }

    public static function density(Product $product): ?float
    {
        return (float) $product->huvant_density > 0 ? (float) $product->huvant_density : null;
    }

    public static function packageSize(Product $product): ?float
    {
        return (float) $product->huvant_package_quantity > 0 && $product->huvant_package_uom_id ? (float) $product->huvant_package_quantity : null;
    }

    public static function describePackage(Product $product): ?string
    {
        if (! static::packageSize($product)) {
            return null;
        }

        $unit = UOM::query()->find($product->huvant_package_uom_id)?->name;

        return rtrim(rtrim(number_format((float) $product->huvant_package_quantity, 2, ',', '.'), '0'), ',').' '.$unit;
    }

    /**
     * "250 mL", "500 g", "25000 g": the quantity and the unit, when the text says so.
     *
     * @return array{0: float, 1: UOM}|null
     */
    public static function parsePackage(?string $text): ?array
    {
        if (! $text || ! preg_match('/^\s*([\d.,]+)\s*(mg|g|kg|mL|ml|L|l|cm|m|pz)\s*$/', $text, $match)) {
            return null;
        }

        $name = match ($match[2]) {
            'ml'    => 'mL',
            'l'     => 'L',
            'pz'    => 'Units',
            default => $match[2],
        };

        $unit = static::unit($name);

        return $unit ? [(float) str_replace(',', '.', $match[1]), $unit] : null;
    }

    private static function otherMassOrVolumeCategory(Product $product): ?int
    {
        $name = $product->uom->category?->name;

        return match ($name) {
            'Weight' => UOMCategory::query()->where('name', 'Volume')->value('id'),
            'Volume' => UOMCategory::query()->where('name', 'Weight')->value('id'),
            default  => null,
        };
    }
}
