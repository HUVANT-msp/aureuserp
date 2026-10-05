<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** For goods lent, on approval or given as samples: are they back? */
enum ReturnStatus: string implements HasColor, HasLabel
{
    case NotShipped = 'not_shipped';
    case Out = 'out';
    case Overdue = 'overdue';
    case Returned = 'returned';

    public function getLabel(): string
    {
        return match ($this) {
            self::NotShipped => 'Not shipped yet',
            self::Out        => 'Out',
            self::Overdue    => 'Overdue',
            self::Returned   => 'Back',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NotShipped => 'gray',
            self::Out        => 'info',
            self::Overdue    => 'danger',
            self::Returned   => 'success',
        };
    }
}
