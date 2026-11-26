<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets\Admin;

use App\Enums\SaleStatusEnum;
use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class ProductSalesChart extends ChartWidget
{
    public ?Model $record = null;

    public ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isDiscovered = false;

    public function getHeading(): string
    {
        return __('panels.product_monthly_sales');
    }

    protected function getData(): array
    {
        if (! $this->record) {
            return ['datasets' => [], 'labels' => []];
        }

        $monthlyUnits = Sale::query()
            ->where('product_id', $this->record->getKey())
            ->where('status', '!=', SaleStatusEnum::CANCELLED)
            ->whereNotNull('sold_at')
            ->orderBy('sold_at')
            ->get(['sold_at', 'quantity'])
            ->groupBy(fn (Sale $sale): string => $sale->sold_at->format('Y-m'))
            ->map(fn ($sales): int => (int) $sales->sum('quantity'));

        return [
            'datasets' => [
                [
                    'label' => __('panels.units_sold'),
                    'data' => $monthlyUnits->values()->all(),
                    'fill' => 'start',
                    'borderColor' => '#004b91',
                    'backgroundColor' => 'rgba(0, 75, 145, 0.12)',
                ],
            ],
            'labels' => $monthlyUnits->keys()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
