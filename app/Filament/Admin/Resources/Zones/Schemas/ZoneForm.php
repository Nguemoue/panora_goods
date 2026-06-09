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
                TextInput::make('name')
                    ->label(__('validation.attributes.name'))
                    ->required(),
                TextInput::make('city')
                    ->label(__('panels.city'))
                    ->required(),
                Select::make('user_id')
                    ->label(__('panels.assigned_user'))
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }
}
