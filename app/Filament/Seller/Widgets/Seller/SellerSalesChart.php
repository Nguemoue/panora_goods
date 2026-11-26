<?php

declare(strict_types=1);

namespace App\Filament\Seller\Widgets\Seller;

use App\Models\Sale;
use App\Settings\PaymentSetting;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class SellerSalesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected string $color = 'info';

    public function getHeading(): string
    {
        return __('panels.my_monthly_performance');
    }

    protected function getData(): array
    {
        $userId = Filament::auth()->id();
        $startDate = $this->filters['start_date'] ?? now()->startOfYear();
        $endDate = $this->filters['end_date'] ?? now();
        $sellerRate = app(PaymentSetting::class)->seller_rate;

        $sales = Sale::query()
            ->where('seller_id', $userId)
            ->whereNotNull('sold_at')
            ->whereBetween('sold_at', [$startDate, $endDate])
            ->orderBy('sold_at')
            ->get(['sold_at', 'sale_price', 'profit']);

        $grouped = $sales->groupBy(fn (Sale $sale): string => $sale->sold_at->format('Y-m'));

        $labels = $grouped->keys()->values()->all();
        $revenues = $grouped->map(fn ($items): float => (float) $items->sum('sale_price'))->values()->all();
        $commissions = $grouped->map(fn ($items): float => round((float) $items->sum('profit') * $sellerRate, 2))->values()->all();

        return [
            'datasets' => [
                [
                    'label' => __('panels.personal_revenue'),
                    'data' => $revenues,
                    'backgroundColor' => '#004b91',
                    'borderColor' => '#004b91',
                ],
                [
                    'label' => 'Ma commission',
                    'data' => $commissions,
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
