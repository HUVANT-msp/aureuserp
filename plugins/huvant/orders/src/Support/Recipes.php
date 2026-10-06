<?php

namespace Huvant\Orders\Support;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Huvant\Orders\Enums\ItemRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Webkul\Manufacturing\Enums\BillOfMaterialConsumption;
use Webkul\Manufacturing\Enums\BillOfMaterialType;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\BillOfMaterialLine;
use Webkul\Product\Models\Product;

/**
 * What one piece of a product takes, written on the product itself: raw materials (or other
 * products, for a kit) with their quantity in any unit. It is kept as the product's bill of
 * materials, so manufacturing orders consume it.
 */
class Recipes
{
    public static function section(): Section
    {
        return Section::make('Materials per piece')
            ->description('What one piece takes. Confirmed orders produce it and take these quantities from production stock.')
            ->schema([
                Repeater::make('huvant_recipe')
                    ->hiddenLabel()
                    ->dehydrated(false)
                    ->defaultItems(0)
                    ->addActionLabel('Add material')
                    ->afterStateHydrated(function (Repeater $component, ?Model $record): void {
                        $component->state($record ? static::lines($record) : []);
                    })
                    ->saveRelationshipsUsing(fn (Model $record, ?array $state) => static::save($record, $state ?? []))
                    ->schema([
                        Select::make('product_id')
                            ->label('Material')
                            ->options(fn (?Model $record): array => static::componentOptions($record))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('unit', Product::query()->find($state)?->uom_id)),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(0.0001)
                            ->required(),
                        Select::make('unit')
                            ->label('Unit')
                            ->options(fn (Get $get): array => ($component = Product::query()->find($get('product_id'))) ? LabUnits::options($component) : [])
                            ->required(),
                    ])
                    ->columns(3),
            ])
            ->visible(fn (Get $get): bool => RecordFields::role($get) === ItemRole::Product);
    }

    /** @return array<int, string> raw materials first, then products (for kits) */
    public static function componentOptions(?Model $record): array
    {
        // Components already in the bill of materials stay selectable, whatever their role.
        $current = $record ? (static::bom($record)?->lines()->pluck('product_id')->all() ?? []) : [];

        return Product::query()
            ->where(fn ($query) => $query->whereIn('huvant_role', ItemRole::components())->orWhereIn('id', $current))
            ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
            ->orderByRaw("huvant_role = '".ItemRole::Material->value."' desc")
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Product $product): array => [$product->id => trim(($product->reference ? "[{$product->reference}] " : '').$product->name)])
            ->all();
    }

    /** The product's bill of materials as recipe lines, in each material's own unit. @return array<int, array<string, mixed>> */
    public static function lines(Model $product): array
    {
        $bom = static::bom($product);

        if (! $bom) {
            return [];
        }

        return $bom->lines()->orderBy('sort')->orderBy('id')->get()
            ->map(fn (BillOfMaterialLine $line): array => [
                'product_id' => $line->product_id,
                'quantity'   => (float) $line->quantity,
                'unit'       => $line->uom_id,
            ])
            ->all();
    }

    /**
     * Writes the recipe as the product's bill of materials: quantities converted to each material's
     * unit, one piece produced.
     *
     * @param  array<int|string, array<string, mixed>>  $lines
     */
    public static function save(Model $product, array $lines): void
    {
        $lines = array_values(array_filter($lines, fn (array $line): bool => filled($line['product_id'] ?? null) && (float) ($line['quantity'] ?? 0) > 0));
        $bom = static::bom($product);

        if (! $bom && $lines === []) {
            return;
        }

        DB::transaction(function () use ($product, $lines, $bom): void {
            $bom ??= BillOfMaterial::query()->create([
                'product_id'  => $product->getKey(),
                'uom_id'      => $product->uom_id,
                'quantity'    => 1,
                'type'        => BillOfMaterialType::NORMAL,
                'consumption' => BillOfMaterialConsumption::FLEXIBLE,
                'company_id'  => $product->company_id,
            ]);

            $bom->lines()->delete();

            foreach ($lines as $sort => $line) {
                $component = Product::query()->findOrFail($line['product_id']);
                $unit = $line['unit'] ?? $component->uom_id;

                BillOfMaterialLine::query()->create([
                    'bill_of_material_id' => $bom->id,
                    'sort'                => $sort,
                    'product_id'          => $component->id,
                    'uom_id'              => $component->uom_id,
                    'quantity'            => LabUnits::toMain($component, (float) $line['quantity'], is_numeric($unit) ? (int) $unit : $unit),
                    'company_id'          => $bom->company_id,
                ]);
            }
        });
    }

    /** The bill of materials the recipe edits: the product's first normal one. */
    public static function bom(Model $product): ?BillOfMaterial
    {
        return BillOfMaterial::query()
            ->where('product_id', $product->getKey())
            ->where('type', BillOfMaterialType::NORMAL)
            ->orderBy('id')
            ->first();
    }
}
