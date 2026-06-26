<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PaymentSetting extends Settings
{
    public float $seller_rate;
    public float $chief_rate;
    public float $zone_cashier_rate;
    public float $central_rate;
    public float $general_cashier_rate;
    public float $delivery_rate;

    public static function group(): string
    {
        return 'payment';
    }


}
