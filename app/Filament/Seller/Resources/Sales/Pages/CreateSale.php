<?php

namespace App\Filament\Seller\Resources\Sales\Pages;

use App\Filament\Seller\Resources\Sales\SaleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;


    // auto set the profit field before creating the record
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $product = \App\Models\Product::find($data['product_id']);
        if (!$product) {
            return $data;
        }
        $data['profit'] = ($product->profit_price * $data['quantity']);
        $data['sale_price'] = ($product->selling_price * $data['quantity']);
        $data['sold_at'] = now();
        return $data;
    }
}
