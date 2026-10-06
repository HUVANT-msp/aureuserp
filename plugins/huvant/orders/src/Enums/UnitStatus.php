<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Where a finished piece is: available, assigned to an offer, out, or sold. */
enum UnitStatus: string implements HasColor, HasLabel
{
    case InLab = 'in_lab';
    case Allocated = 'allocated';
    case Out = 'out';
    case Sold = 'sold';

    public function getLabel(): string
    {
        return match ($this) {
            self::InLab     => __('huvant-orders::lab.status_in_lab'),
            self::Allocated => __('huvant-orders::lab.status_allocated'),
            self::Out       => __('huvant-orders::lab.status_out'),
            self::Sold      => __('huvant-orders::lab.status_sold'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::InLab     => 'success',
            self::Allocated => 'warning',
            self::Out       => 'info',
            self::Sold      => 'gray',
        };
    }
}
