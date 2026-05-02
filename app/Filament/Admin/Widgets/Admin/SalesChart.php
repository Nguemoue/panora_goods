<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class SalesChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Trend';
    protected string $color = 'success';

    protected function getData(): array
    {
        // Simple manual aggregation for compatibility (no flowframe/trend assumed)
        $data = Sale::selectRaw('strftime("%Y-%m", sold_at) as month, sum(sale_price) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->take(12)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Monthly Revenue',
                    'data' => $data->pluck('total')->toArray(),
                ],
            ],
            'labels' => $data->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
