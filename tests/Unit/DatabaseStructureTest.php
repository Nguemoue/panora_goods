<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Specification;
use App\Models\Supplier;
use App\Models\Brand;
use App\Models\User;
use App\Models\Sale;
use App\Enums\UserRoleEnum;

uses(Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

test('a category can have specifications', function () {
    $category = Category::factory()->create();
    $specification = Specification::factory()->create();

    $category->specifications()->attach($specification, ['is_required' => true]);

    expect($category->specifications)->toHaveCount(1)
        ->and($category->specifications->first()->name)->toBe($specification->name)
        ->and($category->specifications->first()->pivot->is_required)->toBe(1);
});

test('a product belongs to a category, brand and supplier', function () {
    $product = Product::factory()->create();

    expect($product->category)->toBeInstanceOf(Category::class)
        ->and($product->brand)->toBeInstanceOf(Brand::class)
        ->and($product->supplier)->toBeInstanceOf(Supplier::class);
});

test('a product can have dynamic specifications', function () {
    $product = Product::factory()->create();
    $specification = Specification::factory()->create();

    $product->specifications()->attach($specification, ['value' => '8GB']);

    expect($product->specifications)->toHaveCount(1)
        ->and($product->specifications->first()->pivot->value)->toBe('8GB');
});

test('a sale records the seller and client roles correctly', function () {
    $sale = Sale::factory()->create();

    expect($sale->seller->role)->toBe(UserRoleEnum::SELLER->value)
        ->and($sale->client->role)->toBe(UserRoleEnum::CLIENT->value);
});
