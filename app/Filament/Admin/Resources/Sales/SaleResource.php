<?php

namespace App\Filament\Admin\Resources\Sales;

use App\Enums\ConfirmationStatusEnum;
use App\Filament\Admin\Resources\Sales\Pages\CreateSale;
use App\Filament\Admin\Resources\Sales\Pages\EditSale;
use App\Filament\Admin\Resources\Sales\Pages\ListSales;
use App\Filament\Admin\Resources\Sales\Pages\ViewSale;
use App\Filament\Admin\Resources\Sales\RelationManagers\SalePaymentRelationManager;
use App\Filament\Admin\Resources\Sales\Schemas\SaleForm;
use App\Filament\Admin\Resources\Sales\Schemas\SaleInfolist;
use App\Filament\Admin\Resources\Sales\Tables\SalesTable;
use App\Models\Sale;
use App\Models\SalePayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Commercial';

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

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = SalePayment::query()
            ->where('confirmation_status', ConfirmationStatusEnum::PENDING)
            ->count();

        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Paiements en attente de validation';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['tracking_code', 'customer_name'];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['product', 'seller', 'client'])
            ->withSum(['approvedSalePayments as paid_amount'], 'amount')
            ->withCount([
                'salePayments as pending_payments_count' => fn ($query) => $query->where('confirmation_status', ConfirmationStatusEnum::PENDING),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
            'view' => ViewSale::route('/{record}'),
            // 'create' => CreateSale::route('/create'),
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
