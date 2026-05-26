<?php

namespace App\Filament\Admin\Resources\Zones\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('city')->required(),
                Select::make('user_id')->relationship('user', 'name')
                    ->required(),
            ]);
    }
}
