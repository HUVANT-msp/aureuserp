<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Where a finished piece is: in the lab, out (rented or lent), or sold. */
enum UnitStatus: string implements HasColor, HasLabel
{
    case InLab = 'in_lab';
    case Out = 'out';
    case Sold = 'sold';

    public function getLabel(): string
    {
        return match ($this) {
            self::InLab => __('huvant-orders::lab.status_in_lab'),
            self::Out   => __('huvant-orders::lab.status_out'),
            self::Sold  => __('huvant-orders::lab.status_sold'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::InLab => 'success',
            self::Out   => 'info',
            self::Sold  => 'gray',
        };
    }
}
