<?php

namespace App\Filament\Infolist\Shared;

use App\Models\Sale;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;

class SaleInfolistSection
{
    public static function make(): Section
    {
        return Section::make('Payments')
            ->columnSpanFull()
            ->columns(3)
            ->schema([
                TextEntry::make('payment_type')->placeholder('-')->label('Payment Type')->badge(),
                TextEntry::make('payment_status')->placeholder("-")->label('Payment Status')->badge(),
                TextEntry::make('payment_date_limit')->placeholder('-')->label('Payment Limit Date')->date(),
                TextEntry::make('total_amount')->weight(FontWeight::Bold)->label('Total Amount')
                    ->state(fn(Sale $record)=>$record->sale_price)
                    ->placeholder('-')
                    ->money(currency: currency()),
                TextEntry::make('paid_amount')
                    ->weight(FontWeight::Bold)
                    ->default(0)
                    ->state(fn(Sale $record) => $record->paid_amount)
                    ->label('Paid Amount')->money(currency: currency()),
                TextEntry::make('remaining_amount')->weight(FontWeight::Bold)
                    ->state(fn(Sale $record)=>$record->sale_price - (int) $record->paid_amount)
                    ->label('Remaining Amount')->money(currency: currency()),
            ]);
    }
}
