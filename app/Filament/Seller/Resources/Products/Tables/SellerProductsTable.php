<?php

namespace App\Filament\Seller\Resources\Products\Tables;

use App\Filament\Seller\Resources\Sales\SaleResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class SellerProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images.path')
                    ->label(__('phones.image'))
                    ->circular()
                    ->stacked()
                    ->limit(1),
                TextColumn::make('name')
                    ->description(fn($record)=>$record->product_code)
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record) => $record->model_number),
                TextColumn::make('category.name')
                    ->label(__('phones.category'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->label(__('phones.brand'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('selling_price')
                    ->label(__('phones.selling_price'))
                    ->money()
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                TextColumn::make('stock_quantity')
                    ->label(__('phones.stock_quantity'))
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
                    ->label(__('phones.category'))
                    ->relationship('category', 'name'),
                SelectFilter::make('brand')
                    ->label(__('phones.brand'))
                    ->relationship('brand', 'name'),
                TernaryFilter::make('in_stock')
                    ->label(__('messages.product_availability'))
                    ->queries(
                        true: fn ($query) => $query->where('stock_quantity', '>', 0),
                        false: fn ($query) => $query->where('stock_quantity', '<=', 0),
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                    Action::make('sell_product')
                        ->label(__('sales.sell_this_product'))
                        ->icon('heroicon-o-shopping-cart')
                        ->url(fn (Product $record): string => SaleResource::getUrl('create', ['product' => $record]))
                        ->visible(fn (Product $record): bool => $record->stock_quantity > 0 && $record->status === 'active'),
                    Action::make('share_product_whatsapp')
                        ->label(__('sales.share_product_whatsapp'))
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->url(fn (Product $record): string => $record->getWhatsAppLink())
                        ->openUrlInNewTab()
                        ->visible(fn (): bool => filled(config('project_configuration.whatsapp'))),
                ]),

            ]);
    }
}
