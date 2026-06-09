<?php

namespace App\Filament\Admin\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('validation.attributes.email'))
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('validation.attributes.phone'))
                    ->searchable(),
                TextColumn::make('contact_person')
                    ->label(__('validation.attributes.contact_person'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
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
