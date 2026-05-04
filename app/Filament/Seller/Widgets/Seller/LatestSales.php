<?php

namespace App\Filament\Seller\Widgets\Seller;

use App\Filament\Seller\Resources\Sales\SaleResource;
use App\Models\Sale;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestSales extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()->where('seller_id', Filament::auth()->id())->latest('sold_at')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->money(),
                Tables\Columns\TextColumn::make('sold_at')
                    ->dateTime()
                    ->since()
                    ->label('Sold'),
            ])
            ->recordUrl(
                fn (Sale $record): string => SaleResource::getUrl('index', ['record' => $record]), // Seller normally only has index/create
            );
    }
}
