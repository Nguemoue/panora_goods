<?php

declare(strict_types=1);

namespace App\Filament\Seller\Resources\Sales\Pages;

use App\Enums\PaymentStatusEnum;
use App\Enums\SaleStatusEnum;
use App\Filament\Seller\Resources\Sales\SaleResource;
use App\Models\Sale;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $sellerId = Filament::auth()->id();

        $overdueCount = Sale::query()
            ->where('seller_id', $sellerId)
            ->overduePayment()
            ->count();

        $pendingPaymentCount = Sale::query()
            ->where('seller_id', $sellerId)
            ->paymentPending()
            ->count();

        return [
            'all' => Tab::make('Toutes mes ventes'),
            'overdue' => Tab::make('En retard')
                ->badge($overdueCount > 0 ? $overdueCount : null)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->overduePayment()),
            'pending_payment' => Tab::make('En cours de paiement')
                ->badge($pendingPaymentCount > 0 ? $pendingPaymentCount : null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->paymentPending()),
            'not_delivered' => Tab::make('À livrer')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->notDelivered()),
            'completed' => Tab::make('Terminées')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', SaleStatusEnum::DELIVERED)->where('payment_status', PaymentStatusEnum::PAID)),
        ];
    }
}
