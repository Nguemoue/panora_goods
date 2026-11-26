<?php

declare(strict_types=1);

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Specification;
use App\Models\Supplier;
use App\Services\CatalogCacheService;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\get;

it('renders the product detail page with all images and full specifications', function () {
    $category = Category::factory()->create(['name' => 'Smartphones']);
    $brand = Brand::factory()->create(['name' => 'Apple']);
    $supplier = Supplier::factory()->create();
    $storage = Specification::factory()->create(['name' => 'Stockage', 'measure' => 'GB']);

    $product = Product::factory()->create([
        'name' => 'iPhone 16 Pro',
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'supplier_id' => $supplier->id,
        'status' => 'active',
        'selling_price' => 850000,
        'stock_quantity' => 5,
    ]);

    $product->specifications()->attach($storage->id, ['value' => '256']);

    ProductImage::query()->create([
        'product_id' => $product->id,
        'path' => 'products/front.jpg',
        'is_primary' => true,
    ]);
    ProductImage::query()->create([
        'product_id' => $product->id,
        'path' => 'products/back.jpg',
        'is_primary' => false,
    ]);
    ProductImage::query()->create([
        'product_id' => $product->id,
        'path' => 'products/side.jpg',
        'is_primary' => false,
    ]);

    get(route('products.show', $product))
        ->assertOk()
        ->assertSee('iPhone 16 Pro')
        ->assertSee('850 000 FCFA')
        ->assertSee('Stockage')
        ->assertSee('256')
        ->assertSee('Toutes les photos (3)');
});

it('caches catalog data and invalidates automatically when models change', function () {
    $cacheService = app(CatalogCacheService::class);
    $cacheService->flushCatalog();

    $category = Category::factory()->create(['name' => 'Tablettes']);

    $cached = $cacheService->getNavCategories();
    expect($cached->pluck('name'))->toContain('Tablettes')
        ->and(Cache::has(CatalogCacheService::KEY_NAV_CATEGORIES))->toBeTrue();

    // Creating a new category must automatically invalidate the cache
    Category::factory()->create(['name' => 'Montres']);

    expect(Cache::has(CatalogCacheService::KEY_NAV_CATEGORIES))->toBeFalse();

    $refreshed = $cacheService->getNavCategories();
    expect($refreshed->pluck('name'))->toContain('Montres');
});

it('works cleanly with the file cache driver under laravel 13 safe unserialization', function () {
    config()->set('cache.default', 'file');
    Cache::forgetDriver('file');

    $cacheService = app(CatalogCacheService::class);
    $cacheService->flushCatalog();

    $category = Category::factory()->create(['name' => 'Audio']);
    $brand = Brand::factory()->create(['name' => 'Sony']);
    Product::factory()->create([
        'name' => 'WH-1000XM5',
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'status' => 'active',
    ]);

    // First call populates file cache; second call reads back from file cache
    $cacheService->getCategoriesWithActiveCount();
    $fromCache = $cacheService->getCategoriesWithActiveCount();

    expect($fromCache)->toHaveCount(1)
        ->and($fromCache->first()->name)->toBe('Audio')
        ->and((int) $fromCache->first()->products_count)->toBe(1);

    $cacheService->getFeaturedProducts(3);
    $featuredFromCache = $cacheService->getFeaturedProducts(3);
    expect($featuredFromCache->first()->name)->toBe('WH-1000XM5');

    $cacheService->flushCatalog();
});
