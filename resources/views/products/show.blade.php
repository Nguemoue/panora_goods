@extends('layouts.app')

@section('title', $product->name)

@php
    $galleryImages = $product->images
        ->map(fn ($img): array => [
            'url' => $img->url,
            'alt' => $product->name,
            'is_primary' => (bool) $img->is_primary,
        ])
        ->values();

    if ($galleryImages->isEmpty()) {
        $galleryImages = collect([[
            'url' => $product->primary_image_url,
            'alt' => $product->name,
            'is_primary' => true,
        ]]);
    }
@endphp

@section('content')
    <div
        class="pb-24 md:pb-12"
        x-data="{
            images: @js($galleryImages),
            activeIndex: 0,
            lightboxOpen: false,
            zoomed: false,
            activeTab: '{{ $product->specifications->isNotEmpty() ? 'specs-key' : 'description' }}',
            touchStartX: 0,
            get currentImage() {
                return this.images[this.activeIndex] || this.images[0];
            },
            selectImage(idx) {
                this.activeIndex = idx;
                this.zoomed = false;
            },
            nextImage() {
                this.activeIndex = (this.activeIndex + 1) % this.images.length;
                this.zoomed = false;
            },
            prevImage() {
                this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length;
                this.zoomed = false;
            },
            openLightbox(idx = null) {
                if (idx !== null) this.activeIndex = idx;
                this.lightboxOpen = true;
                this.zoomed = false;
                document.body.style.overflow = 'hidden';
            },
            closeLightbox() {
                this.lightboxOpen = false;
                this.zoomed = false;
                document.body.style.overflow = '';
            },
            onTouchStart(e) {
                this.touchStartX = e.changedTouches[0].screenX;
            },
            onTouchEnd(e) {
                const diff = e.changedTouches[0].screenX - this.touchStartX;
                if (Math.abs(diff) > 40 && this.images.length > 1) {
                    diff < 0 ? this.nextImage() : this.prevImage();
                }
            }
        }"
        @keydown.escape.window="if (lightboxOpen) closeLightbox()"
        @keydown.arrow-right.window="if (lightboxOpen && images.length > 1) nextImage()"
        @keydown.arrow-left.window="if (lightboxOpen && images.length > 1) prevImage()"
    >
        {{-- Breadcrumbs (Flux UI) --}}
        <div class="mb-6">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item href="{{ route('home') }}" icon="home">
                    {{ __('frontend.nav.breadcrumb_catalog') }}
                </flux:breadcrumbs.item>
                @if($product->category)
                    <flux:breadcrumbs.item href="{{ route('home', ['category' => $product->category_id]) }}#catalog-section">
                        {{ $product->category->name }}
                    </flux:breadcrumbs.item>
                @endif
                <flux:breadcrumbs.item>{{ $product->name }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        {{-- Main 2-Column Product Showcase --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 xl:gap-12 items-start">
            {{-- Left Column (7 cols): Full Interactive Gallery --}}
            <div class="lg:col-span-7 lg:sticky lg:top-20 space-y-4">
                <div class="flex flex-col-reverse sm:flex-row gap-3.5">
                    {{-- All Thumbnails Strip --}}
                    @if($galleryImages->count() > 1)
                        <div class="flex sm:flex-col gap-2.5 overflow-x-auto sm:overflow-y-auto sm:max-h-[540px] pb-1 sm:pb-0 sm:w-22 shrink-0">
                            @foreach($galleryImages as $index => $img)
                                <button
                                    type="button"
                                    @click="selectImage({{ $index }})"
                                    aria-label="Afficher la photo {{ $index + 1 }}"
                                    class="relative size-20 sm:w-20 sm:h-20 shrink-0 rounded-xl bg-white border-2 p-1.5 overflow-hidden transition-all duration-200 cursor-pointer"
                                    :class="activeIndex === {{ $index }} ? 'border-primary ring-2 ring-primary/20 shadow-sm' : 'border-slate-200 hover:border-slate-400 opacity-80 hover:opacity-100'"
                                >
                                    <img
                                        src="{{ $img['url'] }}"
                                        alt="{{ $product->name }} — photo {{ $index + 1 }}"
                                        loading="eager"
                                        decoding="async"
                                        class="h-full w-full object-contain"
                                    >
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Primary High-Res Image Stage --}}
                    <div
                        class="product-image-stage relative flex-1 rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden aspect-square sm:aspect-[4/3] lg:aspect-square flex items-center justify-center group"
                        @touchstart.passive="onTouchStart($event)"
                        @touchend.passive="onTouchEnd($event)"
                    >
                        {{-- Stock Badge --}}
                        <div class="absolute top-4 left-4 z-10 flex items-center gap-2">
                            @if($product->stock_quantity > 0)
                                <flux:badge color="emerald" size="sm" class="shadow-xs !bg-emerald-50/95 backdrop-blur-md">
                                    En stock
                                </flux:badge>
                            @else
                                <flux:badge color="rose" size="sm" class="shadow-xs !bg-rose-50/95 backdrop-blur-md">
                                    {{ __('frontend.product.out_of_stock') }}
                                </flux:badge>
                            @endif
                        </div>

                        {{-- Photo Counter + Fullscreen Zoom Trigger --}}
                        <div class="absolute top-4 right-4 z-10 flex items-center gap-2">
                            @if($galleryImages->count() > 1)
                                <span class="rounded-full bg-slate-900/75 text-white px-2.5 py-1 text-xs font-semibold backdrop-blur-sm tabular-nums">
                                    <span x-text="activeIndex + 1">1</span> / {{ $galleryImages->count() }}
                                </span>
                            @endif
                            <button
                                type="button"
                                @click="openLightbox()"
                                aria-label="Agrandir la photo en plein écran"
                                class="size-9 rounded-full bg-white/90 hover:bg-white text-slate-700 hover:text-primary border border-slate-200 shadow-xs flex items-center justify-center transition-colors cursor-pointer"
                            >
                                <flux:icon.magnifying-glass-plus variant="mini" />
                            </button>
                        </div>

                        {{-- Render ALL Images Preloaded in DOM for Instant Zero-Lag Switching --}}
                        @foreach($galleryImages as $index => $img)
                            <img
                                x-show="activeIndex === {{ $index }}"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-[0.98]"
                                x-transition:enter-end="opacity-100 scale-100"
                                @if($index > 0) x-cloak @endif
                                src="{{ $img['url'] }}"
                                alt="{{ $product->name }} — vue {{ $index + 1 }}"
                                @if($index === 0) fetchpriority="high" @else loading="eager" @endif
                                decoding="async"
                                @click="openLightbox({{ $index }})"
                                class="h-full w-full object-contain p-4 sm:p-6 cursor-zoom-in transition-transform duration-300 group-hover:scale-[1.03]"
                            >
                        @endforeach

                        {{-- Prev / Next Arrows --}}
                        @if($galleryImages->count() > 1)
                            <button
                                type="button"
                                @click="prevImage()"
                                aria-label="Photo précédente"
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-10 size-10 rounded-full bg-white/90 hover:bg-white text-slate-800 border border-slate-200 shadow-md flex items-center justify-center transition-all cursor-pointer"
                            >
                                <flux:icon.chevron-left variant="mini" />
                            </button>
                            <button
                                type="button"
                                @click="nextImage()"
                                aria-label="Photo suivante"
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-10 size-10 rounded-full bg-white/90 hover:bg-white text-slate-800 border border-slate-200 shadow-md flex items-center justify-center transition-all cursor-pointer"
                            >
                                <flux:icon.chevron-right variant="mini" />
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Reassurance Strip under Gallery --}}
                <div class="grid grid-cols-3 gap-2.5">
                    <div class="rounded-xl bg-white border border-slate-200/80 p-3 flex items-center gap-2.5">
                        <flux:icon.shield-check class="size-5 text-primary shrink-0" />
                        <span class="text-xs font-semibold text-slate-700 leading-tight">Produit authentique vérifié</span>
                    </div>
                    <div class="rounded-xl bg-white border border-slate-200/80 p-3 flex items-center gap-2.5">
                        <flux:icon.truck class="size-5 text-secondary shrink-0" />
                        <span class="text-xs font-semibold text-slate-700 leading-tight">Livraison rapide par zone</span>
                    </div>
                    <div class="rounded-xl bg-white border border-slate-200/80 p-3 flex items-center gap-2.5">
                        <flux:icon.qr-code class="size-5 text-emerald-600 shrink-0" />
                        <span class="text-xs font-semibold text-slate-700 leading-tight">Facture QR & suivi en ligne</span>
                    </div>
                </div>
            </div>

            {{-- Right Column (5 cols): Complete Product Details & Conversion --}}
            <div class="lg:col-span-5 flex flex-col">
                {{-- Header Metadata --}}
                <div class="mb-5">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if($product->brand)
                            <flux:badge color="zinc" size="sm">{{ $product->brand->name }}</flux:badge>
                        @endif
                        @if($product->category)
                            <flux:badge color="blue" size="sm">{{ $product->category->name }}</flux:badge>
                        @endif
                        @if($product->reference_number || $product->product_code || $product->model_number)
                            <span class="text-xs text-slate-400 font-mono ml-auto">
                                Réf : {{ $product->reference_number ?? $product->product_code ?? $product->model_number }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-950 tracking-tight leading-tight">
                        {{ $product->name }}
                    </h1>

                    @if($product->short_description)
                        <p class="mt-2.5 text-sm text-slate-600 leading-relaxed">
                            {{ $product->short_description }}
                        </p>
                    @endif
                </div>

                {{-- Conversion Panel: Price, Zone Seller Routing, WhatsApp, Cart, Comparator & Mobile Sticky CTA --}}
                <livewire:product-order-panel :product="$product" />

                {{-- Key Highlights Quick Cards --}}
                @if($product->specifications->isNotEmpty())
                    <div class="rounded-2xl bg-white border border-slate-200/80 p-4 shadow-2xs">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                            Caractéristiques essentielles
                        </h2>
                        <dl class="grid grid-cols-2 gap-2.5">
                            @foreach($product->specifications->take(6) as $spec)
                                <div class="rounded-xl bg-slate-50/90 border border-slate-100 p-3">
                                    <dt class="text-[11px] font-medium text-slate-500">{{ $spec->name }}</dt>
                                    <dd class="text-sm font-bold text-slate-900 mt-0.5">
                                        {{ $spec->pivot->value }}
                                        @if($spec->measure)
                                            <span class="font-normal text-slate-500">{{ $spec->measure }}</span>
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif
            </div>
        </div>

        {{-- Detailed Information Tabs (Specs, Description, Full Photo Gallery) --}}
        <section class="mt-12 rounded-2xl bg-white border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="border-b border-slate-200 px-4 sm:px-6 overflow-x-auto">
                <nav class="-mb-px flex items-center gap-6" aria-label="Détails du produit">
                    @if($product->specifications->isNotEmpty())
                        <button
                            type="button"
                            @click="activeTab = 'specs-key'"
                            class="whitespace-nowrap border-b-2 py-4 text-sm font-semibold transition-colors cursor-pointer"
                            :class="activeTab === 'specs-key' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800'"
                        >
                            {{ __('frontend.product.full_specs') }} ({{ $product->specifications->count() }})
                        </button>
                    @endif

                    <button
                        type="button"
                        @click="activeTab = 'description'"
                        class="whitespace-nowrap border-b-2 py-4 text-sm font-semibold transition-colors cursor-pointer"
                        :class="activeTab === 'description' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    >
                        Description complète
                    </button>

                    <button
                        type="button"
                        @click="activeTab = 'all-photos'"
                        class="whitespace-nowrap border-b-2 py-4 text-sm font-semibold transition-colors cursor-pointer"
                        :class="activeTab === 'all-photos' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800'"
                    >
                        Toutes les photos ({{ $galleryImages->count() }})
                    </button>
                </nav>
            </div>

            <div class="p-5 sm:p-6">
                {{-- Tab 1: Complete Technical Specifications Table --}}
                @if($product->specifications->isNotEmpty())
                    <div x-show="activeTab === 'specs-key'">
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column class="w-1/3">Caractéristique</flux:table.column>
                                <flux:table.column>Détail</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @if($product->brand)
                                    <flux:table.row>
                                        <flux:table.cell variant="strong">Marque</flux:table.cell>
                                        <flux:table.cell>{{ $product->brand->name }}</flux:table.cell>
                                    </flux:table.row>
                                @endif
                                @if($product->category)
                                    <flux:table.row>
                                        <flux:table.cell variant="strong">Catégorie</flux:table.cell>
                                        <flux:table.cell>{{ $product->category->name }}</flux:table.cell>
                                    </flux:table.row>
                                @endif
                                @if($product->model_number)
                                    <flux:table.row>
                                        <flux:table.cell variant="strong">Modèle</flux:table.cell>
                                        <flux:table.cell class="font-mono">{{ $product->model_number }}</flux:table.cell>
                                    </flux:table.row>
                                @endif
                                @if($product->reference_number || $product->product_code)
                                    <flux:table.row>
                                        <flux:table.cell variant="strong">Référence / Code</flux:table.cell>
                                        <flux:table.cell class="font-mono">{{ $product->reference_number ?? $product->product_code }}</flux:table.cell>
                                    </flux:table.row>
                                @endif
                                @foreach($product->specifications as $spec)
                                    <flux:table.row>
                                        <flux:table.cell variant="strong">{{ $spec->name }}</flux:table.cell>
                                        <flux:table.cell>
                                            {{ $spec->pivot->value }}
                                            @if($spec->measure)
                                                <span class="text-slate-500">{{ $spec->measure }}</span>
                                            @endif
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                @endif

                {{-- Tab 2: Detailed Description --}}
                <div x-show="activeTab === 'description'" x-cloak class="prose prose-slate max-w-none text-sm leading-relaxed">
                    @if($product->long_description)
                        {!! $product->long_description !!}
                    @elseif($product->short_description)
                        <p>{{ $product->short_description }}</p>
                    @else
                        <p class="text-slate-500">
                            {{ $product->name }} est disponible chez PanoraGoods. Contactez un conseiller de votre zone via WhatsApp pour obtenir une démonstration ou des conseils personnalisés.
                        </p>
                    @endif
                </div>

                {{-- Tab 3: Complete Photos Grid --}}
                <div x-show="activeTab === 'all-photos'" x-cloak>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($galleryImages as $index => $img)
                            <button
                                type="button"
                                @click="openLightbox({{ $index }})"
                                class="product-image-stage group relative aspect-square rounded-xl border border-slate-200 p-3 overflow-hidden cursor-zoom-in hover:border-primary/40 transition-all"
                            >
                                <img
                                    src="{{ $img['url'] }}"
                                    alt="{{ $product->name }} — galerie {{ $index + 1 }}"
                                    loading="eager"
                                    decoding="async"
                                    class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105"
                                >
                                <span class="absolute bottom-2 right-2 rounded-full bg-slate-900/70 px-2 py-0.5 text-[10px] font-semibold text-white">
                                    #{{ $index + 1 }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- Similar Products --}}
        @if($similarProducts->isNotEmpty())
            <section class="mt-14 pt-8 border-t border-slate-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <flux:heading size="lg" class="!font-extrabold text-slate-950">
                            {{ __('frontend.product.similar_products') }}
                        </flux:heading>
                        <flux:text class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            D'autres modèles dans la même catégorie
                        </flux:text>
                    </div>
                    <flux:button
                        href="{{ route('home', ['category' => $product->category_id]) }}#catalog-section"
                        variant="ghost"
                        size="sm"
                        icon-trailing="arrow-right"
                    >
                        {{ __('frontend.product.view_category') }}
                    </flux:button>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
                    @foreach($similarProducts as $sim)
                        <a
                            href="{{ route('products.show', $sim) }}"
                            class="product-card group flex flex-col rounded-2xl border border-slate-200/80 bg-white overflow-hidden shadow-xs hover:border-primary/35"
                        >
                            <div class="product-image-stage aspect-square p-3 relative border-b border-slate-100">
                                <img
                                    src="{{ $sim->primary_image_url }}"
                                    alt="{{ $sim->name }}"
                                    loading="lazy"
                                    decoding="async"
                                    class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-105"
                                >
                            </div>
                            <div class="p-4 flex flex-col flex-1">
                                <span class="text-[11px] font-medium text-slate-400">{{ $sim->brand?->name ?? $sim->category?->name }}</span>
                                <h3 class="text-sm font-bold text-slate-900 line-clamp-2 mt-0.5 mb-2 group-hover:text-primary transition-colors">
                                    {{ $sim->name }}
                                </h3>
                                <p class="text-base font-extrabold text-slate-950 mt-auto">
                                    {{ format_xaf($sim->selling_price) }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Fullscreen Interactive Lightbox Modal --}}
        <div
            x-show="lightboxOpen"
            x-cloak
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-50 bg-slate-950/95 backdrop-blur-md flex flex-col"
            role="dialog"
            aria-modal="true"
            aria-label="Galerie photo plein écran"
        >
            {{-- Top Bar --}}
            <div class="flex items-center justify-between px-4 sm:px-6 py-4 text-white">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold tabular-nums">
                        <span x-text="activeIndex + 1">1</span> / {{ $galleryImages->count() }}
                    </span>
                    <span class="text-sm font-semibold truncate">{{ $product->name }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="zoomed = !zoomed"
                        class="rounded-lg bg-white/10 hover:bg-white/20 px-3 py-1.5 text-xs font-semibold transition-colors cursor-pointer"
                    >
                        <span x-text="zoomed ? 'Zoom 1×' : 'Zoom 2×'">Zoom 2×</span>
                    </button>
                    <button
                        type="button"
                        @click="closeLightbox()"
                        aria-label="Fermer la galerie"
                        class="size-9 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <flux:icon.x-mark variant="mini" />
                    </button>
                </div>
            </div>

            {{-- Center Image Canvas --}}
            <div class="relative flex-1 flex items-center justify-center overflow-auto p-4 sm:p-8" @click.self="closeLightbox()">
                @if($galleryImages->count() > 1)
                    <button
                        type="button"
                        @click="prevImage()"
                        aria-label="Photo précédente"
                        class="fixed left-4 top-1/2 -translate-y-1/2 z-10 size-11 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <flux:icon.chevron-left />
                    </button>
                @endif

                <img
                    :src="currentImage.url"
                    :alt="currentImage.alt"
                    @click="zoomed = !zoomed"
                    class="max-h-[75vh] max-w-full object-contain rounded-xl transition-transform duration-300"
                    :class="zoomed ? 'scale-150 cursor-zoom-out' : 'scale-100 cursor-zoom-in'"
                >

                @if($galleryImages->count() > 1)
                    <button
                        type="button"
                        @click="nextImage()"
                        aria-label="Photo suivante"
                        class="fixed right-4 top-1/2 -translate-y-1/2 z-10 size-11 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center transition-colors cursor-pointer"
                    >
                        <flux:icon.chevron-right />
                    </button>
                @endif
            </div>

            {{-- Bottom Thumbnails Strip --}}
            @if($galleryImages->count() > 1)
                <div class="px-4 py-3 border-t border-white/10 flex items-center justify-center gap-2 overflow-x-auto">
                    @foreach($galleryImages as $index => $img)
                        <button
                            type="button"
                            @click="selectImage({{ $index }})"
                            class="size-14 shrink-0 rounded-lg bg-white p-1 border-2 transition-all cursor-pointer"
                            :class="activeIndex === {{ $index }} ? 'border-secondary scale-105' : 'border-transparent opacity-60 hover:opacity-100'"
                        >
                            <img src="{{ $img['url'] }}" alt="Miniature {{ $index + 1 }}" class="h-full w-full object-contain">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
