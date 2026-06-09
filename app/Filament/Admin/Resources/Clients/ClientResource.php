<?php

namespace App\Filament\Admin\Resources\Clients;

use App\Filament\Admin\Resources\Clients\Schemas\ClientForm;
use App\Filament\Admin\Resources\Clients\Schemas\ClientInfolist;
use App\Filament\Admin\Resources\Clients\Tables\ClientsTable;
use App\Filament\Enums\AdminNavigationGroupEnum;
use App\Models\Client;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $slug = 'clients';

    protected static string| \UnitEnum|null $navigationGroup = AdminNavigationGroupEnum::PERSONNEL;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;
    protected static bool $hasTitleCaseModelLabel = false;

    public static function getModelLabel(): string
    {
        return __('resources.clients.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.clients.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.clients.navigation');
    }

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
