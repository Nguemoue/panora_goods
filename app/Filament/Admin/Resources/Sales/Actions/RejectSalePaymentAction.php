<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Sales\Actions;

use App\Enums\ConfirmationStatusEnum;
use App\Models\SalePayment;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class RejectSalePaymentAction
{
    public static function make(): Action
    {
        return Action::make('reject')
            ->label(__('sales.reject_payment'))
            ->color('danger')
            ->icon('heroicon-o-x-mark')
            ->visible(fn (SalePayment $record): bool => $record->confirmation_status === ConfirmationStatusEnum::PENDING)
            ->requiresConfirmation()
            ->modalHeading('Rejeter ce versement')
            ->modalDescription('Indiquez éventuellement un motif pour aider le vendeur à corriger sa déclaration.')
            ->schema([
                Textarea::make('reason')
                    ->label('Motif du rejet (optionnel)')
                    ->placeholder('Ex : Référence Mobile Money introuvable, montant incorrect...')
                    ->rows(3)
                    ->maxLength(500),
            ])
            ->action(function (SalePayment $record, array $data): void {
                $reason = trim((string) ($data['reason'] ?? ''));

                $rejectedPayment = DB::transaction(function () use ($record, $reason): ?SalePayment {
                    $payment = SalePayment::query()
                        ->lockForUpdate()
                        ->find($record->id);

                    if (! $payment || $payment->confirmation_status !== ConfirmationStatusEnum::PENDING) {
                        return null;
                    }

                    $notes = $payment->notes;

                    if ($reason !== '') {
                        $notes = filled($notes)
                            ? "{$notes}\n[Rejet] {$reason}"
                            : "[Rejet] {$reason}";
                    }

                    $payment->update([
                        'confirmation_status' => ConfirmationStatusEnum::REJECTED,
                        'notes' => $notes,
                    ]);

                    return $payment->load('sale.seller');
                });

                if (! $rejectedPayment) {
                    Notification::make()
                        ->title('Paiement déjà traité')
                        ->warning()
                        ->send();

                    return;
                }

                if ($rejectedPayment->sale?->seller) {
                    $body = 'Votre versement de '.format_xaf($rejectedPayment->amount)." pour la commande {$rejectedPayment->sale->tracking_code} a été rejeté par l'administration.";

                    if ($reason !== '') {
                        $body .= " Motif : {$reason}";
                    }

                    Notification::make()
                        ->title('Paiement rejeté')
                        ->body($body)
                        ->danger()
                        ->sendToDatabase($rejectedPayment->sale->seller);
                }

                Notification::make()
                    ->title('Paiement rejeté')
                    ->warning()
                    ->send();
            });
    }
}
