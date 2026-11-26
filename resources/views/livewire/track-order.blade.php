@section('title', __('frontend.nav.track_order'))

<div class="max-w-4xl mx-auto py-4 space-y-6">
    {{-- Search Header Card --}}
    <div class="rounded-2xl bg-white border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="flex items-start gap-3.5 mb-6">
                <div class="size-12 rounded-2xl bg-blue-50 text-primary flex items-center justify-center shrink-0">
                    <flux:icon.truck class="size-6" />
                </div>
                <div>
                    <flux:badge size="sm" color="blue" class="mb-2">Suivi en temps réel</flux:badge>
                    <flux:heading size="xl" class="!font-extrabold text-slate-950">
                        {{ __('frontend.track.title') }}
                    </flux:heading>
                    <flux:text class="text-sm text-slate-500 mt-1">
                        {{ __('frontend.track.subtitle') }}
                    </flux:text>
                </div>
            </div>

            <form wire:submit.prevent="track" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="flex-1">
                    <flux:input
                        wire:model.defer="tracking_code"
                        id="tracking_code"
                        icon="qr-code"
                        clearable
                        placeholder="Ex : ABC123XYZ0"
                        class="uppercase font-mono"
                        aria-label="{{ __('frontend.track.input_label') }}"
                    />
                </div>
                <flux:button
                    type="submit"
                    variant="primary"
                    icon="magnifying-glass"
                    class="!bg-primary hover:!bg-primary-hover shrink-0"
                >
                    {{ __('frontend.track.button') }}
                </flux:button>
            </form>

            @error('tracking_code')
                <p class="text-rose-600 text-xs font-medium mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- Synchronized Skeleton Loader during search --}}
        <div wire:loading.block wire:loading.delay.short wire:target="track" class="border-t border-slate-200 p-6 sm:p-8 bg-slate-50/40">
            <flux:skeleton.group animate="shimmer" class="space-y-5">
                <div class="flex items-center justify-between gap-4">
                    <flux:skeleton class="h-6 w-52" />
                    <flux:skeleton class="h-8 w-40 rounded-lg" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <flux:skeleton class="h-20 rounded-xl" />
                    <flux:skeleton class="h-20 rounded-xl" />
                    <flux:skeleton class="h-20 rounded-xl" />
                </div>
                <flux:skeleton class="h-28 rounded-xl" />
            </flux:skeleton.group>
        </div>

        {{-- Tracking Results --}}
        @if($searched)
            <div wire:loading.remove.delay.short wire:target="track" class="border-t border-slate-200 p-6 sm:p-8 bg-slate-50/40">
                @if($sale)
                    @php
                        $paidAmount = $sale->getApprovedPaidAmount();
                        $remainingAmount = $sale->getRemainingAmount();
                        $progress = match ($sale->status->value) {
                            'pending' => 25,
                            'processing' => 50,
                            'shipped' => 75,
                            'delivered' => 100,
                            'cancelled' => 0,
                            default => 0,
                        };
                        $isCancelled = $sale->status->value === 'cancelled';
                    @endphp

                    <div class="space-y-6">
                        {{-- Order Header & Primary Actions --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <h2 class="font-display text-xl font-extrabold text-slate-950">
                                        {{ __('frontend.track.order_title', ['code' => $sale->tracking_code]) }}
                                    </h2>
                                    <flux:badge size="sm" color="{{ $isCancelled ? 'rose' : ($progress === 100 ? 'emerald' : 'blue') }}">
                                        {{ $sale->status->getLabel() }}
                                    </flux:badge>
                                    @if($sale->payment_status)
                                        <flux:badge size="sm" color="{{ $remainingAmount <= 0 ? 'emerald' : 'amber' }}">
                                            {{ $sale->payment_status->getLabel() }}
                                        </flux:badge>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 mt-1">
                                    Client : <strong class="text-slate-800">{{ $sale->customer_name }}</strong>
                                    · Créée le {{ $sale->created_at->format('d/m/Y à H:i') }}
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <flux:button
                                    href="{{ route('invoice.download.pdf', ['sale' => $sale]) }}"
                                    variant="primary"
                                    size="sm"
                                    icon="arrow-down-tray"
                                    class="!bg-primary hover:!bg-primary-hover"
                                >
                                    Télécharger la facture
                                </flux:button>
                                <flux:button
                                    href="{{ $this->supportWhatsappUrl }}"
                                    target="_blank"
                                    rel="noopener"
                                    variant="outline"
                                    size="sm"
                                    icon="chat-bubble-left-ellipsis"
                                >
                                    Support WhatsApp
                                </flux:button>
                            </div>
                        </div>

                        {{-- Delivery Stepper Timeline --}}
                        <div class="rounded-2xl bg-white border border-slate-200/80 p-5">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-700 mb-3">
                                <span>{{ __('frontend.track.progress') }}</span>
                                <span class="tabular-nums {{ $isCancelled ? 'text-rose-600' : 'text-primary' }}">
                                    {{ $isCancelled ? 'Commande annulée' : "{$progress}%" }}
                                </span>
                            </div>

                            <div class="overflow-hidden rounded-full bg-slate-200 h-2.5 mb-4">
                                <div
                                    class="h-full rounded-full transition-all duration-500 {{ $isCancelled ? 'bg-rose-500' : ($progress === 100 ? 'bg-emerald-500' : 'bg-primary') }}"
                                    style="width: {{ $progress }}%"
                                ></div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                @foreach([
                                    ['step' => 25, 'label' => __('frontend.track.status.pending'), 'icon' => 'clock'],
                                    ['step' => 50, 'label' => __('frontend.track.status.processing'), 'icon' => 'cog-6-tooth'],
                                    ['step' => 75, 'label' => __('frontend.track.status.shipped'), 'icon' => 'truck'],
                                    ['step' => 100, 'label' => __('frontend.track.status.delivered'), 'icon' => 'check-circle'],
                                ] as $stage)
                                    @php($reached = ! $isCancelled && $progress >= $stage['step'])
                                    <div class="flex items-center gap-2 rounded-xl px-3 py-2 border {{ $reached ? 'bg-blue-50/70 border-blue-200 text-primary font-semibold' : 'bg-slate-50 border-slate-100 text-slate-400' }}">
                                        <flux:icon :name="$stage['icon']" variant="micro" class="shrink-0" />
                                        <span class="truncate">{{ $stage['label'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Financial Summary Cards --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="rounded-2xl bg-white border border-slate-200/80 p-4">
                                <p class="text-xs font-medium text-slate-500">Montant total</p>
                                <p class="font-display text-xl font-extrabold text-slate-950 mt-1">
                                    {{ format_xaf($sale->sale_price) }}
                                </p>
                                @if($sale->payment_type)
                                    <p class="text-[11px] text-slate-400 mt-1">
                                        Mode : {{ $sale->payment_type->getLabel() }}
                                    </p>
                                @endif
                            </div>

                            <div class="rounded-2xl bg-white border border-emerald-200/80 p-4">
                                <p class="text-xs font-medium text-emerald-700">Montant payé (validé)</p>
                                <p class="font-display text-xl font-extrabold text-emerald-700 mt-1">
                                    {{ format_xaf($paidAmount) }}
                                </p>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    {{ $sale->salePayments->count() }} {{ $sale->salePayments->count() > 1 ? 'versements enregistrés' : 'versement enregistré' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-white border {{ $remainingAmount > 0 ? 'border-amber-200' : 'border-slate-200/80' }} p-4">
                                <p class="text-xs font-medium {{ $remainingAmount > 0 ? 'text-amber-700' : 'text-slate-500' }}">Reste à régler</p>
                                <p class="font-display text-xl font-extrabold {{ $remainingAmount > 0 ? 'text-amber-700' : 'text-slate-950' }} mt-1">
                                    {{ format_xaf($remainingAmount) }}
                                </p>
                                @if($sale->payment_date_limit && $remainingAmount > 0)
                                    <p class="text-[11px] text-amber-600 mt-1">
                                        Échéance : {{ $sale->payment_date_limit->format('d/m/Y') }}
                                    </p>
                                @else
                                    <p class="text-[11px] text-emerald-600 mt-1">
                                        {{ $remainingAmount <= 0 ? 'Soldé intégralement' : 'En cours de règlement' }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Product & Certified Seller Details --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Product Card --}}
                            @if($sale->product)
                                <div class="rounded-2xl bg-white border border-slate-200/80 p-4 flex items-center gap-4">
                                    <a href="{{ route('products.show', $sale->product) }}" class="size-20 shrink-0 rounded-xl bg-slate-50 border border-slate-100 p-2 flex items-center justify-center">
                                        <img
                                            src="{{ $sale->product->primary_image_url }}"
                                            alt="{{ $sale->product->name }}"
                                            class="max-h-full max-w-full object-contain"
                                        >
                                    </a>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[11px] font-semibold uppercase text-slate-400">
                                            {{ $sale->product->brand?->name ?? __('frontend.track.product') }}
                                        </span>
                                        <a href="{{ route('products.show', $sale->product) }}" class="block text-sm font-bold text-slate-950 hover:text-primary line-clamp-2 mt-0.5">
                                            {{ $sale->product->name }}
                                        </a>
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ __('frontend.track.quantity') }} : <strong class="text-slate-800">{{ $sale->quantity }}</strong>
                                        </p>
                                    </div>
                                </div>
                            @endif

                            {{-- Certified Seller Card with Reference Number --}}
                            @if($sale->seller)
                                <div class="rounded-2xl bg-white border border-slate-200/80 p-4 flex items-center gap-4">
                                    <img
                                        src="{{ $sale->seller->avatar_url }}"
                                        alt="{{ $sale->seller->name }}"
                                        class="size-16 shrink-0 rounded-2xl object-cover border border-slate-200"
                                    >
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5">
                                            <flux:badge size="sm" color="emerald" icon="check-badge">Conseiller assigné</flux:badge>
                                        </div>
                                        <p class="text-sm font-bold text-slate-950 mt-1 truncate">{{ $sale->seller->name }}</p>
                                        @if($sale->seller->reference_number)
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                Réf. vendeur : <span class="font-mono font-semibold text-primary">{{ $sale->seller->reference_number }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Payment History Table --}}
                        <div class="rounded-2xl bg-white border border-slate-200/80 overflow-hidden">
                            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                                <h3 class="text-sm font-bold text-slate-950">Historique des paiements</h3>
                                @if($sale->seller?->reference_number)
                                    <span class="text-xs text-slate-500">
                                        Vendeur : <span class="font-mono font-semibold text-slate-700">{{ $sale->seller->reference_number }}</span>
                                    </span>
                                @endif
                            </div>

                            @if($sale->salePayments->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <flux:table>
                                        <flux:table.columns>
                                            <flux:table.column>#</flux:table.column>
                                            <flux:table.column>Montant</flux:table.column>
                                            <flux:table.column>État</flux:table.column>
                                            <flux:table.column>Réf. vendeur</flux:table.column>
                                            <flux:table.column>Date</flux:table.column>
                                        </flux:table.columns>
                                        <flux:table.rows>
                                            @foreach($sale->salePayments as $item)
                                                <flux:table.row>
                                                    <flux:table.cell>{{ $loop->iteration }}</flux:table.cell>
                                                    <flux:table.cell variant="strong">{{ format_xaf($item->amount) }}</flux:table.cell>
                                                    <flux:table.cell>
                                                        <flux:badge size="sm" color="emerald">{{ $item->confirmation_status->getLabel() }}</flux:badge>
                                                    </flux:table.cell>
                                                    <flux:table.cell class="font-mono text-xs">
                                                        {{ $sale->seller?->reference_number ?? '—' }}
                                                    </flux:table.cell>
                                                    <flux:table.cell>{{ $item->created_at->format('d/m/Y H:i') }}</flux:table.cell>
                                                </flux:table.row>
                                            @endforeach
                                        </flux:table.rows>
                                    </flux:table>
                                </div>
                            @else
                                <div class="p-6 text-center text-sm text-slate-500">
                                    Aucun versement enregistré pour cette commande pour le moment.
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-8 space-y-3">
                        <div class="mx-auto size-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                            <flux:icon.magnifying-glass class="size-7" />
                        </div>
                        <h3 class="text-base font-bold text-slate-900">{{ __('frontend.track.no_order_found') }}</h3>
                        <p class="text-sm text-slate-500 max-w-md mx-auto">{{ __('frontend.track.no_order_found_text') }}</p>
                        <div class="pt-2">
                            <flux:button href="{{ $this->supportWhatsappUrl }}" target="_blank" rel="noopener" variant="outline" size="sm" icon="chat-bubble-left-ellipsis">
                                Demander de l'aide sur WhatsApp
                            </flux:button>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
