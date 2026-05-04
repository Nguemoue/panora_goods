<?php

namespace App\Enums;

enum SaleStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return __("frontend.track.status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::PROCESSING => 'info',
            self::SHIPPED => 'warning',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
