<?php

declare(strict_types=1);

namespace App\Filament\Seller\Resources\Sales\Schemas;

use App\Enums\PaymentTypeEnum;
use App\Models\Product;
use App\Models\User;
use App\Settings\PaymentSetting;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
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
                            ->afterStateUpdated(function (Get $get, Set $set, mixed $state): void {
                                if ($product = Product::query()->where('status', 'active')->find($state)) {
                                    $set('sale_price', $product->selling_price);
                                }
                                self::recalculateTotals($get, $set);
                            }),
                        TextInput::make('quantity')
                            ->label(__('sales.units_sold'))
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(fn (Get $get): ?int => Product::query()->whereKey((int) $get('product_id'))->value('stock_quantity'))
                            ->required()
                            ->live()
                            ->placeholder('1')
                            ->afterStateUpdated(function (Get $get, Set $set): void {
                                self::recalculateTotals($get, $set);
                            }),
                        TextInput::make('sale_price')
                            ->label(__('sales.sale_price'))
                            ->numeric()
                            ->readOnly()
                            ->disabled()
                            ->dehydrated(false)
                            ->suffix(currency()),
                        TextInput::make('total_price')
                            ->disabled()
                            ->dehydrated(false)
                            ->label(__('sales.total_price'))
                            ->prefix(currency()),
                        TextInput::make('estimated_commission')
                            ->label('Ma commission estimée')
                            ->disabled()
                            ->dehydrated(false)
                            ->prefix(currency())
                            ->helperText('Calculée selon votre taux vendeur actif.')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),

                Section::make(__('sales.client_information'))
                    ->description(__('sales.client_information_description'))
                    ->icon('heroicon-o-user-group')
                    ->columns(1)
                    ->schema([
                        Select::make('client_id')
                            ->label(__('sales.existing_client'))
                            ->relationship('client', 'name')
                            ->searchable(['name', 'phone_number'])
                            ->required()
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
                            ->placeholder(__('sales.search_registered_client')),
                    ])->columnSpanFull(),

                Section::make(__('sales.delivery_section'))
                    ->description(__('sales.delivery_section_description'))
                    ->icon('heroicon-o-truck')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('delivery_city')
                            ->label(__('panels.city'))
                            ->datalist(function (): array {
                                /** @var User|null $seller */
                                $seller = Filament::auth()->user();

                                return $seller?->zones()->pluck('city')->unique()->values()->all() ?? [];
                            })
                            ->maxLength(120),
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
                            ->live()
                            ->options(PaymentTypeEnum::class),
                        DatePicker::make('payment_date_limit')
                            ->label(__('sales.payment_due_date'))
                            ->minDate(today())
                            ->required(fn (Get $get): bool => self::isInstallment($get('payment_type')))
                            ->visible(fn (Get $get): bool => self::isInstallment($get('payment_type'))),
                        TextInput::make('initial_payment_amount')
                            ->label('Premier versement encaissé (optionnel)')
                            ->numeric()
                            ->minValue(0)
                            ->suffix(currency())
                            ->helperText('Laissez vide si aucun acompte n\'a encore été perçu. Soumis à validation Admin.'),
                        TextInput::make('initial_payment_notes')
                            ->label('Note / Réf. paiement (Orange Money, MTN MoMo…)')
                            ->maxLength(255)
                            ->placeholder('Ex : OM-894512'),
                    ])->columnSpanFull(),

                Textarea::make('more_details')
                    ->label(__('sales.additional_notes'))
                    ->columnSpanFull()
                    ->placeholder(__('sales.additional_notes_placeholder'))
                    ->maxLength(1000),
            ]);
    }

    protected static function isInstallment(mixed $paymentType): bool
    {
        if ($paymentType instanceof PaymentTypeEnum) {
            return $paymentType !== PaymentTypeEnum::ONE_TIME;
        }

        return filled($paymentType) && $paymentType !== PaymentTypeEnum::ONE_TIME->value;
    }

    protected static function recalculateTotals(Get $get, Set $set): void
    {
        $productId = (int) $get('product_id');
        $qty = max(1, (int) ($get('quantity') ?? 1));
        $product = $productId > 0 ? Product::query()->find($productId) : null;

        if (! $product) {
            $set('total_price', 0);
            $set('estimated_commission', 0);

            return;
        }

        $totalPrice = (float) $product->selling_price * $qty;
        $commission = (float) $product->profit_price * $qty * app(PaymentSetting::class)->seller_rate;

        $set('total_price', $totalPrice);
        $set('estimated_commission', round($commission, 2));
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
