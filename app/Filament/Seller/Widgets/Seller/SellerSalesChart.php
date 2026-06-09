<?php

namespace App\Filament\Seller\Widgets\Seller;

use App\Models\Sale;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class SellerSalesChart extends ChartWidget
{
    protected string $color = 'info';

    public function getHeading(): string
    {
        return __('panels.my_monthly_performance');
    }

    protected function getData(): array
    {
        $userId = Filament::auth()->id();

        $data = Sale::where('seller_id', $userId)
            ->selectRaw('date_format("%Y-%m", sold_at) as month, sum(sale_price) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->take(6)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('panels.personal_revenue'),
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
