<?php

namespace Huvant\Orders\Filament\Clusters\Manufacturing\Resources\OfferResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Enums\UnitStatus;
use Huvant\Orders\Filament\Clusters\Manufacturing\Resources\OfferResource;
use Huvant\Orders\Models\ManufacturingEntry;
use Huvant\Orders\Models\ProductUnit;
use Huvant\Orders\Support\ManufacturingFlow;
use Huvant\Orders\Support\ProductionProjects;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use RuntimeException;

class ManageOffer extends Page
{
    use InteractsWithRecord;

    protected static string $resource = OfferResource::class;

    protected string $view = 'huvant-orders::filament.manufacturing.manage-offer';

    public ?int $stockEntryId = null;

    public ?int $selectedUnitId = null;

    public ?int $productionEntryId = null;

    /** @var list<int|string> */
    public array $productionAssignees = [];

    public ?string $productionDeadline = null;

    public ?int $materialsUnitId = null;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        abort_unless($this->record->state === OrderState::Confirmed, 404);
        ManufacturingFlow::prepareEntries($this->record);
    }

    public function getTitle(): string|Htmlable
    {
        return __('huvant-orders::manufacturing.manage_offer', ['order' => $this->record->order_number]);
    }

    public function getSubheading(): ?string
    {
        return trim(($this->record->partner?->name ? $this->record->partner->name.' · ' : '').$this->record->name);
    }

    /** @return array<int, mixed> */
    public function getSubNavigation(): array
    {
        $cluster = static::getCluster();

        return $cluster
            ? $this->generateNavigationItems($cluster::getClusteredComponents())
            : [];
    }

    public function openStock(int $entryId): void
    {
        $entry = $this->entry($entryId);
        $this->stockEntryId = $entry->getKey();
        $this->selectedUnitId = $entry->product_unit_id;
        $this->resetValidation();
        $this->dispatch('open-modal', id: 'hv-stock-choice');
    }

    public function chooseStock(): void
    {
        $this->validate([
            'stockEntryId'   => ['required', 'integer'],
            'selectedUnitId' => ['required', 'integer'],
        ]);

        try {
            ManufacturingFlow::assignStock(
                $this->entry($this->stockEntryId),
                ProductUnit::query()->findOrFail($this->selectedUnitId),
            );
        } catch (RuntimeException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->persistent()->send();

            return;
        }

        $this->dispatch('close-modal', id: 'hv-stock-choice');
        Notification::make()->success()->title(__('huvant-orders::manufacturing.stock_unit_selected'))->send();
    }

    public function openProduction(int $entryId): void
    {
        $entry = $this->entry($entryId)->load('productionTask.projectTask.users');
        $this->productionEntryId = $entry->getKey();
        $this->productionAssignees = $entry->productionTask?->projectTask?->users->pluck('id')->all()
            ?: ProductionProjects::labUsers()->pluck('id')->all();
        $this->productionDeadline = $entry->productionTask?->due_date?->toDateString()
            ?? $this->record->expected_delivery_date?->toDateString()
            ?? today()->addDays(max(1, (int) $entry->product->huvant_production_days))->toDateString();
        $this->resetValidation();
        $this->dispatch('open-modal', id: 'hv-production-choice');
    }

    public function chooseProduction(): void
    {
        $this->validate([
            'productionEntryId'        => ['required', 'integer'],
            'productionAssignees'      => ['required', 'array', 'min:1'],
            'productionAssignees.*'    => ['integer'],
            'productionDeadline'       => ['required', 'date'],
        ]);

        try {
            ManufacturingFlow::assignProduction(
                $this->entry($this->productionEntryId),
                $this->productionAssignees,
                Carbon::parse($this->productionDeadline),
            );
        } catch (RuntimeException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->persistent()->send();

            return;
        }

        $this->dispatch('close-modal', id: 'hv-production-choice');
        Notification::make()->success()->title(__('huvant-orders::manufacturing.production_task_created'))->send();
    }

    public function done(): void
    {
        try {
            ManufacturingFlow::finalize($this->record);
        } catch (RuntimeException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->persistent()->send();

            return;
        }

        Notification::make()->success()->title(__('huvant-orders::manufacturing.offer_managed'))->send();
        $this->redirect(OfferResource::getUrl('index'));
    }

    public function openMaterials(int $unitId): void
    {
        $unit = ProductUnit::query()->where('order_id', $this->record->getKey())->findOrFail($unitId);
        $this->materialsUnitId = $unit->getKey();
        $this->dispatch('open-modal', id: 'hv-unit-materials');
    }

    public function reportUrl(): string
    {
        return route('huvant.orders.manufacturing-report', ['order' => $this->record]);
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $this->record->refresh();
        $entries = ManufacturingFlow::entries($this->record);
        $stockEntry = $this->stockEntryId ? $entries->firstWhere('id', $this->stockEntryId) : null;
        $stockUnits = $stockEntry
            ? ProductUnit::query()
                ->with('creator:id,name')
                ->where('product_id', $stockEntry->product_id)
                ->where(fn ($query) => $query->where('status', 'in_lab')->when(
                    $stockEntry->product_unit_id,
                    fn ($query, $unitId) => $query->orWhereKey($unitId),
                ))
                ->orderByRaw('expiry_date is null, expiry_date')
                ->orderBy('production_date')
                ->get()
            : collect();
        $materialsUnit = $this->materialsUnitId
            ? ProductUnit::query()->with(['product', 'materials.material', 'materials.lot'])->find($this->materialsUnitId)
            : null;
        $availableByProduct = ProductUnit::query()
            ->whereIn('product_id', $entries->pluck('product_id')->unique())
            ->where('status', UnitStatus::InLab)
            ->selectRaw('product_id, count(*) as available_count')
            ->groupBy('product_id')
            ->pluck('available_count', 'product_id');

        return [
            'entries'       => $entries,
            'allManaged'    => $entries->every(fn (ManufacturingEntry $entry): bool => $entry->isManaged()),
            'isComplete'    => ManufacturingFlow::orderIsComplete($this->record),
            'stockEntry'    => $stockEntry,
            'stockUnits'    => $stockUnits,
            'productionEntry' => $this->productionEntryId ? $entries->firstWhere('id', $this->productionEntryId) : null,
            'labUsers'      => ProductionProjects::labUsers(),
            'materialsUnit' => $materialsUnit,
            'availableByProduct' => $availableByProduct,
        ];
    }

    private function entry(int $entryId): ManufacturingEntry
    {
        return ManufacturingEntry::query()
            ->where('order_id', $this->record->getKey())
            ->with(['order', 'product'])
            ->findOrFail($entryId);
    }
}
