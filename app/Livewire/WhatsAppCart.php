<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Product;
use App\Models\Zone;
use App\Services\CatalogCacheService;
use App\Services\WhatsAppOrderService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class WhatsAppCart extends Component
{
    private const string SESSION_KEY = 'panoragoods_whatsapp_cart';

    private const string ZONE_SESSION_KEY = 'panoragoods_selected_zone_id';

    /** @var array<int, int> Map of product_id => quantity */
    public array $items = [];

    public ?int $selectedZoneId = null;

    public function mount(): void
    {
        $this->items = session()->get(self::SESSION_KEY, []);
        $zoneId = session()->get(self::ZONE_SESSION_KEY);
        $this->selectedZoneId = $zoneId ? (int) $zoneId : null;
    }

    public function updatedSelectedZoneId(?int $value): void
    {
        session()->put(self::ZONE_SESSION_KEY, $value);
    }

    #[On('cart-add-product')]
    public function addProduct(int $productId, int $quantity = 1): void
    {
        $product = Product::query()->where('status', 'active')->find($productId);

        if (! $product) {
            return;
        }

        $current = (int) ($this->items[$productId] ?? 0);
        $this->items[$productId] = min(99, $current + max(1, $quantity));
        $this->persist();

        Flux::toast(
            heading: 'Ajouté au panier WhatsApp',
            text: "{$product->name} ({$this->items[$productId]}×)",
            variant: 'success',
        );
    }

    public function increment(int $productId): void
    {
        if (! isset($this->items[$productId])) {
            return;
        }

        $this->items[$productId] = min(99, $this->items[$productId] + 1);
        $this->persist();
    }

    public function decrement(int $productId): void
    {
        if (! isset($this->items[$productId])) {
            return;
        }

        if ($this->items[$productId] <= 1) {
            unset($this->items[$productId]);
        } else {
            $this->items[$productId]--;
        }

        $this->persist();
    }

    public function remove(int $productId): void
    {
        unset($this->items[$productId]);
        $this->persist();

        Flux::toast(
            text: 'Article retiré du panier.',
            variant: 'warning',
        );
    }

    public function clear(): void
    {
        $this->items = [];
        $this->persist();

        Flux::toast(
            text: 'Votre panier WhatsApp a été vidé.',
        );
    }

    /**
     * @return Collection<int, array{product: Product, quantity: int, subtotal: float}>
     */
    #[Computed]
    public function cartItems(): Collection
    {
        if ($this->items === []) {
            return collect();
        }

        $products = Product::query()
            ->with(['brand', 'primaryImage', 'images'])
            ->whereIn('id', array_keys($this->items))
            ->get()
            ->keyBy('id');

        return collect($this->items)
            ->map(function (int $quantity, int $productId) use ($products): ?array {
                $product = $products->get($productId);
                if (! $product) {
                    return null;
                }

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => (float) $product->selling_price * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    #[Computed]
    public function totalCount(): int
    {
        return array_sum($this->items);
    }

    #[Computed]
    public function totalAmount(): float
    {
        return (float) $this->cartItems->sum('subtotal');
    }

    /**
     * @return Collection<int, Zone>
     */
    #[Computed]
    public function zones(): Collection
    {
        return app(CatalogCacheService::class)->getZones();
    }

    /**
     * @return array{phone: string, seller_name: ?string, zone_label: ?string}
     */
    #[Computed]
    public function resolvedContact(): array
    {
        return app(WhatsAppOrderService::class)->resolveContactForZone($this->selectedZoneId);
    }

    #[Computed]
    public function whatsappUrl(): string
    {
        return app(WhatsAppOrderService::class)->buildCartWhatsAppUrl(
            $this->cartItems,
            $this->totalAmount,
            $this->selectedZoneId,
        );
    }

    private function persist(): void
    {
        session()->put(self::SESSION_KEY, $this->items);
    }

    public function render(): View
    {
        return view('livewire.whatsapp-cart');
    }
}
