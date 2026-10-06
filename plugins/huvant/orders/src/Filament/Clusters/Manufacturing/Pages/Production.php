<?php

namespace Huvant\Orders\Filament\Clusters\Manufacturing\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Huvant\Orders\Enums\ProductionTaskStatus;
use Huvant\Orders\Filament\Clusters\Manufacturing;
use Huvant\Orders\Filament\Clusters\Manufacturing\Resources\OfferResource;
use Huvant\Orders\Models\MaterialLot;
use Huvant\Orders\Models\ProductionTask;
use Huvant\Orders\Models\RecipeLine;
use Huvant\Orders\Support\LabInventory;
use Huvant\Orders\Support\ManufacturingFlow;
use Illuminate\Support\Carbon;
use RuntimeException;

class Production extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'huvant-orders::filament.lab.table-page';

    protected static ?string $cluster = Manufacturing::class;

    protected static ?string $slug = 'production';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('huvant-orders::manufacturing.production');
    }

    public function getTitle(): string
    {
        return __('huvant-orders::manufacturing.production');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = ProductionTask::query()->where('status', ProductionTaskStatus::Pending)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProductionTask::query()->with(['order.partner', 'product', 'managedBy']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order.order_number')
                    ->label(__('huvant-orders::manufacturing.offer'))
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label(__('huvant-orders::manufacturing.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('huvant-orders::manufacturing.to_produce'))
                    ->numeric(),
                TextColumn::make('completed_quantity')
                    ->label(__('huvant-orders::manufacturing.completed'))
                    ->numeric(),
                TextColumn::make('order.expected_delivery_date')
                    ->label(__('huvant-orders::manufacturing.deadline'))
                    ->date('d/m/Y')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('order.partner.name')
                    ->label(__('huvant-orders::manufacturing.customer'))
                    ->placeholder(__('huvant-orders::manufacturing.stock_order'))
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('huvant-orders::manufacturing.status'))
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('huvant-orders::manufacturing.status'))
                    ->options(ProductionTaskStatus::class)
                    ->default(ProductionTaskStatus::Pending->value),
            ])
            ->recordActions([
                $this->produceAction(),
                Action::make('offer')
                    ->label(__('huvant-orders::manufacturing.open_offer'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (ProductionTask $record): string => OfferResource::getUrl('manage', ['record' => $record->order_id])),
            ])
            ->emptyStateHeading(__('huvant-orders::manufacturing.no_production'))
            ->emptyStateDescription(__('huvant-orders::manufacturing.no_production_help'));
    }

    public function produceAction(): Action
    {
        return Action::make('produce')
            ->label(__('huvant-orders::manufacturing.record_production'))
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (ProductionTask $record): bool => $record->status === ProductionTaskStatus::Pending)
            ->modalWidth('2xl')
            ->schema(fn (ProductionTask $record): array => $this->productionSchema($record))
            ->action(function (ProductionTask $record, array $data): void {
                try {
                    $units = ManufacturingFlow::completeProduction(
                        $record,
                        (int) $data['pieces'],
                        Carbon::parse($data['production_date']),
                        $data['lots'] ?? [],
                        $data['notes'] ?? null,
                    );
                } catch (RuntimeException $exception) {
                    Notification::make()->danger()->title($exception->getMessage())->persistent()->send();

                    return;
                }

                Notification::make()->success()->title(__('huvant-orders::manufacturing.production_recorded', [
                    'count' => $units->count(),
                ]))->send();
            });
    }

    /** @return array<int, mixed> */
    protected function productionSchema(ProductionTask $task): array
    {
        $recipe = LabInventory::recipe($task->product);

        return array_values(array_filter([
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
                ->maxValue($task->remaining())
                ->default($task->remaining())
                ->required()
                ->live(onBlur: true),
            Placeholder::make('code')
                ->label(__('huvant-orders::lab.code'))
                ->content(fn (Get $get): string => $this->previewCodes($task, $get)),
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
                        ->first(fn (MaterialLot $lot): bool => (float) $lot->remaining_quantity >= (float) $line->quantity * $task->remaining())?->id)
                    ->required()
                    ->helperText(fn (): ?string => LabInventory::availableLots($line->material_id)->isEmpty() ? __('huvant-orders::lab.no_lot_available') : null))
                    ->all()),
            Textarea::make('notes')
                ->label(__('huvant-orders::lab.notes'))
                ->rows(2),
        ]));
    }

    protected function previewCodes(ProductionTask $task, Get $get): string
    {
        $date = Carbon::parse($get('production_date') ?: today());
        $pieces = max(1, min($task->remaining(), (int) $get('pieces')));
        $first = LabInventory::nextCode($task->product, $date);

        return $pieces === 1 ? $first : $first.' … '.LabInventory::nextCode($task->product, $date, $pieces - 1);
    }
}
