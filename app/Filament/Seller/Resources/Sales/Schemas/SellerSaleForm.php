<?php

namespace App\Filament\Seller\Resources\Sales\Schemas;

use App\Enums\PaymentTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Models\Product;
use Dflydev\DotAccessData\Data;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Date;

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
                            ->searchable()
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
                                self::calculateProfit($get, $set);
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
                            ->readOnly()
                            ->disabled()
                            ->suffix(currency()),
                        TextInput::make('total_price')
                            ->disabled()
                            ->label('Total Price')
                            ->prefix(currency())
                            ->required(),
                        Select::make('status')
                            ->visibleOn(['edit'])
                            ->options(SaleStatusEnum::class)
                            ->default(SaleStatusEnum::PENDING)
                            ->required(),
                    ])->columnSpanFull(),

                Section::make('Client Information')
                    ->description('Identify the buyer for this transaction.')
                    ->icon('heroicon-o-user-group')
                    ->columns(2)
                    ->schema([
                        Select::make('client_id')
                            ->label('Existing Client')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->createOptionForm([
                                Section::make('New Client Details')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Full Name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('phone_number')
                                            ->label('Phone Number')
                                            ->required()
                                            ->tel()
                                            ->maxLength(20),
                                        TextInput::make('email')
                                            ->label('Email Address')
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('address')
                                            ->label('Address')
                                            ->maxLength(500)
                                            ->placeholder('Optional address for the client'),
                                    ]),
                            ])
                            ->preload()
                            ->placeholder('Search for a registered client...'),

                    ])->columnSpanFull(),

                Section::make('Delivery Section')
                    ->description('Optional delivery details for this sale.')
                    ->icon('heroicon-o-truck')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('delivery_city'),
                        TextInput::make('delivery_address')
                            ->label('Delivery Address')
                            ->placeholder('Enter address if delivery is needed')
                            ->maxLength(500),

                    ])->columnSpanFull(),
                Section::make('Payment & Profit')
                    ->description('Review  details of this transaction.')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->schema([
                        Select::make('payment_type')
                            ->reactive()
                            ->partiallyRenderComponentsAfterStateUpdated(['payment_date_limit'])
                        ->options(PaymentTypeEnum::class),
                        DatePicker::make('payment_date_limit')
                            ->label('Payment Due Date')
                            ->minDate(now())
                            ->visible(fn (Get $get) => $get('payment_type')  && $get('payment_type') !== PaymentTypeEnum::ONE_TIME),

                    ])->columnSpanFull(),

                Textarea::make('more_details')
                    ->label('Additional Notes')
                    ->columnSpanFull()
                    ->placeholder('Optional notes about this sale...')
                    ->maxLength(1000),
                Hidden::make('seller_id')
                    ->default(fn() => auth()->id()),
                Hidden::make('supplier_price_at_sale'),
                Hidden::make('profit'),
            ]);
    }

    protected static function calculateProfit(Get $get, Set $set): void
    {
        $price = $get('sale_price') ?? 0;
        $supplierPrice = $get('supplier_price_at_sale') ?? 0;
        $qty = $get('quantity') ?? 1;
        $set('total_price', $price * $qty);
        //$set('profit', ($price - $supplierPrice) * $qty);
    }
}
