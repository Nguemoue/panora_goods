<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Enums\UserRoleEnum;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Colors\Color;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProfit = Sale::sum('profit');
        $totalRevenue = Sale::sum('sale_price');
        $clientCount = User::where('role', UserRoleEnum::CLIENT)->count();
        $criticalStockCount = Product::where('stock_quantity', '<', 5)->count();

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('Overall sales income')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Net Profit', '$' . number_format($totalProfit, 2))
                ->description('Total margin after costs')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
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
