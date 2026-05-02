<?php

namespace App\Filament\Seller\Widgets\Seller;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SellerSalesChart extends ChartWidget
{
    protected ?string $heading = 'My Monthly Performance';
    protected string $color = 'info';

    protected function getData(): array
    {
        $userId = Auth::id();
        
        $data = Sale::where('seller_id', $userId)
            ->selectRaw('strftime("%Y-%m", sold_at) as month, sum(sale_price) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->take(6)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Personal Revenue',
                    'data' => $data->pluck('total')->toArray(),
                ],
            ],
            'labels' => $data->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
