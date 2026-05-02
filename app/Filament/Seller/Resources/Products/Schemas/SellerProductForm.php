<?php

namespace App\Filament\Seller\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SellerProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product Details Viewer')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Catalog Information')
                                    ->description('View core product details and pricing.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Product Name')
                                            ->disabled(),
                                        TextInput::make('model_number')
                                            ->label('Model Reference')
                                            ->disabled(),
                                        TextInput::make('category.name')
                                            ->label('Category')
                                            ->disabled(),
                                        TextInput::make('brand.name')
                                            ->label('Brand')
                                            ->disabled(),
                                        TextInput::make('selling_price')
                                            ->label('Current Selling Price')
                                            ->numeric()
                                            ->prefix('$')
                                            ->disabled(),
                                        TextInput::make('stock_quantity')
                                            ->label('Available Units')
                                            ->disabled(),
                                    ]),
                            ]),
                        Tabs\Tab::make('Images')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Product Gallery')
                                    ->description('Visual references for customers.')
                                    ->schema([
                                        Repeater::make('images')
                                            ->relationship('images')
                                            ->schema([
                                                FileUpload::make('path')
                                                    ->image()
                                                    ->disabled(),
                                            ])
                                            ->disabled()
                                            ->columns(2)
                                            ->grid(2),
                                    ]),
                            ]),
                        Tabs\Tab::make('Specifications')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Section::make('Technical Sheet')
                                    ->description('Detailed characteristics to share with clients.')
                                    ->schema([
                                        Repeater::make('product_specifications')
                                            ->relationship('specifications')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->disabled(),
                                                TextInput::make('value')
                                                    ->disabled(),
                                            ])
                                            ->disabled()
                                            ->columns(2),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
