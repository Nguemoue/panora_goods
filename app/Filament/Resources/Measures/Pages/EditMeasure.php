<?php

namespace App\Filament\Resources\Measures\Pages;

use App\Filament\Resources\Measures\MeasureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMeasure extends EditRecord
{
    protected static string $resource = MeasureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
