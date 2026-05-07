<?php

namespace App\Filament\Seller\Resources\Sales\Schemas;

use App\Filament\Infolist\Shared\SaleInfolistProductDetailSection;
use App\Filament\Infolist\Shared\SaleInfolistSection;
use Filament\Schemas\Schema;

class SellerSaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SaleInfolistProductDetailSection::make(),
                //section for sale details
                SaleInfolistSection::make()
            ]);
    }
}
