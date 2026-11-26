<?php

use App\Enums\UserRoleEnum;
use App\Models\User;
use Livewire\Component;

new class extends Component
{
    public string $reference = '';

    public ?User $employee = null;

    public bool $searched = false;

    public function search(): void
    {
        $this->searched = true;

        $this->employee = User::query()
            ->with('zones')
            ->where('reference_number', trim($this->reference))
            ->whereRole(UserRoleEnum::SELLER)
            ->first();
    }
};
?>

<div class="max-w-2xl mx-auto">
    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-xs p-5 sm:p-8 space-y-6">
        <div class="flex items-start gap-3.5">
            <div class="size-11 rounded-xl bg-blue-50 dark:bg-sky-500/15 text-primary dark:text-sky-400 flex items-center justify-center shrink-0">
                <flux:icon.identification class="size-6" />
            </div>
            <div>
                <flux:heading size="xl" class="!font-extrabold text-slate-950 dark:text-white">
                    Vérification d'Employé
                </flux:heading>
                <flux:text class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Entrez le numéro de référence d'un conseiller pour vérifier son identité et ses zones officielles.
                </flux:text>
            </div>
        </div>

        <form wire:submit.prevent="search" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <flux:input
                    wire:model="reference"
                    icon="identification"
                    placeholder="Entrez le numéro de référence"
                    class="font-mono"
                />
            </div>
            <flux:button type="submit" variant="primary" icon="magnifying-glass" class="!bg-primary hover:!bg-primary-hover shrink-0">
                Rechercher
            </flux:button>
        </form>

        @if($this->employee)
            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50/50 dark:bg-emerald-500/10 p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <flux:badge color="emerald" size="sm" icon="check-badge">
                        Employé Certifié PanoraGoods
                    </flux:badge>
                    <span class="font-mono text-xs font-semibold text-slate-500 dark:text-slate-400">
                        {{ $this->employee->reference_number }}
                    </span>
                </div>

                <div class="flex items-center gap-4">
                    <img
                        src="{{ $this->employee->avatar_url }}"
                        alt="Avatar de {{ $this->employee->name }}"
                        class="size-16 rounded-2xl object-cover border-2 border-white dark:border-slate-700 shadow-sm shrink-0"
                    >
                    <div class="min-w-0 space-y-1">
                        <h3 class="font-display text-lg font-extrabold text-slate-950 dark:text-white">
                            {{ $this->employee->name }}
                        </h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300">
                            <span class="font-semibold">Secteur(s) :</span>
                            {{ $this->employee->zones->pluck('name')->join(', ') ?: 'Non assigné' }}
                        </p>
                    </div>
                </div>
            </div>
        @elseif($this->searched)
            <flux:callout variant="danger" icon="exclamation-triangle" heading="Aucun employé trouvé">
                Aucun conseiller actif ne correspond à cette référence. Vérifiez le numéro saisi ou contactez notre service client.
            </flux:callout>
        @endif
    </div>
</div>
