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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->sortable(),
                TextColumn::make('selling_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                //
            ]);
    }
}
