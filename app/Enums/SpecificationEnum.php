<?php

namespace App\Enums;

enum SpecificationEnum: string
{
    case RAM = 'RAM';
    case STORAGE_DISK = 'storage_disk';
    case COLOR = 'color';
    case BATTERY_AMPERAGE = 'battery_amperage';


    public function measure(): ?MeasureEnum
    {
        return match ($this) {
            self::RAM,
            self::STORAGE_DISK => MeasureEnum::GIGA_OCTET,
            self::COLOR => null,
            self::BATTERY_AMPERAGE => MeasureEnum::MILLI_AMP,
        };
    }
}
