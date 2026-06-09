<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\Specification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                Tabs::make(__('panels.product_wizard'))
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make(__('panels.general'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('panels.core_identification'))
                                    ->columnSpanFull()
                                    ->description(__('panels.core_identification_description'))
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('panels.product_name'))
                                            ->placeholder('ex: iPhone 15 Pro')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('model_number')
                                            ->label(__('panels.model_reference'))
                                            ->placeholder('ex: A3102')
                                            ->maxLength(255),
                                        Select::make('category_id')
                                            ->label(__('phones.category'))
                                            ->relationship('category', 'name')
                                            ->required()
                                            ->live()
                                            ->preload(),
                                        Select::make('brand_id')
                                            ->label(__('phones.brand'))
                                            ->relationship('brand', 'name')
                                            ->preload(),
                                        Select::make('supplier_id')
                                            ->label(__('messages.supplier'))
                                            ->relationship('supplier', 'name')
                                            ->required()
                                            ->preload(),
                                        Select::make('status')
                                            ->label(__('messages.status'))
                                            ->options([
                                                'active' => __('panels.active_access_granted'),
                                                'inactive' => __('messages.inactive'),
                                            ])
                                            ->default('active')
                                            ->required(),

                                    ]),
                                Section::make(__('panels.detailed_description'))
                                    ->description(__('panels.detailed_description_description'))
                                    ->columnSpanFull()
                                    ->schema([
                                        Textarea::make('short_description')
                                            ->label(__('panels.short_description'))
                                            ->maxLength(255)
                                            ->required(),
                                        RichEditor::make('long_description')
                                            ->label(__('panels.long_description'))
                                            ->maxLength(2000),
                                    ]),
                                Section::make(__('messages.pricing_inventory'))
                                    ->description(__('panels.pricing_inventory_description'))
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('supplier_price')
                                            ->label(__('panels.purchase_price'))
                                            ->numeric()
                                            ->prefix(currency())
                                            ->required(),
                                        TextInput::make('selling_price')
                                            ->label(__('panels.retail_price'))
                                            ->gte('supplier_price')
                                            ->numeric()
                                            ->prefix(currency())
                                            ->required(),
                                        TextInput::make('stock_quantity')
                                            ->label(__('panels.quantity_in_stock'))
                                            ->numeric()
                                            ->default(0)
                                            ->required(),
                                    ]),
                            ]),
                        Tabs\Tab::make(__('panels.photos'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                // images sections
                                Section::make(__('panels.visual_assets'))
                                    ->description(__('panels.visual_assets_description'))
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
                                                    ->label(__('messages.type'))
                                                    ->options([
                                                        'face_1' => __('panels.front_view_1'),
                                                        'face_2' => __('panels.front_view_2'),
                                                        'front' => __('panels.front_detail'),
                                                        'back' => __('panels.back_view'),
                                                        'other' => __('panels.additional_detail'),
                                                    ])
                                                    ->required(),
                                                Toggle::make('is_primary')
                                                    ->label(__('panels.main_display_image')),
                                            ])
                                            ->columns(2)
                                            ->minItems(1)
                                            ->grid(2),
                                    ]),
                            ]),
                        Tabs\Tab::make(__('panels.technical_specs'))
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                Section::make(__('panels.custom_attributes'))
                                    ->description(__('panels.custom_attributes_description'))
                                    ->schema([
                                        Repeater::make('product_specifications')
                                            ->relationship('productSpecifications')
                                            ->schema([
                                                Select::make('specification_id')
                                                    ->label(__('panels.spec_name'))
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
                                                    ->label(__('panels.attribute_value'))
                                                    ->placeholder('ex: 256GB, 400W')
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
