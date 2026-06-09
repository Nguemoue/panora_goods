<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum PaymentStatusEnum: string implements HasColor, HasLabel
{
    case NOT_INITIATED = 'not_initiated';
    case PENDING = 'pending';
    case PAID = 'paid';

    public function getLabel(): string|Htmlable|null
    {
        return __("sales.payment_statuses.{$this->value}");
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NOT_INITIATED => 'gray',
            self::PENDING => 'warning',
            self::PAID => 'success',
        };
    }
}
