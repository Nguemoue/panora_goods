<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Settings\PaymentSetting;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;

class PaymentSettingPage extends Page
{
    protected static ?string $navigationLabel = 'Paramètres';

    protected static ?string $title = 'Paramètres de répartition financière';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Cog;

    public function content(Schema $schema): Schema
    {
        $settings = app(PaymentSetting::class);

        $rates = [
            'seller_rate' => ['label' => 'Part Vendeur', 'rate' => (float) $settings->seller_rate, 'color' => 'success'],
            'chief_rate' => ['label' => 'Part Chef de zone', 'rate' => (float) $settings->chief_rate, 'color' => 'info'],
            'zone_cashier_rate' => ['label' => 'Caisse Zone', 'rate' => (float) $settings->zone_cashier_rate, 'color' => 'primary'],
            'central_rate' => ['label' => 'Caisse Centrale', 'rate' => (float) $settings->central_rate, 'color' => 'primary'],
            'general_cashier_rate' => ['label' => 'Caisse Générale', 'rate' => (float) $settings->general_cashier_rate, 'color' => 'gray'],
            'delivery_rate' => ['label' => 'Livraison', 'rate' => (float) $settings->delivery_rate, 'color' => 'warning'],
        ];

        $totalPercent = round(array_sum(array_column($rates, 'rate')) * 100, 2);
        $isBalanced = abs($totalPercent - 100.0) < 0.01;
        $simulationBase = 100_000;

        return $schema
            ->components([
                Section::make('Clé de répartition du bénéfice (lecture seule)')
                    ->description('Ces taux sont verrouillés en lecture seule pour éviter toute modification accidentelle des règles comptables.')
                    ->icon(Heroicon::ShieldCheck)
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->schema([
                        ...collect($rates)->map(fn (array $item, string $key): TextEntry => TextEntry::make("rate_{$key}")
                            ->label($item['label'])
                            ->state(number_format($item['rate'] * 100, 1, ',', ' ').' %')
                            ->badge()
                            ->color($item['color'])
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold))->values()->all(),
                        TextEntry::make('total_rate_check')
                            ->label('Total des taux configurés')
                            ->state(number_format($totalPercent, 1, ',', ' ').' %'.($isBalanced ? ' — Équilibré' : ' — Déséquilibré (doit égaler 100 %)'))
                            ->badge()
                            ->color($isBalanced ? 'success' : 'danger')
                            ->columnSpanFull(),
                    ]),

                Section::make('Simulation de répartition (pour 100 000 FCFA de bénéfice net)')
                    ->description('Aperçu concret du partage appliqué lors de la clôture mensuelle.')
                    ->icon(Heroicon::Calculator)
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->schema(
                        collect($rates)->map(fn (array $item, string $key): TextEntry => TextEntry::make("sim_{$key}")
                            ->label($item['label'].' ('.number_format($item['rate'] * 100, 1, ',', ' ').' %)')
                            ->state(round($simulationBase * $item['rate'], 2))
                            ->money(currency())
                            ->weight(FontWeight::Bold)
                            ->color($item['color']))->values()->all()
                    ),
            ]);
    }
}
