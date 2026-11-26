<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Enums\UserRoleEnum;
use App\Filament\Admin\Widgets\Admin\AdminStatsOverview;
use App\Filament\Admin\Widgets\Admin\LowStockProducts;
use App\Filament\Admin\Widgets\Admin\PendingSalePaymentsWidget;
use App\Filament\Admin\Widgets\Admin\SalesChart;
use App\Filament\Admin\Widgets\Admin\SellerPerformanceWidget;
use App\Filament\Admin\Widgets\Admin\SupplierStats;
use App\Models\User;
use App\Models\Zone;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
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
                        Select::make('period_preset')
                            ->label('Période rapide')
                            ->placeholder('Personnalisée')
                            ->options([
                                'this_month' => 'Ce mois',
                                'last_month' => 'Mois dernier',
                                'this_quarter' => 'Ce trimestre',
                                'this_year' => 'Cette année',
                            ])
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                match ($state) {
                                    'this_month' => [
                                        $set('start_date', now()->startOfMonth()->toDateString()),
                                        $set('end_date', now()->toDateString()),
                                    ],
                                    'last_month' => [
                                        $set('start_date', now()->subMonth()->startOfMonth()->toDateString()),
                                        $set('end_date', now()->subMonth()->endOfMonth()->toDateString()),
                                    ],
                                    'this_quarter' => [
                                        $set('start_date', now()->startOfQuarter()->toDateString()),
                                        $set('end_date', now()->toDateString()),
                                    ],
                                    'this_year' => [
                                        $set('start_date', now()->startOfYear()->toDateString()),
                                        $set('end_date', now()->toDateString()),
                                    ],
                                    default => null,
                                };
                            }),
                        DatePicker::make('start_date')->label(__('panels.from')),
                        DatePicker::make('end_date')->label(__('panels.to')),
                        Select::make('zone_id')
                            ->label('Zone')
                            ->placeholder('Toutes les zones')
                            ->searchable()
                            ->options(fn (): array => Zone::query()->orderBy('city')->orderBy('name')->get()->mapWithKeys(fn (Zone $z): array => [$z->id => "{$z->city} — {$z->name}"])->all()),
                        Select::make('seller_id')
                            ->label('Vendeur')
                            ->placeholder('Tous les vendeurs')
                            ->searchable()
                            ->options(fn (): array => User::query()->where('role', UserRoleEnum::SELLER)->orderBy('name')->pluck('name', 'id')->all()),
                    ])
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 5]),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            AdminStatsOverview::class,
            PendingSalePaymentsWidget::class,
            SalesChart::class,
            SellerPerformanceWidget::class,
            SupplierStats::class,
            LowStockProducts::class,
        ];
    }
}
