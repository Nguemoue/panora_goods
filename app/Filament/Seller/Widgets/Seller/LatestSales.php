<?php

namespace App\Filament\Seller\Widgets\Seller;

use App\Filament\Seller\Resources\Products\ProductResource;
use App\Filament\Seller\Resources\Sales\SaleResource;
use App\Models\Sale;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestSales extends BaseWidget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        $startDate = $this->filters['start_date'] ?? now()->startOfYear();
        $endDate = $this->filters['end_date'] ?? now();
        return $table
            ->query(
                Sale::query()->where('seller_id', Filament::auth()->id())
                    ->whereBetween('sold_at', [$startDate, $endDate])
                    ->latest('sold_at')->limit(5)
            )
            ->heading("Les 10 dernières ventes de ".$startDate . ' a '.$endDate)
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->icon(Heroicon::ShoppingBag)
                    ->url(fn (Sale $record) => $record->product ? ProductResource::getUrl('view', ['record'=>$record->product]) : null)
                    ->label('Produit'),
                Tables\Columns\TextColumn::make('client.name')
                    ->placeholder("N/A")
                    ->description(fn(Sale $record) => $record->client?->email .' / '. $record->client->phone_number)
                    ->label('Client'),
                Tables\Columns\TextColumn::make('quantité')
                    ->prefix('x')
                    ->numeric(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Prix')
                    ->money(currency: currency()),
                Tables\Columns\TextColumn::make('sold_at')
                    ->dateTime()
                    ->sinceTooltip()
                    ->label('Date vente'),
            ])
            ->recordUrl(
                fn (Sale $record): string => SaleResource::getUrl('index', ['record' => $record]), // Seller normally only has index/create
            );
    }
}
