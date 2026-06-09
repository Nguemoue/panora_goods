<?php

namespace App\Filament\Admin\Resources\Sales\Actions;

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\SalePayment;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ApproveSalePaymentAction
{
    public static function make(): Action
    {
        return Action::make('approveSalePayment')
            ->label(__('sales.approve_payment'))
            ->icon('heroicon-o-check')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (SalePayment $record) => $record->confirmation_status === ConfirmationStatusEnum::PENDING)
            ->action(function (SalePayment $record) {
                $paymentFinished = false;
                $remainingAmount = $record->sale->getRemainingAmount();

                if ($record->sale->isOneTimePayment() && (float) $record->amount !== (float) $record->sale->sale_price) {
                    Notification::make()
                        ->body(__('sales.one_time_payment_requires_full_amount', [
                            'amount' => number_format((float) $record->sale->sale_price, 2),
                        ]))
                        ->danger()
                        ->send();

                    return;
                }

                if ((float) $record->amount > $remainingAmount) {
                    Notification::make()
                        ->danger()
                        ->body(__('sales.payment_greater_than_remaining', [
                            'amount' => number_format($remainingAmount, 2),
                        ]))
                        ->send();

                    return;
                }

                if ((float) $record->amount === $remainingAmount) {
                    $paymentFinished = true;
                }

                if ($paymentFinished) {
                    $record->sale()->update(['payment_status' => PaymentStatusEnum::PAID]);
                } else {
                    $record->sale()->update(['payment_status' => PaymentStatusEnum::PENDING]);
                }

                $record->update(['confirmation_status' => ConfirmationStatusEnum::APPROVED]);
            })
            ->requiresConfirmation();
    }
}
