<?php

namespace App\Filament\Admin\Resources\Products;

use App\Filament\Admin\Resources\Products\Pages\CreateProduct;
use App\Filament\Admin\Resources\Products\Pages\EditProduct;
use App\Filament\Admin\Resources\Products\Pages\ListProducts;
use App\Filament\Admin\Resources\Products\Schemas\ProductForm;
use App\Filament\Admin\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static string| \UnitEnum|null $navigationGroup = 'Inventaire';
    protected static ?int $navigationSort = 1;
    protected static bool $hasTitleCaseModelLabel = false;

    public static function getModelLabel(): string
    {
        return __('resources.products.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.products.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.products.navigation');
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
