<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\Specification;
use App\Settings\PaymentSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

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
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nom de la catégorie')
                                                    ->required()
                                                    ->maxLength(255),
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->rows(2)
                                                    ->maxLength(500),
                                            ]),
                                        Select::make('brand_id')
                                            ->label(__('phones.brand'))
                                            ->relationship('brand', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nom de la marque')
                                                    ->required()
                                                    ->maxLength(255),
                                            ]),
                                        Select::make('supplier_id')
                                            ->label(__('messages.supplier'))
                                            ->relationship('supplier', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label('Nom du fournisseur')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('phone')
                                                    ->label('Téléphone')
                                                    ->tel()
                                                    ->maxLength(30),
                                                TextInput::make('email')
                                                    ->label('Email')
                                                    ->email()
                                                    ->maxLength(255),
                                            ]),
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
                                            ->minValue(0)
                                            ->prefix(currency())
                                            ->live(onBlur: true)
                                            ->required(),
                                        TextInput::make('selling_price')
                                            ->label(__('panels.retail_price'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->gte('supplier_price')
                                            ->prefix(currency())
                                            ->live(onBlur: true)
                                            ->required(),
                                        TextInput::make('stock_quantity')
                                            ->label(__('panels.quantity_in_stock'))
                                            ->integer()
                                            ->minValue(0)
                                            ->default(0)
                                            ->live(onBlur: true)
                                            ->required(),

                                        TextEntry::make('live_unit_margin')
                                            ->label('Marge unitaire estimée')
                                            ->state(function (Get $get): string {
                                                $purchase = (float) ($get('supplier_price') ?? 0);
                                                $selling = (float) ($get('selling_price') ?? 0);
                                                $margin = $selling - $purchase;
                                                $rate = $selling > 0 ? round(($margin / $selling) * 100, 1) : 0.0;

                                                return format_xaf($margin)." ({$rate} %)";
                                            })
                                            ->badge()
                                            ->color(fn (Get $get): string => ((float) ($get('selling_price') ?? 0) - (float) ($get('supplier_price') ?? 0)) > 0 ? 'success' : 'danger')
                                            ->weight(FontWeight::Bold),

                                        TextEntry::make('live_seller_commission')
                                            ->label('Commission vendeur estimée / unité')
                                            ->state(function (Get $get): string {
                                                $margin = max(0.0, (float) ($get('selling_price') ?? 0) - (float) ($get('supplier_price') ?? 0));
                                                $sellerRate = (float) app(PaymentSetting::class)->seller_rate;

                                                return format_xaf(round($margin * $sellerRate, 2));
                                            })
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('live_stock_value')
                                            ->label('Valeur totale du stock (achat)')
                                            ->state(fn (Get $get): string => format_xaf((float) ($get('supplier_price') ?? 0) * max(0, (int) ($get('stock_quantity') ?? 0))))
                                            ->badge()
                                            ->color('gray'),
                                    ]),
                            ]),

                        Tabs\Tab::make(__('panels.photos'))
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make(__('panels.visual_assets'))
                                    ->description(__('panels.visual_assets_description').' Utilisez l\'éditeur d\'image intégré pour recadrer ou pivoter vos photos avant enregistrement.')
                                    ->schema([
                                        Repeater::make('images')
                                            ->relationship('images')
                                            ->schema([
                                                FileUpload::make('path')
                                                    ->label('Photo du produit')
                                                    ->disk('public')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->imageEditorAspectRatios([null, '1:1', '4:3', '4:5'])
                                                    ->openable()
                                                    ->downloadable()
                                                    ->directory('products')
                                                    ->visibility('public')
                                                    ->required()
                                                    ->columnSpanFull(),
                                                Select::make('type')
                                                    ->label(__('messages.type'))
                                                    ->options([
                                                        'face_1' => __('panels.front_view_1'),
                                                        'face_2' => __('panels.front_view_2'),
                                                        'front' => __('panels.front_detail'),
                                                        'back' => __('panels.back_view'),
                                                        'other' => __('panels.additional_detail'),
                                                    ])
                                                    ->default('face_1')
                                                    ->required(),
                                                Toggle::make('is_primary')
                                                    ->label(__('panels.main_display_image'))
                                                    ->helperText('Une seule photo principale par produit (les autres sont décochées automatiquement).'),
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
                                                    ->options(function (Get $get): array {
                                                        $categoryId = $get('../../category_id');
                                                        if (! $categoryId) {
                                                            return Specification::query()->orderBy('name')->pluck('name', 'id')->all();
                                                        }

                                                        return Category::query()->find($categoryId)
                                                            ?->specifications()
                                                            ->orderBy('name')
                                                            ->pluck('name', 'specifications.id')
                                                            ->all() ?? [];
                                                    })
                                                    ->searchable()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->required()
                                                    ->live(),
                                                TextInput::make('value')
                                                    ->label(__('panels.attribute_value'))
                                                    ->placeholder('ex: 256GB, 400W')
                                                    ->required()
                                                    ->maxLength(255),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
