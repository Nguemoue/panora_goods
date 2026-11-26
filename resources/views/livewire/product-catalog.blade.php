<section id="catalog-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 scroll-mt-20">
    {{-- Enhanced Search & Filter Command Bar --}}
    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 shadow-xs mb-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
            {{-- Search Input --}}
            <div class="sm:col-span-2 lg:col-span-6">
                <flux:field>
                    <flux:label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
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
                    <flux:label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
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
                    <flux:label class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
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
        <div class="pt-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <flux:button
                wire:click="selectCategory(null)"
                size="sm"
                :variant="$category === null ? 'primary' : 'outline'"
                class="shrink-0 !rounded-full {{ $category === null ? '!bg-primary !border-primary !text-white' : '' }}"
            >
                Toutes les catégories
            </flux:button>

            @foreach($this->categories as $cat)
                <flux:button
                    wire:key="cat-pill-{{ $cat->id }}"
                    wire:click="selectCategory({{ $cat->id }})"
                    size="sm"
                    :variant="$category === $cat->id ? 'primary' : 'outline'"
                    class="shrink-0 !rounded-full {{ $category === $cat->id ? '!bg-primary !border-primary !text-white' : '' }}"
                >
                    <span>{{ $cat->name }}</span>
                    <span class="ml-1 text-[10px] opacity-75">({{ $cat->products_count }})</span>
                </flux:button>
            @endforeach
        </div>

        {{-- Active Filter Chips (Clearable Individually) --}}
        @if($this->hasActiveFilters)
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">Filtres actifs :</span>

                @if($search !== '')
                    <button
                        type="button"
                        wire:click="$set('search', '')"
                        class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 dark:bg-sky-500/15 border border-blue-200 dark:border-sky-500/30 px-2.5 py-1 text-xs font-medium text-primary dark:text-sky-300 hover:bg-blue-100 dark:hover:bg-sky-500/25 transition-colors cursor-pointer"
                    >
                        <span>Recherche : « {{ $search }} »</span>
                        <flux:icon.x-mark variant="micro" class="size-3.5" />
                    </button>
                @endif

                @if($this->selectedCategory)
                    <button
                        type="button"
                        wire:click="selectCategory(null)"
                        class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 dark:bg-sky-500/15 border border-blue-200 dark:border-sky-500/30 px-2.5 py-1 text-xs font-medium text-primary dark:text-sky-300 hover:bg-blue-100 dark:hover:bg-sky-500/25 transition-colors cursor-pointer"
                    >
                        <span>Catégorie : {{ $this->selectedCategory->name }}</span>
                        <flux:icon.x-mark variant="micro" class="size-3.5" />
                    </button>
                @endif

                @if($this->selectedBrand)
                    <button
                        type="button"
                        wire:click="$set('brand', null)"
                        class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 dark:bg-sky-500/15 border border-blue-200 dark:border-sky-500/30 px-2.5 py-1 text-xs font-medium text-primary dark:text-sky-300 hover:bg-blue-100 dark:hover:bg-sky-500/25 transition-colors cursor-pointer"
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
                    class="ml-auto text-rose-600 dark:text-rose-400 hover:text-rose-700"
                >
                    Tout réinitialiser
                </flux:button>
            </div>
        @endif
    </div>

    {{-- Section Heading + Results Meta --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <flux:heading size="lg" class="!font-extrabold text-slate-950 dark:text-white">
                @if($this->selectedCategory)
                    {{ $this->selectedCategory->name }}
                @elseif($this->selectedBrand)
                    Produits {{ $this->selectedBrand->name }}
                @else
                    Catalogue PanoraGoods
                @endif
            </flux:heading>
            <flux:text class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                @if($search !== '')
                    Résultats pour « <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $search }}</span> » —
                @endif
                Affichage de {{ $products->count() }} sur {{ $totalProducts }} {{ $totalProducts > 1 ? 'produits' : 'produit' }}
            </flux:text>
        </div>

        {{-- Inline Synchronized Status Pill during Catalog Filtering --}}
        <div
            wire:loading.flex
            wire:target="search,category,brand,sort,selectCategory,resetFilters"
            class="items-center gap-2 rounded-full bg-blue-50 dark:bg-sky-500/15 border border-blue-200/80 dark:border-sky-500/30 px-3 py-1 text-xs font-medium text-primary dark:text-sky-300"
        >
            <flux:icon.loading class="size-3.5" />
            <span>Actualisation du catalogue…</span>
        </div>
    </div>

    {{-- Synchronized Skeleton Loader Grid (when changing filters/search) --}}
    <div
        wire:loading.grid
        wire:loading.delay.short
        wire:target="search,category,brand,sort,selectCategory,resetFilters"
        class="grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5"
    >
        @for($i = 0; $i < 8; $i++)
            <x-frontend.product-card-skeleton />
        @endfor
    </div>

    {{-- Real Product Grid --}}
    <div
        wire:loading.remove.delay.short
        wire:target="search,category,brand,sort,selectCategory,resetFilters"
    >
        @if($products->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
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

            {{-- Synchronized Infinite Scroll Append Skeletons (shown while loadMore() runs) --}}
            <div
                wire:loading.grid
                wire:target="loadMore"
                class="grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5 mt-3 sm:mt-5"
            >
                @for($i = 0; $i < 4; $i++)
                    <x-frontend.product-card-skeleton />
                @endfor
            </div>

            {{-- Infinite Scroll Sentinel & Load More Controls --}}
            @if($hasMore)
                <div
                    wire:key="infinite-sentinel-{{ $loadedCount }}"
                    x-data="{
                        triggered: false,
                        init() {
                            const observer = new IntersectionObserver((entries) => {
                                if (entries[0].isIntersecting && !this.triggered) {
                                    this.triggered = true;
                                    $wire.loadMore();
                                }
                            }, { rootMargin: '260px' });
                            observer.observe($el);
                        }
                    }"
                    class="mt-8 flex flex-col items-center justify-center gap-3 py-2"
                >
                    <div wire:loading.flex wire:target="loadMore" class="items-center gap-2.5 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-4 py-2 text-xs font-semibold text-primary dark:text-sky-400 shadow-xs">
                        <flux:icon.loading class="size-4" />
                        <span>Chargement des produits suivants…</span>
                    </div>

                    <div wire:loading.remove wire:target="loadMore">
                        <flux:button
                            wire:click="loadMore"
                            variant="outline"
                            size="sm"
                            icon="chevron-down"
                        >
                            Charger plus de produits ({{ $remainingCount }} {{ $remainingCount > 1 ? 'restants' : 'restant' }})
                        </flux:button>
                    </div>
                </div>
            @elseif($totalProducts > \App\Livewire\ProductCatalog::PER_PAGE)
                <div class="mt-8 flex items-center justify-center gap-3 text-xs font-medium text-slate-400 dark:text-slate-500">
                    <span class="h-px w-12 bg-slate-200 dark:bg-slate-800"></span>
                    <span>Tous les {{ $totalProducts }} produits sont affichés</span>
                    <span class="h-px w-12 bg-slate-200 dark:bg-slate-800"></span>
                </div>
            @endif
        @else
            <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-10 sm:p-12 text-center">
                <div class="flex size-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 mb-4">
                    <flux:icon.magnifying-glass class="size-7" />
                </div>
                <flux:heading size="lg">Aucun produit trouvé</flux:heading>
                <flux:text class="mt-1 max-w-md text-slate-500 dark:text-slate-400">
                    Essayez de modifier votre recherche ou de réinitialiser les filtres pour découvrir tous nos modèles disponibles.
                </flux:text>
                <div class="mt-5">
                    <flux:button wire:click="resetFilters" variant="primary" icon="arrow-path" class="!bg-primary hover:!bg-primary-hover !text-white">
                        Afficher tout le catalogue
                    </flux:button>
                </div>
            </div>
        @endif
    </div>
</section>
