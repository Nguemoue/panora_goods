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
                Tabs::make(__('panels.product_details'))
                    ->tabs([
                        Tabs\Tab::make(__('panels.overview'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('panels.product_info'))
                                    ->description(__('panels.product_info_description'))
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('panels.product_name'))
                                            ->disabled(),
                                        TextInput::make('model_number')
                                            ->label(__('panels.model_reference'))
                                            ->disabled(),
                                        TextInput::make('category.name')
                                            ->label(__('phones.category'))
                                            ->disabled(),
                                        TextInput::make('brand.name')
                                            ->label(__('phones.brand'))
                                            ->disabled(),
                                        TextInput::make('selling_price')
                                            ->label(__('phones.selling_price'))
                                            ->numeric()
                                            ->prefix('$')
                                            ->disabled(),
                                    ])->columnSpanFull(),
                            ]),
                        Tabs\Tab::make(__('panels.photos'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make(__('panels.gallery'))
                                    ->description(__('panels.product_gallery_description'))
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
                        Tabs\Tab::make(__('panels.technical_sheet'))
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make(__('messages.specifications'))
                                    ->description(__('panels.technical_sheet_description'))
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
