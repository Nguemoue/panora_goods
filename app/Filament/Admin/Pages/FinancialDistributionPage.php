<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Enums\ConfirmationStatusEnum;
use App\Enums\UserRoleEnum;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\User;
use App\Settings\PaymentSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class FinancialDistributionPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'Répartition & Clôture';

    protected static ?string $title = 'Répartition Financière & Clôture Mensuelle';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calculator;

    protected static string|UnitEnum|null $navigationGroup = 'Commercial';

    protected static ?int $navigationSort = 2;

    public int $month;

    public int $year;

    public function mount(): void
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;
    }

    public function updatedMonth(): void
    {
        $this->resetTable();
    }

    public function updatedYear(): void
    {
        $this->resetTable();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label('Exporter la clôture (CSV)')
                ->icon(Heroicon::ArrowDownTray)
                ->color('primary')
                ->action(fn (): StreamedResponse => $this->exportCsv()),
        ];
    }

    public function content(Schema $schema): Schema
    {
        $metrics = $this->getPeriodMetrics();
        $settings = app(PaymentSetting::class);
        $totalRatePercent = round((
            $settings->seller_rate
            + $settings->chief_rate
            + $settings->zone_cashier_rate
            + $settings->central_rate
            + $settings->general_cashier_rate
            + $settings->delivery_rate
        ) * 100, 2);

        return $schema
            ->components([
                Section::make('Période de clôture')
                    ->icon(Heroicon::CalendarDays)
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Select::make('month')
                            ->label('Mois')
                            ->options(
                                collect(range(1, 12))
                                    ->mapWithKeys(fn (int $m): array => [
                                        $m => ucfirst(Carbon::create(null, $m, 1)->translatedFormat('F')),
                                    ])
                                    ->all()
                            )
                            ->live()
                            ->native(false),
                        Select::make('year')
                            ->label('Année')
                            ->options(
                                collect(range(now()->year - 2, now()->year + 1))
                                    ->mapWithKeys(fn (int $y): array => [$y => (string) $y])
                                    ->all()
                            )
                            ->live()
                            ->native(false),
                    ]),

                Section::make('Synthèse financière — '.ucfirst($metrics['period_label']))
                    ->icon(Heroicon::ChartBar)
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 4])
                    ->schema([
                        TextEntry::make('total_revenue')
                            ->label("Chiffre d'affaires ({$metrics['total_sales']} ventes)")
                            ->state($metrics['total_revenue'])
                            ->money(currency())
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold),
                        TextEntry::make('total_profit')
                            ->label('Bénéfice net à répartir')
                            ->state($metrics['total_profit'])
                            ->money(currency())
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->color('primary'),
                        TextEntry::make('approved_collected')
                            ->label('Encaissé validé')
                            ->state($metrics['approved_collected'])
                            ->money(currency())
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->color('success'),
                        TextEntry::make('remaining_to_collect')
                            ->label('Reste à recouvrer')
                            ->state($metrics['remaining_to_collect'])
                            ->money(currency())
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->color($metrics['remaining_to_collect'] > 0 ? 'warning' : 'success'),
                    ]),

                Section::make('Répartition du bénéfice')
                    ->description("Total des taux configurés : {$totalRatePercent} %")
                    ->icon(Heroicon::Banknotes)
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->schema([
                        TextEntry::make('split_seller')
                            ->label('Part Vendeurs ('.round($settings->seller_rate * 100, 1).' %)')
                            ->state($metrics['total_profit'] * $settings->seller_rate)
                            ->money(currency())
                            ->badge()
                            ->color('success'),
                        TextEntry::make('split_chief')
                            ->label('Part Chefs de zone ('.round($settings->chief_rate * 100, 1).' %)')
                            ->state($metrics['total_profit'] * $settings->chief_rate)
                            ->money(currency())
                            ->badge()
                            ->color('info'),
                        TextEntry::make('split_zone_cashier')
                            ->label('Caisse Zone ('.round($settings->zone_cashier_rate * 100, 1).' %)')
                            ->state($metrics['total_profit'] * $settings->zone_cashier_rate)
                            ->money(currency())
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('split_central')
                            ->label('Caisse Centrale ('.round($settings->central_rate * 100, 1).' %)')
                            ->state($metrics['total_profit'] * $settings->central_rate)
                            ->money(currency())
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('split_general_cashier')
                            ->label('Caisse Générale ('.round($settings->general_cashier_rate * 100, 1).' %)')
                            ->state($metrics['total_profit'] * $settings->general_cashier_rate)
                            ->money(currency())
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('split_delivery')
                            ->label('Livraison ('.round($settings->delivery_rate * 100, 1).' %)')
                            ->state($metrics['total_profit'] * $settings->delivery_rate)
                            ->money(currency())
                            ->badge()
                            ->color('warning'),
                    ]),

                EmbeddedTable::make(),
            ]);
    }

    public function table(Table $table): Table
    {
        [$start, $end] = $this->getDateRange();
        $sellerRate = app(PaymentSetting::class)->seller_rate;

        return $table
            ->heading('Détail des commissions par vendeur')
            ->description('Classement des vendeurs et commissions calculées sur la période sélectionnée.')
            ->query(
                User::query()
                    ->where('role', UserRoleEnum::SELLER)
                    ->with('zones')
                    ->withCount(['sales as month_sales_count' => fn (Builder $q) => $q->whereBetween('sold_at', [$start, $end])])
                    ->withSum(['sales as month_revenue' => fn (Builder $q) => $q->whereBetween('sold_at', [$start, $end])], 'sale_price')
                    ->withSum(['sales as month_profit' => fn (Builder $q) => $q->whereBetween('sold_at', [$start, $end])], 'profit')
                    ->orderByDesc('month_revenue')
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
                    ->placeholder('—'),
                TextColumn::make('month_sales_count')
                    ->label('Ventes')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('month_revenue')
                    ->label("Chiffre d'affaires")
                    ->default(0)
                    ->money(currency())
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('month_profit')
                    ->label('Bénéfice')
                    ->default(0)
                    ->money(currency())
                    ->sortable(),
                TextColumn::make('commission')
                    ->label('Commission vendeur')
                    ->state(fn (User $record): float => (float) ($record->month_profit ?? 0) * $sellerRate)
                    ->money(currency())
                    ->badge()
                    ->color('success'),
            ])
            ->emptyStateHeading('Aucun vendeur trouvé')
            ->emptyStateIcon(Heroicon::UserGroup);
    }

    public function exportCsv(): StreamedResponse
    {
        [$start, $end] = $this->getDateRange();
        $sellerRate = app(PaymentSetting::class)->seller_rate;

        $sellers = User::query()
            ->where('role', UserRoleEnum::SELLER)
            ->with('zones')
            ->withCount(['sales as month_sales_count' => fn (Builder $q) => $q->whereBetween('sold_at', [$start, $end])])
            ->withSum(['sales as month_revenue' => fn (Builder $q) => $q->whereBetween('sold_at', [$start, $end])], 'sale_price')
            ->withSum(['sales as month_profit' => fn (Builder $q) => $q->whereBetween('sold_at', [$start, $end])], 'profit')
            ->orderByDesc('month_revenue')
            ->get();

        return response()->streamDownload(function () use ($sellers, $sellerRate): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Vendeur', 'Référence', 'Zones', 'Ventes', 'CA (XAF)', 'Bénéfice (XAF)', 'Commission Vendeur (XAF)'], ';');

            foreach ($sellers as $seller) {
                $profit = (float) ($seller->month_profit ?? 0);
                fputcsv($handle, [
                    $seller->name,
                    $seller->reference_number ?? '—',
                    $seller->zones->pluck('name')->implode(', ') ?: '—',
                    (int) $seller->month_sales_count,
                    (float) ($seller->month_revenue ?? 0),
                    $profit,
                    round($profit * $sellerRate, 2),
                ], ';');
            }

            fclose($handle);
        }, "cloture-{$this->year}-{$this->month}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function getDateRange(): array
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();

        return [$start, $start->copy()->endOfMonth()];
    }

    /**
     * @return array{period_label: string, total_revenue: float, total_profit: float, total_sales: int, approved_collected: float, remaining_to_collect: float}
     */
    protected function getPeriodMetrics(): array
    {
        [$start, $end] = $this->getDateRange();

        $totalRevenue = (float) Sale::query()->whereBetween('sold_at', [$start, $end])->sum('sale_price');
        $totalProfit = (float) Sale::query()->whereBetween('sold_at', [$start, $end])->sum('profit');
        $totalSales = Sale::query()->whereBetween('sold_at', [$start, $end])->count();

        $approvedCollected = (float) SalePayment::query()
            ->where('confirmation_status', ConfirmationStatusEnum::APPROVED)
            ->whereHas('sale', fn (Builder $q) => $q->whereBetween('sold_at', [$start, $end]))
            ->sum('amount');

        return [
            'period_label' => $start->translatedFormat('F Y'),
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'total_sales' => $totalSales,
            'approved_collected' => $approvedCollected,
            'remaining_to_collect' => max(0, $totalRevenue - $approvedCollected),
        ];
    }
}
