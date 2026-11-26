<?php

declare(strict_types=1);

namespace App\Filament\Seller\Pages;

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Settings\PaymentSetting;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use UnitEnum;

class MyCommissionsPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'Mes Commissions';

    protected static ?string $title = 'Mes Commissions & Recouvrements';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Transactions';

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

    public function content(Schema $schema): Schema
    {
        $metrics = $this->getPeriodMetrics();

        return $schema
            ->components([
                Section::make('Période')
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

                Section::make('Mes commissions — '.ucfirst($metrics['period_label']))
                    ->description("Taux vendeur actif : {$metrics['rate_percent']} %")
                    ->icon(Heroicon::Banknotes)
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 4])
                    ->schema([
                        TextEntry::make('earned_commission')
                            ->label('Commission acquise (ventes soldées)')
                            ->state($metrics['earned_commission'])
                            ->money(currency())
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->color('success'),
                        TextEntry::make('pending_commission')
                            ->label('Commission potentielle (en cours)')
                            ->state($metrics['pending_commission'])
                            ->money(currency())
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->color('warning'),
                        TextEntry::make('approved_payments')
                            ->label('Acomptes validés')
                            ->state($metrics['approved_payments'])
                            ->money(currency())
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold),
                        TextEntry::make('pending_payments')
                            ->label('En attente validation Admin')
                            ->state($metrics['pending_payments'])
                            ->money(currency())
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->color('primary'),
                    ]),

                EmbeddedTable::make(),
            ]);
    }

    public function table(Table $table): Table
    {
        [$start, $end] = $this->getDateRange();
        $sellerId = Filament::auth()->id();

        return $table
            ->heading('Historique de mes versements')
            ->description('Suivi de vos acomptes déclarés et de leur statut de validation.')
            ->query(
                SalePayment::query()
                    ->with(['sale.product', 'sale.client'])
                    ->whereHas('sale', fn (Builder $q) => $q->where('seller_id', $sellerId))
                    ->whereBetween('created_at', [$start, $end])
                    ->latest()
            )
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('sale.tracking_code')
                    ->label(__('sales.tracking_code'))
                    ->badge()
                    ->copyable()
                    ->searchable(),
                TextColumn::make('sale.customer_name')
                    ->label(__('sales.client'))
                    ->description(fn (SalePayment $record): ?string => $record->sale?->product?->name)
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Montant')
                    ->money(currency())
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('confirmation_status')
                    ->label('Statut')
                    ->badge(),
            ])
            ->emptyStateHeading('Aucun versement sur cette période')
            ->emptyStateIcon(Heroicon::Banknotes);
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
     * @return array{period_label: string, rate_percent: float, earned_commission: float, pending_commission: float, approved_payments: float, pending_payments: float}
     */
    protected function getPeriodMetrics(): array
    {
        [$start, $end] = $this->getDateRange();
        $sellerId = Filament::auth()->id();
        $rate = app(PaymentSetting::class)->seller_rate;

        $paidSalesProfit = (float) Sale::query()
            ->where('seller_id', $sellerId)
            ->where('payment_status', PaymentStatusEnum::PAID)
            ->whereBetween('sold_at', [$start, $end])
            ->sum('profit');

        $pendingSalesProfit = (float) Sale::query()
            ->where('seller_id', $sellerId)
            ->whereNot('payment_status', PaymentStatusEnum::PAID)
            ->whereBetween('sold_at', [$start, $end])
            ->sum('profit');

        $approvedAmount = (float) SalePayment::query()
            ->where('confirmation_status', ConfirmationStatusEnum::APPROVED)
            ->whereHas('sale', fn (Builder $q) => $q->where('seller_id', $sellerId))
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $pendingAmount = (float) SalePayment::query()
            ->where('confirmation_status', ConfirmationStatusEnum::PENDING)
            ->whereHas('sale', fn (Builder $q) => $q->where('seller_id', $sellerId))
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        return [
            'period_label' => $start->translatedFormat('F Y'),
            'rate_percent' => round($rate * 100, 1),
            'earned_commission' => $paidSalesProfit * $rate,
            'pending_commission' => $pendingSalesProfit * $rate,
            'approved_payments' => $approvedAmount,
            'pending_payments' => $pendingAmount,
        ];
    }
}
