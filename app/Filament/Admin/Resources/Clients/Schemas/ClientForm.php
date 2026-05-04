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
                    ->label('Name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->unique()
                    ->email(),
                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->tel()
            ]);
    }
}
