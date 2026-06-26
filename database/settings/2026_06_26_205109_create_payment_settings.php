<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payment.seller_rate', 0.3);
        $this->migrator->add('payment.chief_rate', 0.1);
        $this->migrator->add('payment.zone_cashier_rate', 0.15);
        $this->migrator->add('payment.central_rate', 0.1);
        $this->migrator->add('payment.general_cashier_rate', 0.05);
        $this->migrator->add('payment.delivery_rate', 0.3);
    }
};
