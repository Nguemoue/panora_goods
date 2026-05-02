<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Filament\Admin\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockProducts extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()->where('stock_quantity', '<', 10)->orderBy('stock_quantity', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Quantity Left')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 2 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('supplier.name'),
            ])
            ->recordUrl(
                fn (Product $record): string => ProductResource::getUrl('edit', ['record' => $record]),
            );
    }
}
