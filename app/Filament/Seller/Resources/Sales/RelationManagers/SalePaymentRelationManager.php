<?php

declare(strict_types=1);

namespace App\Filament\Seller\Resources\Sales\RelationManagers;

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\SaleStatusEnum;
use App\Models\Sale;
use App\Models\SalePayment;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Sale getOwnerRecord()
 */
class SalePaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'salePayments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('resources.relation_managers.sale_payments');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->label(__('sales.paid_amount'))
                    ->columnSpanFull()
                    ->belowContent(function (?SalePayment $record): string {
                        $sale = $this->getOwnerRecord();
                        $available = $this->getAvailableCeiling($record);

                        if ($sale->isOneTimePayment()) {
                            return __('sales.one_time_payment_requires_full_amount', [
                                'amount' => format_xaf($sale->sale_price),
                            ]);
                        }

                        return __('sales.remaining_payment_hint', [
                            'amount' => format_xaf($available),
                            'date' => $sale->payment_date_limit?->format('d/m/Y') ?? '-',
                        ]);
                    })
                    ->maxValue(function (?SalePayment $record): float {
                        $sale = $this->getOwnerRecord();

                        if ($sale->isOneTimePayment()) {
                            return (float) $sale->sale_price;
                        }

                        return $this->getAvailableCeiling($record);
                    })
                    ->minValue(function (): float {
                        $sale = $this->getOwnerRecord();

                        if ($sale->isOneTimePayment()) {
                            return (float) $sale->sale_price;
                        }

                        return 100;
                    })
                    ->required()
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull()
                    ->label(__('sales.additional_notes'))
                    ->placeholder('Ex : Référence Orange Money / MTN MoMo')
                    ->maxLength(500),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id'),
                TextEntry::make('amount')->money(currency()),
                TextEntry::make('confirmation_status')->badge(),
                TextEntry::make('notes')->placeholder('—'),
                TextEntry::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime('d/m/Y H:i'),
                TextEntry::make('updated_at')
                    ->label(__('messages.updated_at'))
                    ->dateTime('d/m/Y H:i'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultCurrency(currency: currency())
            ->columns([
                TextColumn::make('#')->rowIndex(),
                TextColumn::make('amount')->label(__('sales.paid_amount'))->money()->weight('bold'),
                TextColumn::make('confirmation_status')->label(__('messages.status'))->badge(),
                TextColumn::make('notes')->label('Note / Réf.')->placeholder('—')->limit(40),
                TextColumn::make('created_at')->label(__('sales.date'))->dateTime('d/m/Y H:i'),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make()
                    ->label(__('sales.add_payment'))
                    ->visible(fn (): bool => $this->canAddPayment())
                    ->mutateDataUsing(function (array $data): array {
                        $data['confirmation_status'] = ConfirmationStatusEnum::PENDING;

                        return $data;
                    })
                    ->createAnother(false),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (SalePayment $record): bool => $record->confirmation_status === ConfirmationStatusEnum::PENDING)
                    ->mutateDataUsing(function (array $data): array {
                        $data['confirmation_status'] = ConfirmationStatusEnum::PENDING;

                        return $data;
                    }),
            ])
            ->toolbarActions([]);
    }

    protected function canAddPayment(): bool
    {
        $sale = $this->getOwnerRecord();

        if ($sale->payment_status === PaymentStatusEnum::PAID || $sale->status === SaleStatusEnum::CANCELLED) {
            return false;
        }

        return $sale->getAvailablePaymentAmount() > 0;
    }

    protected function getAvailableCeiling(?SalePayment $editingRecord = null): float
    {
        $available = $this->getOwnerRecord()->getAvailablePaymentAmount();

        if ($editingRecord && $editingRecord->confirmation_status === ConfirmationStatusEnum::PENDING) {
            return $available + (float) $editingRecord->amount;
        }

        return $available;
    }
}
