<?php

namespace App\Filament\Client\Resources\Products;

use App\Filament\Client\Resources\Products\Pages\ListProducts;
use App\Filament\Client\Resources\Products\Pages\ViewProduct;
use App\Filament\Client\Resources\Products\Schemas\ClientProductForm;
use App\Filament\Client\Resources\Products\Tables\ClientProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
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
        return ClientProductForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientProductsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', 'active');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'view' => ViewProduct::route('/{record}'),
        ];
    }
}
