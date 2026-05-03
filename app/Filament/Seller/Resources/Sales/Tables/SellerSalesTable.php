<?php

namespace App\Filament\Seller\Resources\Sales\Tables;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SellerSalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_code')
                    ->searchable()
                    ->copyable()
                    ->label('Tracking #'),
                TextColumn::make('product.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => $state->getColor())
                    ->sortable(),
                TextColumn::make('sold_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultCurrency(currency())
            ->recordActions([
                Action::make('download_invoice')
                    ->label('Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn (Sale $record) => self::downloadInvoice($record)),
            ])
            ->filters([
                //
            ]);
    }

    protected static function downloadInvoice(Sale $record)
    {
        $pdf = Pdf::loadView('pdf.invoice', ['sale' => $record]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "invoice-{$record->tracking_code}.pdf");
    }
}
