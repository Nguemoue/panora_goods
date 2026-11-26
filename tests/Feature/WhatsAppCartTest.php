<?php

declare(strict_types=1);

use App\Enums\UserRoleEnum;
use App\Livewire\WhatsAppCart;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Zone;

use function Pest\Livewire\livewire;

it('adds products, updates quantities and builds a consolidated whatsapp link', function () {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();
    $supplier = Supplier::factory()->create();

    $phone = Product::factory()->create([
        'name' => 'iPhone 15',
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'supplier_id' => $supplier->id,
        'status' => 'active',
        'selling_price' => 500000,
    ]);

    $component = livewire(WhatsAppCart::class)
        ->call('addProduct', $phone->id)
        ->assertSet('items', [$phone->id => 1])
        ->call('increment', $phone->id)
        ->assertSet('items', [$phone->id => 2]);

    expect($component->instance()->totalCount)->toBe(2)
        ->and($component->instance()->totalAmount)->toBe(1000000.0)
        ->and($component->instance()->whatsappUrl)->toContain('https://wa.me/')
        ->and(urldecode($component->instance()->whatsappUrl))->toContain('iPhone 15')
        ->and(urldecode($component->instance()->whatsappUrl))->toContain('1 000 000 FCFA');

    $component->call('decrement', $phone->id)
        ->assertSet('items', [$phone->id => 1])
        ->call('remove', $phone->id)
        ->assertSet('items', []);
});

it('routes whatsapp orders to the active seller assigned to the selected zone', function () {
    $zone = Zone::query()->create([
        'name' => 'Akwa',
        'city' => 'Douala',
    ]);

    $seller = User::factory()->create([
        'name' => 'Paul Commercial',
        'role' => UserRoleEnum::SELLER,
        'status' => 'active',
        'phone_number' => '+237 677 11 22 33',
    ]);
    $zone->users()->attach($seller->id);

    $product = Product::factory()->create([
        'name' => 'Samsung A55',
        'status' => 'active',
        'selling_price' => 250000,
    ]);

    $component = livewire(WhatsAppCart::class)
        ->call('addProduct', $product->id)
        ->set('selectedZoneId', $zone->id);

    expect($component->instance()->resolvedContact['seller_name'])->toBe('Paul Commercial')
        ->and($component->instance()->whatsappUrl)->toStartWith('https://wa.me/237677112233?text=')
        ->and(urldecode($component->instance()->whatsappUrl))->toContain('Douala — Akwa');
});
