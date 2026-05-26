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

        $totalProfit = Sale::query()->where('sold_at',[$startDate,$endDate])->sum('profit');
        $totalRevenue = Sale::query()->where('sold_at',[$startDate,$endDate])->sum('sale_price');
        $clientCount = Client::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $criticalStockCount = Product::where('stock_quantity', '<', 5)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return [
            Stat::make('Total Revenue', Number::currency($totalRevenue))
                ->description('Overall sales income')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            //capital stat
            Stat::make('Capital', Number::currency($totalRevenue - $totalProfit))
                ->description('Total expenses on products')
                ->descriptionIcon(Heroicon::CurrencyDollar)
                ->color('danger'),
            Stat::make('Net Profit', Number::currency($totalProfit))
                ->description('Total margin after costs')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            //number of sellers
            Stat::make('Sellers', User::seller()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count())
                ->description('Total sellers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
            Stat::make('Active Clients', $clientCount)
                ->description('Registered buyers')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('Low Stock Alerts', $criticalStockCount)
                ->description('Products below 5 units')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($criticalStockCount > 0 ? 'danger' : 'success'),
            // sales
            Stat::make('Total sales', Sale::query()
                ->whereBetween('sold_at', [$startDate, $endDate])
                ->count())
                ->description("The numbers total of sales")
                ->descriptionIcon(Heroicon::CurrencyDollar),

            Stat::make('Total sales with finished payment', Sale::query()->paymentFinished()
                ->whereBetween('sold_at', [$startDate, $endDate])
                ->count())
                ->description("The numbers of sales where the payment is finished")
                ->descriptionIcon(Heroicon::CurrencyDollar),
            Stat::make('Total sales with finished pending', Sale::query()->paymentPending()
                ->whereBetween('sold_at', [$startDate, $endDate])
                ->count())
                ->description("The numbers of sales where the payment is still pending")
                ->descriptionIcon(Heroicon::CurrencyDollar),
            //order delivered or not

            Stat::make('Sale delivered',Sale::delivered()
                ->whereBetween('sold_at', [$startDate, $endDate])
                ->count())
                ->description("Total sale delivered"),
            Stat::make('Sale not delivered',Sale::notDelivered()
                ->whereBetween('sold_at', [$startDate, $endDate])
                ->count())
                ->description("Total sale not delivered"),

        ];
    }
}
