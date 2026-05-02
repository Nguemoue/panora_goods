<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use function Pest\Laravel\get;
use function Pest\Laravel\withoutExceptionHandling;

test('home page is accessible', function () {
    withoutExceptionHandling();
    get('/')
        ->assertOk();
});

test('home page displays products', function () {
    $category = Category::factory()->create(['name' => 'Smartphones']);
    $brand = Brand::factory()->create(['name' => 'Apple']);
    $supplier = Supplier::factory()->create();
    
    Product::factory()->create([
        'name' => 'iPhone 15 Pro',
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'supplier_id' => $supplier->id,
        'status' => 'active',
        'selling_price' => 750000,
    ]);

    get('/')
        ->assertSee('iPhone 15 Pro')
        ->assertSee('Smartphones')
        ->assertSee('750 000');
});
