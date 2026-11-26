<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Clients\Tables;

use App\Enums\PaymentStatusEnum;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->withCount('sales')
                ->withCount(['sales as unpaid_sales_count' => fn (Builder $q) => $q->whereNot('payment_status', PaymentStatusEnum::PAID)])
                ->withSum('sales', 'sale_price'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('phone_number')
                    ->label(__('panels.phone_number'))
                    ->copyable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('validation.attributes.email'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sales_count')
                    ->label('Commandes')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('sales_sum_sale_price')
                    ->label('Total acheté')
                    ->default(0)
                    ->money(currency())
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('unpaid_sales_count')
                    ->label('Impayés')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('with_unpaid')
                    ->label('Clients avec impayés')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereHas('sales', fn (Builder $q) => $q->whereNot('payment_status', PaymentStatusEnum::PAID))),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('whatsapp')
                        ->label('Contacter WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->visible(fn (Client $record): bool => filled($record->phone_number))
                        ->url(fn (Client $record): string => 'https://wa.me/'.preg_replace('/[^\d]/', '', (string) $record->phone_number))
                        ->openUrlInNewTab(),
                    EditAction::make(),
                    DeleteAction::make()->disabled(fn (Client $record): bool => $record->sales_count > 0),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
