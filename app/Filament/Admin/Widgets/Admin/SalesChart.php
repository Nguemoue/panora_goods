<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class SalesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    public ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    protected string $color = 'primary';

    public function getHeading(): string
    {
        return __('panels.revenue_trend');
    }

    protected function getData(): array
    {
        $startDate = $this->filters['start_date'] ?? now()->startOfYear();
        $endDate = $this->filters['end_date'] ?? now();
        $zoneId = $this->filters['zone_id'] ?? null;
        $sellerId = $this->filters['seller_id'] ?? null;

        $sales = Sale::query()
            ->whereNotNull('sold_at')
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->when($sellerId, fn (Builder $q) => $q->where('seller_id', $sellerId))
            ->when($zoneId, fn (Builder $q) => $q->whereHas('seller.zones', fn (Builder $z) => $z->where('zones.id', $zoneId)))
            ->orderBy('sold_at')
            ->get(['sold_at', 'sale_price', 'profit']);

        $grouped = $sales->groupBy(fn (Sale $sale): string => $sale->sold_at->format('Y-m'));

        $labels = $grouped->keys()->values()->all();
        $revenues = $grouped->map(fn ($items): float => (float) $items->sum('sale_price'))->values()->all();
        $profits = $grouped->map(fn ($items): float => (float) $items->sum('profit'))->values()->all();

        return [
            'datasets' => [
                [
                    'label' => __('panels.monthly_revenue'),
                    'data' => $revenues,
                    'borderColor' => '#004b91',
                    'backgroundColor' => 'rgba(0, 75, 145, 0.12)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
                [
                    'label' => __('sales.net_profit'),
                    'data' => $profits,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.10)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
