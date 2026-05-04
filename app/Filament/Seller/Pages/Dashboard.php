<?php

namespace App\Filament\Seller\Pages;

use App\Filament\Seller\Widgets\Seller\LatestSales;
use App\Filament\Seller\Widgets\Seller\SellerSalesChart;
use App\Filament\Seller\Widgets\Seller\SellerStatsOverview;

class Dashboard extends \Filament\Pages\Dashboard
{
        public function getWidgets(): array
        {
            return [
                SellerStatsOverview::class,
                LatestSales::class,
                SellerSalesChart::class
            ];
        }
}
