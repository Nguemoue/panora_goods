<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProfit = Sale::sum('profit');
        $totalRevenue = Sale::sum('sale_price');
        $clientCount = Client::query()->count();
        $criticalStockCount = Product::where('stock_quantity', '<', 5)->count();

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
            Stat::make('Sellers', User::seller()->count())
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
            Stat::make('Total sales', Sale::query()->count())
                ->description("The numbers total of sales")
                ->descriptionIcon(Heroicon::CurrencyDollar),

            Stat::make('Total sales with finished payment', Sale::query()->paymentFinished()->count())
                ->description("The numbers of sales where the payment is finished")
                ->descriptionIcon(Heroicon::CurrencyDollar),
            Stat::make('Total sales with finished pending', Sale::query()->paymentPending()->count())
                ->description("The numbers of sales where the payment is still pending")
                ->descriptionIcon(Heroicon::CurrencyDollar),
            //order delivered or not

            Stat::make('Sale delivered',Sale::delivered()->count())
                ->description("Total sale delivered"),
            Stat::make('Sale not delivered',Sale::notDelivered()->count())
                ->description("Total sale not delivered"),

        ];
    }
}
