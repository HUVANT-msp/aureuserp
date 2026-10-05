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

    public function getTitle(): string
    {
        return $this->record->order_number
            ? "Order {$this->record->order_number}"
            : "Offer {$this->record->name}";
    }

    public function getSubheading(): ?string
    {
        $parts = [$this->record->state->getLabel(), $this->record->supply_type->getLabel()];

        if ($this->record->order_number) {
            $parts[] = "from offer {$this->record->name}";
        }

        if ($this->record->state->isVisibleToProduction()) {
            $parts[] = 'Production: '.Orders::productionStatus($this->record)->getLabel();
        }

        if ($returnStatus = Shipping::returnStatus($this->record)) {
            $parts[] = 'Return: '.$returnStatus->getLabel();
        }

        return implode(' · ', $parts);
    }

    protected function getHeaderActions(): array
    {
        $isOffer = fn (): bool => $this->record->state->isOffer();

        return [
            $this->stateAction('confirm', 'Confirm order', 'success', fn () => Orders::confirm($this->record, allowOverbooking: true), $isOffer)
                ->requiresConfirmation()
                ->modalDescription(function (): string {
                    $description = 'Products with a bill of materials get a manufacturing order for the lab; goods get a delivery.';

                    if ($conflicts = Rentals::conflicts($this->record)) {
                        $description .= ' Warning, rentals overbooked: '.implode('; ', $conflicts).'.';
                    }

                    return $description;
                }),
            $this->stateAction('send', 'Mark as sent', 'info', fn () => Orders::send($this->record), fn (): bool => $this->record->state === OrderState::Draft),
            $this->stateAction('hold', 'Put on hold', 'warning', fn () => Orders::hold($this->record), fn (): bool => in_array($this->record->state, [OrderState::Draft, OrderState::Sent], true)),
            Action::make('offerPdf')
                ->label('Offer PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->url(fn (): string => route('huvant.orders.offer', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => Orders::canSeePrices()),
            ActionGroup::make([
                $this->stateAction('close', 'Close order', 'success', fn () => Orders::close($this->record), fn (): bool => $this->record->state === OrderState::Confirmed && $this->record->closing_state === ClosingState::Open),
                $this->stateAction('contest', 'Mark as contested', 'danger', fn () => Orders::close($this->record, ClosingState::Contested), fn (): bool => $this->record->state === OrderState::Confirmed && $this->record->closing_state === ClosingState::Open),
                $this->stateAction('reject', 'Rejected by the customer', 'danger', fn () => Orders::reject($this->record), $isOffer),
                $this->stateAction('cancel', 'Cancel', 'danger', fn () => Orders::cancel($this->record), fn (): bool => $this->record->state->isOffer() || $this->record->state === OrderState::Confirmed)
                    ->requiresConfirmation()
                    ->modalDescription('Manufacturing orders not yet finished are cancelled too.'),
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
