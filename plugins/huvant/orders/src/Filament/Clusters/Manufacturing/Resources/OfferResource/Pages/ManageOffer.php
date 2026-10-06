<?php

namespace Huvant\Orders\Filament\Clusters\Manufacturing\Resources\OfferResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Filament\Clusters\Manufacturing\Resources\OfferResource;
use Huvant\Orders\Models\OrderLine;
use Huvant\Orders\Support\ManufacturingFlow;
use Illuminate\Contracts\Support\Htmlable;
use RuntimeException;

class ManageOffer extends Page
{
    use InteractsWithRecord;

    protected static string $resource = OfferResource::class;

    protected string $view = 'huvant-orders::filament.manufacturing.manage-offer';

    /** @var array<int|string, int|string|null> */
    public array $stockQuantities = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        abort_unless($this->record->state === OrderState::Confirmed, 404);

        foreach (ManufacturingFlow::productLines($this->record) as $line) {
            $requested = (int) round((float) $line->quantity);
            $this->stockQuantities[$line->getKey()] = $line->manufacturing_managed_at
                ? (int) $line->stock_quantity
                : min($requested, ManufacturingFlow::stockAvailable($line->product_id));
        }
    }

    public function getTitle(): string|Htmlable
    {
        return __('huvant-orders::manufacturing.manage_offer', ['order' => $this->record->order_number]);
    }

    public function getSubheading(): ?string
    {
        return trim(($this->record->partner?->name ? $this->record->partner->name.' · ' : '').$this->record->name);
    }

    public function manage(): void
    {
        try {
            ManufacturingFlow::manage($this->record, $this->stockQuantities);
        } catch (RuntimeException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->persistent()->send();

            return;
        }

        Notification::make()->success()->title(__('huvant-orders::manufacturing.offer_managed'))->send();
        $this->redirect(OfferResource::getUrl('index'));
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return [
            'lines' => ManufacturingFlow::productLines($this->record)->map(fn (OrderLine $line): array => [
                'line'      => $line,
                'requested' => (int) round((float) $line->quantity),
                'available' => ManufacturingFlow::stockAvailable($line->product_id),
            ]),
        ];
    }
}
