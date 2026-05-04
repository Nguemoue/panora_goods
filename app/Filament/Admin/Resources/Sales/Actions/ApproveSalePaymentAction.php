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
            ->label('Approve Payment')
            ->icon('heroicon-o-check')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (SalePayment $record) => $record->confirmation_status === ConfirmationStatusEnum::PENDING)
            ->action(function (SalePayment $record) {

                $paymentFinished = false;
                $remainingAmount = $record->sale->sale_price -  (float) $record->sale->approvedSalePayments()->sum('amount');
                //validate the payment amount rules
                if ($record->sale->isOneTimePayment() && $record->amount !== $record->sale->sale_price) {
                    Notification::make()
                        ->body('Payment amount is less than the total price of the sale. This payment type is one time payment, you must paid the full amount: ' . number_format($record->sale->sale_price, 2))
                        ->danger()
                        ->send();
                    return;
                }
                if ($record->amount > $remainingAmount) {
                    Notification::make()->danger()->body('Payment amount is greater than the remaining amount to be paid. The remaining amount is: ' . number_format($remainingAmount, 2))->send();
                    return;
                }
                if ($record->amount === $remainingAmount) {
                    $paymentFinished = true;
                }
                if ($paymentFinished) {
                    $record->sale->update(['payment_status' => PaymentStatusEnum::PAID]);
                }
                //approve the payment by updating the confirmation_status field to approved and
                $record->update(['confirmation_status' => ConfirmationStatusEnum::APPROVED]);

            })
            ->requiresConfirmation();
    }
}
