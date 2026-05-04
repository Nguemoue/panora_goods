<?php

namespace App\Filament\Admin\Resources\Sales\Schemas;

use App\Enums\SaleStatusEnum;
use App\Models\Product;
use App\Enums\UserRoleEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //section for status
                Select::make('status')->options(SaleStatusEnum::class),
                Section::make('Transaction Header')
                    ->description('Core details for this sales record.')
                    ->icon('heroicon-o-shopping-bag')
                    ->columns(2)
                    ->disabled()
                    ->schema([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->required()
                            ->live()
                            ->preload()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($product = Product::find($state)) {
                                    $set('sale_price', $product->selling_price);
                                    $set('supplier_price_at_sale', $product->supplier_price);
                                }
                            }),
                        Select::make('seller_id')
                            ->label('Responsible Seller')
                            ->relationship('seller', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columnSpanFull(),

                Section::make('Customer & Timing')
                    ->disabled()
                    ->icon('heroicon-o-user-group')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('client.name')->label('Client'),
                        TextEntry::make('client.email')->label('Client Email'),
                        TextEntry::make('client.phone_number')->label('Client Phone number'),

                        DateTimePicker::make('sold_at')
                            ->label('Date of Sale')
                            ->default(now())
                            ->required(),
                    ])->columnSpanFull(),

                Section::make('Financial Details')
                    ->description('Prices and calculated margins.')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(3)
                    ->disabled()
                    ->schema([
                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateProfit($get, $set);
                            }),
                        TextInput::make('sale_price')
                            ->label('Final Sale Price')
                            ->numeric()
                            ->readOnly()
                            ->disabled()
                            ->prefix(currency())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateProfit($get, $set);
                            }),
                        TextInput::make('supplier_price_at_sale')
                            ->label('Acquisition Cost')
                            ->numeric()
                            ->prefix(currency())
                            ->disabled()
                            ->dehydrated(),
                    ])->columnSpanFull(),
            ]);
    }

    protected static function calculateProfit(Get $get, Set $set): void
    {
        $price = $get('sale_price') ?? 0;
        $supplierPrice = $get('supplier_price_at_sale') ?? 0;
        $qty = $get('quantity') ?? 1;
        $set('profit', ($price - $supplierPrice) * $qty);
    }
}
