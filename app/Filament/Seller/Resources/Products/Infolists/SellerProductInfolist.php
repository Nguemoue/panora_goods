<?php

namespace App\Filament\Seller\Resources\Products\Infolists;

use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SellerProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Presentation')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Group::make([
                                    TextEntry::make('name')
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('model_number')
                                        ->label('Model #')
                                        ->copyable(),
                                    TextEntry::make('category.name')
                                        ->badge()
                                        ->color('info'),
                                    TextEntry::make('brand.name')
                                        ->badge()
                                        ->color('gray'),
                                ])->columnSpan(1),
                                
                                Group::make([
                                    TextEntry::make('selling_price')
                                        ->money()
                                        ->size(TextEntry\TextEntrySize::Large)
                                        ->color('success')
                                        ->weight(FontWeight::Bold),
                                    TextEntry::make('stock_quantity')
                                        ->label('In Stock')
                                        ->badge()
                                        ->color(fn ($state) => $state > 5 ? 'success' : 'danger'),
                                    TextEntry::make('status')
                                        ->badge(),
                                ])->columnSpan(1),

                                ImageEntry::make('images.path')
                                    ->label('Primary Preview')
                                    ->circular()
                                    ->limit(1)
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Gallery & Details')
                    ->columns(2)
                    ->schema([
                        RepeatableEntry::make('images')
                            ->label('Product Photos')
                            ->schema([
                                ImageEntry::make('path')
                                    ->hiddenLabel()
                                    ->width(200)
                                    ->height(200),
                                TextEntry::make('type')
                                    ->badge(),
                            ])
                            ->grid(2)
                            ->columnSpan(1),

                        RepeatableEntry::make('product_specifications')
                            ->label('Technical Specifications')
                            ->schema([
                                TextEntry::make('specification.name')
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('value')
                                    ->color('primary'),
                            ])
                            ->grid(2)
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
