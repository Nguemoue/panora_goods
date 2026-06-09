<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class AdminStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = $this->filters['start_date'] ?? now()->startOfYear();
        $endDate = $this->filters['end_date'] ?? now();

        $totalProfit = Sale::query()->whereBetween('sold_at', [$startDate, $endDate])->sum('profit');
        $totalRevenue = Sale::query()->whereBetween('sold_at', [$startDate, $endDate])->sum('sale_price');
        $clientCount = Client::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $criticalStockCount = Product::where('stock_quantity', '<', 5)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $overduePaymentCount = Sale::query()->overduePayment()->count();

        return [
            Stat::make(__('sales.total_revenue'), Number::currency($totalRevenue))
                ->description(__('sales.dashboard_revenue_description'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(__('sales.capital'), Number::currency($totalRevenue - $totalProfit))
                ->description(__('sales.capital_description'))
                ->descriptionIcon(Heroicon::CurrencyDollar)
                ->color('danger'),
            Stat::make(__('sales.net_profit'), Number::currency($totalProfit))
                ->description(__('sales.net_profit_description'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make(__('users.sellers'), User::seller()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count())
                ->description(__('sales.total_sellers_description'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
            Stat::make(__('sales.active_clients'), $clientCount)
                ->description(__('sales.active_clients_description'))
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make(__('sales.low_stock_alerts'), $criticalStockCount)
                ->description(__('sales.low_stock_alerts_description'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($criticalStockCount > 0 ? 'danger' : 'success'),
            Stat::make(__('sales.total_sales'), Sale::query()
                ->whereBetween('sold_at', [$startDate, $endDate])
                ->count())
                ->description(__('sales.total_sales_description'))
                ->descriptionIcon(Heroicon::CurrencyDollar),

            Stat::make(__('sales.total_paid_sales'), Sale::query()->paymentFinished()
                ->whereBetween('sold_at', [$startDate, $endDate])
                ->count())
                ->description(__('sales.total_paid_sales_description'))
                ->descriptionIcon(Heroicon::CurrencyDollar),
            Stat::make(__('sales.total_pending_payment_sales'), Sale::query()->paymentPending()
                ->whereBetween('sold_at', [$startDate, $endDate])
                ->count())
                ->description(__('sales.total_pending_payment_sales_description'))
                ->descriptionIcon(Heroicon::CurrencyDollar),

            Stat::make(__('sales.sale_delivered'), Sale::delivered()
                ->whereBetween('sold_at', [$startDate, $endDate])
                ->count())
                ->description(__('sales.sale_delivered_description')),
            Stat::make(__('sales.sale_not_delivered'), Sale::notDelivered()
                ->whereBetween('sold_at', [$startDate, $endDate])
                ->count())
                ->description(__('sales.sale_not_delivered_description')),
            Stat::make(__('sales.dashboard_overdue_payments'), $overduePaymentCount)
                ->description(__('sales.dashboard_overdue_payments_description'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($overduePaymentCount > 0 ? 'danger' : 'success'),

        ];
    }
}
