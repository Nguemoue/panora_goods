<div class="inline-flex items-center">
    {{-- Trigger Button in Navbar --}}
    <flux:modal.trigger name="whatsapp-cart">
        <button
            type="button"
            class="relative inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-primary/40 px-3 py-2 text-sm font-semibold text-slate-800 transition-colors cursor-pointer shadow-2xs"
            aria-label="Ouvrir le panier WhatsApp"
        >
            <flux:icon.shopping-bag variant="mini" class="text-primary" />
            <span class="hidden sm:inline">Panier</span>
            @if($this->totalCount > 0)
                <flux:badge size="sm" color="amber" variant="solid" class="!px-1.5 !py-0.5 !text-[11px] !rounded-full">
                    {{ $this->totalCount }}
                </flux:badge>
            @endif
        </button>
    </flux:modal.trigger>

    {{-- Flyout Drawer (Flux UI Modal) --}}
    <flux:modal name="whatsapp-cart" variant="flyout" class="w-full max-w-md">
        <div class="flex h-full flex-col space-y-5">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-3 pr-6">
                <div>
                    <flux:heading size="lg">Panier WhatsApp</flux:heading>
                    <flux:text class="text-xs text-slate-500 mt-0.5">
                        Groupez vos articles pour un devis instantané unique.
                    </flux:text>
                </div>
                @if($this->totalCount > 0)
                    <flux:badge color="blue" size="sm">
                        {{ $this->totalCount }} {{ $this->totalCount > 1 ? 'articles' : 'article' }}
                    </flux:badge>
                @endif
            </div>

            <flux:separator />

            {{-- Cart Items List --}}
            @if($this->cartItems->isNotEmpty())
                <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                    @foreach($this->cartItems as $item)
                        @php($product = $item['product'])
                        <div
                            wire:key="cart-item-{{ $product->id }}"
                            class="flex items-center gap-3 rounded-xl border border-slate-200/80 bg-slate-50/60 p-3"
                        >
                            <a href="{{ route('products.show', $product) }}" class="size-16 shrink-0 rounded-lg bg-white border border-slate-100 p-1.5 flex items-center justify-center">
                                <img
                                    src="{{ $product->primary_image_url }}"
                                    alt="{{ $product->name }}"
                                    loading="lazy"
                                    class="max-h-full max-w-full object-contain"
                                >
                            </a>

                            <div class="min-w-0 flex-1">
                                <a href="{{ route('products.show', $product) }}" class="text-sm font-bold text-slate-900 line-clamp-1 hover:text-primary">
                                    {{ $product->name }}
                                </a>
                                <p class="text-xs text-slate-500">
                                    {{ format_xaf($product->selling_price) }} / unité
                                </p>

                                <div class="mt-2 flex items-center justify-between gap-2">
                                    <div class="inline-flex items-center rounded-lg border border-slate-200 bg-white">
                                        <button
                                            type="button"
                                            wire:click="decrement({{ $product->id }})"
                                            class="px-2 py-1 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-l-lg cursor-pointer"
                                            aria-label="Diminuer la quantité"
                                        >−</button>
                                        <span class="px-2.5 py-1 text-xs font-bold text-slate-900 tabular-nums">
                                            {{ $item['quantity'] }}
                                        </span>
                                        <button
                                            type="button"
                                            wire:click="increment({{ $product->id }})"
                                            class="px-2 py-1 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-r-lg cursor-pointer"
                                            aria-label="Augmenter la quantité"
                                        >+</button>
                                    </div>

                                    <span class="text-xs font-extrabold text-slate-900">
                                        {{ format_xaf($item['subtotal']) }}
                                    </span>
                                </div>
                            </div>

                            <flux:button
                                wire:click="remove({{ $product->id }})"
                                variant="ghost"
                                size="xs"
                                square
                                icon="trash"
                                class="text-slate-400 hover:text-rose-600 self-start"
                                aria-label="Retirer {{ $product->name }}"
                            />
                        </div>
                    @endforeach
                </div>

                {{-- Zone Routing Selector --}}
                <div class="rounded-xl bg-blue-50/70 border border-blue-200/70 p-3.5 space-y-2.5">
                    <flux:field>
                        <flux:label class="text-xs font-bold text-slate-800">
                            Votre zone de livraison (routage vendeur direct)
                        </flux:label>
                        <flux:select wire:model.live="selectedZoneId" placeholder="Choisir votre zone (optionnel)">
                            <flux:select.option value="">Service client central PanoraGoods</flux:select.option>
                            @foreach($this->zones as $zone)
                                <flux:select.option value="{{ $zone->id }}">
                                    {{ $zone->city }} — {{ $zone->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    @if($this->resolvedContact['seller_name'])
                        <div class="flex items-center gap-2 text-xs text-emerald-800 bg-emerald-50 border border-emerald-200/80 rounded-lg px-2.5 py-1.5">
                            <flux:icon.check-badge variant="micro" class="text-emerald-600 shrink-0" />
                            <span>
                                Conseiller assigné : <strong>{{ $this->resolvedContact['seller_name'] }}</strong>
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Totals & WhatsApp Checkout CTA --}}
                <div class="space-y-3 pt-1">
                    <div class="flex items-center justify-between text-base font-extrabold text-slate-950">
                        <span>Total estimé</span>
                        <span>{{ format_xaf($this->totalAmount) }}</span>
                    </div>

                    <flux:button
                        href="{{ $this->whatsappUrl }}"
                        target="_blank"
                        rel="noopener"
                        variant="primary"
                        icon="chat-bubble-left-ellipsis"
                        class="w-full !bg-emerald-600 hover:!bg-emerald-700 !border-emerald-600 shadow-md"
                    >
                        Commander sur WhatsApp
                    </flux:button>

                    <div class="flex justify-center">
                        <flux:button wire:click="clear" variant="ghost" size="xs" class="text-slate-500">
                            Vider le panier
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-center py-10">
                    <div class="size-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                        <flux:icon.shopping-bag class="size-7" />
                    </div>
                    <flux:heading size="md">Votre panier est vide</flux:heading>
                    <flux:text class="text-xs text-slate-500 mt-1 max-w-xs">
                        Ajoutez un ou plusieurs produits du catalogue pour générer un bon de commande WhatsApp unique.
                    </flux:text>
                </div>
            @endif
        </div>
    </flux:modal>
</div>
