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
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'category', except: null)]
    public ?int $category = null;

    #[Url(as: 'brand', except: null)]
    public ?int $brand = null;

    #[Url(as: 'sort', except: 'latest')]
    public string $sort = 'latest';

    public int $perPage = 12;

    /** @var array<int, int> */
    public array $compareIds = [];

    public function mount(): void
    {
        $this->compareIds = array_values(array_map('intval', session()->get('panoragoods_compare_ids', [])));
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedBrand(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->category = $categoryId;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category', 'brand', 'sort']);
        $this->resetPage();
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
        $query = Product::query()
            ->with(['category', 'brand', 'primaryImage', 'images', 'specifications'])
            ->where('status', 'active');

        if ($this->search !== '') {
            $term = trim($this->search);
            $query->where(function (Builder $q) use ($term): void {
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
            $query->where('category_id', $this->category);
        }

        if ($this->brand !== null) {
            $query->where('brand_id', $this->brand);
        }

        match ($this->sort) {
            'price_asc' => $query->orderBy('selling_price', 'asc'),
            'price_desc' => $query->orderBy('selling_price', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate($this->perPage);

        return view('livewire.product-catalog', [
            'products' => $products,
        ]);
    }
}
