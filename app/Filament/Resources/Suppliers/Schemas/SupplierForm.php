<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SupplierForm
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
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label(__('validation.attributes.phone'))
                    ->tel(),
                Textarea::make('address')
                    ->label(__('validation.attributes.address'))
                    ->columnSpanFull(),
                TextInput::make('contact_person')
                    ->label(__('validation.attributes.contact_person')),
            ]);
    }
}
