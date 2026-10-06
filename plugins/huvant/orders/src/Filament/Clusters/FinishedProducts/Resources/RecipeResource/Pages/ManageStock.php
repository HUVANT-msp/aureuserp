<?php

namespace Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Huvant\Orders\Enums\UnitStatus;
use Huvant\Orders\Filament\Clusters\FinishedProducts\Resources\RecipeResource;
use Huvant\Orders\Filament\Concerns\ShowsUnitMaterials;
use Huvant\Orders\Models\MaterialLot;
use Huvant\Orders\Models\ProductUnit;
use Huvant\Orders\Models\RecipeLine;
use Huvant\Orders\Settings\OrdersSettings;
use Huvant\Orders\Support\LabInventory;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use RuntimeException;
use Webkul\Product\Models\Product;

/** The pieces of one product, each with its code; new pieces are added here. */
class ManageStock extends Page implements HasTable
{
    use InteractsWithRecord;
    use InteractsWithTable;
    use ShowsUnitMaterials;

    protected static string $resource = RecipeResource::class;

    protected string $view = 'huvant-orders::filament.lab.table-page';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::lab.stock');
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    public function getSubheading(): ?string
    {
        $counts = LabInventory::counts($this->record);

        return __('huvant-orders::lab.stock_summary', $counts);
    }

    protected function getHeaderActions(): array
    {
        return [$this->addToStockAction()];
    }

    public function addToStockAction(): Action
    {
        /** @var Product $product */
        $product = $this->record;
        $recipe = LabInventory::recipe($product);

        return Action::make('addToStock')
            ->label(__('huvant-orders::lab.add_to_stock'))
            ->icon('heroicon-o-plus-circle')
            ->modalWidth('2xl')
            ->schema(array_values(array_filter([
                DatePicker::make('production_date')
                    ->label(__('huvant-orders::lab.production_date'))
                    ->default(today())
                    ->maxDate(today())
                    ->required()
                    ->live(),
                TextInput::make('pieces')
                    ->label(__('huvant-orders::lab.pieces'))
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(50)
                    ->default(1)
                    ->required()
                    ->live(onBlur: true),
                Placeholder::make('code')
                    ->label(__('huvant-orders::lab.code'))
                    ->content(fn (Get $get): string => $this->previewCodes($get)),
                Placeholder::make('expiry')
                    ->label(__('huvant-orders::lab.expires_on'))
                    ->content(fn (Get $get): string => LabInventory::expiryFor($product, Carbon::parse($get('production_date') ?: today()))?->format('d/m/Y')
                        ?? __('huvant-orders::lab.no_shelf_life')),
                $recipe->isEmpty() ? null : Section::make(__('huvant-orders::lab.material_lots_used'))
                    ->description(__('huvant-orders::lab.one_lot_per_material'))
                    ->schema($recipe->map(fn (RecipeLine $line) => Select::make("lots.{$line->material_id}")
                        ->label(fn (Get $get): string => sprintf(
                            '%s · %s',
                            $line->material->name,
                            LabInventory::format((float) $line->quantity * max(1, (int) $get('../pieces')), $line->material->huvant_package_unit),
                        ))
                        ->options(fn (): array => LabInventory::availableLots($line->material_id)->mapWithKeys(fn (MaterialLot $lot): array => [
                            $lot->id => __('huvant-orders::lab.lot_option', [
                                'lot'      => $lot->lot_number,
                                'quantity' => LabInventory::format((float) $lot->remaining_quantity, $line->material->huvant_package_unit),
                                'expiry'   => $lot->expiry_date ? __('huvant-orders::lab.lot_expiry_suffix', ['date' => $lot->expiry_date->format('d/m/Y')]) : '',
                            ]),
                        ])->all())
                        ->default(fn (): ?int => LabInventory::availableLots($line->material_id)
                            ->first(fn (MaterialLot $lot): bool => (float) $lot->remaining_quantity >= (float) $line->quantity)?->id)
                        ->required()
                        ->helperText(fn (): ?string => LabInventory::availableLots($line->material_id)->isEmpty() ? __('huvant-orders::lab.no_lot_available') : null))
                        ->all())
                    ->columns(1),
                Textarea::make('notes')
                    ->label(__('huvant-orders::lab.notes'))
                    ->rows(2),
            ])))
            ->action(function (array $data) use ($product): void {
                try {
                    $units = LabInventory::produce($product, Carbon::parse($data['production_date']), (int) $data['pieces'], $data['lots'] ?? [], $data['notes'] ?? null);
                } catch (RuntimeException $e) {
                    Notification::make()->danger()->title($e->getMessage())->persistent()->send();

                    return;
                }

                Notification::make()->success()->title($units->count() === 1
                    ? __('huvant-orders::lab.added_unit', ['code' => $units->first()->code])
                    : __('huvant-orders::lab.added_units', [
                        'count' => $units->count(),
                        'first' => $units->first()->code,
                        'last'  => $units->last()->code,
                    ]))->send();
            });
    }

    protected function previewCodes(Get $get): string
    {
        $date = Carbon::parse($get('production_date') ?: today());
        $pieces = max(1, (int) $get('pieces'));
        $first = LabInventory::nextCode($this->record, $date);

        return $pieces === 1 ? $first : $first.' … '.LabInventory::nextCode($this->record, $date, $pieces - 1);
    }

    public function table(Table $table): Table
    {
        $warningDays = app(OrdersSettings::class)->expiry_warning_days;

        return $table
            ->query(ProductUnit::query()->where('product_id', $this->record->getKey())->withCount('materials'))
            ->defaultSort('code', 'desc')
            ->columns([
                TextColumn::make('code')->label(__('huvant-orders::lab.code'))->fontFamily('mono')->searchable()->copyable(),
                TextColumn::make('production_date')->label(__('huvant-orders::lab.produced'))->date('d/m/Y')->sortable(),
                TextColumn::make('expiry_date')
                    ->label(__('huvant-orders::lab.expires'))
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->color(fn (ProductUnit $record): ?string => match (true) {
                        $record->expiry_date?->endOfDay()->isPast() === true                        => 'danger',
                        $record->expiry_date?->isBefore(today()->addDays($warningDays)) === true    => 'warning',
                        default                                                                     => null,
                    })
                    ->sortable(),
                TextColumn::make('status')->label(__('huvant-orders::lab.where'))->badge(),
                TextColumn::make('status_since')->label(__('huvant-orders::lab.since'))->date('d/m/Y')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('materials_count')->label(__('huvant-orders::lab.material_lots')),
                TextColumn::make('notes')->label(__('huvant-orders::lab.notes'))->limit(40)->placeholder('—')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('huvant-orders::lab.where'))->options(UnitStatus::class)->default(UnitStatus::InLab->value),
            ])
            ->recordActions([$this->materialsAction()])
            ->emptyStateHeading(__('huvant-orders::lab.no_pieces'))
            ->emptyStateDescription(__('huvant-orders::lab.no_pieces_help'));
    }
}
