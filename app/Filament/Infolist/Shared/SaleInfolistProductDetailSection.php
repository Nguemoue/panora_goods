<?php

namespace App\Filament\Infolist\Shared;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\FontWeight;

class SaleInfolistProductDetailSection
{
    public static function make(): Section
    {
        return Section::make('Informations du produit')->description("List of details")
            ->columnSpanFull()
            ->columns()
            ->schema([
                TextEntry::make('product.name')->label('Product Name'),
                TextEntry::make('product.short_description')->label("Description")->placeholder("N/A"),
                TextEntry::make('sold_at')->dateTime()->label("Sold at"),
                TextEntry::make('quantity')->label("Quantity")->prefix("x"),
                TextEntry::make('tracking_code')->columnSpanFull()->label("Tracking code")->badge()
                    ->weight(FontWeight::Bold)

            ]);
    }
}
