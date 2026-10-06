<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Where the order stands in production, rolled up from its manufacturing orders. */
enum ProductionStatus: string implements HasColor, HasLabel
{
    case None = 'none';
    case NotTakenOn = 'not_taken_on';
    case ToStart = 'to_start';
    case InProgress = 'in_progress';
    case Late = 'late';
    case Done = 'done';

    public function getLabel(): string
    {
        return match (app()->getLocale()) {
            'it' => match ($this) {
                self::None       => 'Non prevista',
                self::NotTakenOn => 'Non presa in carico',
                self::ToStart    => 'Da avviare',
                self::InProgress => 'In corso',
                self::Late       => 'In ritardo',
                self::Done       => 'Completata',
            },
            default => match ($this) {
                self::None       => 'Nothing to produce',
                self::NotTakenOn => 'Not taken on',
                self::ToStart    => 'To start',
                self::InProgress => 'In progress',
                self::Late       => 'Late',
                self::Done       => 'Done',
            },
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::None       => 'gray',
            self::NotTakenOn => 'warning',
            self::ToStart    => 'info',
            self::InProgress => 'primary',
            self::Late       => 'danger',
            self::Done       => 'success',
        };
    }
}
