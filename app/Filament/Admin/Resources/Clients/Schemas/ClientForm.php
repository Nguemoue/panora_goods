<?php

namespace App\Filament\Admin\Resources\Clients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('validation.attributes.name'))
                    ->required(),
                TextInput::make('email')
                    ->label(__('validation.attributes.email'))
                    ->unique()
                    ->email(),
                TextInput::make('phone_number')
                    ->label(__('panels.phone_number'))
                    ->tel(),
            ]);
    }
}
