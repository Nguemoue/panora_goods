<div>
    {{-- Main Conversion Card (Desktop & Mobile inline) --}}
    <div class="p-5 sm:p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-xs mb-6 sm:mb-8 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">
                    {{ __('frontend.product.reference_price') }}
                </p>
                <div class="flex items-baseline gap-2.5">
                    <span class="text-3xl sm:text-4xl font-black text-slate-950 dark:text-white tracking-tight">
                        {{ format_xaf($product->selling_price) }}
                    </span>
                </div>
            </div>

            @if($product->stock_quantity > 0)
                <flux:badge color="emerald" size="sm">En stock</flux:badge>
            @else
                <flux:badge color="rose" size="sm">Rupture de stock</flux:badge>
            @endif
        </div>

        {{-- Smart Zone Routing Selector --}}
        @if($this->zones->isNotEmpty())
            <div class="rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200/80 dark:border-slate-700 p-3.5 space-y-2">
                <flux:field>
                    <flux:label class="text-xs font-bold text-slate-700 dark:text-slate-200">
                        Sélectionnez votre zone pour contacter le conseiller le plus proche
                    </flux:label>
                    <flux:select wire:model.live="selectedZoneId" placeholder="Choisir votre zone">
                        <flux:select.option value="">Service client central PanoraGoods</flux:select.option>
                        @foreach($this->zones as $zone)
                            <flux:select.option value="{{ $zone->id }}">
                                {{ $zone->city }} — {{ $zone->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>

                @if($this->resolvedContact['seller_name'])
                    <div class="flex items-center gap-2 text-xs text-emerald-700 dark:text-emerald-400 font-medium">
                        <flux:icon.check-badge variant="micro" class="text-emerald-600 dark:text-emerald-400 shrink-0" />
                        <span>Conseiller certifié pour cette zone : <strong>{{ $this->resolvedContact['seller_name'] }}</strong></span>
                    </div>
                @endif
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <flux:button
                href="{{ $this->whatsappUrl }}"
                target="_blank"
                rel="noopener"
                variant="primary"
                icon="chat-bubble-left-ellipsis"
                class="flex-1 !bg-emerald-600 hover:!bg-emerald-700 !border-emerald-600 shadow-sm"
            >
                {{ __('frontend.product.request_quote') }}
            </flux:button>

            <flux:button
                wire:click="addToCart"
                variant="primary"
                icon="shopping-bag"
                class="!bg-primary hover:!bg-primary-hover"
            >
                Ajouter au panier
            </flux:button>

            <flux:tooltip content="{{ $isComparing ? 'Retirer du comparateur' : 'Comparer ce modèle' }}">
                <flux:button
                    wire:click="toggleCompare"
                    :variant="$isComparing ? 'primary' : 'outline'"
                    icon="scale"
                    class="{{ $isComparing ? '!bg-secondary hover:!bg-secondary-hover !border-secondary' : '' }}"
                >
                    {{ $isComparing ? 'Comparé' : 'Comparer' }}
                </flux:button>
            </flux:tooltip>
        </div>

        <p class="text-[11px] text-slate-400 dark:text-slate-500 text-center">
            {{ __('frontend.product.catalog_only') }}
        </p>
    </div>

    {{-- Sticky Mobile Conversion CTA Bar (md:hidden) --}}
    <div class="sticky-mobile-cta md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 px-4 pt-3">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <p class="text-[10px] font-semibold uppercase text-slate-400 dark:text-slate-500">Prix</p>
                <p class="text-base font-black text-slate-950 dark:text-white truncate">
                    {{ format_xaf($product->selling_price) }}
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <flux:button
                    wire:click="addToCart"
                    variant="outline"
                    size="sm"
                    square
                    icon="shopping-bag"
                    aria-label="Ajouter au panier"
                />
                <flux:button
                    href="{{ $this->whatsappUrl }}"
                    target="_blank"
                    rel="noopener"
                    variant="primary"
                    size="sm"
                    icon="chat-bubble-left-ellipsis"
                    class="!bg-emerald-600 hover:!bg-emerald-700 !border-emerald-600"
                >
                    WhatsApp
                </flux:button>
            </div>
        </div>
    </div>
</div>
