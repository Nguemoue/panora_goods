<?php

namespace App\Filament\Seller\Resources\Sales\Schemas;

use App\Models\Product;
use App\Enums\UserRoleEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;

class SellerSaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('New Transaction')
                    ->description('Fill in the details to record a new sale.')
                    ->icon('heroicon-o-shopping-cart')
                    ->columns(2)
                    ->schema([
                        Select::make('product_id')
                            ->label('Select Product')
                            ->relationship('product', 'name')
                            ->required()
                            ->live()
                            ->preload()
                            ->placeholder('Choose a product from stock...')
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($product = Product::find($state)) {
                                    $set('sale_price', $product->selling_price);
                                    $set('supplier_price_at_sale', $product->supplier_price);
                                }
                            }),
                        TextInput::make('quantity')
                            ->label('Units Sold')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live()
                            ->placeholder('1')
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateProfit($get, $set);
                            }),
                        TextInput::make('sale_price')
                            ->label('Selling Price (Unit)')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateProfit($get, $set);
                            }),
                        DateTimePicker::make('sold_at')
                            ->label('Transaction Date')
                            ->default(now())
                            ->required(),
                    ])->columnSpanFull(),

                Section::make('Client Information')
                    ->description('Identify the buyer for this transaction.')
                    ->icon('heroicon-o-user-group')
                    ->columns(2)
                    ->schema([
                        Select::make('client_id')
                            ->label('Existing Client')
                            ->relationship('client', 'name', fn ($query) => $query->where('role', UserRoleEnum::CLIENT))
                            ->searchable()
                            ->preload()
                            ->placeholder('Search for a registered client...'),
                        TextInput::make('customer_name')
                            ->label('Guest Name')
                            ->placeholder('Enter guest name if not registered')
                            ->maxLength(255),
                    ])->columnSpanFull(),

                Hidden::make('seller_id')
                    ->default(fn () => auth()->id()),
                Hidden::make('supplier_price_at_sale'),
                Hidden::make('profit'),
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
