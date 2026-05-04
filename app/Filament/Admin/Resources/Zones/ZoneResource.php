<?php

namespace App\Filament\Admin\Resources\Zones;

use App\Filament\Admin\Resources\Zones\Schemas\ZoneForm;
use App\Filament\Admin\Resources\Zones\Schemas\ZoneInfolist;
use App\Filament\Admin\Resources\Zones\Tables\ZonesTable;
use App\Models\Zone;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ZoneResource extends Resource
{
    protected static ?string $model = Zone::class;

    protected static ?string $slug = 'zones';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ZoneForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ZoneInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ZonesTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListZones::route('/'),
            'create' => Pages\CreateZone::route('/create'),
            'edit' => Pages\EditZone::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
