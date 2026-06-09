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
                Section::make(__('panels.supplier_contact'))
                    ->description(__('panels.supplier_contact_description'))
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('panels.company_name'))
                            ->placeholder(__('panels.supplier_name_placeholder'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('contact_person')
                            ->label(__('validation.attributes.contact_person'))
                            ->placeholder(__('panels.contact_person_placeholder'))
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('panels.email_address'))
                            ->placeholder('contact@supplier.com')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label(__('panels.phone_number'))
                            ->placeholder('+237...')
                            ->tel()
                            ->maxLength(255),
                    ])->columnSpanFull(),

                Section::make(__('panels.location_details'))
                    ->description(__('panels.location_details_description'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Textarea::make('address')
                            ->label(__('panels.office_warehouse_address'))
                            ->placeholder(__('panels.office_warehouse_placeholder'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
