<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Sales\Tables;

use App\DownloadInvoicePdfAction;
use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\SaleStatusEnum;
use App\Enums\UserRoleEnum;
use App\Models\Sale;
use App\Models\User;
use App\Models\Zone;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_code')
                    ->label(__('sales.tracking_code'))
                    ->badge()
                    ->copyable()
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label(__('sales.product'))
                    ->description(fn (Sale $record): string => 'Qté : ×'.$record->quantity)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('seller.name')
                    ->label(__('sales.seller'))
                    ->description(fn (Sale $record): ?string => $record->seller?->reference_number)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label(__('sales.client'))
                    ->description(fn (Sale $record): ?string => $record->client?->phone_number)
                    ->searchable(),
                TextColumn::make('supplier_price_at_sale')
                    ->label(__('sales.supplier_price'))
                    ->money()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sale_price')
                    ->label(__('sales.total_price'))
                    ->money()
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->label(__('sales.paid_amount'))
                    ->default(0)
                    ->money()
                    ->badge()
                    ->color('success'),
                TextColumn::make('remaining_amount')
                    ->label(__('sales.remaining_amount'))
                    ->money()
                    ->state(fn (Sale $record): float => $record->getRemainingAmount())
                    ->badge()
                    ->color(fn (float $state): string => $state > 0 ? 'danger' : 'success'),
                TextColumn::make('pending_payments_count')
                    ->label('À valider')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'warning' : 'gray')
                    ->toggleable(),
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
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sold_at')
                    ->label(__('sales.sold_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultCurrency(currency())
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('seller_id')
                    ->label(__('sales.seller'))
                    ->searchable()
                    ->options(fn (): array => User::query()->where('role', UserRoleEnum::SELLER)->orderBy('name')->pluck('name', 'id')->all()),
                SelectFilter::make('zone')
                    ->label('Zone')
                    ->searchable()
                    ->options(fn (): array => Zone::query()->orderBy('city')->orderBy('name')->get()->mapWithKeys(fn (Zone $z): array => [$z->id => "{$z->city} — {$z->name}"])->all())
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value']) ? $query->whereHas('seller.zones', fn (Builder $z) => $z->where('zones.id', $data['value'])) : $query),
                SelectFilter::make('status')
                    ->label('Statut livraison')
                    ->options(SaleStatusEnum::class),
                SelectFilter::make('payment_status')
                    ->label('Statut paiement')
                    ->options(PaymentStatusEnum::class),
                Filter::make('pending_validation')
                    ->label('Paiements à valider')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereHas('salePayments', fn (Builder $q) => $q->where('confirmation_status', ConfirmationStatusEnum::PENDING))),
                Filter::make('overdue_payments')
                    ->label(__('sales.overdue_payments'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->overduePayment()),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('mark_delivered')
                        ->label('Marquer comme livrée')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (Sale $record): bool => ! in_array($record->status, [SaleStatusEnum::DELIVERED, SaleStatusEnum::CANCELLED], true))
                        ->action(function (Sale $record): void {
                            $record->update(['status' => SaleStatusEnum::DELIVERED]);

                            if ($record->seller) {
                                Notification::make()
                                    ->title('Commande livrée')
                                    ->body("La commande {$record->tracking_code} a été marquée comme livrée.")
                                    ->success()
                                    ->sendToDatabase($record->seller);
                            }

                            Notification::make()
                                ->title('Statut mis à jour : Livrée')
                                ->success()
                                ->send();
                        }),
                    Action::make('download_invoice')
                        ->label(__('sales.invoice'))
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(fn (Sale $record) => app(DownloadInvoicePdfAction::class)->handle(sale: $record)),
                    Action::make('open_tracking')
                        ->label(__('sales.open_tracking'))
                        ->icon('heroicon-o-map-pin')
                        ->url(fn (Sale $record): string => route('track.order', ['tracking_code' => $record->tracking_code]))
                        ->openUrlInNewTab(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $deletedCount = 0;
                            $skippedCount = 0;

                            foreach ($records as $record) {
                                /** @var Sale $record */
                                if ($record->canBeDeleted()) {
                                    $record->delete();
                                    $deletedCount++;
                                } else {
                                    $skippedCount++;
                                }
                            }

                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->title("{$deletedCount} vente(s) supprimée(s)")
                                    ->success()
                                    ->send();
                            }

                            if ($skippedCount > 0) {
                                Notification::make()
                                    ->title("{$skippedCount} vente(s) protégée(s)")
                                    ->body('Les ventes livrées ou ayant des paiements approuvés ne peuvent pas être supprimées.')
                                    ->warning()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
