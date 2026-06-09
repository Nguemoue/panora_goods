<?php

namespace App\Filament\Admin\Resources\Clients\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('validation.attributes.name'))->searchable()->sortable(),
                TextColumn::make('email')->label(__('validation.attributes.email'))->searchable()->sortable(),
                TextColumn::make('phone_number')->label(__('panels.phone_number'))->searchable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()->disabled(fn ($record) => $record->sales()->count()),
                ])

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
