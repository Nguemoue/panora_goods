<?php

namespace App\Filament\Resources\Measures\Pages;

use App\Filament\Resources\Measures\MeasureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeasure extends CreateRecord
{
    protected static string $resource = MeasureResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
