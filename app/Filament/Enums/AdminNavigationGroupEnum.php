<?php

namespace App\Filament\Enums;

use Filament\Support\Contracts\HasLabel;

enum AdminNavigationGroupEnum implements HasLabel
{
    case PERSONNEL;
    case SALES;

    public function getLabel(): string
    {
        return match ($this) {
            self::PERSONNEL => __('resources.groups.personnel'),
            self::SALES => __('resources.groups.sales'),
        };
    }
}
