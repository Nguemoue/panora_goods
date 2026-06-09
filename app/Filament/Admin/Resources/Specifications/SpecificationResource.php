<?php

namespace App\Filament\Admin\Resources\Specifications;

use App\Filament\Admin\Resources\Specifications\Pages\CreateSpecification;
use App\Filament\Admin\Resources\Specifications\Pages\EditSpecification;
use App\Filament\Admin\Resources\Specifications\Pages\ListSpecifications;
use App\Filament\Admin\Resources\Specifications\Schemas\SpecificationForm;
use App\Filament\Admin\Resources\Specifications\Tables\SpecificationsTable;
use App\Models\Specification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SpecificationResource extends Resource
{
    protected static ?string $model = Specification::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static string| \UnitEnum|null $navigationGroup = 'Inventaire';
    protected static ?int $navigationSort = 4;
    protected static bool $hasTitleCaseModelLabel = false;

    public static function getModelLabel(): string
    {
        return __('resources.specifications.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.specifications.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.specifications.navigation');
    }

    public static function form(Schema $schema): Schema
    {
        return SpecificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SpecificationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpecifications::route('/'),
            'create' => CreateSpecification::route('/create'),
            'edit' => EditSpecification::route('/{record}/edit'),
        ];
    }
}
