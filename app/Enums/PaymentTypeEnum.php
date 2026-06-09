<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PaymentTypeEnum: string implements HasLabel
{
    case ONE_TIME = 'not_initiated';
    case MANY_TIME_WITH_DEBT_ACKNOWLEDGE = 'many_time_with_debt_acknowledge';
    case MANY_TIME_WITHOUT_DEBT_ACKNOWLEDGE = 'many_time_without_debt_acknowledge';

    public function getLabel(): string|Htmlable|null
    {
        return __("sales.payment_types.{$this->value}");
    }
}
