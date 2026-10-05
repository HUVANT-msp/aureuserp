<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasLabel;

/** Why the goods leave: it sets the reason printed on the delivery note and whether they come back. */
enum SupplyType: string implements HasLabel
{
    case Sale = 'sale';
    case CustomSale = 'custom_sale';
    case FreeSample = 'free_sample';
    case Loan = 'loan';
    case OnApproval = 'on_approval';
    case Research = 'research';
    case WarrantyReplacement = 'warranty_replacement';

    public function getLabel(): string
    {
        return match ($this) {
            self::Sale                => 'Sale',
            self::CustomSale          => 'Custom sale',
            self::FreeSample          => 'Free sample',
            self::Loan                => 'Loan',
            self::OnApproval          => 'On approval',
            self::Research            => 'R&D',
            self::WarrantyReplacement => 'Warranty replacement',
        };
    }

    /** Reason for transport on the Italian delivery note (DDT). */
    public function deliveryNoteReason(): string
    {
        return match ($this) {
            self::Sale                => 'Vendita',
            self::CustomSale          => 'Vendita personalizzata',
            self::FreeSample          => 'Campione gratuito, da restituire',
            self::Loan                => "Comodato d'uso, da restituire al termine dell'utilizzo",
            self::OnApproval          => 'Conto visione, da restituire',
            self::Research            => 'R&D, non destinato alla vendita',
            self::WarrantyReplacement => 'Sostituzione in garanzia',
        };
    }

    public function isReturnable(): bool
    {
        return in_array($this, [self::FreeSample, self::Loan, self::OnApproval], true);
    }
}
