<?php

declare(strict_types=1);

namespace App\Filament\Seller\Resources\Sales\Pages;

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Filament\Seller\Resources\Sales\SaleResource;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Settings\PaymentSetting;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function afterFill(): void
    {
        $productId = request()->integer('product');

        if (! $productId) {
            return;
        }

        $product = Product::query()
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->find($productId);

        if (! $product) {
            return;
        }

        $estimatedCommission = round((float) $product->profit_price * app(PaymentSetting::class)->seller_rate, 2);

        $this->form->fill([
            'product_id' => $product->id,
            'quantity' => 1,
            'sale_price' => $product->selling_price,
            'total_price' => $product->selling_price,
            'estimated_commission' => $estimatedCommission,
            'payment_type' => PaymentTypeEnum::ONE_TIME->value,
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        $initialPaymentAmount = isset($data['initial_payment_amount']) && $data['initial_payment_amount'] !== ''
            ? (float) $data['initial_payment_amount']
            : 0.0;
        $initialPaymentNotes = isset($data['initial_payment_notes']) ? trim((string) $data['initial_payment_notes']) : null;

        unset($data['initial_payment_amount'], $data['initial_payment_notes'], $data['total_price'], $data['estimated_commission']);

        return DB::transaction(function () use ($data, $initialPaymentAmount, $initialPaymentNotes): Model {
            $product = Product::query()
                ->lockForUpdate()
                ->find($data['product_id']);

            $quantity = (int) ($data['quantity'] ?? 0);

            if (! $product || $product->status !== 'active' || $quantity < 1 || $product->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'data.quantity' => __('sales.insufficient_stock'),
                ]);
            }

            $salePrice = (float) $product->selling_price * $quantity;
            $paymentType = $data['payment_type'] instanceof PaymentTypeEnum
                ? $data['payment_type']
                : PaymentTypeEnum::tryFrom((string) ($data['payment_type'] ?? ''));

            if ($initialPaymentAmount > 0) {
                if ($paymentType === PaymentTypeEnum::ONE_TIME && $initialPaymentAmount !== $salePrice) {
                    throw ValidationException::withMessages([
                        'data.initial_payment_amount' => __('sales.one_time_payment_requires_full_amount', [
                            'amount' => number_format($salePrice, 2),
                        ]),
                    ]);
                }

                if ($initialPaymentAmount > $salePrice) {
                    throw ValidationException::withMessages([
                        'data.initial_payment_amount' => __('sales.payment_greater_than_remaining', [
                            'amount' => number_format($salePrice, 2),
                        ]),
                    ]);
                }
            }

            $client = filled($data['client_id'] ?? null)
                ? Client::query()->find($data['client_id'])
                : null;

            // Enforce authoritative server-side values (anti-tampering)
            $data['seller_id'] = Filament::auth()->id();
            $data['status'] = SaleStatusEnum::PENDING;
            $data['payment_status'] = PaymentStatusEnum::NOT_INITIATED;
            $data['supplier_price_at_sale'] = $product->supplier_price;
            $data['sale_price'] = $salePrice;
            $data['profit'] = (float) $product->profit_price * $quantity;
            $data['sold_at'] = now();
            $data['customer_name'] = $client?->name ?? ($data['customer_name'] ?? 'Client');

            /** @var Sale $record */
            $record = parent::handleRecordCreation($data);

            $product->decrement('stock_quantity', $quantity);

            if ($initialPaymentAmount > 0) {
                SalePayment::query()->create([
                    'sale_id' => $record->id,
                    'amount' => $initialPaymentAmount,
                    'notes' => $initialPaymentNotes ?: null,
                    'confirmation_status' => ConfirmationStatusEnum::PENDING,
                ]);
            }

            return $record;
        });
    }
}
