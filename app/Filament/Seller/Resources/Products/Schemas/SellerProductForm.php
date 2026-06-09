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
                Tabs::make(__('panels.product_details_viewer'))
                    ->tabs([
                        Tabs\Tab::make(__('panels.general'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('panels.catalog_information'))
                                    ->description(__('panels.catalog_information_description'))
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
                                            ->label(__('panels.current_selling_price'))
                                            ->numeric()
                                            ->prefix('$')
                                            ->disabled(),
                                        TextInput::make('stock_quantity')
                                            ->label(__('panels.available_units'))
                                            ->disabled(),
                                    ]),
                            ]),
                        Tabs\Tab::make(__('panels.photos'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make(__('panels.product_gallery'))
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
                                            ->grid(2),
                                    ]),
                            ]),
                        Tabs\Tab::make(__('messages.specifications'))
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Section::make(__('panels.technical_sheet'))
                                    ->description(__('panels.technical_sheet_seller_description'))
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
