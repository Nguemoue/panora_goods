<?php

declare(strict_types=1);

namespace App\Filament\Seller\Resources\Clients;

use App\Enums\PaymentStatusEnum;
use App\Filament\Seller\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Transactions';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Mes Clients';

    protected static ?string $modelLabel = 'Client';

    protected static ?string $pluralModelLabel = 'Mes Clients';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $sellerId = Filament::auth()->id();

        return parent::getEloquentQuery()
            ->whereHas('sales', fn (Builder $q) => $q->where('seller_id', $sellerId))
            ->withCount(['sales as my_sales_count' => fn (Builder $q) => $q->where('seller_id', $sellerId)])
            ->withCount([
                'sales as my_unpaid_count' => fn (Builder $q) => $q
                    ->where('seller_id', $sellerId)
                    ->whereNot('payment_status', PaymentStatusEnum::PAID),
            ])
            ->withSum(['sales as my_total_spent' => fn (Builder $q) => $q->where('seller_id', $sellerId)], 'sale_price');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('phone_number')
                    ->label(__('panels.phone_number'))
                    ->copyable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('validation.attributes.email'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('my_sales_count')
                    ->label('Commandes')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('my_total_spent')
                    ->label('Total acheté')
                    ->default(0)
                    ->money(currency())
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('my_unpaid_count')
                    ->label('Impayés')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'success')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('with_unpaid')
                    ->label('Avec impayés')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereHas('sales', fn (Builder $q) => $q->where('seller_id', Filament::auth()->id())->whereNot('payment_status', PaymentStatusEnum::PAID))),
            ])
            ->recordActions([
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->visible(fn (Client $record): bool => filled($record->phone_number))
                    ->url(fn (Client $record): string => 'https://wa.me/'.preg_replace('/[^\d]/', '', (string) $record->phone_number))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
        ];
    }
}
