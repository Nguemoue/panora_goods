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
        return Section::make(__('panels.payments'))
            ->columnSpanFull()
            ->columns(3)
            ->schema([
                TextEntry::make('payment_type')->placeholder('-')->label(__('sales.payment_type'))->badge(),
                TextEntry::make('payment_status')->placeholder('-')->label(__('sales.payment_status'))->badge(),
                TextEntry::make('payment_date_limit')->placeholder('-')->label(__('panels.payment_limit_date'))->date(),
                TextEntry::make('total_amount')->weight(FontWeight::Bold)->label(__('panels.total_amount'))
                    ->state(fn (Sale $record) => $record->sale_price)
                    ->placeholder('-')
                    ->money(currency: currency()),
                TextEntry::make('paid_amount')
                    ->weight(FontWeight::Bold)
                    ->default(0)
                    ->state(fn(Sale $record) => $record->paid_amount)
                    ->label(__('sales.paid_amount'))->money(currency: currency()),
                TextEntry::make('remaining_amount')->weight(FontWeight::Bold)
                    ->state(fn (Sale $record) => $record->getRemainingAmount())
                    ->label(__('sales.remaining_amount'))->money(currency: currency()),
            ]);
    }
}
