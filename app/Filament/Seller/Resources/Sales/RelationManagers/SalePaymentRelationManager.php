<?php
declare(strict_types=1);

namespace App\Filament\Seller\Resources\Sales\RelationManagers;

use App\Enums\ConfirmationStatusEnum;
use App\Models\Sale;
use App\Models\SalePayment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Number;

/**
 * @method Sale getOwnerRecord()
 */
class SalePaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'salePayments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->columnSpanFull()
                    ->belowContent(function ($set) {
                        if ($this->getOwnerRecord()->isOneTimePayment()) {
                            return 'This payment type is one time payment, you must paid the full amount: ' . Number::currency((float)$this->getOwnerRecord()->sale_price);
                        }
                        $remainingAmount = (float)$this->getOwnerRecord()->sale_price - (float)$this->getOwnerRecord()->paid_amount;
                        return 'The remaining amount to be paid is: ' . Number::currency($remainingAmount) . ' Payable before: ' . $this->getOwnerRecord()->payment_date_limit;
                    })
                    ->minValue(function () {
                        //if the payment type was one time there is no need to calculate the remaining amount
                        if ($this->getOwnerRecord()->isOneTimePayment()) {
                            return $this->getOwnerRecord()->sale_price;
                        }
                        return 100;
                    })
                    ->required()
                    ->numeric(),
                Textarea::make('notes')->columnSpanFull()->disabled()->label("Notes")
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id'),

                TextEntry::make('amount')
                ,

                TextEntry::make('confirmation_status'),

                TextEntry::make('created_at')
                    ->label('Created Date')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label('Last Modified Date')
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
                TextColumn::make('amount')->money(),
                TextColumn::make('confirmation_status')->badge(),
                TextColumn::make('created_at')->label('Date')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->label('Add Payment')
                    ->mutateDataUsing(function (array $data) {
                        return $data;
                    })
                    ->createAnother(false),
            ])
            ->recordActions([
                EditAction::make()->disabled(fn(SalePayment $record) => $record->confirmation_status === ConfirmationStatusEnum::APPROVED),
                //DeleteAction::make(),
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
