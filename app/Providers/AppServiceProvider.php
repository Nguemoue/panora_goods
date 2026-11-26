<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Zone;
use App\Services\CatalogCacheService;
use Illuminate\Support\Number;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCurrency();
        $this->registerCatalogCacheInvalidation();
    }

    private function configureCurrency(): void
    {
        Number::useCurrency(currency: currency());
    }

    private function registerCatalogCacheInvalidation(): void
    {
        $flushProduct = static function (Product $product): void {
            app(CatalogCacheService::class)->flushProduct((int) $product->id);
        };

        Product::saved($flushProduct);
        Product::deleted($flushProduct);

        $flushImage = static function (ProductImage $image): void {
            app(CatalogCacheService::class)->flushProduct((int) $image->product_id);
        };

        ProductImage::saved($flushImage);
        ProductImage::deleted($flushImage);

        $flushCatalog = static function (): void {
            app(CatalogCacheService::class)->flushCatalog();
        };

        Category::saved($flushCatalog);
        Category::deleted($flushCatalog);
        Brand::saved($flushCatalog);
        Brand::deleted($flushCatalog);

        $flushZones = static function (): void {
            app(CatalogCacheService::class)->flushZones();
        };

        Zone::saved($flushZones);
        Zone::deleted($flushZones);
    }
}
