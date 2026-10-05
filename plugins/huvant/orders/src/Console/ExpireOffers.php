<?php

namespace Huvant\Orders\Console;

use Huvant\Orders\Support\Orders;
use Illuminate\Console\Command;

class ExpireOffers extends Command
{
    protected $signature = 'huvant:expire-offers';

    protected $description = 'Mark as expired the offers past their validity date that the customer never answered';

    public function handle(): int
    {
        $this->info(Orders::expireOverdue().' offers expired.');

        return self::SUCCESS;
    }
}
