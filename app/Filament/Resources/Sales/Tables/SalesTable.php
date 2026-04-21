<?php

namespace App\Filament\Resources\Sales\Tables;

use App\Models\Sale;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sold_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('sales.sold_at')),
                TextColumn::make('phone.name')
                    ->searchable()
                    ->description(fn (Sale $record): string => $record->phone->brand->name),
                TextColumn::make('customer_name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable()
                    ->alignment(Alignment::Center),
                TextColumn::make('sale_price')
                    ->label(__('sales.price_unit'))
                    ->money()
                    ->sortable()
                    ->alignment(Alignment::End),
                TextColumn::make('total_revenue')
                    ->label(__('sales.total_revenue'))
                    ->state(fn (Sale $record): float => (float) $record->sale_price * $record->quantity)
                    ->money()
                    ->alignment(Alignment::End),
                TextColumn::make('profit')
                    ->label(__('sales.total_profit'))
                    ->money()
                    ->sortable()
                    ->color('success')
                    ->alignment(Alignment::End),
            ])
            ->filters([
                SelectFilter::make('phone')
                    ->relationship('phone', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('brand')
                    ->relationship('phone.brand', 'name')
                    ->label(__('sales.brand'))
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
