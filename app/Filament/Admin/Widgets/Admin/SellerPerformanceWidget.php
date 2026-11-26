<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets\Admin;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SellerPerformanceWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        $startDate = $this->filters['start_date'] ?? now()->startOfYear();
        $endDate = $this->filters['end_date'] ?? now();
        $zoneId = $this->filters['zone_id'] ?? null;

        return $table
            ->heading('Performance des vendeurs sur la période')
            ->description('Classement par chiffre d\'affaires généré, volume de ventes et retards clients.')
            ->query(
                User::query()
                    ->where('role', UserRoleEnum::SELLER)
                    ->with('zones')
                    ->when($zoneId, fn (Builder $q) => $q->whereHas('zones', fn (Builder $z) => $z->where('zones.id', $zoneId)))
                    ->withCount([
                        'sales as period_sales_count' => fn (Builder $q) => $q->whereBetween('sold_at', [$startDate, $endDate]),
                        'sales as overdue_sales_count' => fn (Builder $q) => $q->overduePayment(),
                    ])
                    ->withSum(
                        ['sales as period_revenue' => fn (Builder $q) => $q->whereBetween('sold_at', [$startDate, $endDate])],
                        'sale_price'
                    )
                    ->withSum(
                        ['sales as period_profit' => fn (Builder $q) => $q->whereBetween('sold_at', [$startDate, $endDate])],
                        'profit'
                    )
                    ->orderByDesc('period_revenue')
            )
            ->columns([
                ImageColumn::make('photo')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn (User $record): string => $record->avatar_url),
                TextColumn::make('name')
                    ->label('Vendeur')
                    ->description(fn (User $record): ?string => $record->reference_number)
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('zones.name')
                    ->label('Zones')
                    ->badge()
                    ->separator(', ')
                    ->placeholder('Non assigné'),
                TextColumn::make('period_sales_count')
                    ->label('Ventes')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('period_revenue')
                    ->label('Chiffre d\'affaires')
                    ->default(0)
                    ->money(currency())
                    ->weight('bold')
                    ->color('success')
                    ->sortable(),
                TextColumn::make('period_profit')
                    ->label('Bénéfice généré')
                    ->default(0)
                    ->money(currency())
                    ->sortable(),
                TextColumn::make('overdue_sales_count')
                    ->label('Retards actifs')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success')
                    ->sortable(),
            ])
            ->paginated([5, 10, 25]);
    }
}
