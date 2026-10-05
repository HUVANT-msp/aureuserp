<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasLabel;

enum Fulfilment: string implements HasLabel
{
    case Courier = 'courier';
    case HandDelivery = 'hand_delivery';
    case Stock = 'stock';

    public function getLabel(): string
    {
        return match ($this) {
            self::Courier      => 'Courier',
            self::HandDelivery => 'Hand delivery',
            self::Stock        => 'Production for stock',
        };
    }

    /** Production for stock has no customer and nothing to ship. */
    public function needsCustomer(): bool
    {
        return $this !== self::Stock;
    }
}
