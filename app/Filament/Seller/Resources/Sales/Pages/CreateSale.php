<?php

namespace App\Filament\Seller\Resources\Sales\Pages;

use App\Filament\Seller\Resources\Sales\SaleResource;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function afterFill(): void
    {
        $productId = request()->integer('product');

        if (! $productId) {
            return;
        }

        $product = Product::query()
            ->where('status', 'active')
            ->where('stock_quantity', '>', 0)
            ->find($productId);

        if (! $product) {
            return;
        }

        $this->form->fill([
            'product_id' => $product->id,
            'quantity' => 1,
            'sale_price' => $product->selling_price,
            'supplier_price_at_sale' => $product->supplier_price,
            'total_price' => $product->selling_price,
            'profit' => $product->profit_price,
        ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $product = Product::query()
                ->lockForUpdate()
                ->find($data['product_id']);

            $quantity = (int) ($data['quantity'] ?? 0);

            if (! $product || $quantity < 1 || $product->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'data.quantity' => __('sales.insufficient_stock'),
                ]);
            }

            $data['supplier_price_at_sale'] = $product->supplier_price;
            $data['sale_price'] = $product->selling_price * $quantity;
            $data['profit'] = $product->profit_price * $quantity;
            $data['sold_at'] = now();

            $record = parent::handleRecordCreation($data);

            $product->decrement('stock_quantity', $quantity);

            return $record;
        });
    }
}
