<?php

namespace App\Filament\Admin\Resources\Sales\RelationManagers;

use App\Filament\Admin\Resources\Sales\Actions\ApproveSalePaymentAction;
use App\Filament\Admin\Resources\Sales\Actions\RejectSalePaymentAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SalePaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'salePayments';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id'),
                TextEntry::make('amount'),
                TextEntry::make('confirmation_status'),
                TextEntry::make('created_at')->label('Created Date')->dateTime(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultCurrency(currency())
            ->columns([
                TextColumn::make('amount')->money(),
                TextColumn::make('confirmation_status'),
                TextColumn::make('created_at')->label('Emited at')->dateTime(),
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
                    RejectSalePaymentAction::make()
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
