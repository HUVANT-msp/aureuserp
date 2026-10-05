<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('huvant_orders.hourly_cost', 0.0);
        $this->migrator->add('huvant_orders.expiry_warning_days', 45);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('huvant_orders.hourly_cost');
        $this->migrator->deleteIfExists('huvant_orders.expiry_warning_days');
    }
};
