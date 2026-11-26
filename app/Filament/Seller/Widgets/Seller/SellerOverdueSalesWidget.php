<?php

declare(strict_types=1);

namespace App\Filament\Seller\Widgets\Seller;

use App\Enums\PaymentStatusEnum;
use App\Filament\Seller\Resources\Sales\SaleResource;
use App\Models\Sale;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class SellerOverdueSalesWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        $sellerId = Filament::auth()->id();

        return $table
            ->heading('Mes relances prioritaires')
            ->description('Clients avec échéance dépassée ou arrivant dans les 3 prochains jours.')
            ->query(
                Sale::query()
                    ->with(['product', 'client'])
                    ->withSum(['approvedSalePayments as paid_amount'], 'amount')
                    ->where('seller_id', $sellerId)
                    ->whereNot('payment_status', PaymentStatusEnum::PAID)
                    ->whereNotNull('payment_date_limit')
                    ->whereDate('payment_date_limit', '<=', today()->addDays(3))
                    ->orderBy('payment_date_limit')
            )
            ->columns([
                TextColumn::make('tracking_code')
                    ->label(__('sales.tracking_code'))
                    ->badge()
                    ->copyable(),
                TextColumn::make('customer_name')
                    ->label(__('sales.client'))
                    ->description(fn (Sale $record): ?string => $record->client?->phone_number)
                    ->weight('bold'),
                TextColumn::make('product.name')
                    ->label(__('sales.product')),
                TextColumn::make('remaining_amount')
                    ->label('Reste à régler')
                    ->state(fn (Sale $record): float => $record->getRemainingAmount())
                    ->money(currency())
                    ->badge()
                    ->color('danger'),
                TextColumn::make('payment_date_limit')
                    ->label('Échéance')
                    ->date('d/m/Y')
                    ->badge()
                    ->color(fn (Sale $record): string => $record->payment_date_limit?->lt(today()) ? 'danger' : 'warning'),
            ])
            ->recordActions([
                Action::make('remind_whatsapp')
                    ->label('Relancer WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn (Sale $record): bool => filled($record->client?->phone_number))
                    ->url(function (Sale $record): string {
                        $phone = preg_replace('/[^\d]/', '', (string) $record->client?->phone_number);
                        $remaining = format_xaf($record->getRemainingAmount());
                        $trackingUrl = route('track.order', ['tracking_code' => $record->tracking_code]);
                        $message = "Bonjour {$record->customer_name}, petit rappel concernant votre commande {$record->tracking_code} ({$record->product?->name}). Il reste {$remaining} à régler. Suivi : {$trackingUrl}";

                        return "https://wa.me/{$phone}?text=".urlencode($message);
                    })
                    ->openUrlInNewTab(),
                Action::make('view_sale')
                    ->label('Ouvrir')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Sale $record): string => SaleResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateHeading('Aucune relance urgente')
            ->emptyStateDescription('Tous vos clients sont à jour sur leurs échéances.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
