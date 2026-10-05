<?php

namespace Huvant\Orders\Settings;

use Spatie\LaravelSettings\Settings;

class OrdersSettings extends Settings
{
    /** Cost of one hour of lab work, for the margin of an order. */
    public float $hourly_cost;

    /** Lots expiring within this many days are flagged in the lab stock. */
    public int $expiry_warning_days;

    public static function group(): string
    {
        return 'huvant_orders';
    }
}
