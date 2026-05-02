<?php

namespace App\Filament\Client\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ClientProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product Details')
                    ->tabs([
                        Tabs\Tab::make('Overview')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Product Info')
                                    ->description('General specifications and current price.')
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
                                            ->label('Price')
                                            ->numeric()
                                            ->prefix('$')
                                            ->disabled(),
                                    ])->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Photos')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Gallery')
                                    ->description('Visual showcase of the product.')
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
                                            ->grid(3),
                                    ])->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Technical Sheet')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Specifications')
                                    ->description('Full list of technical characteristics.')
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
                                    ])->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
