<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->withCount('products')
                ->withCount(['products as low_stock_products_count' => fn (Builder $q) => $q->where('stock_quantity', '<', 5)]))
            ->columns([
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('contact_person')
                    ->label(__('validation.attributes.contact_person'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('validation.attributes.phone'))
                    ->copyable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('validation.attributes.email'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('products_count')
                    ->label('Produits fournis')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('low_stock_products_count')
                    ->label('Stock faible (< 5)')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
