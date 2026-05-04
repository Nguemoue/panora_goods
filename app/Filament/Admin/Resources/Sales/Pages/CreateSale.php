<?php
declare(strict_types=1);

namespace App\Filament\Admin\Resources\Sales\Pages;

use App\Filament\Admin\Resources\Sales\SaleResource;
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
        return $data;
    }
}
