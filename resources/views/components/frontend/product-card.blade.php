@props([
    'product',
    'isComparing' => false,
    'eager' => false,
])

@php
    $orderedImages = $product->images->sortByDesc('is_primary')->values();
    $primaryUrl = $orderedImages->first()?->url ?? $product->primary_image_url;
    $secondaryUrl = $orderedImages->count() > 1 ? $orderedImages->get(1)?->url : null;
    $imageCount = $orderedImages->count();
    $inStock = $product->stock_quantity > 0;
@endphp

<article class="product-card group relative flex flex-col overflow-hidden rounded-2xl bg-white border border-slate-200/80 hover:border-primary/35 shadow-xs">
    {{-- Prominent Studio Image Stage --}}
    <div class="product-image-stage relative aspect-square w-full overflow-hidden p-2.5 sm:p-3 border-b border-slate-100">
        {{-- Top Badges --}}
        <div class="absolute top-2.5 left-2.5 right-2.5 z-10 flex items-start justify-between gap-2 pointer-events-none">
            @if($product->brand)
                <flux:badge size="sm" color="zinc" class="backdrop-blur-md !bg-white/95 shadow-2xs">
                    {{ $product->brand->name }}
                </flux:badge>
            @else
                <span></span>
            @endif

            @if($inStock)
                <flux:badge size="sm" color="emerald" class="!bg-emerald-50/95 backdrop-blur-md">
                    En stock
                </flux:badge>
            @else
                <flux:badge size="sm" color="rose" class="!bg-rose-50/95 backdrop-blur-md">
                    Rupture
                </flux:badge>
            @endif
        </div>

        <a href="{{ route('products.show', $product) }}" class="relative flex h-full w-full items-center justify-center">
            <img
                src="{{ $primaryUrl }}"
                alt="{{ $product->name }}"
                @if($eager) fetchpriority="high" @else loading="lazy" @endif
                decoding="async"
                class="h-full w-full object-contain transition-all duration-500 ease-out {{ $secondaryUrl ? 'group-hover:opacity-0 group-hover:scale-105' : 'group-hover:scale-105' }}"
            >

            @if($secondaryUrl)
                <img
                    src="{{ $secondaryUrl }}"
                    alt="{{ $product->name }} — vue alternative"
                    loading="lazy"
                    decoding="async"
                    class="absolute inset-0 h-full w-full object-contain opacity-0 scale-100 transition-all duration-500 ease-out group-hover:opacity-100 group-hover:scale-105"
                >
            @endif
        </a>

        {{-- Bottom-left Compare Quick Toggle --}}
        <div class="absolute bottom-2.5 left-2.5 z-10">
            <flux:tooltip content="{{ $isComparing ? 'Retirer du comparateur' : 'Comparer ce produit' }}">
                <button
                    type="button"
                    wire:click="toggleCompare({{ $product->id }})"
                    aria-label="Comparer {{ $product->name }}"
                    class="size-8 rounded-full border shadow-xs flex items-center justify-center transition-all cursor-pointer {{ $isComparing ? 'bg-secondary border-secondary text-white' : 'bg-white/90 hover:bg-white border-slate-200 text-slate-600 hover:text-primary' }}"
                >
                    <flux:icon.scale variant="micro" class="size-4" />
                </button>
            </flux:tooltip>
        </div>

        {{-- Photo Count Pill --}}
        @if($imageCount > 1)
            <div class="absolute bottom-2.5 right-2.5 z-10 pointer-events-none inline-flex items-center gap-1 rounded-full bg-slate-900/75 px-2 py-0.5 text-[10px] font-semibold text-white backdrop-blur-sm">
                <flux:icon.photo variant="micro" class="size-3" />
                <span>{{ $imageCount }}</span>
            </div>
        @endif
    </div>

    {{-- Body --}}
    <div class="flex flex-1 flex-col p-3.5 sm:p-4">
        <div class="flex items-center justify-between gap-2 text-[11px] font-medium text-slate-400 mb-1">
            <span class="truncate">{{ $product->category?->name ?? 'Catalogue' }}</span>
            @if($product->reference_number)
                <span class="font-mono text-[10px] text-slate-400 shrink-0">{{ $product->reference_number }}</span>
            @endif
        </div>

        <h3 class="text-sm sm:text-[15px] font-bold text-slate-900 leading-snug line-clamp-2 min-h-[2.5rem] group-hover:text-primary transition-colors">
            <a href="{{ route('products.show', $product) }}" class="focus:outline-none">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Dynamic Specs Preview --}}
        @if($product->specifications->isNotEmpty())
            <div class="mt-2.5 flex flex-wrap gap-1">
                @foreach($product->specifications->take(3) as $spec)
                    <span class="inline-flex items-center rounded-md bg-slate-100/90 px-2 py-0.5 text-[10px] font-medium text-slate-600">
                        {{ $spec->pivot->value }}{{ $spec->measure ? ' '.$spec->measure : '' }}
                    </span>
                @endforeach
            </div>
        @endif

        {{-- Price & Full-Width Add to Cart CTA --}}
        <div class="mt-auto pt-4 space-y-3">
            <div class="flex items-baseline justify-between gap-2">
                <span class="text-lg sm:text-xl font-extrabold text-slate-950 tracking-tight">
                    {{ format_xaf($product->selling_price) }}
                </span>
            </div>

            <flux:button
                wire:click="addToCart({{ $product->id }})"
                wire:target="addToCart({{ $product->id }})"
                variant="primary"
                size="sm"
                icon="shopping-bag"
                class="w-full !bg-primary hover:!bg-primary-hover text-xs font-semibold"
            >
                Ajouter au panier
            </flux:button>
        </div>
    </div>
</article>
