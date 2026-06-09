<?php

declare(strict_types=1);

namespace App\Filament\Seller\Resources\Sales\RelationManagers;

use App\Enums\ConfirmationStatusEnum;
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
use Illuminate\Support\Number;

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
                    ->belowContent(function ($set) {
                        if ($this->getOwnerRecord()->isOneTimePayment()) {
                            return __('sales.one_time_payment_requires_full_amount', [
                                'amount' => Number::currency((float) $this->getOwnerRecord()->sale_price),
                            ]);
                        }

                        return __('sales.remaining_payment_hint', [
                            'amount' => Number::currency($this->getOwnerRecord()->getRemainingAmount()),
                            'date' => $this->getOwnerRecord()->payment_date_limit?->format('d/m/Y') ?? '-',
                        ]);
                    })
                    ->maxValue(function () {
                        if ($this->getOwnerRecord()->isOneTimePayment()) {
                            return $this->getOwnerRecord()->sale_price;

                        }

                        return $this->getOwnerRecord()->getRemainingAmount();
                    })
                    ->minValue(function () {
                        if ($this->getOwnerRecord()->isOneTimePayment()) {
                            return $this->getOwnerRecord()->sale_price;
                        }

                        return 100;
                    })
                    ->required()
                    ->numeric(),
                Textarea::make('notes')->columnSpanFull()->disabled()->label(__('sales.additional_notes')),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id'),

                TextEntry::make('amount'),

                TextEntry::make('confirmation_status'),

                TextEntry::make('created_at')
                    ->label(__('messages.created_at'))
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label(__('messages.updated_at'))
                    ->dateTime(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultCurrency(currency: currency())
            ->columns([
                TextColumn::make('#')->rowIndex(),
                TextColumn::make('amount')->label(__('sales.paid_amount'))->money(),
                TextColumn::make('confirmation_status')->label(__('messages.status'))->badge(),
                TextColumn::make('created_at')->label(__('sales.date'))->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label(__('sales.add_payment'))
                    ->mutateDataUsing(function (array $data) {
                        return $data;
                    })
                    ->createAnother(false),
            ])
            ->recordActions([
                EditAction::make()->disabled(fn (SalePayment $record) => $record->confirmation_status === ConfirmationStatusEnum::APPROVED),
                // DeleteAction::make(),
            ])
            ->toolbarActions([

            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
