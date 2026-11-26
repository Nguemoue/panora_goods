<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Sales\Pages;

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\SaleStatusEnum;
use App\Filament\Admin\Resources\Sales\SaleResource;
use App\Models\Sale;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label('Exporter CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn (): StreamedResponse => $this->exportCsv()),
        ];
    }

    public function getTabs(): array
    {
        $pendingValidationCount = Sale::query()
            ->whereHas('salePayments', fn (Builder $q) => $q->where('confirmation_status', ConfirmationStatusEnum::PENDING))
            ->count();

        $overdueCount = Sale::query()->overduePayment()->count();

        return [
            'all' => Tab::make('Toutes'),
            'pending_validation' => Tab::make('À valider')
                ->badge($pendingValidationCount > 0 ? $pendingValidationCount : null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereHas('salePayments', fn (Builder $q) => $q->where('confirmation_status', ConfirmationStatusEnum::PENDING))),
            'overdue' => Tab::make('En retard')
                ->badge($overdueCount > 0 ? $overdueCount : null)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->overduePayment()),
            'not_delivered' => Tab::make('Non livrées')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->notDelivered()),
            'completed' => Tab::make('Soldées & livrées')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', SaleStatusEnum::DELIVERED)->where('payment_status', PaymentStatusEnum::PAID)),
        ];
    }

    protected function exportCsv(): StreamedResponse
    {
        $sales = $this->getFilteredTableQuery()
            ->with(['product', 'seller', 'client'])
            ->get();

        return response()->streamDownload(function () use ($sales): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Code suivi',
                'Date vente',
                'Produit',
                'Quantité',
                'Vendeur',
                'Réf. vendeur',
                'Client',
                'Prix vente (XAF)',
                'Prix fournisseur (XAF)',
                'Bénéfice (XAF)',
                'Encaissé validé (XAF)',
                'Reste à payer (XAF)',
                'Statut livraison',
                'Statut paiement',
            ], ';');

            foreach ($sales as $sale) {
                $paid = $sale->getApprovedPaidAmount();
                fputcsv($handle, [
                    $sale->tracking_code,
                    $sale->sold_at?->format('d/m/Y H:i') ?? $sale->created_at?->format('d/m/Y H:i'),
                    $sale->product?->name ?? '',
                    $sale->quantity,
                    $sale->seller?->name ?? '',
                    $sale->seller?->reference_number ?? '',
                    $sale->client?->name ?? $sale->customer_name ?? '',
                    $sale->sale_price,
                    $sale->supplier_price_at_sale,
                    $sale->profit,
                    $paid,
                    max(0, (float) $sale->sale_price - $paid),
                    $sale->status?->getLabel() ?? '',
                    $sale->payment_status?->getLabel() ?? '',
                ], ';');
            }

            fclose($handle);
        }, 'ventes-panoragoods-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
