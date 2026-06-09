<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\Admin\AdminStatsOverview;
use App\Filament\Admin\Widgets\Admin\LowStockProducts;
use App\Filament\Admin\Widgets\Admin\ProductSalesChart;
use App\Filament\Admin\Widgets\Admin\SalesChart;
use App\Filament\Admin\Widgets\Admin\SupplierStats;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->heading(__('panels.filters'))
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        DatePicker::make('start_date')->label(__('panels.from')),
                        DatePicker::make('end_date')->label(__('panels.to')),
                    ])
                    ->columns(),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            AdminStatsOverview::class,
            SupplierStats::class,
            LowStockProducts::class,
            // SalesChart::class,
            // ProductSalesChart::class
        ];
    }
}
