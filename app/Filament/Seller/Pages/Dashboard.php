<?php

namespace App\Filament\Seller\Pages;

use App\Filament\Seller\Widgets\Seller\LatestSales;
use App\Filament\Seller\Widgets\Seller\SellerSalesChart;
use App\Filament\Seller\Widgets\Seller\SellerStatsOverview;
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
                        // ...
                    ])
                    ->columns(),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            SellerStatsOverview::class,
            LatestSales::class,
            SellerSalesChart::class,
        ];
    }
}
