<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Sales\Pages;

use App\Enums\SaleStatusEnum;
use App\Filament\Admin\Resources\Sales\SaleResource;
use App\Models\Product;
use App\Models\Sale;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditSale extends EditRecord
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (Sale $record): bool => $record->canBeDeleted()),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Sale $record */
        $statusChangedToCancelled = false;

        $updatedSale = DB::transaction(function () use ($record, $data, &$statusChangedToCancelled): Sale {
            $sale = Sale::query()
                ->lockForUpdate()
                ->findOrFail($record->id);

            $rawNewStatus = $data['status'] ?? $sale->status;
            $newStatus = $rawNewStatus instanceof SaleStatusEnum
                ? $rawNewStatus
                : SaleStatusEnum::from((string) $rawNewStatus);

            $wasCancelled = $sale->status === SaleStatusEnum::CANCELLED;
            $willBeCancelled = $newStatus === SaleStatusEnum::CANCELLED;

            if ($wasCancelled !== $willBeCancelled) {
                $product = Product::query()
                    ->lockForUpdate()
                    ->find($sale->product_id);

                if ($product) {
                    if (! $wasCancelled && $willBeCancelled) {
                        $product->increment('stock_quantity', (int) $sale->quantity);
                        $statusChangedToCancelled = true;
                    } elseif ($wasCancelled && ! $willBeCancelled) {
                        if ($product->stock_quantity < (int) $sale->quantity) {
                            Notification::make()
                                ->title('Stock insuffisant')
                                ->body("Impossible de réactiver cette commande : seulement {$product->stock_quantity} unité(s) disponible(s) en stock.")
                                ->danger()
                                ->send();

                            throw new Halt;
                        }

                        $product->decrement('stock_quantity', (int) $sale->quantity);
                    }
                }
            }

            $sale->update(['status' => $newStatus]);

            return $sale->load('seller');
        });

        if ($statusChangedToCancelled && $updatedSale->seller) {
            Notification::make()
                ->title('Commande annulée')
                ->body("La commande {$updatedSale->tracking_code} a été annulée par l'administration et le stock ({$updatedSale->quantity}) a été restitué.")
                ->warning()
                ->sendToDatabase($updatedSale->seller);
        }

        return $updatedSale;
    }
}
