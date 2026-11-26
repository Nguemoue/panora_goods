<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Product;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductComparator extends Component
{
    private const string SESSION_KEY = 'panoragoods_compare_ids';

    public const int MAX_ITEMS = 3;

    /** @var array<int, int> */
    public array $productIds = [];

    public function mount(): void
    {
        $this->productIds = array_values(array_map('intval', session()->get(self::SESSION_KEY, [])));
    }

    #[On('compare-toggle-product')]
    public function toggleProduct(int $productId): void
    {
        if (in_array($productId, $this->productIds, true)) {
            $this->productIds = array_values(array_filter(
                $this->productIds,
                fn (int $id): bool => $id !== $productId,
            ));
            $this->persist();

            Flux::toast(text: 'Produit retiré du comparateur.');

            return;
        }

        if (count($this->productIds) >= self::MAX_ITEMS) {
            Flux::toast(
                heading: 'Limite atteinte',
                text: 'Vous pouvez comparer au maximum '.self::MAX_ITEMS.' produits à la fois.',
                variant: 'warning',
            );

            return;
        }

        $product = Product::query()->where('status', 'active')->find($productId);
        if (! $product) {
            return;
        }

        $this->productIds[] = $productId;
        $this->persist();

        Flux::toast(
            heading: 'Ajouté au comparateur',
            text: "{$product->name} (".count($this->productIds).'/'.self::MAX_ITEMS.')',
            variant: 'info',
        );
    }

    public function remove(int $productId): void
    {
        $this->productIds = array_values(array_filter(
            $this->productIds,
            fn (int $id): bool => $id !== $productId,
        ));
        $this->persist();
    }

    public function clear(): void
    {
        $this->productIds = [];
        $this->persist();
    }

    public function addToCart(int $productId): void
    {
        $this->dispatch('cart-add-product', productId: $productId);
    }

    /**
     * @return Collection<int, Product>
     */
    #[Computed]
    public function products(): Collection
    {
        if ($this->productIds === []) {
            return collect();
        }

        $loaded = Product::query()
            ->with(['brand', 'category', 'primaryImage', 'images', 'specifications'])
            ->whereIn('id', $this->productIds)
            ->get()
            ->keyBy('id');

        return collect($this->productIds)
            ->map(fn (int $id) => $loaded->get($id))
            ->filter()
            ->values();
    }

    /**
     * All unique specification names across the compared products.
     *
     * @return Collection<int, string>
     */
    #[Computed]
    public function specificationNames(): Collection
    {
        return $this->products
            ->flatMap(fn (Product $product) => $product->specifications->pluck('name'))
            ->unique()
            ->values();
    }

    private function persist(): void
    {
        session()->put(self::SESSION_KEY, $this->productIds);
        $this->dispatch('compare-updated', ids: $this->productIds);
    }

    public function render(): View
    {
        return view('livewire.product-comparator');
    }
}
