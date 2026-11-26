<?php

declare(strict_types=1);

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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static string|UnitEnum|null $navigationGroup = 'Inventaire';

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

    public static function getNavigationBadge(): ?string
    {
        $lowStockCount = Product::query()
            ->where('status', 'active')
            ->where('stock_quantity', '<=', 5)
            ->count();

        return $lowStockCount > 0 ? (string) $lowStockCount : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $outOfStockExists = Product::query()
            ->where('status', 'active')
            ->where('stock_quantity', '<=', 0)
            ->exists();

        return $outOfStockExists ? 'danger' : 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Produits actifs en stock faible ou rupture';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'product_code', 'model_number', 'brand.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Product $record */
        return array_filter([
            'Code' => $record->product_code,
            'Marque' => $record->brand?->name,
            'Prix' => format_xaf($record->selling_price),
            'Stock' => (string) $record->stock_quantity,
        ]);
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
