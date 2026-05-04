<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Enums\UserRoleEnum;
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
        $clientCount = User::where('role', UserRoleEnum::CLIENT)->count();
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
        ];
    }
}
