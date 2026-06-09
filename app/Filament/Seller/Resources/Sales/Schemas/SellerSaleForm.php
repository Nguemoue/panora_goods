<?php

namespace App\Filament\Seller\Resources\Sales\Schemas;

use App\Enums\PaymentTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class SellerSaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('sales.new_transaction'))
                    ->description(__('sales.new_transaction_description'))
                    ->icon('heroicon-o-shopping-cart')
                    ->columns(2)
                    ->schema([
                        Select::make('product_id')
                            ->label(__('sales.select_product'))
                            ->searchable()
                            ->relationship(
                                'product',
                                'name',
                                modifyQueryUsing: fn (Builder $query): Builder => $query
                                    ->where('status', 'active')
                                    ->where('stock_quantity', '>', 0),
                            )
                            ->required()
                            ->live()
                            ->preload()
                            ->placeholder(__('sales.choose_product_from_stock'))
                            ->helperText(fn (Get $get): ?string => self::getProductStockMessage((int) $get('product_id')))
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($product = Product::find($state)) {
                                    $set('sale_price', $product->selling_price);
                                    $set('supplier_price_at_sale', $product->supplier_price);
                                }
                                self::calculateProfit($get, $set);
                            }),
                        TextInput::make('quantity')
                            ->label(__('sales.units_sold'))
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(fn (Get $get): ?int => Product::find((int) $get('product_id'))?->stock_quantity)
                            ->required()
                            ->live()
                            ->placeholder('1')
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateProfit($get, $set);

                            }),
                        TextInput::make('sale_price')
                            ->label(__('sales.sale_price'))
                            ->numeric()
                            ->readOnly()
                            ->disabled()
                            ->suffix(currency()),
                        TextInput::make('total_price')
                            ->disabled()
                            ->dehydrated(false)
                            ->label(__('sales.total_price'))
                            ->prefix(currency())
                            ->required(),
                        Select::make('status')
                            ->visibleOn(['edit'])
                            ->options(SaleStatusEnum::class)
                            ->default(SaleStatusEnum::PENDING)
                            ->required(),
                    ])->columnSpanFull(),

                Section::make(__('sales.client_information'))
                    ->description(__('sales.client_information_description'))
                    ->icon('heroicon-o-user-group')
                    ->columns(2)
                    ->schema([
                        Select::make('client_id')
                            ->label(__('sales.existing_client'))
                            ->relationship('client', 'name')
                            ->searchable()
                            ->createOptionForm([
                                Section::make(__('sales.new_client_details'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('sales.full_name'))
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('phone_number')
                                            ->label(__('sales.phone_number'))
                                            ->required()
                                            ->tel()
                                            ->maxLength(20),
                                        TextInput::make('email')
                                            ->label(__('sales.email_address'))
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('address')
                                            ->label(__('validation.attributes.address'))
                                            ->maxLength(500)
                                            ->placeholder(__('sales.optional_client_address')),
                                    ]),
                            ])
                            ->preload()
                            ->placeholder(__('sales.search_registered_client')),

                    ])->columnSpanFull(),

                Section::make(__('sales.delivery_section'))
                    ->description(__('sales.delivery_section_description'))
                    ->icon('heroicon-o-truck')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('delivery_city'),
                        TextInput::make('delivery_address')
                            ->label(__('sales.delivery_address'))
                            ->placeholder(__('sales.delivery_address_placeholder'))
                            ->maxLength(500),

                    ])->columnSpanFull(),
                Section::make(__('sales.payment_profit'))
                    ->description(__('sales.payment_profit_description'))
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->schema([
                        Select::make('payment_type')
                            ->label(__('sales.payment_type'))
                            ->required()
                            ->default(PaymentTypeEnum::ONE_TIME)
                            ->reactive()
                            ->partiallyRenderComponentsAfterStateUpdated(['payment_date_limit'])
                            ->options(PaymentTypeEnum::class),
                        DatePicker::make('payment_date_limit')
                            ->label(__('sales.payment_due_date'))
                            ->minDate(now())
                            ->visible(fn (Get $get): bool => filled($get('payment_type')) && $get('payment_type') !== PaymentTypeEnum::ONE_TIME->value),

                    ])->columnSpanFull(),

                Textarea::make('more_details')
                    ->label(__('sales.additional_notes'))
                    ->columnSpanFull()
                    ->placeholder(__('sales.additional_notes_placeholder'))
                    ->maxLength(1000),
                Hidden::make('seller_id')
                    ->default(fn (): ?int => Filament::auth()->id()),
                Hidden::make('supplier_price_at_sale'),
                Hidden::make('profit'),
            ]);
    }

    protected static function calculateProfit(Get $get, Set $set): void
    {
        $price = $get('sale_price') ?? 0;
        $supplierPrice = $get('supplier_price_at_sale') ?? 0;
        $qty = $get('quantity') ?? 1;
        $set('total_price', $price * $qty);
        $set('profit', ($price - $supplierPrice) * $qty);
    }

    protected static function getProductStockMessage(int $productId): ?string
    {
        if (! $productId) {
            return null;
        }

        $stockQuantity = Product::query()->whereKey($productId)->value('stock_quantity');

        if ($stockQuantity === null) {
            return null;
        }

        if ($stockQuantity <= 0) {
            return __('sales.stock_empty');
        }

        if ($stockQuantity <= 5) {
            return trans_choice('sales.stock_low_warning', $stockQuantity, ['count' => $stockQuantity]);
        }

        return trans_choice('sales.stock_available', $stockQuantity, ['count' => $stockQuantity]);
    }
}
