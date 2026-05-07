<?php

namespace App\Filament\Seller\Resources\Sales;

use App\Enums\ConfirmationStatusEnum;
use App\Filament\Seller\Resources\Sales\Pages\CreateSale;
use App\Filament\Seller\Resources\Sales\Pages\ListSales;
use App\Filament\Seller\Resources\Sales\Pages\ViewSale;
use App\Filament\Seller\Resources\Sales\RelationManagers\SalePaymentRelationManager;
use App\Filament\Seller\Resources\Sales\Schemas\SellerSaleForm;
use App\Filament\Seller\Resources\Sales\Schemas\SellerSaleInfolist;
use App\Filament\Seller\Resources\Sales\Tables\SellerSalesTable;
use App\Models\Sale;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string|\UnitEnum|null $navigationGroup = 'Transactions';


    public static function infolist(Schema $schema): Schema
    {
        return SellerSaleInfolist::configure($schema);
    }

    public static function form(Schema $schema): Schema
    {
        return SellerSaleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SellerSalesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withSum(['approvedSalePayments as paid_amount'], 'amount')
            ->where('seller_id', Filament::auth()->id());
    }

    public static function getRelations(): array
    {
        return [
            SalePaymentRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
            'create' => CreateSale::route('/create'),
            'view' => ViewSale::route('/{record}'),
        ];
    }
}
