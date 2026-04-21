<?php

namespace App\Filament\Resources\Measures\Pages;

use App\Filament\Resources\Measures\MeasureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMeasures extends ListRecords
{
    protected static string $resource = MeasureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
