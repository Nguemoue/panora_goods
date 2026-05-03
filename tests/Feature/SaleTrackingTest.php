<?php

use App\Enums\SaleStatusEnum;
use App\Enums\UserRoleEnum;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('generates a tracking code when a sale is created', function () {
    $seller = User::factory()->create(['role' => UserRoleEnum::SELLER]);
    $product = Product::factory()->create();

    $sale = Sale::create([
        'product_id' => $product->id,
        'seller_id' => $seller->id,
        'quantity' => 1,
        'sale_price' => 100,
        'supplier_price_at_sale' => 80,
        'profit' => 20,
        'customer_name' => 'John Doe',
        'sold_at' => now(),
        'status' => SaleStatusEnum::PENDING,
    ]);

    expect($sale->tracking_code)->not->toBeNull()
        ->and(strlen($sale->tracking_code))->toBe(10);
});

it('can find a sale by tracking code', function () {
    $seller = User::factory()->create(['role' => UserRoleEnum::SELLER]);
    $product = Product::factory()->create();

    $sale = Sale::create([
        'product_id' => $product->id,
        'seller_id' => $seller->id,
        'quantity' => 1,
        'sale_price' => 100,
        'supplier_price_at_sale' => 80,
        'profit' => 20,
        'customer_name' => 'John Doe',
        'sold_at' => now(),
        'status' => SaleStatusEnum::PENDING,
    ]);

    $foundSale = Sale::where('tracking_code', $sale->tracking_code)->first();

    expect($foundSale->id)->toBe($sale->id);
});
