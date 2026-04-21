<?php

namespace App\Filament\Resources\Phones;

use App\Filament\Resources\Phones\Pages\CreatePhone;
use App\Filament\Resources\Phones\Pages\EditPhone;
use App\Filament\Resources\Phones\Pages\ListPhones;
use App\Filament\Resources\Phones\Schemas\PhoneForm;
use App\Filament\Resources\Phones\Tables\PhonesTable;
use App\Models\Phone;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PhoneResource extends Resource
{
    protected static ?string $model = Phone::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDevicePhoneMobile;

    protected static ?int $navigationSort = 1;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('messages.phone');
    }

    public static function getPluralModelLabel(): string
    {
        return __('messages.phones');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.phones');
    }

    public static function form(Schema $schema): Schema
    {
        return PhoneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PhonesTable::configure($table);
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
            'index' => ListPhones::route('/'),
            'create' => CreatePhone::route('/create'),
            'edit' => EditPhone::route('/{record}/edit'),
        ];
    }
}
