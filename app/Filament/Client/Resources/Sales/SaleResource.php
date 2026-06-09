<?php

namespace App\Filament\Client\Resources\Sales;

use App\Filament\Client\Resources\Sales\Pages\ListSales;
use App\Filament\Client\Resources\Sales\Pages\ViewSale;
use App\Filament\Client\Resources\Sales\Tables\ClientSalesTable;
use App\Models\Sale;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptRefund;
    protected static bool $hasTitleCaseModelLabel = false;

    public static function getModelLabel(): string
    {
        return __('resources.sales.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.sales.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.sales.navigation');
    }

    public static function table(Table $table): Table
    {
        return ClientSalesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('client_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
            'view' => ViewSale::route('/{record}'),
        ];
    }
}
