<?php

namespace App\Filament\Seller\Resources\Sales\Schemas;

use App\Filament\Infolist\Shared\SaleInfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SellerSaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('product.name')->label('Product Name'),

                //section for sale details
                SaleInfolistSection::make()
            ]);
    }
}
