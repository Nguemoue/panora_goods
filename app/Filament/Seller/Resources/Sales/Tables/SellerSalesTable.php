<?php

namespace App\Filament\Seller\Resources\Sales\Tables;

use App\DownloadInvoicePdfAction;
use App\Models\Sale;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SellerSalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_code')
                    ->searchable()
                    ->badge()
                    ->copyable()
                    ->label(__('sales.tracking_code')),
                TextColumn::make('product.name')
                    ->label(__('sales.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('sales.quantity'))
                    ->prefix('x')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->label(__('sales.total_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->label(__('sales.paid_amount'))
                    ->state(fn (Sale $record): float => $record->getApprovedPaidAmount())
                    ->money()
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('remaining_amount')
                    ->label(__('sales.remaining_amount'))
                    ->state(fn (Sale $record): float => $record->getRemainingAmount())
                    ->money()
                    ->badge()
                    ->color(fn (float $state): string => $state > 0 ? 'danger' : 'success'),
                TextColumn::make('client.name')
                    ->label(__('sales.client'))
                    ->description(fn (Sale $record) => $record->client?->phone_number)
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn ($state) => $state->getColor())
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label(__('sales.payment_status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('payment_date_limit')
                    ->label(__('sales.payment_deadline'))
                    ->date()
                    ->badge()
                    ->color(fn (Sale $record): string => $record->getRemainingAmount() > 0 && $record->payment_date_limit?->lt(today()) ? 'danger' : 'gray')
                    ->toggleable(),
                TextColumn::make('sold_at')
                    ->label(__('sales.sold_at'))
                    ->dateTime()
                    ->sortable(),
            ])

            ->defaultCurrency(currency())
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('open_tracking')
                        ->label(__('sales.open_tracking'))
                        ->icon('heroicon-o-map-pin')
                        ->url(fn (Sale $record): string => self::trackingUrl($record))
                        ->openUrlInNewTab(),
                    Action::make('share_tracking_whatsapp')
                        ->label(__('sales.share_tracking_whatsapp'))
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->url(fn (Sale $record): string => self::whatsAppTrackingUrl($record))
                        ->openUrlInNewTab()
                        ->visible(fn (Sale $record): bool => filled($record->client?->phone_number)),
                    Action::make('download_invoice')
                        ->label(__('sales.invoice'))
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(fn (Sale $record) => self::downloadInvoice($record)),
                ]),

            ])
            ->filters([
                Filter::make('overdue_payments')
                    ->label(__('sales.overdue_payments'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->overduePayment()),
            ]);
    }

    protected static function downloadInvoice(Sale $record): StreamedResponse|Response
    {
        return app(DownloadInvoicePdfAction::class)->handle(sale: $record);
    }

    protected static function trackingUrl(Sale $record): string
    {
        return route('track.order', ['tracking_code' => $record->tracking_code]);
    }

    protected static function whatsAppTrackingUrl(Sale $record): string
    {
        $phone = preg_replace('/\D+/', '', (string) $record->client?->phone_number);
        $message = __('sales.tracking_whatsapp_message', [
            'client' => $record->client?->name ?? __('sales.client'),
            'tracking_code' => $record->tracking_code,
            'tracking_url' => self::trackingUrl($record),
        ]);

        return "https://wa.me/{$phone}?text=".urlencode($message);
    }
}
