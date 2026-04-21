<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable()
                    ->icon(Heroicon::OutlinedUser)
                    ->iconPosition('before')
                    ->toggleable(),

                TextColumn::make('email')
                    ->label(__('validation.attributes.email'))
                    ->searchable()
                    ->sortable()
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->iconPosition('before')
                    ->copyable()
                    ->copyableState(fn (string $state): string => $state),

                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->icons([
                        'success' => Heroicon::OutlinedCheckCircle,
                        'danger' => Heroicon::OutlinedXCircle,
                    ])
                    ->formatStateUsing(fn (string $state): string => __("messages.{$state}"))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('role')
                    ->label(__('messages.user_role'))
                    ->badge()
                    ->colors([
                        'danger' => 'admin',
                        'warning' => 'editor',
                        'info' => 'user',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => __('messages.administrator'),
                        'editor' => __('messages.editor'),
                        'user' => __('messages.regular_user'),
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('email_verified_at')
                    ->label(__('messages.email_verified'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? __('messages.verified') : __('messages.unverified'))
                    ->colors([
                        'success' => fn ($state) => $state !== null,
                        'danger' => fn ($state) => $state === null,
                    ])
                    ->icons([
                        'success' => Heroicon::OutlinedCheckCircle,
                        'danger' => Heroicon::OutlinedXCircle,
                    ])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('two_factor_confirmed_at')
                    ->label(__('messages.two_factor'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? __('messages.enabled') : __('messages.disabled'))
                    ->colors([
                        'success' => fn ($state) => $state !== null,
                        'warning' => fn ($state) => $state === null,
                    ])
                    ->icons([
                        'success' => Heroicon::OutlinedShieldCheck,
                        'warning' => Heroicon::OutlinedExclamationTriangle,
                    ])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('messages.joined'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label(__('messages.last_updated'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('messages.status'))
                    ->options([
                        'active' => __('messages.active'),
                        'inactive' => __('messages.inactive'),
                    ])
                    ->placeholder(__('messages.all_statuses')),

                SelectFilter::make('role')
                    ->label(__('messages.user_role'))
                    ->options([
                        'admin' => __('messages.administrator'),
                        'editor' => __('messages.editor'),
                        'user' => __('messages.regular_user'),
                    ])
                    ->placeholder(__('messages.all_roles')),

                SelectFilter::make('email_verified_at')
                    ->label(__('messages.email_verification'))
                    ->options([
                        'verified' => __('messages.verified'),
                        'unverified' => __('messages.unverified'),
                    ])
                    ->query(function ($query, $value) {
                        return match ($value) {
                            'verified' => $query->whereNotNull('email_verified_at'),
                            'unverified' => $query->whereNull('email_verified_at'),
                            default => $query,
                        };
                    })
                    ->placeholder(__('messages.all_verifications')),

                SelectFilter::make('two_factor_confirmed_at')
                    ->label(__('messages.two_factor'))
                    ->options([
                        'enabled' => __('messages.enabled'),
                        'disabled' => __('messages.disabled'),
                    ])
                    ->query(function ($query, $value) {
                        return match ($value) {
                            'enabled' => $query->whereNotNull('two_factor_confirmed_at'),
                            'disabled' => $query->whereNull('two_factor_confirmed_at'),
                            default => $query,
                        };
                    })
                    ->placeholder(__('messages.all_settings')),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->icon(Heroicon::OutlinedPencil),
                    DeleteAction::make()
                        ->icon(Heroicon::OutlinedTrash)
                        ->requiresConfirmation()
                        ->modalHeading(__('messages.delete_user_confirmation_title'))
                        ->modalDescription(__('messages.delete_user_confirmation_description'))
                        ->modalSubmitActionLabel(__('messages.delete'))
                        ->modalCancelActionLabel(__('messages.cancel')),
                ])
                    ->icon(Heroicon::EllipsisVertical)
                    ->label(__('messages.actions'))
                    ->tooltip(__('messages.actions')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon(Heroicon::OutlinedTrash)
                        ->requiresConfirmation()
                        ->modalHeading(__('messages.bulk_delete_users_title'))
                        ->modalDescription(__('messages.bulk_delete_users_description'))
                        ->modalSubmitActionLabel(__('messages.delete'))
                        ->modalCancelActionLabel(__('messages.cancel')),
                ]),
            ])
            ->emptyStateHeading(__('messages.no_users'))
            ->emptyStateDescription(__('messages.create_first_user'))
            ->emptyStateIcon(Heroicon::OutlinedUsers);
    }
}
