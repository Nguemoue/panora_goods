<?php

namespace App\Filament\Admin\Resources\Sales\Actions;

use App\Enums\ConfirmationStatusEnum;
use App\Models\SalePayment;
use Filament\Actions\Action;

class RejectSalePaymentAction
{
    public static function make(): Action
    {
        return Action::make('reject')
            ->label(__('sales.reject_payment'))
            ->color('danger')
            ->icon('heroicon-o-x-mark')
            ->visible(fn (SalePayment $record) => $record->confirmation_status === ConfirmationStatusEnum::PENDING)
            ->requiresConfirmation()
            ->action(function ($record) {
                $record->update(['confirmation_status' => ConfirmationStatusEnum::REJECTED]);
            });
    }
}
