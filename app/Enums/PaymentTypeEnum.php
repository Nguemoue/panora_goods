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
        return match ($this) {
          self::ONE_TIME => 'One Time',
          self::MANY_TIME_WITH_DEBT_ACKNOWLEDGE => 'Many Time With Debt Acknowledge',
          self::MANY_TIME_WITHOUT_DEBT_ACKNOWLEDGE => 'Many Time Without Debt Acknowledge',
        };
    }
}
