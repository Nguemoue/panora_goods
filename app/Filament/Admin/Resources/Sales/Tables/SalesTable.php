<?php

namespace App\Filament\Admin\Resources\Sales\Tables;

use App\Models\Sale;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_code')->label("Code"),
                TextColumn::make('product.name')->searchable(),
                TextColumn::make('quantity')->prefix('x')->numeric(),
                TextColumn::make('supplier_price_at_sale')->label('Supplier Price')->money(),
                TextColumn::make('sale_price')->label('Sale Price')->money(),
                TextColumn::make('paid_amount')->default(0)->money()->badge(),
                TextColumn::make('remaining_amount')->money()
                    ->state(fn(Sale $record)=>$record->sale_price - (int)($record->paid_amount))
                    ->badge()
                    ->color(fn($state)=>$state > 0 ? 'danger' : 'success')
                ,
                TextColumn::make('sold_at')->label("Vendu le")->dateTime("d/m/Y H:i"),
            ])
            ->defaultCurrency(currency())
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
