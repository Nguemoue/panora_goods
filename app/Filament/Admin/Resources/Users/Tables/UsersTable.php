<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Enums\UserRoleEnum;
use App\Models\User;
use App\Models\Zone;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with('zones')
                ->withCount('sales')
                ->withSum('sales', 'sale_price'))
            ->columns([
                ImageColumn::make('photo')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn (User $record): string => $record->avatar_url),
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->description(fn (User $record): string => $record->email)
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('reference_number')
                    ->label('Référence')
                    ->badge()
                    ->copyable()
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('role')
                    ->label(__('messages.role'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('zones.name')
                    ->label('Zones')
                    ->badge()
                    ->separator(', ')
                    ->placeholder('—'),
                TextColumn::make('sales_count')
                    ->label('Ventes')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('sales_sum_sale_price')
                    ->label('CA généré')
                    ->default(0)
                    ->money(currency())
                    ->weight('bold')
                    ->color('success')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label(__('messages.role'))
                    ->options(UserRoleEnum::class),
                SelectFilter::make('status')
                    ->label(__('messages.status'))
                    ->options([
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                    ]),
                SelectFilter::make('zone')
                    ->label('Zone')
                    ->searchable()
                    ->options(fn (): array => Zone::query()->orderBy('city')->orderBy('name')->get()->mapWithKeys(fn (Zone $z): array => [$z->id => "{$z->city} — {$z->name}"])->all())
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value']) ? $query->whereHas('zones', fn (Builder $z) => $z->where('zones.id', $data['value'])) : $query),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $deletedCount = 0;
                            $skippedCount = 0;
                            $currentAdminId = auth()->id();

                            foreach ($records as $record) {
                                /** @var User $record */
                                if ($record->id === $currentAdminId) {
                                    $skippedCount++;

                                    continue;
                                }

                                if ($record->role === UserRoleEnum::ADMIN && $record->status === 'active') {
                                    $remainingActiveAdmins = User::query()
                                        ->where('role', UserRoleEnum::ADMIN)
                                        ->where('status', 'active')
                                        ->whereKeyNot($record->id)
                                        ->count();

                                    if ($remainingActiveAdmins === 0) {
                                        $skippedCount++;

                                        continue;
                                    }
                                }

                                $record->delete();
                                $deletedCount++;
                            }

                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->title("{$deletedCount} utilisateur(s) supprimé(s)")
                                    ->success()
                                    ->send();
                            }

                            if ($skippedCount > 0) {
                                Notification::make()
                                    ->title("{$skippedCount} compte(s) protégé(s)")
                                    ->body('Vous ne pouvez pas supprimer votre propre compte ni le dernier administrateur actif.')
                                    ->warning()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
