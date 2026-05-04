<?php

namespace App\Filament\Admin\Resources\Sales\Actions;

use App\Enums\ConfirmationStatusEnum;
use App\Models\SalePayment;

class RejectSalePaymentAction
{
    public static function make(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('reject')
            ->label('Reject Payment')
            ->color('danger')
            ->icon('heroicon-o-x-mark')
            ->visible(fn (SalePayment $record) => $record->confirmation_status === ConfirmationStatusEnum::PENDING)
            ->requiresConfirmation()
            ->action(function ($record) {
                $record->update(['confirmation_status' => ConfirmationStatusEnum::REJECTED]);
            });
    }
}
