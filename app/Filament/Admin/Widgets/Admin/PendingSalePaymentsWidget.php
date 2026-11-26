<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets\Admin;

use App\Enums\ConfirmationStatusEnum;
use App\Filament\Admin\Resources\Sales\Actions\ApproveSalePaymentAction;
use App\Filament\Admin\Resources\Sales\Actions\RejectSalePaymentAction;
use App\Filament\Admin\Resources\Sales\SaleResource;
use App\Models\SalePayment;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingSalePaymentsWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Paiements en attente de validation')
            ->description('Approuvez ou rejetez directement les acomptes déclarés par vos vendeurs.')
            ->query(
                SalePayment::query()
                    ->with(['sale.product', 'sale.seller', 'sale.client'])
                    ->where('confirmation_status', ConfirmationStatusEnum::PENDING)
                    ->latest()
            )
            ->columns([
                TextColumn::make('sale.tracking_code')
                    ->label(__('sales.tracking_code'))
                    ->badge()
                    ->copyable()
                    ->searchable(),
                TextColumn::make('sale.seller.name')
                    ->label(__('sales.seller'))
                    ->description(fn (SalePayment $record): ?string => $record->sale?->seller?->reference_number)
                    ->searchable(),
                TextColumn::make('sale.customer_name')
                    ->label(__('sales.client'))
                    ->description(fn (SalePayment $record): ?string => $record->sale?->product?->name)
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Montant déclaré')
                    ->money(currency())
                    ->weight('bold')
                    ->color('warning'),
                TextColumn::make('notes')
                    ->label('Réf. / Note')
                    ->placeholder('—')
                    ->wrap()
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Déclaré le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                ApproveSalePaymentAction::make(),
                RejectSalePaymentAction::make(),
                Action::make('view_sale')
                    ->label('Voir vente')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (SalePayment $record): string => SaleResource::getUrl('view', ['record' => $record->sale_id])),
            ])
            ->emptyStateHeading('Aucun paiement en attente')
            ->emptyStateDescription('Tous les versements déclarés par vos vendeurs ont été traités.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
