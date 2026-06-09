<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
class SalesChart extends ChartWidget
{
    public ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 'full';
    protected string $color = 'success';

    public function getHeading(): string
    {
        return __('panels.revenue_trend');
    }

    protected function getData(): array
    {
        // Simple manual aggregation for compatibility (no flowframe/trend assumed)
        $data = Sale::selectRaw('date_format("%Y-%m", sold_at) as month, sum(sale_price) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->take(12)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('panels.monthly_revenue'),
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
