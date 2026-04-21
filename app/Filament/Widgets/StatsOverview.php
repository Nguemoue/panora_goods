<?php

namespace App\Filament\Widgets;

use App\Models\Phone;
use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected string|int|array $columnSpan = 'full';
    protected function getStats(): array
    {
        $totalRevenue = Sale::sum(\DB::raw('sale_price * quantity'));
        $totalProfit = Sale::sum('profit');
        $totalPhones = Phone::sum('stock_quantity');
        $inventoryValue = Phone::sum(\DB::raw('supplier_price * stock_quantity'));

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('Total revenue from all sales')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Profit', '$' . number_format($totalProfit, 2))
                ->description('Net profit from sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make('Current Inventory Value', '$' . number_format($inventoryValue, 2))
                ->description($totalPhones . ' phones currently in stock')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),
        ];
    }
}
