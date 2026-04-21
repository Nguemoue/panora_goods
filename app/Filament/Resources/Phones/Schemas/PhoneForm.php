<?php

namespace App\Filament\Resources\Phones\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PhoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Phone Management')
                    ->id('phone-tabs')
                    ->contained(false)
                    ->scrollable()
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tabs\Tab::make(__('messages.general'))
                            ->icon(Heroicon::OutlinedInformationCircle)
                            ->schema([
                                Section::make(__('messages.general_information'))
                                    ->description(__('messages.enter_phone_details'))
                                    ->icon(Heroicon::OutlinedSparkles)
                                    ->columns([
                                        'default' => 1,
                                        'sm' => 2,
                                        'xl' => 2,
                                    ])
                                    ->schema([
                                        Select::make('brand_id')
                                            ->relationship('brand', 'name')
                                            ->label(__('validation.attributes.brand'))
                                            ->placeholder(__('messages.select_brand'))
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(['default' => 1, 'sm' => 1]),
                                        Select::make('supplier_id')
                                            ->relationship('supplier', 'name')
                                            ->label(__('validation.attributes.supplier'))
                                            ->placeholder(__('messages.select_supplier'))
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(['default' => 1, 'sm' => 1]),
                                        TextInput::make('name')
                                            ->label(__('validation.attributes.name'))
                                            ->placeholder(__('messages.enter_phone_name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        TextInput::make('model_number')
                                            ->label(__('validation.attributes.model_number'))
                                            ->placeholder(__('messages.enter_model_number'))
                                            ->maxLength(255)
                                            ->columnSpan(['default' => 1, 'sm' => 1]),
                                        TextInput::make('color')
                                            ->label(__('validation.attributes.color'))
                                            ->placeholder(__('messages.enter_color'))
                                            ->maxLength(255)
                                            ->columnSpan(['default' => 1, 'sm' => 1]),
                                    ]),
                            ]),

                        Tabs\Tab::make(__('messages.pricing'))
                            ->icon(Heroicon::OutlinedBanknotes)
                            ->schema([
                                Section::make(__('messages.pricing_inventory'))
                                    ->description(__('messages.manage_prices_stock'))
                                    ->icon(Heroicon::OutlinedCurrencyDollar)
                                    ->columns([
                                        'default' => 1,
                                        'sm' => 3,
                                        'xl' => 3,
                                    ])
                                    ->schema([
                                        TextInput::make('supplier_price')
                                            ->label(__('validation.attributes.supplier_price'))
                                            ->placeholder(__('messages.enter_supplier_price'))
                                            ->required()
                                            ->numeric()
                                            ->prefix('$')
                                            ->live(onBlur: true)
                                            ->helperText(__('messages.cost_price_help')),
                                        TextInput::make('selling_price')
                                            ->label(__('validation.attributes.selling_price'))
                                            ->placeholder(__('messages.enter_selling_price'))
                                            ->required()
                                            ->numeric()
                                            ->prefix('$')
                                            ->live(onBlur: true)
                                            ->helperText(__('messages.retail_price_help')),
                                        TextInput::make('stock_quantity')
                                            ->label(__('validation.attributes.stock_quantity'))
                                            ->placeholder(__('messages.enter_stock_qty'))
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->helperText(__('messages.current_stock_level')),
                                    ]),

                                Section::make(__('messages.status'))
                                    ->description(__('messages.product_availability'))
                                    ->icon(Heroicon::OutlinedCheckBadge)
                                    ->schema([
                                        Select::make('status')
                                            ->label(__('validation.attributes.status'))
                                            ->options([
                                                'in_stock' => __('messages.in_stock'),
                                                'out_of_stock' => __('messages.out_of_stock'),
                                                'discontinued' => __('messages.discontinued'),
                                            ])
                                            ->required()
                                            ->default('in_stock')
                                            ->native(false),
                                    ]),
                            ]),

                        Tabs\Tab::make(__('messages.specifications'))
                            ->icon(Heroicon::OutlinedCpuChip)
                            ->schema([
                                Section::make(__('messages.specs'))
                                    ->description(__('messages.phone_specs_details'))
                                    ->icon(Heroicon::OutlinedCog6Tooth)
                                    ->columns([
                                        'default' => 1,
                                        'sm' => 3,
                                        'xl' => 3,
                                    ])
                                    ->schema([
                                        TextInput::make('ram')
                                            ->numeric()
                                            ->label(__('validation.attributes.ram'))
                                            ->placeholder(__('messages.enter_ram_gb'))
                                            ->suffix('GB')
                                            ->helperText(__('messages.ram_memory')),
                                        TextInput::make('storage')
                                            ->numeric()
                                            ->label(__('validation.attributes.storage'))
                                            ->placeholder(__('messages.enter_storage_gb'))
                                            ->suffix('GB')
                                            ->helperText(__('messages.internal_storage')),
                                        TextInput::make('color')
                                            ->label(__('validation.attributes.color'))
                                            ->placeholder(__('messages.color_variant'))
                                            ->maxLength(255),
                                    ]),

                                Section::make(__('messages.additional_specs'))
                                    ->description(__('messages.processor_battery_display'))
                                    ->schema([
                                        Textarea::make('specs')
                                            ->label(__('validation.attributes.specs'))
                                            ->placeholder(__('messages.specs_placeholder'))
                                            ->rows(6)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make(__('messages.images'))
                            ->icon(Heroicon::OutlinedPhoto)
                            ->schema([
                                Section::make(__('messages.phone_images'))
                                    ->description(__('messages.upload_phone_images'))
                                    ->icon(Heroicon::OutlinedArchiveBox)
                                    ->schema([
                                        FileUpload::make('images')
                                            ->label(__('validation.attributes.images'))
                                            ->disk('public')
                                            ->multiple()
                                            ->minFiles(1)
                                            ->image()
                                            ->automaticallyResizeImagesMode('cover')
                                            ->automaticallyCropImagesToAspectRatio('16:9')
                                            ->reorderable()
                                            ->directory('phones')
                                            ->visibility('public')
                                            ->helperText(__('messages.upload_at_least_4_images'))
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
