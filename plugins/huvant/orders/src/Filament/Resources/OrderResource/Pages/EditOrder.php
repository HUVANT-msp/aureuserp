<?php

namespace Huvant\Orders\Filament\Resources\OrderResource\Pages;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Huvant\Orders\Enums\ClosingState;
use Huvant\Orders\Enums\OrderState;
use Huvant\Orders\Filament\Resources\OrderResource;
use Huvant\Orders\Models\Order;
use Huvant\Orders\Support\Orders;
use Huvant\Orders\Support\Rentals;
use Huvant\Orders\Support\Shipping;
use RuntimeException;

/** @property Order $record */
class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    /** The offer and its lines are saved together or not at all. */
    protected ?bool $hasDatabaseTransactions = true;

    public function getTitle(): string
    {
        $isIt = app()->getLocale() === 'it';

        return $this->record->order_number
            ? ($isIt ? "Ordine {$this->record->order_number}" : "Order {$this->record->order_number}")
            : ($isIt ? "Offerta {$this->record->name}" : "Offer {$this->record->name}");
    }

    public function getSubheading(): ?string
    {
        $isIt = app()->getLocale() === 'it';
        $parts = [$this->record->state->getLabel(), $this->record->supply_type->getLabel()];

        if ($this->record->order_number) {
            $parts[] = $isIt ? "dall'offerta {$this->record->name}" : "from offer {$this->record->name}";
        }

        if ($this->record->state->isVisibleToProduction()) {
            $parts[] = ($isIt ? 'Produzione: ' : 'Production: ').Orders::productionStatus($this->record)->getLabel();
        }

        if ($returnStatus = Shipping::returnStatus($this->record)) {
            $parts[] = ($isIt ? 'Reso: ' : 'Return: ').$returnStatus->getLabel();
        }

        return implode(' · ', $parts);
    }

    protected function getHeaderActions(): array
    {
        $isOffer = fn (): bool => $this->record->state->isOffer();
        $isIt = app()->getLocale() === 'it';

        return [
            $this->stateAction('confirm', $isIt ? 'Conferma ordine' : 'Confirm order', 'success', fn () => Orders::confirm($this->record, allowOverbooking: true), $isOffer)
                ->requiresConfirmation()
                ->modalDescription(function () use ($isIt): string {
                    $description = $isIt
                        ? 'L’offerta entrerà in Manufacturing › Offerte, dove il laboratorio sceglierà cosa prelevare dalla giacenza e cosa produrre.'
                        : 'The offer will enter Manufacturing › Offers, where the lab chooses what to take from stock and what to produce.';

                    if ($conflicts = Rentals::conflicts($this->record)) {
                        $description .= ($isIt ? ' Attenzione, date in conflitto per noleggi: ' : ' Warning, rentals overbooked: ').implode('; ', $conflicts).'.';
                    }

                    return $description;
                }),
            $this->stateAction('send', $isIt ? 'Segna come inviata' : 'Mark as sent', 'info', fn () => Orders::send($this->record), fn (): bool => $this->record->state === OrderState::Draft),
            $this->stateAction('hold', $isIt ? 'Metti in attesa' : 'Put on hold', 'warning', fn () => Orders::hold($this->record), fn (): bool => in_array($this->record->state, [OrderState::Draft, OrderState::Sent], true)),
            Action::make('offerPdf')
                ->label($isIt ? 'PDF offerta' : 'Offer PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->url(fn (): string => route('huvant.orders.offer', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => Orders::canSeePrices()),
            ActionGroup::make([
                $this->stateAction('close', $isIt ? 'Chiudi ordine' : 'Close order', 'success', fn () => Orders::close($this->record), fn (): bool => $this->record->state === OrderState::Confirmed && $this->record->closing_state === ClosingState::Open),
                $this->stateAction('contest', $isIt ? 'Segna come contestato' : 'Mark as contested', 'danger', fn () => Orders::close($this->record, ClosingState::Contested), fn (): bool => $this->record->state === OrderState::Confirmed && $this->record->closing_state === ClosingState::Open),
                $this->stateAction('reject', $isIt ? 'Rifiutata dal cliente' : 'Rejected by the customer', 'danger', fn () => Orders::reject($this->record), $isOffer),
                $this->stateAction('cancel', $isIt ? 'Annulla' : 'Cancel', 'danger', fn () => Orders::cancel($this->record), fn (): bool => $this->record->state->isOffer() || $this->record->state === OrderState::Confirmed)
                    ->requiresConfirmation()
                    ->modalDescription($isIt ? 'Le unità assegnate torneranno disponibili e le produzioni aperte verranno annullate.' : 'Allocated units return to stock and open production tasks are cancelled.'),
            ]),
        ];
    }

    protected function stateAction(string $name, string $label, string $color, Closure $apply, Closure $visible): Action
    {
        return Action::make($name)
            ->label($label)
            ->color($color)
            ->visible($visible)
            ->action(function () use ($apply): void {
                try {
                    // Unsaved edits go with the transition.
                    if ($this->record->state->isOffer()) {
                        $this->save(shouldRedirect: false, shouldSendSavedNotification: false);
                    }
                    $apply();
                } catch (RuntimeException $e) {
                    Notification::make()->danger()->title($e->getMessage())->send();

                    return;
                }

                $this->refreshFormData(['state', 'confirmed_at', 'closing_state']);
                $this->redirect(OrderResource::getUrl('edit', ['record' => $this->record]));
            });
    }
}
