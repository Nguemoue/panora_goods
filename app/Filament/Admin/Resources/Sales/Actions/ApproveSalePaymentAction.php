<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Sales\Actions;

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Sale;
use App\Models\SalePayment;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ApproveSalePaymentAction
{
    public static function make(): Action
    {
        return Action::make('approveSalePayment')
            ->label(__('sales.approve_payment'))
            ->icon('heroicon-o-check')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (SalePayment $record): bool => $record->confirmation_status === ConfirmationStatusEnum::PENDING)
            ->action(function (SalePayment $record): void {
                $result = DB::transaction(function () use ($record): array {
                    $payment = SalePayment::query()
                        ->lockForUpdate()
                        ->find($record->id);

                    if (! $payment || $payment->confirmation_status !== ConfirmationStatusEnum::PENDING) {
                        return ['status' => 'already_processed'];
                    }

                    $sale = Sale::query()
                        ->lockForUpdate()
                        ->find($payment->sale_id);

                    if (! $sale) {
                        return ['status' => 'not_found'];
                    }

                    $alreadyPaid = (float) $sale->approvedSalePayments()->sum('amount');
                    $salePrice = round((float) $sale->sale_price, 2);
                    $remainingAmount = max(0.0, round($salePrice - $alreadyPaid, 2));
                    $paymentAmount = round((float) $payment->amount, 2);

                    if ($sale->isOneTimePayment() && $paymentAmount !== $salePrice) {
                        return [
                            'status' => 'one_time_mismatch',
                            'sale_price' => $salePrice,
                        ];
                    }

                    if ($paymentAmount > $remainingAmount) {
                        return [
                            'status' => 'exceeds_remaining',
                            'remaining' => $remainingAmount,
                        ];
                    }

                    $paymentFinished = $paymentAmount >= $remainingAmount;

                    $payment->update([
                        'confirmation_status' => ConfirmationStatusEnum::APPROVED,
                    ]);

                    $sale->update([
                        'payment_status' => $paymentFinished ? PaymentStatusEnum::PAID : PaymentStatusEnum::PENDING,
                    ]);

                    return [
                        'status' => 'approved',
                        'sale' => $sale->load('seller'),
                        'payment' => $payment,
                    ];
                });

                if ($result['status'] === 'already_processed') {
                    Notification::make()
                        ->title('Paiement déjà traité')
                        ->body('Ce versement a déjà été approuvé ou rejeté.')
                        ->warning()
                        ->send();

                    return;
                }

                if ($result['status'] === 'one_time_mismatch') {
                    Notification::make()
                        ->body(__('sales.one_time_payment_requires_full_amount', [
                            'amount' => number_format((float) $result['sale_price'], 2),
                        ]))
                        ->danger()
                        ->send();

                    return;
                }

                if ($result['status'] === 'exceeds_remaining') {
                    Notification::make()
                        ->danger()
                        ->body(__('sales.payment_greater_than_remaining', [
                            'amount' => number_format((float) $result['remaining'], 2),
                        ]))
                        ->send();

                    return;
                }

                if ($result['status'] !== 'approved') {
                    return;
                }

                /** @var Sale $sale */
                $sale = $result['sale'];
                /** @var SalePayment $payment */
                $payment = $result['payment'];

                if ($sale->seller) {
                    Notification::make()
                        ->title('Paiement approuvé')
                        ->body('Votre versement de '.format_xaf($payment->amount)." pour la commande {$sale->tracking_code} a été validé.")
                        ->success()
                        ->sendToDatabase($sale->seller);
                }

                Notification::make()
                    ->title('Paiement validé avec succès')
                    ->success()
                    ->send();
            });
    }
}
