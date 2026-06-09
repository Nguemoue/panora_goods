<?php

namespace App\Filament\Client\Resources\Sales\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientSalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('sales.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('sales.quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label(__('sales.total_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('sold_at')
                    ->label(__('sales.sold_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ]);
    }
}
