<?php

use App\Models\User;
use Livewire\Component;

new class extends Component {

    public string $reference = '';

    public ?User $employee = null;

    public bool $searched = false;

    public function search(): void
    {
        $this->searched = true;

        $this->employee = User::query()
            ->where('reference_number', $this->reference)
            ->whereRole(\App\Enums\UserRoleEnum::SELLER)
            ->first();
    }
};
?>

<div class="max-w-4xl mx-auto p-6">

    <div class="bg-white shadow rounded-md p-6 space-y-4">

        <h2 class="text-2xl font-bold">
            Recherche Employé
        </h2>

        <div class="flex gap-3">

            <input
                type="text"
                wire:model="reference"
                placeholder="Entrez le numéro de référence"
                class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring focus:ring-blue-300"
            >
            <button
                wire:click="search"
                class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700"
            >
                <span class="in-data-loading:hidden">Recherche</span>
                <span class="not-in-data-loading:hidden">Recherche...</span>
            </button>

        </div>


        @if($this->employee)

            <div class="border rounded-xl p-5 bg-gray-50 space-y-3">

                <h3 class="text-xl font-semibold text-green-700">
                    Employé trouvé
                </h3>

                <div>
                    <img src="{{ $this->employee->avatar_url }}" alt="Avatar de {{ $this->employee->name }}" class="w-16 h-16 rounded-full object-cover"/>
                </div>
                <div>
                    <span class="font-bold">Nom :</span>
                    {{ $this->employee->name }}
                </div>

                <div>
                    <span class="font-bold">Secteur(s) :</span>
                    {{ $this->employee->zones->pluck('name')->join(', ') }}
                </div>

                <div>
                    <span class="font-bold">Référence :</span>
                    {{ $this->employee->reference_number }}
                </div>

            </div>

        @elseif($this->searched)

            <div class="bg-red-100 text-red-700 p-4 rounded-lg">
                Aucun employé trouvé avec cette référence.
            </div>

        @endif


    </div>

</div>
