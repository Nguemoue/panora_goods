<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets\Admin;

use App\Filament\Admin\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class LowStockProducts extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public ?string $pollingInterval = null;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Alertes de stock critique')
            ->description('Produits actifs disposant de moins de 10 unités en stock.')
            ->query(
                Product::query()
                    ->with(['category', 'supplier'])
                    ->where('status', 'active')
                    ->where('stock_quantity', '<', 10)
                    ->orderBy('stock_quantity', 'asc')
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('category.name')
                    ->label(__('phones.category'))
                    ->placeholder('—'),
                TextColumn::make('stock_quantity')
                    ->label(__('panels.quantity_left'))
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 2 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('supplier.name')
                    ->label(__('messages.supplier'))
                    ->placeholder('—'),
            ])
            ->recordActions([
                Action::make('restock')
                    ->label('Réapprovisionner')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading(fn (Product $record): string => "Réapprovisionner : {$record->name}")
                    ->schema([
                        TextInput::make('quantity_to_add')
                            ->label('Quantité à ajouter')
                            ->integer()
                            ->minValue(1)
                            ->default(10)
                            ->required(),
                    ])
                    ->action(function (Product $record, array $data): void {
                        $added = (int) $data['quantity_to_add'];

                        DB::transaction(function () use ($record, $added): void {
                            $product = Product::query()->lockForUpdate()->findOrFail($record->id);
                            $product->increment('stock_quantity', $added);
                        });

                        Notification::make()
                            ->title('Stock mis à jour')
                            ->body("+{$added} unité(s) ajoutée(s) pour {$record->name}.")
                            ->success()
                            ->send();
                    }),
            ])
            ->recordUrl(
                fn (Product $record): string => ProductResource::getUrl('edit', ['record' => $record]),
            )
            ->emptyStateHeading('Aucun produit en stock critique')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
