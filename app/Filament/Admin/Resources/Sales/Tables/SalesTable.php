<?php

namespace App\Filament\Admin\Resources\Sales\Tables;

use App\Models\Sale;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_code')->label(__('sales.tracking_code')),
                TextColumn::make('product.name')->label(__('sales.product'))->searchable(),
                TextColumn::make('quantity')->label(__('sales.quantity'))->prefix('x')->numeric(),
                TextColumn::make('supplier_price_at_sale')->label(__('sales.supplier_price'))->money(),
                TextColumn::make('sale_price')->label(__('sales.total_price'))->money(),
                TextColumn::make('paid_amount')->label(__('sales.paid_amount'))->default(0)->money()->badge(),
                TextColumn::make('remaining_amount')->label(__('sales.remaining_amount'))->money()
                    ->state(fn (Sale $record): float => $record->getRemainingAmount())
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                TextColumn::make('payment_date_limit')
                    ->label(__('sales.payment_deadline'))
                    ->date()
                    ->toggleable(),
                TextColumn::make('sold_at')->label(__('sales.sold_at'))->dateTime('d/m/Y H:i'),
            ])
            ->defaultCurrency(currency())
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('overdue_payments')
                    ->label(__('sales.overdue_payments'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->overduePayment()),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
