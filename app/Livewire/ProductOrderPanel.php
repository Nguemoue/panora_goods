<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Product;
use App\Models\Zone;
use App\Services\CatalogCacheService;
use App\Services\WhatsAppOrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductOrderPanel extends Component
{
    public Product $product;

    public ?int $selectedZoneId = null;

    public bool $isComparing = false;

    public function mount(Product $product): void
    {
        $this->product = $product->loadMissing(['brand', 'category', 'specifications']);
        $zoneId = session()->get('panoragoods_selected_zone_id');
        $this->selectedZoneId = $zoneId ? (int) $zoneId : null;
        $compareIds = array_map('intval', session()->get('panoragoods_compare_ids', []));
        $this->isComparing = in_array($this->product->id, $compareIds, true);
    }

    public function updatedSelectedZoneId(?int $value): void
    {
        session()->put('panoragoods_selected_zone_id', $value);
    }

    public function addToCart(): void
    {
        $this->dispatch('cart-add-product', productId: $this->product->id);
    }

    public function toggleCompare(): void
    {
        $this->dispatch('compare-toggle-product', productId: $this->product->id);
    }

    /**
     * @param  array<int, int>  $ids
     */
    #[On('compare-updated')]
    public function syncCompareState(array $ids): void
    {
        $this->isComparing = in_array($this->product->id, array_map('intval', $ids), true);
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
        $contact = $this->resolvedContact;

        return $this->product->getWhatsAppLink(
            phone: $contact['phone'],
            zoneName: $contact['zone_label'],
        );
    }

    public function render(): View
    {
        return view('livewire.product-order-panel');
    }
}
