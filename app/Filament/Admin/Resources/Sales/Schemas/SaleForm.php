<?php

namespace App\Filament\Admin\Resources\Sales\Schemas;

use App\Enums\SaleStatusEnum;
use App\Models\Product;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
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
                // section for status
                Select::make('status')->options(SaleStatusEnum::class),
                Section::make(__('panels.transaction_header'))
                    ->description(__('panels.transaction_header_description'))
                    ->icon('heroicon-o-shopping-bag')
                    ->columns(2)
                    ->disabled()
                    ->schema([
                        Select::make('product_id')
                            ->label(__('sales.product'))
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
                            ->label(__('panels.responsible_seller'))
                            ->relationship('seller', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columnSpanFull(),

                Section::make(__('sales.customer_timing'))
                    ->disabled()
                    ->icon('heroicon-o-user-group')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('client.name')->label(__('sales.client')),
                        TextEntry::make('client.email')->label(__('panels.client_email')),
                        TextEntry::make('client.phone_number')->label(__('panels.client_phone_number')),

                        DateTimePicker::make('sold_at')
                            ->label(__('sales.sold_at'))
                            ->default(now())
                            ->required(),
                    ])->columnSpanFull(),

                Section::make(__('panels.financial_details'))
                    ->description(__('panels.financial_details_description'))
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(3)
                    ->disabled()
                    ->schema([
                        TextInput::make('quantity')
                            ->label(__('sales.quantity'))
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateProfit($get, $set);
                            }),
                        TextInput::make('sale_price')
                            ->label(__('sales.total_price'))
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
                            ->label(__('panels.acquisition_cost'))
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
