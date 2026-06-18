<?php

use App\DownloadCatalogPdfAction;
use App\Filament\Admin\Resources\Products\Pages\ListProducts;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Specification;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use function Pest\Livewire\livewire;

it('builds catalog data for selected categories only', function () {
    $selectedCategory = Category::factory()->create(['name' => 'Téléphones']);
    $ignoredCategory = Category::factory()->create(['name' => 'Télévisions']);
    $brand = Brand::factory()->create(['name' => 'Samsung']);
    $specification = Specification::factory()->create([
        'name' => 'RAM',
        'measure' => 'Go',
    ]);

    $selectedProduct = Product::factory()->create([
        'category_id' => $selectedCategory->id,
        'brand_id' => $brand->id,
        'name' => 'Galaxy S25',
    ]);

    Product::factory()->create([
        'category_id' => $ignoredCategory->id,
        'name' => 'TV OLED',
    ]);

    $selectedProduct->specifications()->attach($specification->id, [
        'value' => '12',
    ]);

    $categories = app(DownloadCatalogPdfAction::class)->catalogData([$selectedCategory->id]);

    expect($categories)->toHaveCount(1)
        ->and($categories->first()->is($selectedCategory))->toBeTrue()
        ->and($categories->first()->products)->toHaveCount(1)
        ->and($categories->first()->products->first()->name)->toBe('Galaxy S25')
        ->and($categories->first()->products->first()->brand->name)->toBe('Samsung')
        ->and($categories->first()->products->first()->specifications->first()->pivot->value)->toBe('12');
});

it('shows the catalog download action on the admin products page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(ListProducts::class)
        ->assertActionExists('download_catalog');
});

it('renders the selected catalog as a card pdf document', function (int $columns) {
    $category = Category::factory()->create(['name' => 'Ordinateurs']);
    Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Laptop Pro',
    ]);

    $categories = app(DownloadCatalogPdfAction::class)->catalogData([$category->id]);

    $pdf = Pdf::loadView('pdf.catalog', [
        'categories' => $categories,
        'columns' => $columns,
        'generatedAt' => now(),
    ]);

    expect($pdf->output())->toStartWith('%PDF');
})->with([2, 3]);
