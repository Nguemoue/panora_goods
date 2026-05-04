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
            Stat::make('Products Catalog', $productCount)
                ->description('Total items from this supplier')
                ->descriptionIcon('heroicon-m-rectangle-stack'),
            Stat::make('Total Units Sold', $totalSoldVolume)
                ->description('Market performance')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),
            Stat::make('Total Profit Generated', currency().number_format($totalProfitGenerated, 2))
                ->description('Earnings from these products')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}
