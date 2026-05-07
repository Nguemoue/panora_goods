<?php

namespace App\Filament\Admin\Resources\Sales\Schemas;

use App\Enums\SaleStatusEnum;
use App\Filament\Infolist\Shared\SaleInfolistProductDetailSection;
use App\Filament\Infolist\Shared\SaleInfolistSection;
use App\Models\Product;
use App\Models\Sale;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class SaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SaleInfolistProductDetailSection::make(),
                SaleInfolistSection::make()
            ]);
    }

}
