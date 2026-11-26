<section id="catalog-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 scroll-mt-20">
    {{-- Enhanced Search & Filter Command Bar --}}
    <div class="rounded-2xl bg-white border border-slate-200/80 p-4 sm:p-5 shadow-xs mb-6 space-y-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
            {{-- Search Input --}}
            <div class="lg:col-span-6">
                <flux:field>
                    <flux:label class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Recherche rapide
                    </flux:label>
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        icon="magnifying-glass"
                        clearable
                        placeholder="Nom, marque, modèle, capacité (ex: 256 Go) ou référence..."
                        aria-label="Rechercher un produit"
                    />
                </flux:field>
            </div>

            {{-- Dynamic Brands from Database --}}
            <div class="lg:col-span-3">
                <flux:field>
                    <flux:label class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Marque
                    </flux:label>
                    <flux:select wire:model.live="brand" placeholder="Toutes les marques" aria-label="Filtrer par marque">
                        <flux:select.option value="">Toutes les marques ({{ $this->brands->count() }})</flux:select.option>
                        @foreach($this->brands as $brandItem)
                            <flux:select.option value="{{ $brandItem->id }}">
                                {{ $brandItem->name }}@if(($brandItem->products_count ?? 0) > 0) ({{ $brandItem->products_count }})@endif
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            {{-- Sort Order --}}
            <div class="lg:col-span-3">
                <flux:field>
                    <flux:label class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Trier par
                    </flux:label>
                    <flux:select wire:model.live="sort" aria-label="Trier les produits">
                        <flux:select.option value="latest">Plus récents d'abord</flux:select.option>
                        <flux:select.option value="price_asc">Prix croissant</flux:select.option>
                        <flux:select.option value="price_desc">Prix décroissant</flux:select.option>
                    </flux:select>
                </flux:field>
            </div>
        </div>

        {{-- Category Filter Pills --}}
        <div class="pt-3.5 border-t border-slate-100 flex items-center gap-2 overflow-x-auto pb-1">
            <flux:button
                wire:click="selectCategory(null)"
                size="sm"
                :variant="$category === null ? 'primary' : 'outline'"
                class="shrink-0 !rounded-full {{ $category === null ? '!bg-primary !border-primary' : '' }}"
            >
                Toutes les catégories
            </flux:button>

            @foreach($this->categories as $cat)
                <flux:button
                    wire:key="cat-pill-{{ $cat->id }}"
                    wire:click="selectCategory({{ $cat->id }})"
                    size="sm"
                    :variant="$category === $cat->id ? 'primary' : 'outline'"
                    class="shrink-0 !rounded-full {{ $category === $cat->id ? '!bg-primary !border-primary' : '' }}"
                >
                    <span>{{ $cat->name }}</span>
                    <span class="ml-1 text-[10px] opacity-75">({{ $cat->products_count }})</span>
                </flux:button>
            @endforeach
        </div>

        {{-- Active Filter Chips (Clearable Individually) --}}
        @if($this->hasActiveFilters)
            <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-semibold text-slate-400">Filtres actifs :</span>

                @if($search !== '')
                    <button
                        type="button"
                        wire:click="$set('search', '')"
                        class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 border border-blue-200 px-2.5 py-1 text-xs font-medium text-primary hover:bg-blue-100 transition-colors cursor-pointer"
                    >
                        <span>Recherche : « {{ $search }} »</span>
                        <flux:icon.x-mark variant="micro" class="size-3.5" />
                    </button>
                @endif

                @if($this->selectedCategory)
                    <button
                        type="button"
                        wire:click="selectCategory(null)"
                        class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 border border-blue-200 px-2.5 py-1 text-xs font-medium text-primary hover:bg-blue-100 transition-colors cursor-pointer"
                    >
                        <span>Catégorie : {{ $this->selectedCategory->name }}</span>
                        <flux:icon.x-mark variant="micro" class="size-3.5" />
                    </button>
                @endif

                @if($this->selectedBrand)
                    <button
                        type="button"
                        wire:click="$set('brand', null)"
                        class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 border border-blue-200 px-2.5 py-1 text-xs font-medium text-primary hover:bg-blue-100 transition-colors cursor-pointer"
                    >
                        <span>Marque : {{ $this->selectedBrand->name }}</span>
                        <flux:icon.x-mark variant="micro" class="size-3.5" />
                    </button>
                @endif

                <flux:button
                    wire:click="resetFilters"
                    variant="ghost"
                    size="xs"
                    icon="arrow-path"
                    class="ml-auto text-rose-600 hover:text-rose-700"
                >
                    Tout réinitialiser
                </flux:button>
            </div>
        @endif
    </div>

    {{-- Section Heading + Results Meta --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <flux:heading size="lg" class="!font-extrabold text-slate-950">
                @if($this->selectedCategory)
                    {{ $this->selectedCategory->name }}
                @elseif($this->selectedBrand)
                    Produits {{ $this->selectedBrand->name }}
                @else
                    Catalogue PanoraGoods
                @endif
            </flux:heading>
            <flux:text class="text-xs sm:text-sm text-slate-500 mt-0.5">
                @if($search !== '')
                    Résultats pour « <span class="font-semibold text-slate-800">{{ $search }}</span> » —
                @endif
                {{ $products->total() }} {{ $products->total() > 1 ? 'produits disponibles' : 'produit disponible' }}
            </flux:text>
        </div>

        {{-- Inline Synchronized Status Pill during Catalog Filtering --}}
        <div
            wire:loading.flex
            wire:target="search,category,brand,sort,selectCategory,resetFilters,previousPage,nextPage,gotoPage"
            class="items-center gap-2 rounded-full bg-blue-50 border border-blue-200/80 px-3 py-1 text-xs font-medium text-primary"
        >
            <flux:icon.loading class="size-3.5" />
            <span>Actualisation du catalogue…</span>
        </div>
    </div>

    {{-- Synchronized Skeleton Loader Grid (Anti-Flicker 150ms delay, CLS = 0) --}}
    <div
        wire:loading.grid
        wire:loading.delay.short
        wire:target="search,category,brand,sort,selectCategory,resetFilters,previousPage,nextPage,gotoPage"
        class="grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5"
    >
        @for($i = 0; $i < 8; $i++)
            <x-frontend.product-card-skeleton />
        @endfor
    </div>

    {{-- Real Product Grid --}}
    <div
        wire:loading.remove.delay.short
        wire:target="search,category,brand,sort,selectCategory,resetFilters,previousPage,nextPage,gotoPage"
    >
        @if($products->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach($products as $index => $product)
                    <div wire:key="product-card-{{ $product->id }}">
                        <x-frontend.product-card
                            :product="$product"
                            :is-comparing="in_array($product->id, $compareIds, true)"
                            :eager="$index < 4"
                        />
                    </div>
                @endforeach
            </div>

            {{-- Modern Flux UI Pagination --}}
            @if($products->hasPages())
                <div class="catalog-pagination mt-8 rounded-2xl bg-white border border-slate-200/80 px-4 py-3.5 shadow-2xs">
                    <flux:pagination :paginator="$products" scroll-to="#catalog-section" />
                </div>
            @endif
        @else
            <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white p-12 text-center">
                <div class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-4">
                    <flux:icon.magnifying-glass class="size-7" />
                </div>
                <flux:heading size="lg">Aucun produit trouvé</flux:heading>
                <flux:text class="mt-1 max-w-md text-slate-500">
                    Essayez de modifier votre recherche ou de réinitialiser les filtres pour découvrir tous nos modèles disponibles.
                </flux:text>
                <div class="mt-5">
                    <flux:button wire:click="resetFilters" variant="primary" icon="arrow-path">
                        Afficher tout le catalogue
                    </flux:button>
                </div>
            </div>
        @endif
    </div>
</section>
