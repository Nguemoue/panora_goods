<?php

declare(strict_types=1);

use App\Enums\SaleStatusEnum;
use App\Enums\UserRoleEnum;
use App\Filament\Admin\Resources\Products\Pages\EditProduct;
use App\Filament\Admin\Resources\Products\Pages\ListProducts;
use App\Filament\Admin\Resources\Products\ProductResource;
use App\Filament\Admin\Widgets\Admin\ProductSalesChart;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Sale;
use App\Models\Specification;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('renders ProductSalesChart without SQL errors and protects products with sales from deletion', function () {
    $admin = User::factory()->create(['role' => UserRoleEnum::ADMIN, 'status' => 'active']);
    $soldProduct = Product::factory()->create(['name' => 'iPhone 16 Pro', 'status' => 'active', 'stock_quantity' => 3]);
    $unsoldProduct = Product::factory()->create(['name' => 'Pixel 9', 'status' => 'active', 'stock_quantity' => 8]);

    Sale::factory()->create([
        'product_id' => $soldProduct->id,
        'quantity' => 2,
        'status' => SaleStatusEnum::DELIVERED,
        'sold_at' => now(),
    ]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
    actingAs($admin);

    livewire(ProductSalesChart::class, ['record' => $soldProduct])
        ->assertOk();

    // Product with sales cannot be deleted from EditProduct
    livewire(EditProduct::class, ['record' => $soldProduct->id])
        ->assertOk()
        ->assertActionHidden(DeleteAction::class);

    // Product without sales can still be deleted
    livewire(EditProduct::class, ['record' => $unsoldProduct->id])
        ->assertOk()
        ->assertActionVisible(DeleteAction::class);
});

it('duplicates a product safely with images and specifications as inactive with zero stock', function () {
    $admin = User::factory()->create(['role' => UserRoleEnum::ADMIN, 'status' => 'active']);
    $product = Product::factory()->create([
        'name' => 'Samsung Galaxy S25',
        'status' => 'active',
        'stock_quantity' => 12,
        'supplier_price' => 350000,
        'selling_price' => 500000,
    ]);

    $spec = Specification::factory()->create(['name' => 'Stockage']);
    $product->productSpecifications()->create([
        'specification_id' => $spec->id,
        'value' => '256GB',
    ]);

    $product->images()->create([
        'path' => 'products/sample-phone.jpg',
        'type' => 'face_1',
        'is_primary' => true,
    ]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
    actingAs($admin);

    livewire(EditProduct::class, ['record' => $product->id])
        ->callAction('duplicate_product')
        ->assertNotified()
        ->assertRedirect();

    $copy = Product::query()->where('id', '!=', $product->id)->latest('id')->firstOrFail();

    expect($copy->name)->toBe('Samsung Galaxy S25 (Copie)')
        ->and($copy->status)->toBe('inactive')
        ->and((int) $copy->stock_quantity)->toBe(0)
        ->and($copy->product_code)->not->toBe($product->product_code)
        ->and($copy->images()->count())->toBe(1)
        ->and($copy->productSpecifications()->count())->toBe(1);
});

it('enforces a single primary image per product and supports stock adjustment and code search', function () {
    $admin = User::factory()->create(['role' => UserRoleEnum::ADMIN, 'status' => 'active']);
    $product = Product::factory()->create([
        'name' => 'Tecno Phantom V',
        'model_number' => 'AD10-PRO',
        'status' => 'active',
        'stock_quantity' => 4,
    ]);

    $img1 = ProductImage::create([
        'product_id' => $product->id,
        'path' => 'products/img1.jpg',
        'type' => 'face_1',
        'is_primary' => true,
    ]);

    $img2 = ProductImage::create([
        'product_id' => $product->id,
        'path' => 'products/img2.jpg',
        'type' => 'back',
        'is_primary' => true,
    ]);

    expect((bool) $img1->fresh()->is_primary)->toBeFalse()
        ->and((bool) $img2->fresh()->is_primary)->toBeTrue();

    expect(ProductResource::getNavigationBadge())->toBe('1')
        ->and(ProductResource::getGloballySearchableAttributes())->toContain('product_code', 'model_number');

    Filament::setCurrentPanel(Filament::getPanel('admin'));
    actingAs($admin);

    livewire(ListProducts::class)
        ->assertOk()
        ->searchTable('AD10-PRO')
        ->assertCanSeeTableRecords([$product])
        ->callAction(TestAction::make('adjust_stock')->table($product), [
            'operation' => 'add',
            'quantity' => 6,
        ])
        ->assertNotified();

    expect((int) $product->fresh()->stock_quantity)->toBe(10);
});
