<?php

namespace App\Filament\Admin\Resources\Suppliers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Supplier Contact')
                    ->description('Primary details and contact person for this supplier.')
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Company Name')
                            ->placeholder('Enter supplier business name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('contact_person')
                            ->label('Contact Person')
                            ->placeholder('Full name of your main contact')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->placeholder('contact@supplier.com')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->placeholder('+1 (555) 000-0000')
                            ->tel()
                            ->maxLength(255),
                    ])->columnSpanFull(),

                Section::make('Location Details')
                    ->description('Physical address for logistics and deliveries.')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Textarea::make('address')
                            ->label('Office/Warehouse Address')
                            ->placeholder('Street name, City, Country...')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
