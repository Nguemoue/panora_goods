<?php

use App\Enums\UserRoleEnum;
use App\Livewire\TrackOrder;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the tracking page', function () {
    $this->get(route('track.order'))
        ->assertStatus(200)
        ->assertSee('Track Your Order');
});

it('can track an order with a valid code', function () {
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
    ]);

    Livewire::test(TrackOrder::class)
        ->set('tracking_code', $sale->tracking_code)
        ->call('track')
        ->assertSee($sale->tracking_code)
        ->assertSee('John Doe')
        ->assertSee($product->name);
});

it('shows an error message for invalid tracking code', function () {
    Livewire::test(TrackOrder::class)
        ->set('tracking_code', 'INVALIDCODE')
        ->call('track')
        ->assertSee('No order found');
});
