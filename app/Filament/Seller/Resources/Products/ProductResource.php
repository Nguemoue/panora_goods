<?php

namespace App\Filament\Seller\Resources\Products;

use App\Filament\Seller\Resources\Products\Pages\ListProducts;
use App\Filament\Seller\Resources\Products\Pages\ViewProduct;
use App\Filament\Seller\Resources\Products\Schemas\SellerProductForm;
use App\Filament\Seller\Resources\Products\Tables\SellerProductsTable;
use App\Filament\Seller\Resources\Products\Infolists\SellerProductInfolist;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Infolists\Infolist;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string| \UnitEnum|null $navigationGroup = 'Catalog';

    public static function form(Schema $schema): Schema
    {
        return SellerProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SellerProductsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SellerProductInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'view' => ViewProduct::route('/{record}'),
        ];
    }
}
