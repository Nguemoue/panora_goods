<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\UserRoleEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('panels.profile_information'))
                    ->description(__('panels.profile_information_description'))
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('panels.full_name'))
                            ->placeholder('Jean Dupont')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone_number')
                            ->label(__('panels.phone_number'))
                            ->placeholder('+237...')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('email')
                            ->label(__('panels.email_address'))
                            ->placeholder('john@example.com')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label(__('panels.secure_password'))
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->placeholder('••••••••'),
                        TextInput::make('reference_number')
                            ->label(__('panels.reference_number'))
                            ->unique(),
                        FileUpload::make('photo')->image()->disk('public')
                    ])->columnSpanFull(),

                Section::make(__('panels.access_status'))
                    ->description(__('panels.access_status_description'))
                    ->icon('heroicon-o-shield-check')
                    ->columns(3)
                    ->schema([
                        CheckboxList::make('zones')
                            ->label(__('panels.operational_zone'))
                            ->relationship('zones', 'name')
                            ->columnSpanFull()
                            ->searchable(),
                        Select::make('role')
                            ->label(__('panels.assigned_role'))
                            ->options(UserRoleEnum::class)
                            ->required()
                            ->helperText(__('panels.panel_access_helper')),
                        Select::make('status')
                            ->label(__('panels.account_status'))
                            ->options([
                                'active' => __('panels.active_access_granted'),
                                'inactive' => __('panels.inactive_locked'),
                            ])
                            ->default('active')
                            ->required(),
                    ])->columnSpanFull(),
            ]);
    }
}
