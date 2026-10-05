<?php

namespace Huvant\Orders\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/** Offer and order lifecycle. Pending offers are already visible to production, unconfirmed. */
enum OrderState: string implements HasColor, HasLabel
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Rejected = 'rejected';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft     => 'Draft',
            self::Sent      => 'Sent',
            self::Pending   => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Rejected  => 'Rejected',
            self::Expired   => 'Expired',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft                                    => 'gray',
            self::Sent                                     => 'info',
            self::Pending                                  => 'warning',
            self::Confirmed                                => 'success',
            self::Rejected, self::Expired, self::Cancelled => 'danger',
        };
    }

    public function isOffer(): bool
    {
        return in_array($this, [self::Draft, self::Sent, self::Pending], true);
    }

    /** Production sees confirmed orders and offers on hold. */
    public function isVisibleToProduction(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed], true);
    }
}
