<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets\Admin;

use App\Enums\ConfirmationStatusEnum;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class AdminStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $startDate = $this->filters['start_date'] ?? now()->startOfYear();
        $endDate = $this->filters['end_date'] ?? now();
        $zoneId = $this->filters['zone_id'] ?? null;
        $sellerId = $this->filters['seller_id'] ?? null;

        $salesScope = fn (): Builder => Sale::query()
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->when($sellerId, fn (Builder $q) => $q->where('seller_id', $sellerId))
            ->when($zoneId, fn (Builder $q) => $q->whereHas('seller.zones', fn (Builder $z) => $z->where('zones.id', $zoneId)));

        $totalRevenue = (float) $salesScope()->sum('sale_price');
        $totalProfit = (float) $salesScope()->sum('profit');
        $totalSalesCount = $salesScope()->count();
        $paidSalesCount = $salesScope()->paymentFinished()->count();
        $pendingPaymentSalesCount = $salesScope()->paymentPending()->count();
        $deliveredCount = $salesScope()->delivered()->count();
        $notDeliveredCount = $salesScope()->notDelivered()->count();

        $approvedCollected = (float) SalePayment::query()
            ->where('confirmation_status', ConfirmationStatusEnum::APPROVED)
            ->whereHas('sale', function (Builder $q) use ($startDate, $endDate, $sellerId, $zoneId): void {
                $q->whereBetween('sold_at', [$startDate, $endDate])
                    ->when($sellerId, fn (Builder $sq) => $sq->where('seller_id', $sellerId))
                    ->when($zoneId, fn (Builder $sq) => $sq->whereHas('seller.zones', fn (Builder $z) => $z->where('zones.id', $zoneId)));
            })
            ->sum('amount');

        $remainingToCollect = max(0, $totalRevenue - $approvedCollected);

        $pendingValidationCount = SalePayment::query()
            ->where('confirmation_status', ConfirmationStatusEnum::PENDING)
            ->count();

        // Real-time inventory & team alerts (NOT filtered by product created_at!)
        $criticalStockCount = Product::query()
            ->where('status', 'active')
            ->where('stock_quantity', '<', 5)
            ->count();

        $activeSellersCount = User::seller()
            ->where('status', 'active')
            ->when($zoneId, fn (Builder $q) => $q->whereHas('zones', fn (Builder $z) => $z->where('zones.id', $zoneId)))
            ->count();

        $clientCount = Client::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $overduePaymentCount = Sale::query()
            ->overduePayment()
            ->when($sellerId, fn (Builder $q) => $q->where('seller_id', $sellerId))
            ->when($zoneId, fn (Builder $q) => $q->whereHas('seller.zones', fn (Builder $z) => $z->where('zones.id', $zoneId)))
            ->count();

        return [
            Stat::make(__('sales.total_revenue'), format_xaf($totalRevenue))
                ->description(__('sales.dashboard_revenue_description'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Encaissé validé', format_xaf($approvedCollected))
                ->description('Reste à recouvrer : '.format_xaf($remainingToCollect))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($remainingToCollect > 0 ? 'warning' : 'success'),
            Stat::make(__('sales.net_profit'), format_xaf($totalProfit))
                ->description(__('sales.capital').' : '.format_xaf($totalRevenue - $totalProfit))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make('Paiements à valider', $pendingValidationCount)
                ->description('Acomptes déclarés en attente')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color($pendingValidationCount > 0 ? 'warning' : 'success'),
            Stat::make(__('sales.dashboard_overdue_payments'), $overduePaymentCount)
                ->description(__('sales.dashboard_overdue_payments_description'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($overduePaymentCount > 0 ? 'danger' : 'success'),
            Stat::make(__('sales.low_stock_alerts'), $criticalStockCount)
                ->description('Produits actifs avec stock < 5')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($criticalStockCount > 0 ? 'danger' : 'success'),
            Stat::make(__('sales.total_sales'), $totalSalesCount)
                ->description("Soldées : {$paidSalesCount} · En cours : {$pendingPaymentSalesCount}")
                ->descriptionIcon(Heroicon::ShoppingBag)
                ->color('info'),
            Stat::make(__('sales.sale_delivered'), "{$deliveredCount} / {$totalSalesCount}")
                ->description("Non livrées : {$notDeliveredCount}")
                ->descriptionIcon('heroicon-m-truck')
                ->color($notDeliveredCount > 0 ? 'warning' : 'success'),
            Stat::make(__('users.sellers'), $activeSellersCount)
                ->description("Nouveaux clients sur période : {$clientCount}")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('gray'),
        ];
    }
}
