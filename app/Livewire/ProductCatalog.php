<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\CatalogCacheService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProductCatalog extends Component
{
    public const int PER_PAGE = 12;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'category', except: null)]
    public ?int $category = null;

    #[Url(as: 'brand', except: null)]
    public ?int $brand = null;

    #[Url(as: 'sort', except: 'latest')]
    public string $sort = 'latest';

    public int $loadedCount = self::PER_PAGE;

    /** @var array<int, int> */
    public array $compareIds = [];

    public function mount(): void
    {
        $this->compareIds = array_values(array_map('intval', session()->get('panoragoods_compare_ids', [])));
    }

    public function updatedSearch(): void
    {
        $this->loadedCount = self::PER_PAGE;
    }

    public function updatedCategory(): void
    {
        $this->loadedCount = self::PER_PAGE;
    }

    public function updatedBrand(): void
    {
        $this->loadedCount = self::PER_PAGE;
    }

    public function updatedSort(): void
    {
        $this->loadedCount = self::PER_PAGE;
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->category = $categoryId;
        $this->loadedCount = self::PER_PAGE;
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category', 'brand', 'sort']);
        $this->loadedCount = self::PER_PAGE;
    }

    public function loadMore(): void
    {
        $this->loadedCount += self::PER_PAGE;
    }

    public function addToCart(int $productId): void
    {
        $this->dispatch('cart-add-product', productId: $productId);
    }

    public function toggleCompare(int $productId): void
    {
        $this->dispatch('compare-toggle-product', productId: $productId);
    }

    /**
     * @param  array<int, int>  $ids
     */
    #[On('compare-updated')]
    public function syncCompareIds(array $ids): void
    {
        $this->compareIds = array_values(array_map('intval', $ids));
    }

    /**
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return app(CatalogCacheService::class)->getCategoriesWithActiveCount();
    }

    /**
     * @return Collection<int, Brand>
     */
    #[Computed]
    public function brands(): Collection
    {
        return app(CatalogCacheService::class)->getBrands();
    }

    #[Computed]
    public function selectedCategory(): ?Category
    {
        if (! $this->category) {
            return null;
        }

        return $this->categories->firstWhere('id', $this->category);
    }

    #[Computed]
    public function selectedBrand(): ?Brand
    {
        if (! $this->brand) {
            return null;
        }

        return $this->brands->firstWhere('id', $this->brand);
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->category !== null
            || $this->brand !== null
            || $this->sort !== 'latest';
    }

    public function render(): View
    {
        $baseQuery = Product::query()->where('status', 'active');

        if ($this->search !== '') {
            $term = trim($this->search);
            $baseQuery->where(function (Builder $q) use ($term): void {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('selling_price', 'like', "%{$term}%")
                    ->orWhere('reference_number', 'like', "%{$term}%")
                    ->orWhere('product_code', 'like', "%{$term}%")
                    ->orWhere('model_number', 'like', "%{$term}%")
                    ->orWhere('short_description', 'like', "%{$term}%")
                    ->orWhereHas('brand', fn (Builder $b) => $b->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('category', fn (Builder $c) => $c->where('name', 'like', "%{$term}%"))
                    ->orWhereHas('specifications', fn (Builder $s) => $s->where('product_specification.value', 'like', "%{$term}%"));
            });
        }

        if ($this->category !== null) {
            $baseQuery->where('category_id', $this->category);
        }

        if ($this->brand !== null) {
            $baseQuery->where('brand_id', $this->brand);
        }

        $totalProducts = (clone $baseQuery)->count();

        match ($this->sort) {
            'price_asc' => $baseQuery->orderBy('selling_price', 'asc'),
            'price_desc' => $baseQuery->orderBy('selling_price', 'desc'),
            default => $baseQuery->latest(),
        };

        $products = $baseQuery
            ->with(['category', 'brand', 'primaryImage', 'images', 'specifications'])
            ->take($this->loadedCount)
            ->get();

        $hasMore = $products->count() < $totalProducts;
        $remainingCount = max(0, $totalProducts - $products->count());

        return view('livewire.product-catalog', [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'hasMore' => $hasMore,
            'remainingCount' => $remainingCount,
        ]);
    }
}
