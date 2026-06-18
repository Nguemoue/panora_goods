<?php

namespace App\Filament\Client\Resources\Products\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->description(fn($record)=>$record->product_code)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('phones.category'))
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->label(__('phones.brand'))
                    ->sortable(),
                TextColumn::make('selling_price')
                    ->label(__('phones.selling_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                //
            ]);
    }
}
