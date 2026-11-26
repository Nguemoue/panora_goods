<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Sales\RelationManagers;

use App\Filament\Admin\Resources\Sales\Actions\ApproveSalePaymentAction;
use App\Filament\Admin\Resources\Sales\Actions\RejectSalePaymentAction;
use Filament\Actions\ActionGroup;
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
                TextEntry::make('amount')->label(__('sales.paid_amount'))->money(currency()),
                TextEntry::make('confirmation_status')->label(__('messages.status'))->badge(),
                TextEntry::make('notes')->label('Réf. / Note')->placeholder('—'),
                TextEntry::make('created_at')->label(__('panels.created_date'))->dateTime('d/m/Y H:i'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultCurrency(currency())
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('amount')
                    ->label(__('sales.paid_amount'))
                    ->money()
                    ->weight('bold'),
                TextColumn::make('confirmation_status')
                    ->label(__('messages.status'))
                    ->badge(),
                TextColumn::make('notes')
                    ->label('Réf. / Note')
                    ->placeholder('—')
                    ->wrap()
                    ->limit(60),
                TextColumn::make('created_at')
                    ->label(__('panels.emitted_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
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
                ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
