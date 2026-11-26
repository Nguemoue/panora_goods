<?php

declare(strict_types=1);

use App\Livewire\ProductCatalog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;

use function Pest\Livewire\livewire;

it('filters products by search, category, brand and sort order', function () {
    $smartphones = Category::factory()->create(['name' => 'Smartphones']);
    $accessories = Category::factory()->create(['name' => 'Accessoires']);
    $apple = Brand::factory()->create(['name' => 'Apple']);
    $samsung = Brand::factory()->create(['name' => 'Samsung']);
    $supplier = Supplier::factory()->create();

    Product::factory()->create([
        'name' => 'iPhone 16 Pro Max',
        'category_id' => $smartphones->id,
        'brand_id' => $apple->id,
        'supplier_id' => $supplier->id,
        'status' => 'active',
        'selling_price' => 950000,
    ]);

    Product::factory()->create([
        'name' => 'Galaxy S24 Ultra',
        'category_id' => $smartphones->id,
        'brand_id' => $samsung->id,
        'supplier_id' => $supplier->id,
        'status' => 'active',
        'selling_price' => 650000,
    ]);

    Product::factory()->create([
        'name' => 'Chargeur MagSafe',
        'category_id' => $accessories->id,
        'brand_id' => $apple->id,
        'supplier_id' => $supplier->id,
        'status' => 'active',
        'selling_price' => 25000,
    ]);

    livewire(ProductCatalog::class)
        ->assertSee('iPhone 16 Pro Max')
        ->assertSee('Galaxy S24 Ultra')
        ->assertSee('Chargeur MagSafe')
        ->set('search', 'Galaxy')
        ->assertSee('Galaxy S24 Ultra')
        ->assertDontSee('iPhone 16 Pro Max')
        ->call('resetFilters')
        ->call('selectCategory', $accessories->id)
        ->assertSee('Chargeur MagSafe')
        ->assertDontSee('Galaxy S24 Ultra')
        ->call('resetFilters')
        ->set('brand', $apple->id)
        ->assertSee('iPhone 16 Pro Max')
        ->assertDontSee('Galaxy S24 Ultra')
        ->set('sort', 'price_asc')
        ->assertSeeInOrder(['Chargeur MagSafe', 'iPhone 16 Pro Max']);
});

it('paginates active products with flux pagination', function () {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();
    $supplier = Supplier::factory()->create();

    Product::factory()->count(15)->create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'supplier_id' => $supplier->id,
        'status' => 'active',
    ]);

    livewire(ProductCatalog::class)
        ->assertSeeHtml('data-flux-pagination');
});
