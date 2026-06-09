<?php

namespace App\Filament\Admin\Resources\Sales;

use App\Filament\Admin\Resources\Sales\Pages\CreateSale;
use App\Filament\Admin\Resources\Sales\Pages\EditSale;
use App\Filament\Admin\Resources\Sales\Pages\ListSales;
use App\Filament\Admin\Resources\Sales\Pages\ViewSale;
use App\Filament\Admin\Resources\Sales\RelationManagers\SalePaymentRelationManager;
use App\Filament\Admin\Resources\Sales\Schemas\SaleForm;
use App\Filament\Admin\Resources\Sales\Schemas\SaleInfolist;
use App\Filament\Admin\Resources\Sales\Tables\SalesTable;
use App\Models\Sale;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static string| \UnitEnum|null $navigationGroup = 'Commercial';
    protected static ?int $navigationSort = 1;
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

    public static function form(Schema $schema): Schema
    {
        return SaleForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SaleInfolist::configure($schema);
    }
    public static function table(Table $table): Table
    {
        return SalesTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withSum(['approvedSalePayments as paid_amount'], 'amount');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
            'view' => ViewSale::route('/{record}'),
            //'create' => CreateSale::route('/create'),
            'edit' => EditSale::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            SalePaymentRelationManager::class,
        ];
    }
}
