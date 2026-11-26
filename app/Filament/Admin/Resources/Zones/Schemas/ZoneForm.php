<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Zones\Schemas;

use App\Enums\UserRoleEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ZoneForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('validation.attributes.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('city')
                    ->label(__('panels.city'))
                    ->required()
                    ->maxLength(255),
                Select::make('user_id')
                    ->label(__('panels.assigned_user'))
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query): Builder => $query
                            ->where('status', 'active')
                            ->whereIn('role', [UserRoleEnum::ADMIN, UserRoleEnum::SELLER])
                            ->orderBy('name'),
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
