<?php

namespace App\Observers;

use App\Models\Sale;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        $sale->phone->decrement('stock_quantity', $sale->quantity);
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        if ($sale->wasChanged('quantity')) {
            $oldQty = (int) $sale->getOriginal('quantity');
            $newQty = (int) $sale->quantity;
            $diff = $newQty - $oldQty;

            if ($diff > 0) {
                $sale->phone->decrement('stock_quantity', $diff);
            } else {
                $sale->phone->increment('stock_quantity', abs($diff));
            }
        }

        if ($sale->wasChanged('phone_id')) {
            $oldPhoneId = $sale->getOriginal('phone_id');
            $oldQty = $sale->getOriginal('quantity');
            
            \App\Models\Phone::find($oldPhoneId)?->increment('stock_quantity', $oldQty);
            $sale->phone->decrement('stock_quantity', $sale->quantity);
        }
    }

    /**
     * Handle the Sale "deleted" event.
     */
    public function deleted(Sale $sale): void
    {
        $sale->phone->increment('stock_quantity', $sale->quantity);
    }
}
