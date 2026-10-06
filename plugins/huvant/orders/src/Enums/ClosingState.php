<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ClosingState: string implements HasColor, HasLabel
{
    case Open = 'open';
    case Closed = 'closed';
    case Contested = 'contested';

    public function getLabel(): string
    {
        return match (app()->getLocale()) {
            'it' => match ($this) {
                self::Open      => 'Aperto',
                self::Closed    => 'Chiuso',
                self::Contested => 'Contestato',
            },
            default => match ($this) {
                self::Open      => 'Open',
                self::Closed    => 'Closed',
                self::Contested => 'Contested',
            },
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Open      => 'gray',
            self::Closed    => 'success',
            self::Contested => 'danger',
        };
    }
}
