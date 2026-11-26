<?php

declare(strict_types=1);

namespace App\Filament\Seller\Pages;

use App\Filament\Seller\Widgets\Seller\LatestSales;
use App\Filament\Seller\Widgets\Seller\SellerOverdueSalesWidget;
use App\Filament\Seller\Widgets\Seller\SellerSalesChart;
use App\Filament\Seller\Widgets\Seller\SellerStatsOverview;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('seller_badge')
                ->label('Ma carte d\'identité vendeur')
                ->icon(Heroicon::Identification)
                ->color('info')
                ->modalHeading('Carte Officielle Vendeur PanoraGoods')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fermer')
                ->infolist(function (): array {
                    /** @var User|null $seller */
                    $seller = Filament::auth()->user();
                    $zones = $seller?->zones()->pluck('name')->implode(', ') ?: 'Non assignée';

                    return [
                        TextEntry::make('name')
                            ->label('Nom complet')
                            ->state($seller?->name ?? '—'),
                        TextEntry::make('reference_number')
                            ->label('Matricule / Référence officielle')
                            ->state($seller?->reference_number ?? 'Non attribué')
                            ->badge()
                            ->color('primary')
                            ->copyable(),
                        TextEntry::make('zones')
                            ->label('Zone(s) d\'affectation')
                            ->state($zones),
                        TextEntry::make('verification_url')
                            ->label('Lien de vérification à partager au client')
                            ->state(route('employee-verification'))
                            ->copyable(),
                    ];
                }),
        ];
    }

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
                    ])
                    ->columns(['default' => 1, 'sm' => 3]),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            SellerStatsOverview::class,
            SellerOverdueSalesWidget::class,
            LatestSales::class,
            SellerSalesChart::class,
        ];
    }
}
