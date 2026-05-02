<?php

namespace App\Filament\Admin\Resources\Specifications\Pages;

use App\Filament\Admin\Resources\Specifications\SpecificationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpecification extends EditRecord
{
    protected static string $resource = SpecificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
