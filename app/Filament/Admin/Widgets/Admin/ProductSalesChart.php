<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class ProductSalesChart extends ChartWidget
{
    public ?Model $record = null;
    protected ?string $heading = 'Product Monthly Sales';

    public static function canView(): bool
    {
        return false; // Hidden from dashboard, used manually in pages
    }

    protected function getData(): array
    {
        if (!$this->record) {
            return ['datasets' => [], 'labels' => []];
        }

        $data = Sale::where('product_id', $this->record->id)
            ->selectRaw('strftime("%Y-%m", sold_at) as month, count(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Units Sold',
                    'data' => $data->pluck('count')->toArray(),
                    'fill' => 'start',
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
