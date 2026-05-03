<?php

namespace App\Filament\Seller\Resources\Products\Tables;

use App\Models\Product;
use App\Models\Sale;
use App\Enums\UserRoleEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

use Illuminate\Support\Facades\Auth;

class SellerProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images.path')
                    ->label('Photo')
                    ->circular()
                    ->stacked()
                    ->limit(1),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record) => $record->model_number),
                TextColumn::make('category.name')
                    ->badge()
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('selling_price')
                    ->label('Price')
                    ->money()
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    }),
            ])
            ->defaultCurrency(currency: currency())
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                SelectFilter::make('brand')
                    ->relationship('brand', 'name'),
                TernaryFilter::make('in_stock')
                    ->label('Availability')
                    ->queries(
                        true: fn ($query) => $query->where('stock_quantity', '>', 0),
                        false: fn ($query) => $query->where('stock_quantity', '<=', 0),
                    ),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),

            ]);
    }
}
