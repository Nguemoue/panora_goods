<?php

declare(strict_types=1);

namespace App\Filament\Seller\Widgets\Seller;

use App\Enums\ConfirmationStatusEnum;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Settings\PaymentSetting;
use Filament\Facades\Filament;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class SellerStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = Filament::auth()->id();
        $startDate = $this->filters['start_date'] ?? now()->startOfYear();
        $endDate = $this->filters['end_date'] ?? now();

        $personalRevenue = (float) Sale::query()
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->where('seller_id', $userId)
            ->sum('sale_price');

        $profit = (float) Sale::query()
            ->where('seller_id', $userId)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->sum('profit');

        $salesCount = Sale::query()
            ->where('seller_id', $userId)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->count();

        $itemsSold = (int) Sale::query()
            ->where('seller_id', $userId)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->sum('quantity');

        $approvedCollected = (float) SalePayment::query()
            ->where('confirmation_status', ConfirmationStatusEnum::APPROVED)
            ->whereHas('sale', fn (Builder $q) => $q->where('seller_id', $userId)->whereBetween('sold_at', [$startDate, $endDate]))
            ->sum('amount');

        $remainingToCollect = max(0, $personalRevenue - $approvedCollected);

        $pendingValidationCount = SalePayment::query()
            ->where('confirmation_status', ConfirmationStatusEnum::PENDING)
            ->whereHas('sale', fn (Builder $q) => $q->where('seller_id', $userId))
            ->count();

        $overduePayments = Sale::query()
            ->where('seller_id', $userId)
            ->overduePayment()
            ->count();

        $myCommission = app(PaymentSetting::class)->seller_rate * $profit;
        $recoveryRate = $personalRevenue > 0
            ? round(($approvedCollected / $personalRevenue) * 100, 1)
            : 100.0;

        return [
            Stat::make(__('sales.dashboard_revenue'), format_xaf($personalRevenue))
                ->description('Encaissé : '.format_xaf($approvedCollected)." ({$recoveryRate} %)")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(__('sales.dashboard_my_profit'), format_xaf($myCommission))
                ->description('Commission estimée sur la période')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make('Reste à recouvrer', format_xaf($remainingToCollect))
                ->description($pendingValidationCount > 0 ? "{$pendingValidationCount} acompte(s) en attente Admin" : 'Aucun acompte en attente')
                ->descriptionIcon('heroicon-m-wallet')
                ->color($remainingToCollect > 0 ? 'warning' : 'success'),
            Stat::make(__('sales.dashboard_sales_count'), $salesCount)
                ->description("Articles vendus : {$itemsSold}")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
            Stat::make(__('sales.dashboard_overdue_payments'), $overduePayments)
                ->description(__('sales.dashboard_overdue_payments_description'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($overduePayments > 0 ? 'danger' : 'success'),
        ];
    }
}
