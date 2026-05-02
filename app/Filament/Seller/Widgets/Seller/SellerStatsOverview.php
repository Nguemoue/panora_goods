<?php

namespace App\Filament\Seller\Widgets\Seller;

use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SellerStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        
        $personalRevenue = Sale::where('seller_id', $userId)->sum('sale_price');
        $personalProfit = Sale::where('seller_id', $userId)->sum('profit');
        $salesCount = Sale::where('seller_id', $userId)->count();
        $itemsSold = Sale::where('seller_id', $userId)->sum('quantity');

        return [
            Stat::make('My Total Revenue', '$' . number_format($personalRevenue, 2))
                ->description('My overall sales income')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('My Generated Profit', '$' . number_format($personalProfit, 2))
                ->description('Margin from my sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make('My Sales Count', $salesCount)
                ->description('Total transactions')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
            Stat::make('Items Sold', $itemsSold)
                ->description('Total units moved')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),
        ];
    }
}
