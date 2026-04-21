<?php

namespace App\Filament\Resources\Phones\Tables;

use App\Models\Phone;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PhonesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Product Image & Name
                ImageColumn::make('images.path')
                    ->label(__('validation.attributes.image'))
                    ->disk('public')
                    ->limit(3)
                    ->circular()
                    ->stacked(),

                // Phone Name with Brand & Model
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable()
                    ->description(fn (Phone $record): string => "{$record->brand->name} | {$record->model_number}")
                    ->icon(Heroicon::OutlinedDevicePhoneMobile)
                    ->iconPosition('before'),

                // Brand Column
                TextColumn::make('brand.name')
                    ->label(__('validation.attributes.brand'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->icon(Heroicon::OutlinedBuildingStorefront)
                    ->iconPosition('before'),

                // Supplier Column
                TextColumn::make('supplier.name')
                    ->label(__('validation.attributes.supplier'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->icon(Heroicon::OutlinedTruck)
                    ->iconPosition('before'),

                // Supplier Price
                TextColumn::make('supplier_price')
                    ->label(__('validation.attributes.supplier_price'))
                    ->money('USD')
                    ->sortable()
                    ->alignment(Alignment::End)
                    ->toggleable(isToggledHiddenByDefault: true),

                // Selling Price
                TextColumn::make('selling_price')
                    ->label(__('validation.attributes.selling_price'))
                    ->money('USD')
                    ->sortable()
                    ->alignment(Alignment::End)
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->iconPosition('before'),

                // Profit Margin
                TextColumn::make('margin')
                    ->label(__('messages.margin'))
                    ->state(fn (Phone $record): float => (float) $record->selling_price - (float) $record->supplier_price)
                    ->money('USD')
                    ->color(fn (float $state): string => $state > 0 ? 'success' : 'danger')
                    ->iconPosition('before')
                    ->alignment(Alignment::End)
                    ->sortable()
                    ->toggleable(),

                // Stock Quantity with Color Alert
                TextColumn::make('stock_quantity')
                    ->label(__('validation.attributes.stock_quantity'))
                    ->numeric()
                    ->sortable()
                    ->icon(Heroicon::OutlinedCube)
                    ->iconPosition('before')
                    ->color(fn (int $state): string => match (true) {
                        $state < 5 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->weight('bold')
                    ->alignment(Alignment::Center)
                    ->badge(),

                // Status with Badge
                TextColumn::make('status')
                    ->label(__('validation.attributes.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_stock' => 'success',
                        'out_of_stock' => 'danger',
                        'discontinued' => 'gray',
                        default => 'primary',
                    })

                    ->formatStateUsing(fn (string $state): string => __("messages.{$state}"))
                    ->searchable()
                    ->sortable(),

                // RAM
                TextColumn::make('ram')
                    ->label(__('validation.attributes.ram'))
                    ->formatStateUsing(fn ($state): string => $state ? "{$state} GB" : '—')
                    ->alignment(Alignment::Center)
                    ->toggleable(isToggledHiddenByDefault: true),

                // Storage
                TextColumn::make('storage')
                    ->label(__('validation.attributes.storage'))
                    ->formatStateUsing(fn ($state): string => $state ? "{$state} GB" : '—')
                    ->alignment(Alignment::Center)
                    ->toggleable(isToggledHiddenByDefault: true),

                // Created Date
                TextColumn::make('created_at')
                    ->label(__('messages.created'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('brand')
                    ->label(__('validation.attributes.brand'))
                    ->relationship('brand', 'name')
                    ->placeholder(__('messages.all_brands'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('supplier')
                    ->label(__('validation.attributes.supplier'))
                    ->relationship('supplier', 'name')
                    ->placeholder(__('messages.all_suppliers'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label(__('validation.attributes.status'))
                    ->options([
                        'in_stock' => __('messages.in_stock'),
                        'out_of_stock' => __('messages.out_of_stock'),
                        'discontinued' => __('messages.discontinued'),
                    ])
                    ->placeholder(__('messages.all_statuses')),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ActionGroup::make([
                    Action::make('addStock')
                        ->label(__('actions.add_stock'))
                        ->icon(Heroicon::OutlinedPlusCircle)
                        ->color('success')
                        ->form([
                            TextInput::make('quantity_to_add')
                                ->label(__('validation.attributes.quantity'))
                                ->numeric()
                                ->required()
                                ->minValue(1),
                            TextInput::make('new_supplier_price')
                                ->label(__('validation.attributes.supplier_price'))
                                ->numeric()
                                ->prefix('$')
                                ->placeholder(fn (Phone $record): string => (string) $record->supplier_price),
                        ])
                        ->action(function (Phone $record, array $data): void {
                            $record->increment('stock_quantity', $data['quantity_to_add']);

                            if ($data['new_supplier_price']) {
                                $record->update(['supplier_price' => $data['new_supplier_price']]);
                            }

                            if ($record->stock_quantity > 0 && $record->status === 'out_of_stock') {
                                $record->update(['status' => 'in_stock']);
                            }
                        })
                        ->successNotification(null)
                        ->modalHeading(__('messages.add_stock'))
                        ->modalSubmitActionLabel(__('actions.save')),

                    EditAction::make()
                        ->label(__('actions.edit'))
                        ->icon(Heroicon::OutlinedPencil),

                    DeleteAction::make()
                        ->label(__('actions.delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->requiresConfirmation()
                        ->modalHeading(__('messages.delete_phone'))
                        ->modalDescription(__('messages.delete_phone_description'))
                        ->modalSubmitActionLabel(__('actions.delete'))
                        ->modalCancelActionLabel(__('actions.cancel')),
                ])
                    ->icon(Heroicon::EllipsisVertical)
                    ->label(__('messages.actions'))
                    ->tooltip(__('messages.actions')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('actions.bulk_delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->requiresConfirmation()
                        ->modalHeading(__('messages.bulk_delete_phones'))
                        ->modalDescription(__('messages.bulk_delete_confirmation'))
                        ->modalSubmitActionLabel(__('actions.delete'))
                        ->modalCancelActionLabel(__('actions.cancel')),
                ]),
            ])
            ->emptyStateHeading(__('messages.no_phones'))
            ->emptyStateDescription(__('messages.create_first_phone'));
    }
}
