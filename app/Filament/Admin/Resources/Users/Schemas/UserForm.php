<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\UserRoleEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->description('Manage user credentials and identification.')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->placeholder('John Doe')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->placeholder('john@example.com')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('Secure Password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255)
                            ->placeholder('••••••••'),
                    ])->columnSpanFull(),

                Section::make('Access & Status')
                    ->description('Control the user permissions and account state.')
                    ->icon('heroicon-o-shield-check')
                    ->columns(2)
                    ->schema([
                        Select::make('role')
                            ->label('Assigned Role')
                            ->options(UserRoleEnum::class)
                            ->required()
                            ->helperText('Determines which panel the user can access.'),
                        Select::make('status')
                            ->label('Account Status')
                            ->options([
                                'active' => 'Active (Access Granted)',
                                'inactive' => 'Inactive (Locked)',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columnSpanFull(),
            ]);
    }
}
