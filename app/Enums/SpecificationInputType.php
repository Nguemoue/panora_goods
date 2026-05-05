<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum SpecificationInputType: string implements HasLabel
{
    case SELECT = 'select';
    case TEXT = 'text';
    case NUMBER = 'number';


    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::SELECT => 'Select',
            self::TEXT => 'Text',
            self::NUMBER => 'Number',
        };
    }
}
