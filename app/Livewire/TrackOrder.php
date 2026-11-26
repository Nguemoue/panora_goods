<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Sale;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class TrackOrder extends Component
{
    public string $tracking_code = '';

    public ?Sale $sale = null;

    public bool $searched = false;

    /** @var array<string, string> */
    protected $rules = [
        'tracking_code' => 'required|string|min:8',
    ];

    public function mount(): void
    {
        $this->tracking_code = strtoupper(trim((string) request()->query('tracking_code', '')));
    }

    public function track(): void
    {
        $this->tracking_code = strtoupper(trim($this->tracking_code));
        $this->validate();

        $this->sale = Sale::query()
            ->with([
                'product.brand',
                'product.primaryImage',
                'seller.zones',
                'salePayments' => fn ($query) => $query->latest(),
            ])
            ->where('tracking_code', $this->tracking_code)
            ->first();

        $this->searched = true;
    }

    public function getSupportWhatsappUrlProperty(): string
    {
        $rawPhone = (string) config('project_configuration.whatsapp');
        $cleanPhone = preg_replace('/[^\d]/', '', $rawPhone) ?: '237696896758';

        $code = $this->sale?->tracking_code ?: $this->tracking_code;
        $message = "Bonjour PanoraGoods, j'ai une question concernant ma commande avec le code de suivi : {$code}.";

        return 'https://wa.me/'.$cleanPhone.'?text='.urlencode($message);
    }

    public function render(): View
    {
        return view('livewire.track-order')
            ->layout('layouts.app');
    }
}
