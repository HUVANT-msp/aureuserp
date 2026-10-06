<?php

namespace Huvant\Orders\Support;

use Carbon\CarbonInterface;
use Huvant\Orders\Enums\ItemRole;
use Huvant\Orders\Enums\LabArea;
use Huvant\Orders\Enums\ShelfLifeUnit;
use Huvant\Orders\Enums\UnitStatus;
use Huvant\Orders\Models\MaterialLot;
use Huvant\Orders\Models\ProductUnit;
use Huvant\Orders\Models\ProductUnitMaterial;
use Huvant\Orders\Models\RecipeLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Webkul\Product\Models\Product;

/**
 * The lab inventory: raw materials kept as packages, each with its lot and what is left of it, and
 * finished pieces, each with its identification code and the material lots it was made from.
 * Quantities are in each material's own unit (the unit of its package); nothing is converted.
 */
class LabInventory
{
    /** Stock within this share above the minimum shows as running low. */
    public const NEAR_MINIMUM = 1.25;

    /** @return Collection<int, Product> */
    public static function materials(): Collection
    {
        return Product::query()->where('huvant_role', ItemRole::Material->value)->orderBy('name')->get();
    }

    /** @return Collection<int, Product> */
    public static function finishedProducts(): Collection
    {
        return Product::query()->where('huvant_role', ItemRole::Product->value)->orderBy('name')->get();
    }

    public static function unit(Product $material): string
    {
        return (string) $material->huvant_package_unit;
    }

    public static function format(float $quantity, ?string $unit = null): string
    {
        return trim(rtrim(rtrim(number_format($quantity, 2, ',', '.'), '0'), ',').' '.$unit);
    }

    // ---- Recipes ---------------------------------------------------------------------------

    /** @return Collection<int, RecipeLine> */
    public static function recipe(Product $product): Collection
    {
        return RecipeLine::query()->with('material')->where('product_id', $product->getKey())->orderBy('sort')->orderBy('id')->get();
    }

    /** @param  array<int|string, array{material_id: int|string|null, quantity: float|string|null}>  $lines */
    public static function saveRecipe(Product $product, array $lines): void
    {
        DB::transaction(function () use ($product, $lines): void {
            RecipeLine::query()->where('product_id', $product->getKey())->delete();

            foreach (array_values($lines) as $sort => $line) {
                if (blank($line['material_id'] ?? null) || (float) ($line['quantity'] ?? 0) <= 0) {
                    continue;
                }

                RecipeLine::query()->create([
                    'product_id'  => $product->getKey(),
                    'material_id' => (int) $line['material_id'],
                    'quantity'    => (float) $line['quantity'],
                    'sort'        => $sort,
                ]);
            }
        });
    }

    // ---- Raw materials in the lab ----------------------------------------------------------

    /**
     * Packages received: one row each, all with the same lot and expiry.
     *
     * @return Collection<int, MaterialLot>
     */
    public static function addPackages(Product $material, string $lotNumber, ?CarbonInterface $expiry, int $packages = 1, ?float $quantity = null, LabArea $area = LabArea::Production, ?CarbonInterface $receivedOn = null, ?string $notes = null): Collection
    {
        $quantity ??= (float) $material->huvant_package_quantity;

        if ($quantity <= 0) {
            throw new RuntimeException(__('huvant-orders::lab.error_package_quantity', ['material' => $material->name]));
        }

        if ($packages < 1) {
            throw new RuntimeException(__('huvant-orders::lab.error_at_least_one_package'));
        }

        return DB::transaction(fn (): Collection => collect(range(1, $packages))->map(fn (): MaterialLot => MaterialLot::query()->create([
            'material_id'        => $material->getKey(),
            'lot_number'         => trim($lotNumber),
            'expiry_date'        => $expiry?->toDateString(),
            'received_on'        => ($receivedOn ?? today())->toDateString(),
            'area'               => $area,
            'initial_quantity'   => $quantity,
            'remaining_quantity' => $quantity,
            'notes'              => $notes,
        ])));
    }

    public static function setRemaining(MaterialLot $lot, float $remaining): MaterialLot
    {
        if ($remaining < 0 || $remaining > (float) $lot->initial_quantity) {
            throw new RuntimeException(__('huvant-orders::lab.error_remaining_range', [
                'maximum' => static::format((float) $lot->initial_quantity, static::unit($lot->material)),
            ]));
        }

        $lot->update(['remaining_quantity' => $remaining, 'finished_at' => $remaining > 0 ? null : now()]);

        return $lot;
    }

    public static function finish(MaterialLot $lot): MaterialLot
    {
        $lot->update(['remaining_quantity' => 0, 'finished_at' => now()]);

        return $lot;
    }

    /**
     * Packages of a material still in the lab, those expiring first first.
     *
     * @return Collection<int, MaterialLot>
     */
    public static function availableLots(Product|int $material, ?LabArea $area = LabArea::Production): Collection
    {
        return MaterialLot::query()
            ->inLab()
            ->where('material_id', $material instanceof Product ? $material->getKey() : $material)
            ->when($area, fn ($query) => $query->where('area', $area))
            ->orderByRaw('expiry_date is null, expiry_date')
            ->orderBy('id')
            ->get();
    }

    /** What is left of a material for production, all packages together. */
    public static function total(Product $material, ?LabArea $area = LabArea::Production): float
    {
        return (float) MaterialLot::query()
            ->inLab()
            ->where('material_id', $material->getKey())
            ->when($area, fn ($query) => $query->where('area', $area))
            ->sum('remaining_quantity');
    }

    /** "ok", "low" (near the minimum), "below" the minimum, or "unset" without a minimum. */
    public static function level(Product $material, float $total): string
    {
        $minimum = (float) $material->huvant_min_quantity;

        return match (true) {
            $minimum <= 0                                  => 'unset',
            $total < $minimum                              => 'below',
            $total < $minimum * self::NEAR_MINIMUM         => 'low',
            default                                        => 'ok',
        };
    }

    // ---- Finished pieces -------------------------------------------------------------------

    /** The code prefix of a product: its acronym, or one made from its name. */
    public static function prefix(Product $product): string
    {
        $prefix = Str::upper(trim((string) $product->huvant_code_prefix));

        return $prefix !== '' ? $prefix : Str::upper(Str::substr(Str::slug($product->name, ''), 0, 3));
    }

    /** PREFIX-YYYYMMDD-NN: the production date, then a counter of the pieces made that day. */
    public static function nextCode(Product $product, CarbonInterface $date, int $offset = 0): string
    {
        $stem = static::prefix($product).'-'.$date->format('Ymd').'-';

        $last = ProductUnit::query()->where('code', 'like', $stem.'%')->pluck('code')
            ->map(fn (string $code): int => (int) Str::afterLast($code, '-'))
            ->max() ?? 0;

        return $stem.str_pad((string) ($last + 1 + $offset), 2, '0', STR_PAD_LEFT);
    }

    public static function expiryFor(Product $product, CarbonInterface $productionDate): ?CarbonInterface
    {
        $unit = ShelfLifeUnit::tryFrom((string) $product->huvant_shelf_life_unit);

        return $unit && (int) $product->huvant_shelf_life > 0 ? $unit->addTo($productionDate, (int) $product->huvant_shelf_life) : null;
    }

    /**
     * Pieces made: each gets its code and expiry, and the recipe quantities are taken from the
     * chosen material lot (one lot per material).
     *
     * @param  array<int|string, int|string|null>  $lots  material id => material lot id
     * @return Collection<int, ProductUnit>
     */
    public static function produce(Product $product, CarbonInterface $productionDate, int $pieces = 1, array $lots = [], ?string $notes = null): Collection
    {
        if ($pieces < 1) {
            throw new RuntimeException(__('huvant-orders::lab.error_at_least_one_piece'));
        }

        return DB::transaction(function () use ($product, $productionDate, $pieces, $lots, $notes): Collection {
            $usage = [];

            foreach (static::recipe($product) as $line) {
                $lotId = $lots[$line->material_id] ?? null;
                $needed = (float) $line->quantity * $pieces;
                $unit = static::unit($line->material);

                if (! $lotId) {
                    throw new RuntimeException(__('huvant-orders::lab.error_choose_lot', ['material' => $line->material->name]));
                }

                $lot = MaterialLot::query()->inLab()->where('material_id', $line->material_id)->lockForUpdate()->find($lotId)
                    ?? throw new RuntimeException(__('huvant-orders::lab.error_lot_unavailable', ['material' => $line->material->name]));

                if ((float) $lot->remaining_quantity + 0.00001 < $needed) {
                    throw new RuntimeException(__('huvant-orders::lab.error_not_enough', [
                        'material' => $line->material->name,
                        'lot'      => $lot->lot_number,
                        'needed'   => static::format($needed, $unit),
                        'left'     => static::format((float) $lot->remaining_quantity, $unit),
                    ]));
                }

                $left = max(0, (float) $lot->remaining_quantity - $needed);
                $lot->update(['remaining_quantity' => $left, 'finished_at' => $left > 0 ? null : now()]);
                $usage[] = [$line, $lot];
            }

            $expiry = static::expiryFor($product, $productionDate);

            return collect(range(0, $pieces - 1))->map(function (int $offset) use ($product, $productionDate, $expiry, $usage, $notes): ProductUnit {
                $unit = ProductUnit::query()->create([
                    'product_id'      => $product->getKey(),
                    'code'            => static::nextCode($product, $productionDate),
                    'production_date' => $productionDate->toDateString(),
                    'expiry_date'     => $expiry?->toDateString(),
                    'status'          => UnitStatus::InLab,
                    'status_since'    => $productionDate->toDateString(),
                    'notes'           => $notes,
                ]);

                foreach ($usage as [$line, $lot]) {
                    ProductUnitMaterial::query()->create([
                        'product_unit_id' => $unit->id,
                        'material_id'     => $line->material_id,
                        'material_lot_id' => $lot->id,
                        'lot_number'      => $lot->lot_number,
                        'quantity'        => $line->quantity,
                    ]);
                }

                return $unit;
            });
        });
    }

    /** @return array{in_lab: int, out: int, sold: int} */
    public static function counts(Product $product): array
    {
        $counts = ProductUnit::query()->where('product_id', $product->getKey())
            ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return [
            'in_lab' => (int) ($counts[UnitStatus::InLab->value] ?? 0),
            'out'    => (int) ($counts[UnitStatus::Out->value] ?? 0),
            'sold'   => (int) ($counts[UnitStatus::Sold->value] ?? 0),
        ];
    }
}
