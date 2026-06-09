<?php

namespace App\Filament\Admin\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('phones.category'))
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->label(__('phones.brand'))
                    ->sortable(),
                TextColumn::make('supplier_price')
                    ->label(__('phones.supplier_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('selling_price')
                    ->label(__('phones.selling_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('stock_quantity')
                    ->label(__('phones.stock_quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultCurrency(currency())
            ->filters([
                //
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
