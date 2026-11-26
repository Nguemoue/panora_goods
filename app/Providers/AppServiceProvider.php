<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\ConfirmationStatusEnum;
use App\Enums\UserRoleEnum;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SalePayment;
use App\Models\User;
use App\Models\Zone;
use App\Services\CatalogCacheService;
use App\Services\ProductImageOptimizer;
use Filament\Notifications\Notification;
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
        $this->registerPaymentNotifications();
    }

    private function registerPaymentNotifications(): void
    {
        SalePayment::created(static function (SalePayment $payment): void {
            if ($payment->confirmation_status !== ConfirmationStatusEnum::PENDING) {
                return;
            }

            $admins = User::query()
                ->where('role', UserRoleEnum::ADMIN)
                ->where('status', 'active')
                ->get();

            if ($admins->isEmpty()) {
                return;
            }

            $sale = $payment->sale()->with('seller')->first();
            $sellerName = $sale?->seller?->name ?? 'Un vendeur';
            $tracking = $sale?->tracking_code ?? "#{$payment->sale_id}";

            Notification::make()
                ->title('Nouvel acompte à valider')
                ->body("{$sellerName} a enregistré un paiement de ".format_xaf($payment->amount)." sur la commande {$tracking}.")
                ->warning()
                ->sendToDatabase($admins);
        });
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
            app(ProductImageOptimizer::class)->generateThumbnail($image);
            app(CatalogCacheService::class)->flushProduct((int) $image->product_id);
        };

        ProductImage::saved($flushImage);
        ProductImage::deleted(static function (ProductImage $image): void {
            app(CatalogCacheService::class)->flushProduct((int) $image->product_id);
        });

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
