<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Products\Tables;

use App\Enums\SaleStatusEnum;
use App\Filament\Admin\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['images', 'category', 'brand', 'supplier'])
                ->withSum(
                    ['sales as units_sold' => fn (Builder $q) => $q->where('status', '!=', SaleStatusEnum::CANCELLED)],
                    'quantity'
                ))
            ->columns([
                ImageColumn::make('primary_thumb_url')
                    ->label('')
                    ->state(fn (Product $record): string => $record->primary_thumb_url)
                    ->circular(),
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->description(fn (Product $record): ?string => collect([$record->product_code, $record->model_number])->filter()->implode(' · ') ?: null)
                    ->searchable(['name', 'product_code', 'model_number'])
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category.name')
                    ->label(__('phones.category'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('brand.name')
                    ->label(__('phones.brand'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('supplier_price')
                    ->label(__('phones.supplier_price'))
                    ->money()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('selling_price')
                    ->label(__('phones.selling_price'))
                    ->money()
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('unit_margin')
                    ->label('Marge unitaire')
                    ->state(fn (Product $record): float => (float) $record->selling_price - (float) $record->supplier_price)
                    ->description(function (Product $record): string {
                        $selling = (float) $record->selling_price;
                        $margin = $selling - (float) $record->supplier_price;

                        return $selling > 0 ? round(($margin / $selling) * 100, 1).' %' : '0 %';
                    })
                    ->money()
                    ->badge()
                    ->color(fn (float $state): string => $state > 0 ? 'success' : 'danger')
                    ->toggleable(),
                TextColumn::make('stock_quantity')
                    ->label(__('phones.stock_quantity'))
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),
                TextColumn::make('stock_value')
                    ->label('Valeur stock')
                    ->state(fn (Product $record): float => (float) $record->supplier_price * max(0, (int) $record->stock_quantity))
                    ->money()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('units_sold')
                    ->label('Vendus')
                    ->default(0)
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('messages.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultCurrency(currency())
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('messages.status'))
                    ->options([
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                    ]),
                SelectFilter::make('category_id')
                    ->label(__('phones.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('brand_id')
                    ->label(__('phones.brand'))
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('supplier_id')
                    ->label(__('messages.supplier'))
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('stock_level')
                    ->label('Niveau de stock')
                    ->options([
                        'out' => 'En rupture (0)',
                        'low' => 'Stock faible (1–5)',
                        'ok' => 'Stock confortable (> 5)',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'out' => $query->where('stock_quantity', '<=', 0),
                        'low' => $query->whereBetween('stock_quantity', [1, 5]),
                        'ok' => $query->where('stock_quantity', '>', 5),
                        default => $query,
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('adjust_stock')
                        ->label('Ajuster le stock')
                        ->icon('heroicon-o-archive-box-arrow-down')
                        ->color('warning')
                        ->schema([
                            Select::make('operation')
                                ->label('Type de mouvement')
                                ->options([
                                    'add' => 'Entrée de stock (+)',
                                    'subtract' => 'Sortie de stock (-)',
                                    'set' => 'Définir le stock exact',
                                ])
                                ->default('add')
                                ->required(),
                            TextInput::make('quantity')
                                ->label('Quantité')
                                ->integer()
                                ->minValue(0)
                                ->required(),
                        ])
                        ->action(function (Product $record, array $data): void {
                            $qty = (int) $data['quantity'];

                            $newStock = DB::transaction(function () use ($record, $data, $qty): int {
                                $product = Product::query()->lockForUpdate()->findOrFail($record->id);
                                $computed = match ($data['operation']) {
                                    'add' => (int) $product->stock_quantity + $qty,
                                    'subtract' => max(0, (int) $product->stock_quantity - $qty),
                                    default => $qty,
                                };

                                $product->update(['stock_quantity' => $computed]);

                                return $computed;
                            });

                            Notification::make()
                                ->title("Stock mis à jour : {$newStock} unité(s)")
                                ->success()
                                ->send();
                        }),
                    Action::make('toggle_status')
                        ->label(fn (Product $record): string => $record->status === 'active' ? 'Désactiver' : 'Activer')
                        ->icon(fn (Product $record): string => $record->status === 'active' ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn (Product $record): string => $record->status === 'active' ? 'gray' : 'success')
                        ->action(function (Product $record): void {
                            $newStatus = $record->status === 'active' ? 'inactive' : 'active';
                            $record->update(['status' => $newStatus]);

                            Notification::make()
                                ->title($newStatus === 'active' ? 'Produit activé' : 'Produit désactivé')
                                ->success()
                                ->send();
                        }),
                    Action::make('duplicate')
                        ->label('Dupliquer')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function (Product $record) {
                            $copy = $record->duplicateWithRelations();

                            Notification::make()
                                ->title('Produit dupliqué (inactif, stock à 0)')
                                ->success()
                                ->send();

                            return redirect()->to(ProductResource::getUrl('edit', ['record' => $copy]));
                        }),
                    Action::make('view_storefront')
                        ->label('Voir sur la boutique')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('gray')
                        ->url(fn (Product $record): string => route('products.show', $record))
                        ->openUrlInNewTab(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Activer la sélection')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $count = $records->count();
                            Product::query()->whereKey($records->modelKeys())->update(['status' => 'active']);

                            Notification::make()
                                ->title("{$count} produit(s) activé(s)")
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('deactivate')
                        ->label('Désactiver la sélection')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            $count = $records->count();
                            Product::query()->whereKey($records->modelKeys())->update(['status' => 'inactive']);

                            Notification::make()
                                ->title("{$count} produit(s) désactivé(s)")
                                ->warning()
                                ->send();
                        }),
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $deletedCount = 0;
                            $skippedCount = 0;

                            foreach ($records as $record) {
                                /** @var Product $record */
                                if ($record->canBeDeleted()) {
                                    $record->delete();
                                    $deletedCount++;
                                } else {
                                    $skippedCount++;
                                }
                            }

                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->title("{$deletedCount} produit(s) supprimé(s)")
                                    ->success()
                                    ->send();
                            }

                            if ($skippedCount > 0) {
                                Notification::make()
                                    ->title("{$skippedCount} produit(s) protégé(s)")
                                    ->body('Les produits ayant déjà des ventes ne peuvent pas être supprimés. Désactivez-les à la place.')
                                    ->warning()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
