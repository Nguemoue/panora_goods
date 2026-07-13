<?php

namespace App\Livewire;

use App\Models\Sale;
use Livewire\Component;

class TrackOrder extends Component
{
    public string $tracking_code = '';

    public ?Sale $sale = null;

    public bool $searched = false;

    protected $rules = [
        'tracking_code' => 'required|string|min:8',
    ];

    public function mount(): void
    {
        $this->tracking_code = strtoupper((string) request()->query('tracking_code', ''));
    }

    public function track(): void
    {
        $this->validate();

        $this->sale = Sale::where('tracking_code', strtoupper($this->tracking_code))->first();
        $this->searched = true;
    }

    public function render()
    {
        return view('livewire.track-order')
            ->layout('layouts.app');
    }
}
