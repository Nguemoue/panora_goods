<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\Specification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product Wizard')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Core Identification')
                                    ->columnSpanFull()
                                    ->description('Enter the main details to identify the product.')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->placeholder('e.g., iPhone 15 Pro')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('model_number')
                                            ->placeholder('e.g., A3102')
                                            ->maxLength(255),
                                        Select::make('category_id')
                                            ->relationship('category', 'name')
                                            ->required()
                                            ->live()
                                            ->preload(),
                                        Select::make('brand_id')
                                            ->relationship('brand', 'name')
                                            ->preload(),
                                        Select::make('supplier_id')
                                            ->relationship('supplier', 'name')
                                            ->required()
                                            ->preload(),
                                        Select::make('status')
                                            ->options([
                                                'active' => 'Active (Ready to sell)',
                                                'inactive' => 'Inactive (Hidden)',
                                            ])
                                            ->default('active')
                                            ->required(),
                                    ]),
                                Section::make('Pricing & Inventory')
                                    ->description('Manage your margins and stock availability.')
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('supplier_price')
                                            ->label('Purchase Price')
                                            ->numeric()
                                            ->prefix(currency())
                                            ->required(),
                                        TextInput::make('selling_price')
                                            ->label('Retail Price')
                                            ->gte('supplier_price')
                                            ->numeric()
                                            ->prefix(currency())
                                            ->required(),
                                        TextInput::make('stock_quantity')
                                            ->label('Quantity in Stock')
                                            ->numeric()
                                            ->default(0)
                                            ->required(),
                                    ]),
                            ]),
                        Tabs\Tab::make('Images')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                //images sections
                                Section::make('Visual Assets')
                                    ->description('Upload high-quality photos. Minimum 4 required.')
                                    ->schema([
                                        Repeater::make('images')
                                            ->relationship('images')
                                            ->schema([
                                                FileUpload::make('path')
                                                    ->disk('public')
                                                    ->image()
                                                    ->directory('products')
                                                    ->visibility('public')
                                                    ->required(),
                                                Select::make('type')
                                                    ->options([
                                                        'face_1' => 'Front View 1',
                                                        'face_2' => 'Front View 2',
                                                        'front' => 'Front Detail',
                                                        'back' => 'Back View',
                                                        'other' => 'Additional Detail',
                                                    ])
                                                    ->required(),
                                                Toggle::make('is_primary')
                                                    ->label('Main Display Image'),
                                            ])
                                            ->columns(2)
                                            ->minItems(1)
                                            ->grid(2),
                                    ]),
                            ]),
                        Tabs\Tab::make('Technical Specs')
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                Section::make('Custom Attributes')
                                    ->description('Add detailed specifications based on category selection.')
                                    ->schema([
                                        Repeater::make('product_specifications')
                                            ->relationship('productSpecifications')
                                            ->schema([
                                                Select::make('specification_id')
                                                    ->label('Spec Name')
                                                    ->options(function (Get $get) {
                                                        $categoryId = $get('../../category_id');
                                                        if (!$categoryId) {
                                                            return Specification::pluck('name', 'id');
                                                        }
                                                        return Category::query()->find($categoryId)
                                                            ?->specifications()
                                                            ->pluck('name', 'specifications.id');
                                                    })
                                                    ->required()
                                                    ->reactive(),
                                                TextInput::make('value')
                                                    ->label('Attribute Value')
                                                    ->placeholder('e.g., 256GB, 400W')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
