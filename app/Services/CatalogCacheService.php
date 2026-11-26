<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CatalogCacheService
{
    public const string KEY_NAV_CATEGORIES = 'catalog.v2.nav_categories';

    public const string KEY_CATEGORIES_WITH_COUNT = 'catalog.v2.categories_with_count';

    public const string KEY_BRANDS = 'catalog.v2.brands';

    public const string KEY_FEATURED_PRODUCTS = 'catalog.v2.featured_product_ids';

    public const string KEY_INITIAL_CATALOG_IDS = 'catalog.v2.initial_product_ids';

    public const string KEY_ACTIVE_PRODUCTS_COUNT = 'catalog.v2.active_products_count';

    public const string KEY_ZONES = 'catalog.v2.zones';

    private const int TTL_SHORT = 300; // 5 minutes

    private const int TTL_MEDIUM = 600; // 10 minutes

    private const int TTL_LONG = 900; // 15 minutes

    /**
     * @return Collection<int, Category>
     */
    public function getNavCategories(): Collection
    {
        /** @var array<int, array<string, mixed>> $rows */
        $rows = Cache::remember(
            self::KEY_NAV_CATEGORIES,
            self::TTL_MEDIUM,
            fn (): array => Category::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (Category $category): array => $category->getAttributes())
                ->all(),
        );

        return Category::hydrate($rows);
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategoriesWithActiveCount(): Collection
    {
        /** @var array<int, array<string, mixed>> $rows */
        $rows = Cache::remember(
            self::KEY_CATEGORIES_WITH_COUNT,
            self::TTL_MEDIUM,
            fn (): array => Category::query()
                ->withCount(['products' => fn (Builder $query) => $query->where('status', 'active')])
                ->orderBy('name')
                ->get()
                ->map(fn (Category $category): array => $category->getAttributes())
                ->all(),
        );

        return Category::hydrate($rows);
    }

    /**
     * @return Collection<int, Brand>
     */
    public function getBrands(): Collection
    {
        /** @var array<int, array<string, mixed>> $rows */
        $rows = Cache::remember(
            self::KEY_BRANDS,
            self::TTL_MEDIUM,
            fn (): array => Brand::query()
                ->withCount(['products' => fn (Builder $query) => $query->where('status', 'active')])
                ->orderBy('name')
                ->get()
                ->map(fn (Brand $brand): array => $brand->getAttributes())
                ->all(),
        );

        return Brand::hydrate($rows);
    }

    /**
     * @return array<int, int>
     */
    public function getInitialCatalogProductIds(int $limit = 8): array
    {
        /** @var array<int, int> $ids */
        $ids = Cache::remember(
            self::KEY_INITIAL_CATALOG_IDS.'.'.$limit,
            self::TTL_SHORT,
            fn (): array => Product::query()
                ->where('status', 'active')
                ->latest()
                ->take($limit)
                ->pluck('id')
                ->map(fn (mixed $id): int => (int) $id)
                ->all(),
        );

        return $ids;
    }

    public function getActiveProductsCount(): int
    {
        return (int) Cache::remember(
            self::KEY_ACTIVE_PRODUCTS_COUNT,
            self::TTL_SHORT,
            fn (): int => Product::query()->where('status', 'active')->count(),
        );
    }

    /**
     * @return Collection<int, Product>
     */
    public function getFeaturedProducts(int $limit = 3): Collection
    {
        /** @var array<int, int> $ids */
        $ids = Cache::remember(
            self::KEY_FEATURED_PRODUCTS.'.'.$limit,
            self::TTL_SHORT,
            fn (): array => Product::query()
                ->where('status', 'active')
                ->latest()
                ->take($limit)
                ->pluck('id')
                ->map(fn (mixed $id): int => (int) $id)
                ->all(),
        );

        if ($ids === []) {
            return collect();
        }

        $products = Product::query()
            ->with([
                'brand',
                'category',
                'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('id'),
            ])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return collect($ids)
            ->map(fn (int $id) => $products->get($id))
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, Zone>
     */
    public function getZones(): Collection
    {
        /** @var array<int, array<string, mixed>> $rows */
        $rows = Cache::remember(
            self::KEY_ZONES,
            self::TTL_LONG,
            fn (): array => Zone::query()
                ->orderBy('city')
                ->orderBy('name')
                ->get(['id', 'name', 'city'])
                ->map(fn (Zone $zone): array => $zone->getAttributes())
                ->all(),
        );

        return Zone::hydrate($rows);
    }

    /**
     * @return Collection<int, Product>
     */
    public function getSimilarProducts(Product $product, int $limit = 4): Collection
    {
        /** @var array<int, int> $ids */
        $ids = Cache::remember(
            "catalog.v2.similar_product_ids.{$product->id}.{$limit}",
            self::TTL_SHORT,
            fn (): array => Product::query()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('status', 'active')
                ->latest()
                ->take($limit)
                ->pluck('id')
                ->map(fn (mixed $id): int => (int) $id)
                ->all(),
        );

        if ($ids === []) {
            return collect();
        }

        $products = Product::query()
            ->with([
                'brand',
                'category',
                'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('id'),
                'specifications',
            ])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        return collect($ids)
            ->map(fn (int $id) => $products->get($id))
            ->filter()
            ->values();
    }

    public function flushCatalog(): void
    {
        Cache::forget(self::KEY_NAV_CATEGORIES);
        Cache::forget(self::KEY_CATEGORIES_WITH_COUNT);
        Cache::forget(self::KEY_BRANDS);
        Cache::forget(self::KEY_FEATURED_PRODUCTS.'.3');
        Cache::forget(self::KEY_INITIAL_CATALOG_IDS.'.8');
        Cache::forget(self::KEY_ACTIVE_PRODUCTS_COUNT);
    }

    public function flushProduct(int $productId): void
    {
        Cache::forget(self::KEY_CATEGORIES_WITH_COUNT);
        Cache::forget(self::KEY_BRANDS);
        Cache::forget(self::KEY_FEATURED_PRODUCTS.'.3');
        Cache::forget(self::KEY_INITIAL_CATALOG_IDS.'.8');
        Cache::forget(self::KEY_ACTIVE_PRODUCTS_COUNT);
        Cache::forget("catalog.v2.similar_product_ids.{$productId}.4");
    }

    public function flushZones(): void
    {
        Cache::forget(self::KEY_ZONES);
    }
}
