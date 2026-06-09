<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class SupplierStats extends BaseWidget
{
    public ?Model $record = null;
    public ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return false; // Hidden from dashboard, used manually in pages
    }

    protected function getStats(): array
    {
        if (! $this->record) {
            return [];
        }

        $productCount = $this->record->products()->count();
        $totalSoldVolume = Sale::whereIn('product_id', $this->record->products()->pluck('id'))->sum('quantity');
        $totalProfitGenerated = Sale::whereIn('product_id', $this->record->products()->pluck('id'))->sum('profit');

        return [
            Stat::make(__('panels.products_catalog'), $productCount)
                ->description(__('panels.supplier_products_description'))
                ->descriptionIcon('heroicon-m-rectangle-stack'),
            Stat::make(__('panels.total_units_sold'), $totalSoldVolume)
                ->description(__('panels.supplier_units_description'))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),
            Stat::make(__('panels.total_profit_generated'), currency() . number_format($totalProfitGenerated, 2))
                ->description(__('panels.supplier_profit_description'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}
