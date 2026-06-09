<?php

namespace App\Filament\Admin\Resources\Sales\RelationManagers;

use App\Filament\Admin\Resources\Sales\Actions\ApproveSalePaymentAction;
use App\Filament\Admin\Resources\Sales\Actions\RejectSalePaymentAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SalePaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'salePayments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resources.relation_managers.sale_payments');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id'),
                TextEntry::make('amount')->label(__('sales.paid_amount')),
                TextEntry::make('confirmation_status')->label(__('messages.status')),
                TextEntry::make('created_at')->label(__('panels.created_date'))->dateTime(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultCurrency(currency())
            ->columns([
                TextColumn::make('amount')->label(__('sales.paid_amount'))->money(),
                TextColumn::make('confirmation_status')->label(__('messages.status')),
                TextColumn::make('created_at')->label(__('panels.emitted_at'))->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ApproveSalePaymentAction::make(),
                    RejectSalePaymentAction::make(),
                ])

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
