<?php

namespace App\Filament\Admin\Resources\Specifications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpecificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('panels.characteristic_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('input_type')
                    ->label(__('panels.input_method'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('measure')
                    ->label(__('panels.unit_of_measure'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('panels.created_date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
