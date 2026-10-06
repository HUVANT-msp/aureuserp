<?php

namespace Huvant\Orders\Enums;

use Carbon\CarbonInterface;
use Filament\Support\Contracts\HasLabel;

enum ShelfLifeUnit: string implements HasLabel
{
    case Days = 'days';
    case Months = 'months';
    case Years = 'years';

    public function getLabel(): string
    {
        return match ($this) {
            self::Days   => __('huvant-orders::lab.shelf_days'),
            self::Months => __('huvant-orders::lab.shelf_months'),
            self::Years  => __('huvant-orders::lab.shelf_years'),
        };
    }

    public function addTo(CarbonInterface $date, int $amount): CarbonInterface
    {
        return match ($this) {
            self::Days   => $date->copy()->addDays($amount),
            self::Months => $date->copy()->addMonthsNoOverflow($amount),
            self::Years  => $date->copy()->addYearsNoOverflow($amount),
        };
    }
}
