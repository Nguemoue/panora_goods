<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Schemas;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

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
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->rule(Password::min(8))
                            ->maxLength(255)
                            ->placeholder('••••••••')
                            ->helperText('Minimum 8 caractères.'),
                        TextInput::make('reference_number')
                            ->label(__('panels.reference_number'))
                            ->default(fn (string $operation): ?string => $operation === 'create' ? User::generateUniqueReferenceNumber() : null)
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        FileUpload::make('photo')
                            ->label(__('users.profile_photo'))
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1', null])
                            ->circleCropper()
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public'),
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
                            ->disabled(fn (?User $record): bool => $record !== null && $record->id === auth()->id())
                            ->dehydrated()
                            ->helperText(fn (?User $record): string => $record !== null && $record->id === auth()->id()
                                ? 'Vous ne pouvez pas modifier votre propre rôle.'
                                : __('panels.panel_access_helper')),
                        Select::make('status')
                            ->label(__('panels.account_status'))
                            ->options([
                                'active' => __('panels.active_access_granted'),
                                'inactive' => __('panels.inactive_locked'),
                            ])
                            ->default('active')
                            ->required()
                            ->disabled(fn (?User $record): bool => $record !== null && $record->id === auth()->id())
                            ->dehydrated()
                            ->helperText(fn (?User $record): ?string => $record !== null && $record->id === auth()->id()
                                ? 'Vous ne pouvez pas désactiver votre propre compte.'
                                : null),
                    ])->columnSpanFull(),
            ]);
    }
}
