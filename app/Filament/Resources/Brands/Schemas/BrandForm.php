<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('validation.attributes.name'))
                    ->placeholder(__('brands.enter_brand_name'))
                    ->helperText(__('brands.brand_information'))
                    ->required(),
            ]);
    }
}
