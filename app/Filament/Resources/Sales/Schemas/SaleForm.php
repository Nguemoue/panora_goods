<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Models\Phone;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('sales.sale_details'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('phone_id')
                                    ->relationship('phone', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        if (!$state) return;
                                        $phone = Phone::find($state);
                                        if ($phone) {
                                            $set('supplier_price_at_sale', $phone->supplier_price);
                                            $set('sale_price', $phone->selling_price);
                                            $set('profit', $phone->selling_price - $phone->supplier_price);
                                        }
                                    }),
                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        $qty = (int) $state;
                                        $salePrice = (float) $get('sale_price');
                                        $supplierPrice = (float) $get('supplier_price_at_sale');
                                        $set('profit', ($salePrice - $supplierPrice) * $qty);
                                    }),
                            ]),
                    ]),

                Section::make(__('sales.pricing_profit'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('supplier_price_at_sale')
                                    ->label(__('sales.supplier_price_at_sale'))
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->readOnly(),
                                TextInput::make('sale_price')
                                    ->label(__('sales.sale_price'))
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                        $qty = (int) $get('quantity');
                                        $salePrice = (float) $state;
                                        $supplierPrice = (float) $get('supplier_price_at_sale');
                                        $set('profit', ($salePrice - $supplierPrice) * $qty);
                                    }),
                                TextInput::make('profit')
                                    ->label(__('sales.profit'))
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->readOnly(),
                            ]),
                    ]),

                Section::make(__('sales.customer_timing'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('customer_name')
                                    ->maxLength(255),
                                DateTimePicker::make('sold_at')
                                    ->required()
                                    ->default(now()),
                            ]),
                    ]),
            ]);
    }
}
