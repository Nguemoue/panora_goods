<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Sales\Schemas;

use App\Enums\SaleStatusEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->label(__('messages.status'))
                    ->options(SaleStatusEnum::class)
                    ->required()
                    ->helperText('Passer une commande au statut « Annulée » restitue automatiquement la quantité au stock produit.'),

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
                            ->preload(),
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
                            ->required(),
                        TextInput::make('sale_price')
                            ->label(__('sales.total_price'))
                            ->numeric()
                            ->readOnly()
                            ->disabled()
                            ->prefix(currency())
                            ->required(),
                        TextInput::make('supplier_price_at_sale')
                            ->label(__('panels.acquisition_cost'))
                            ->numeric()
                            ->prefix(currency())
                            ->disabled(),
                    ])->columnSpanFull(),
            ]);
    }
}
