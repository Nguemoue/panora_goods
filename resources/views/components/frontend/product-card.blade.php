@props([
    'product',
    'isComparing' => false,
    'eager' => false,
])

@php
    $orderedImages = $product->images->sortByDesc('is_primary')->values();
    $primaryUrl = $orderedImages->first()?->thumb_url ?? $product->primary_thumb_url;
    $secondaryUrl = $orderedImages->count() > 1 ? $orderedImages->get(1)?->thumb_url : null;
    $placeholderUrl = asset('images/cart-placeholder.jpg');
    $imageCount = $orderedImages->count();
    $inStock = $product->stock_quantity > 0;
@endphp

<article class="product-card group relative flex flex-col overflow-hidden rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 hover:border-primary/35 dark:hover:border-sky-500/40 shadow-xs">
    {{-- Prominent Studio Image Stage --}}
    <div class="product-image-stage relative aspect-square w-full overflow-hidden p-2.5 sm:p-3 border-b border-slate-100 dark:border-slate-800">
        {{-- Top Badges --}}
        <div class="absolute top-2 left-2 right-2 sm:top-2.5 sm:left-2.5 sm:right-2.5 z-10 flex items-start justify-between gap-1.5 pointer-events-none">
            @if($product->brand)
                <flux:badge size="sm" color="zinc" class="backdrop-blur-md !bg-white/95 !text-slate-800 shadow-2xs max-w-[55%] truncate">
                    {{ $product->brand->name }}
                </flux:badge>
            @else
                <span></span>
            @endif

            @if($inStock)
                <flux:badge size="sm" color="emerald" class="!bg-emerald-50/95 !text-emerald-800 backdrop-blur-md shrink-0">
                    En stock
                </flux:badge>
            @else
                <flux:badge size="sm" color="rose" class="!bg-rose-50/95 !text-rose-800 backdrop-blur-md shrink-0">
                    Rupture
                </flux:badge>
            @endif
        </div>

        <a href="{{ route('products.show', $product) }}" class="relative flex h-full w-full items-center justify-center">
            <img
                src="{{ $primaryUrl }}"
                alt="{{ $product->name }}"
                width="400"
                height="400"
                @if($eager) fetchpriority="high" @else loading="lazy" @endif
                decoding="async"
                onerror="this.onerror=null;this.src='{{ $placeholderUrl }}';"
                class="h-full w-full object-contain transition-all duration-500 ease-out {{ $secondaryUrl ? 'group-hover:opacity-0 group-hover:scale-105' : 'group-hover:scale-105' }}"
            >

            @if($secondaryUrl)
                <img
                    src="{{ $secondaryUrl }}"
                    alt="{{ $product->name }} — vue alternative"
                    width="400"
                    height="400"
                    loading="lazy"
                    decoding="async"
                    onerror="this.onerror=null;this.src='{{ $placeholderUrl }}';"
                    class="absolute inset-0 h-full w-full object-contain opacity-0 scale-100 transition-all duration-500 ease-out group-hover:opacity-100 group-hover:scale-105"
                >
            @endif
        </a>

        {{-- Bottom-left Compare Quick Toggle --}}
        <div class="absolute bottom-2 left-2 sm:bottom-2.5 sm:left-2.5 z-10">
            <flux:tooltip content="{{ $isComparing ? 'Retirer du comparateur' : 'Comparer ce produit' }}">
                <button
                    type="button"
                    wire:click="toggleCompare({{ $product->id }})"
                    aria-label="Comparer {{ $product->name }}"
                    class="size-8 rounded-full border shadow-xs flex items-center justify-center transition-all cursor-pointer {{ $isComparing ? 'bg-secondary border-secondary text-white' : 'bg-white/95 hover:bg-white border-slate-200 text-slate-700 hover:text-primary' }}"
                >
                    <flux:icon.scale variant="micro" class="size-4" />
                </button>
            </flux:tooltip>
        </div>

        {{-- Photo Count Pill --}}
        @if($imageCount > 1)
            <div class="absolute bottom-2 right-2 sm:bottom-2.5 sm:right-2.5 z-10 pointer-events-none inline-flex items-center gap-1 rounded-full bg-slate-900/75 px-2 py-0.5 text-[10px] font-semibold text-white backdrop-blur-sm">
                <flux:icon.photo variant="micro" class="size-3" />
                <span>{{ $imageCount }}</span>
            </div>
        @endif
    </div>

    {{-- Body --}}
    <div class="flex flex-1 flex-col p-3 sm:p-4">
        <div class="flex items-center justify-between gap-2 text-[11px] font-medium text-slate-400 dark:text-slate-500 mb-1">
            <span class="truncate">{{ $product->category?->name ?? 'Catalogue' }}</span>
            @if($product->reference_number)
                <span class="hidden sm:inline font-mono text-[10px] text-slate-400 dark:text-slate-500 shrink-0">{{ $product->reference_number }}</span>
            @endif
        </div>

        <h3 class="text-xs sm:text-[15px] font-bold text-slate-900 dark:text-white leading-snug line-clamp-2 min-h-[2.25rem] sm:min-h-[2.5rem] group-hover:text-primary dark:group-hover:text-sky-400 transition-colors">
            <a href="{{ route('products.show', $product) }}" class="focus:outline-none">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Dynamic Specs Preview --}}
        @if($product->specifications->isNotEmpty())
            <div class="mt-2 flex flex-wrap gap-1">
                @foreach($product->specifications->take(3) as $spec)
                    <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-slate-800 px-1.5 sm:px-2 py-0.5 text-[10px] font-medium text-slate-600 dark:text-slate-300">
                        {{ $spec->pivot->value }}{{ $spec->measure ? ' '.$spec->measure : '' }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Price & Full-Width Add to Cart CTA --}}
        <div class="mt-auto pt-3.5 sm:pt-4 space-y-2.5 sm:space-y-3">
            <div class="flex items-baseline justify-between gap-2">
                <span class="text-base sm:text-xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                    {{ format_xaf($product->selling_price) }}
                </span>
            </div>

            <flux:button
                wire:click="addToCart({{ $product->id }})"
                wire:target="addToCart({{ $product->id }})"
                variant="primary"
                size="sm"
                icon="shopping-bag"
                class="w-full !bg-primary hover:!bg-primary-hover !text-white text-[11px] sm:text-xs font-semibold"
            >
                Ajouter au panier
            </flux:button>
        </div>
    </div>
</article>
