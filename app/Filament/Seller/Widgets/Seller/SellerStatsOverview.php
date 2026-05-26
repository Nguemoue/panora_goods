<?php

namespace App\Filament\Seller\Widgets\Seller;

use App\Models\Sale;
use Filament\Facades\Filament;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
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

        return [
            Stat::make('My Total Revenue',   Number::currency($personalRevenue))
                ->description('My overall sales income')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('My Generated Profit',   Number::currency($personalProfit))
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
