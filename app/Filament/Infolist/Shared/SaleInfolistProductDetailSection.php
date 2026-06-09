<?php

namespace App\Filament\Infolist\Shared;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;

class SaleInfolistProductDetailSection
{
    public static function make(): Section
    {
        return Section::make(__('panels.product_details'))->description(__('panels.product_details_list'))
            ->columnSpanFull()
            ->columns()
            ->schema([
                TextEntry::make('product.name')->label(__('panels.product_name')),
                TextEntry::make('product.short_description')->label(__('validation.attributes.description'))->placeholder('-'),
                TextEntry::make('sold_at')->dateTime()->label(__('sales.sold_at')),
                TextEntry::make('quantity')->label(__('sales.quantity'))->prefix('x'),
                TextEntry::make('tracking_code')->columnSpanFull()->label(__('sales.tracking_code'))->badge()
                    ->weight(FontWeight::Bold)

            ]);
    }
}
