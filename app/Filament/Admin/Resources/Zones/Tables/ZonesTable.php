<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Zones\Tables;

use App\Models\Zone;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ZonesTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with('user')
                ->withCount('users'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('city')
                    ->label(__('panels.city'))
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Chef de zone')
                    ->description(fn (Zone $record): ?string => $record->user?->phone_number)
                    ->placeholder('Non assigné')
                    ->searchable(),
                TextColumn::make('users_count')
                    ->label('Vendeurs assignés')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('city')
                    ->label(__('panels.city'))
                    ->options(fn (): array => Zone::query()->distinct()->orderBy('city')->pluck('city', 'city')->all()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
