<div>
    {{-- Floating Comparison Dock (shown when >= 1 product selected) --}}
    @if($this->products->isNotEmpty())
        <div
            class="fixed bottom-4 left-1/2 -translate-x-1/2 z-40 w-[calc(100%-1.5rem)] max-w-2xl rounded-2xl bg-slate-950/95 dark:bg-slate-900/95 text-white backdrop-blur-md border border-white/15 shadow-2xl px-3.5 sm:px-4 py-3"
            role="region"
            aria-label="Barre de comparaison de produits"
        >
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="flex -space-x-2">
                        @foreach($this->products as $item)
                            <div class="relative size-10 rounded-xl bg-white p-1 ring-2 ring-slate-900 shrink-0">
                                <img
                                    src="{{ $item->primary_image_url }}"
                                    alt="{{ $item->name }}"
                                    class="size-full object-contain"
                                >
                                <button
                                    type="button"
                                    wire:click="remove({{ $item->id }})"
                                    aria-label="Retirer {{ $item->name }}"
                                    class="absolute -top-1.5 -right-1.5 size-4 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center hover:bg-rose-600 cursor-pointer"
                                >×</button>
                            </div>
                        @endforeach
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-bold text-white">
                            Comparateur ({{ $this->products->count() }}/{{ \App\Livewire\ProductComparator::MAX_ITEMS }})
                        </p>
                        <p class="text-[11px] text-slate-400 truncate">
                            {{ $this->products->pluck('name')->implode(' vs ') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <flux:button wire:click="clear" variant="ghost" size="xs" class="!text-slate-300 hover:!bg-white/10">
                        Effacer
                    </flux:button>

                    <flux:modal.trigger name="product-comparator">
                        <flux:button variant="primary" size="sm" icon="scale" class="!bg-secondary hover:!bg-secondary-hover !text-white !border-0">
                            Comparer
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>
        </div>
    @endif

    {{-- Comparison Modal (Flux UI Modal + Table) --}}
    <flux:modal name="product-comparator" class="w-full max-w-5xl">
        <div class="space-y-5">
            <div>
                <flux:heading size="lg">Comparateur de Produits</flux:heading>
                <flux:text class="text-sm text-slate-500 mt-0.5">
                    Comparez les prix, la disponibilité et les caractéristiques techniques côte à côte.
                </flux:text>
            </div>

            @if($this->products->isNotEmpty())
                <div class="overflow-x-auto">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column class="w-40">Critère</flux:table.column>
                            @foreach($this->products as $product)
                                <flux:table.column wire:key="cmp-col-{{ $product->id }}">
                                    <div class="flex flex-col items-start gap-2 min-w-[180px]">
                                        <div class="flex w-full items-start justify-between gap-2">
                                            <a href="{{ route('products.show', $product) }}" class="size-16 rounded-xl bg-slate-50 border border-slate-200 p-1.5 flex items-center justify-center shrink-0">
                                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain">
                                            </a>
                                            <flux:button
                                                wire:click="remove({{ $product->id }})"
                                                variant="ghost"
                                                size="xs"
                                                square
                                                icon="x-mark"
                                                aria-label="Retirer {{ $product->name }}"
                                            />
                                        </div>
                                        <a href="{{ route('products.show', $product) }}" class="text-sm font-bold text-slate-900 hover:text-primary line-clamp-2">
                                            {{ $product->name }}
                                        </a>
                                    </div>
                                </flux:table.column>
                            @endforeach
                        </flux:table.columns>

                        <flux:table.rows>
                            <flux:table.row>
                                <flux:table.cell variant="strong">Prix</flux:table.cell>
                                @foreach($this->products as $product)
                                    <flux:table.cell class="font-extrabold text-primary text-base">
                                        {{ format_xaf($product->selling_price) }}
                                    </flux:table.cell>
                                @endforeach
                            </flux:table.row>

                            <flux:table.row>
                                <flux:table.cell variant="strong">Marque</flux:table.cell>
                                @foreach($this->products as $product)
                                    <flux:table.cell>{{ $product->brand?->name ?? '—' }}</flux:table.cell>
                                @endforeach
                            </flux:table.row>

                            <flux:table.row>
                                <flux:table.cell variant="strong">Disponibilité</flux:table.cell>
                                @foreach($this->products as $product)
                                    <flux:table.cell>
                                        @if($product->stock_quantity > 0)
                                            <flux:badge size="sm" color="emerald">En stock</flux:badge>
                                        @else
                                            <flux:badge size="sm" color="rose">Rupture</flux:badge>
                                        @endif
                                    </flux:table.cell>
                                @endforeach
                            </flux:table.row>

                            @foreach($this->specificationNames as $specName)
                                <flux:table.row wire:key="cmp-spec-{{ md5($specName) }}">
                                    <flux:table.cell variant="strong">{{ $specName }}</flux:table.cell>
                                    @foreach($this->products as $product)
                                        @php($matched = $product->specifications->firstWhere('name', $specName))
                                        <flux:table.cell>
                                            @if($matched)
                                                {{ $matched->pivot->value }}{{ $matched->measure ? ' '.$matched->measure : '' }}
                                            @else
                                                <span class="text-slate-400">—</span>
                                            @endif
                                        </flux:table.cell>
                                    @endforeach
                                </flux:table.row>
                            @endforeach

                            <flux:table.row>
                                <flux:table.cell variant="strong">Action</flux:table.cell>
                                @foreach($this->products as $product)
                                    <flux:table.cell>
                                        <flux:button
                                            wire:click="addToCart({{ $product->id }})"
                                            variant="primary"
                                            size="sm"
                                            icon="shopping-bag"
                                            class="w-full !bg-primary hover:!bg-primary-hover !text-white"
                                        >
                                            Ajouter au panier
                                        </flux:button>
                                    </flux:table.cell>
                                @endforeach
                            </flux:table.row>
                        </flux:table.rows>
                    </flux:table>
                </div>
            @else
                <flux:text class="text-center py-8 text-slate-500">
                    Sélectionnez au moins un produit dans le catalogue pour le comparer.
                </flux:text>
            @endif
        </div>
    </flux:modal>
</div>
