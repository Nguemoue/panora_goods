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
                Action::make('sell')
                    ->label('Sell')
                    ->icon('heroicon-o-shopping-cart')
                    ->color('success')
                    ->hidden(fn (Product $record) => $record->stock_quantity <= 0)
                    ->form([
                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->maxValue(fn (Product $record) => $record->stock_quantity),
                        TextInput::make('sale_price')
                            ->numeric()
                            ->prefix('$')
                            ->default(fn (Product $record) => $record->selling_price)
                            ->required(),
                        TextInput::make('customer_name')
                            ->placeholder('Enter customer name')
                            ->maxLength(255),
                        Select::make('client_id')
                            ->relationship('client', 'name', fn ($query) => $query->where('role', UserRoleEnum::CLIENT))
                            ->searchable()
                            ->preload(),
                        DateTimePicker::make('sold_at')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (array $data, Product $record): void {
                        $profit = ($data['sale_price'] - $record->supplier_price) * $data['quantity'];

                        Sale::create([
                            'product_id' => $record->id,
                            'seller_id' => Auth::id(),
                            'client_id' => $data['client_id'],
                            'quantity' => $data['quantity'],
                            'supplier_price_at_sale' => $record->supplier_price,
                            'sale_price' => $data['sale_price'],
                            'profit' => $profit,
                            'customer_name' => $data['customer_name'],
                            'sold_at' => $data['sold_at'],
                        ]);

                        $record->decrement('stock_quantity', $data['quantity']);
                    }),
            ]);
    }
}
