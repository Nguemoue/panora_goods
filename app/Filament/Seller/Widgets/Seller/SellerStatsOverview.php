<?php

namespace App\Filament\Seller\Widgets\Seller;

use App\Models\Sale;
use Filament\Facades\Filament;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class SellerStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $userId = Filament::auth()->id();
        $startDate = $this->filters['start_date'] ?? now()->startOfYear();
        $endDate = $this->filters['end_date'] ?? now();

        $personalRevenue = Sale::query()
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->where('seller_id', $userId)
            ->sum('sale_price');
        $personalProfit = Sale::where('seller_id', $userId)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->sum('profit');
        $salesCount = Sale::where('seller_id', $userId)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->count();
        $itemsSold = Sale::where('seller_id', $userId)
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->sum('quantity');
        $overduePayments = Sale::query()
            ->where('seller_id', $userId)
            ->overduePayment()
            ->count();

        return [
            Stat::make(__('sales.dashboard_revenue'), Number::currency($personalRevenue))
                ->description(__('sales.dashboard_revenue_description'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(__('sales.dashboard_profit'), Number::currency($personalProfit))
                ->description(__('sales.dashboard_profit_description'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make(__('sales.dashboard_sales_count'), $salesCount)
                ->description(__('sales.dashboard_sales_count_description'))
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
            Stat::make(__('sales.dashboard_items_sold'), $itemsSold)
                ->description(__('sales.dashboard_items_sold_description'))
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),
            Stat::make(__('sales.dashboard_overdue_payments'), $overduePayments)
                ->description(__('sales.dashboard_overdue_payments_description'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($overduePayments > 0 ? 'danger' : 'success'),
        ];
    }
}
