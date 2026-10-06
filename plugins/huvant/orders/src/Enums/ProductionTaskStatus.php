<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProductionTaskStatus: string implements HasColor, HasLabel
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return __('huvant-orders::manufacturing.status_'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Pending   => 'warning',
            self::Completed => 'success',
            self::Cancelled => 'gray',
        };
    }
}
