<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ShippingStatus: string implements HasColor, HasLabel
{
    case ToShip = 'to_ship';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Problem = 'problem';
    case Returned = 'returned';

    public function getLabel(): string
    {
        return match ($this) {
            self::ToShip    => 'To ship',
            self::InTransit => 'In transit',
            self::Delivered => 'Delivered',
            self::Problem   => 'Problem',
            self::Returned  => 'Returned',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ToShip    => 'gray',
            self::InTransit => 'info',
            self::Delivered => 'success',
            self::Problem   => 'danger',
            self::Returned  => 'primary',
        };
    }
}
