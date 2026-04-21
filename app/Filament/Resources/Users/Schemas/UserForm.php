<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(__('messages.user_management'))
                    ->id('user-management-tabs')
                    ->contained(false)
                    ->scrollable()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tabs\Tab::make(__('messages.personal'))
                            ->icon(Heroicon::OutlinedUser)
                            ->schema([
                                Section::make(__('messages.personal_information'))
                                    ->description(__('messages.update_user_details'))
                                    ->icon(Heroicon::OutlinedInformationCircle)
                                    ->columns([
                                        'sm' => 2,
                                        'lg' => 3,
                                        'xl' => 4,
                                    ])
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('validation.attributes.name'))
                                            ->placeholder(__('messages.enter_full_name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText(__('messages.first_and_last_name'))
                                            ->columnSpan([
                                                'default' => 1,
                                                'sm' => 2,
                                                'xl' => 3,
                                            ]),
                                        TextInput::make('email')
                                            ->label(__('validation.attributes.email'))
                                            ->placeholder(__('messages.enter_email_address'))
                                            ->email()
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(
                                                ignoreRecord: true,
                                            )
                                            ->helperText(__('messages.unique_email_required'))
                                            ->columnSpan([
                                                'default' => 1,
                                                'sm' => 2,
                                                'xl' => 3,
                                            ]),
                                    ]),
                            ]),

                        Tabs\Tab::make(__('messages.security'))
                            ->icon(Heroicon::OutlinedLockClosed)
                            ->schema([
                                Section::make(__('messages.security_settings'))
                                    ->description(__('messages.manage_security_settings'))
                                    ->icon(Heroicon::OutlinedShieldCheck)
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('password')
                                            ->label(__('validation.attributes.password'))
                                            ->password()
                                            ->nullable()
                                            ->required(fn (string $operation) => $operation === 'create')
                                            ->placeholder(__('messages.enter_new_password'))
                                            ->helperText(__('messages.leave_blank_to_keep_current'))
                                            ->columnSpanFull(),
                                        Select::make('status')
                                            ->label(__('messages.account_status'))
                                            ->options([
                                                'active' => __('messages.active'),
                                                'inactive' => __('messages.inactive'),
                                            ])
                                            ->default('active')
                                            ->required()
                                            ->helperText(__('messages.control_user_access')),
                                        Select::make('role')
                                            ->label(__('messages.user_role'))
                                            ->options([
                                                'admin' => __('messages.administrator'),
                                                'editor' => __('messages.editor'),
                                                'user' => __('messages.regular_user'),
                                            ])
                                            ->default('user')
                                            ->required()
                                            ->helperText(__('messages.assign_user_permissions')),
                                    ]),
                            ]),

                        Tabs\Tab::make(__('messages.verification'))
                            ->icon(Heroicon::OutlinedEnvelope)
                            ->schema([
                                Section::make(__('messages.email_verification'))
                                    ->description(__('messages.manage_email_verification'))
                                    ->icon(Heroicon::OutlinedCheckCircle)
                                    ->schema([
                                        DateTimePicker::make('email_verified_at')
                                            ->label(__('messages.email_verified_at'))
                                            ->helperText(__('messages.auto_verify_on_first_login'))
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make(__('messages.two_factor'))
                            ->icon(Heroicon::OutlinedShieldExclamation)
                            ->schema([
                                Section::make(__('messages.two_factor_authentication'))
                                    ->description(__('messages.enhance_account_security'))
                                    ->icon(Heroicon::OutlinedKey)
                                    ->schema([
                                        Toggle::make('two_factor_confirmed_at')
                                            ->label(__('messages.two_factor_enabled'))
                                            ->helperText(__('messages.requires_backup_codes'))
                                            ->columnSpanFull(),
                                        Textarea::make('two_factor_secret')
                                            ->label(__('messages.two_factor_secret'))
                                            ->placeholder(__('messages.two_factor_secret_placeholder'))
                                            ->helperText(__('messages.store_securely'))
                                            ->columnSpanFull()
                                            ->rows(3),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
