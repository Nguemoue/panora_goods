@props([
    'featuredProducts' => collect(),
])

@php
    $slides = collect([
        [
            'badge' => 'Nouveautés High-Tech',
            'badge_color' => 'amber',
            'title' => 'Smartphones & Accessoires Certifiés',
            'subtitle' => 'Commandez en 1 clic sur WhatsApp, payez en toute confiance et suivez votre livraison en temps réel.',
            'cta_label' => 'Explorer le catalogue',
            'cta_href' => '#catalog-section',
            'secondary_label' => 'Suivre ma commande',
            'secondary_href' => route('track.order'),
            'gradient' => 'from-slate-950 via-blue-950 to-primary',
            'accent' => 'bg-secondary/20',
            'image' => $featuredProducts->first()?->primary_image_url,
            'product_name' => $featuredProducts->first()?->name,
            'product_price' => $featuredProducts->first() ? format_xaf($featuredProducts->first()->selling_price) : null,
            'product_href' => $featuredProducts->first() ? route('products.show', $featuredProducts->first()) : null,
        ],
        [
            'badge' => 'Livraison Rapide par Zone',
            'badge_color' => 'emerald',
            'title' => 'Un conseiller vérifié dans votre quartier',
            'subtitle' => 'Choisissez votre zone et discutez directement avec un vendeur agréé PanoraGoods. Vérifiez son matricule à tout moment.',
            'cta_label' => 'Vérifier un employé',
            'cta_href' => route('employee-verification'),
            'secondary_label' => 'Voir les produits',
            'secondary_href' => '#catalog-section',
            'gradient' => 'from-slate-900 via-slate-800 to-blue-950',
            'accent' => 'bg-emerald-500/20',
            'image' => $featuredProducts->skip(1)->first()?->primary_image_url,
            'product_name' => $featuredProducts->skip(1)->first()?->name,
            'product_price' => $featuredProducts->skip(1)->first() ? format_xaf($featuredProducts->skip(1)->first()->selling_price) : null,
            'product_href' => $featuredProducts->skip(1)->first() ? route('products.show', $featuredProducts->skip(1)->first()) : null,
        ],
        [
            'badge' => 'Commande Groupée WhatsApp',
            'badge_color' => 'sky',
            'title' => 'Composez votre panier & comparez vos modèles',
            'subtitle' => 'Ajoutez téléphone, chargeur et accessoires dans un devis WhatsApp unique ou comparez jusqu’à 3 fiches techniques.',
            'cta_label' => 'Découvrir les offres',
            'cta_href' => '#catalog-section',
            'secondary_label' => 'Nous contacter',
            'secondary_href' => route('contact'),
            'gradient' => 'from-blue-950 via-indigo-950 to-slate-950',
            'accent' => 'bg-blue-400/20',
            'image' => $featuredProducts->skip(2)->first()?->primary_image_url,
            'product_name' => $featuredProducts->skip(2)->first()?->name,
            'product_price' => $featuredProducts->skip(2)->first() ? format_xaf($featuredProducts->skip(2)->first()->selling_price) : null,
            'product_href' => $featuredProducts->skip(2)->first() ? route('products.show', $featuredProducts->skip(2)->first()) : null,
        ],
    ]);
@endphp

<section
    aria-label="Offres à la une"
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 pb-2"
    x-data="{
        active: 0,
        total: {{ $slides->count() }},
        paused: false,
        progress: 0,
        touchStartX: 0,
        intervalId: null,
        init() {
            const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (!reduced) {
                this.intervalId = setInterval(() => {
                    if (this.paused) return;
                    this.progress += 2;
                    if (this.progress >= 100) {
                        this.next();
                    }
                }, 120);
            }
        },
        destroy() {
            if (this.intervalId) clearInterval(this.intervalId);
        },
        next() {
            this.active = (this.active + 1) % this.total;
            this.progress = 0;
        },
        prev() {
            this.active = (this.active - 1 + this.total) % this.total;
            this.progress = 0;
        },
        goTo(index) {
            this.active = index;
            this.progress = 0;
        },
        onTouchStart(e) {
            this.touchStartX = e.changedTouches[0].screenX;
        },
        onTouchEnd(e) {
            const diff = e.changedTouches[0].screenX - this.touchStartX;
            if (Math.abs(diff) > 40) {
                diff < 0 ? this.next() : this.prev();
            }
        }
    }"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
    @touchstart.passive="onTouchStart($event)"
    @touchend.passive="onTouchEnd($event)"
>
    <div class="hero-banner-16-3 shadow-lg ring-1 ring-slate-900/10 bg-slate-950">
        @foreach($slides as $index => $slide)
            <div
                x-show="active === {{ $index }}"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 scale-[1.01]"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @if($index > 0) x-cloak @endif
                class="absolute inset-0 bg-gradient-to-r {{ $slide['gradient'] }} flex items-center"
            >
                {{-- Ambient Glow --}}
                <div class="pointer-events-none absolute -right-12 -top-12 size-64 rounded-full blur-3xl {{ $slide['accent'] }}"></div>
                <div class="pointer-events-none absolute left-1/3 -bottom-16 size-56 rounded-full blur-3xl bg-blue-500/10"></div>

                <div class="relative z-10 w-full px-5 sm:px-8 lg:px-10 py-4 flex items-center justify-between gap-6">
                    {{-- Left Content --}}
                    <div class="max-w-2xl space-y-2.5 sm:space-y-3">
                        <div class="flex items-center gap-2">
                            <flux:badge size="sm" :color="$slide['badge_color']" variant="solid" class="shadow-sm">
                                {{ $slide['badge'] }}
                            </flux:badge>
                            <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-medium text-blue-100/80">
                                <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Stock vérifié en direct
                            </span>
                        </div>

                        <h1 class="text-lg sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight leading-tight line-clamp-2">
                            {{ $slide['title'] }}
                        </h1>

                        <p class="hidden sm:block text-xs lg:text-sm text-slate-200/90 max-w-xl line-clamp-2">
                            {{ $slide['subtitle'] }}
                        </p>

                        <div class="flex flex-wrap items-center gap-2.5 pt-1">
                            <flux:button href="{{ $slide['cta_href'] }}" variant="primary" size="sm" icon-trailing="arrow-right" class="!bg-secondary hover:!bg-secondary-hover !border-0 shadow-md">
                                {{ $slide['cta_label'] }}
                            </flux:button>
                            <flux:button href="{{ $slide['secondary_href'] }}" variant="ghost" size="sm" class="!text-white hover:!bg-white/15">
                                {{ $slide['secondary_label'] }}
                            </flux:button>
                        </div>
                    </div>

                    {{-- Right Product Spotlight (Desktop / Tablet) --}}
                    @if($slide['image'] && $slide['product_href'])
                        <a
                            href="{{ $slide['product_href'] }}"
                            class="hidden md:flex items-center gap-4 shrink-0 rounded-2xl bg-white/12 hover:bg-white/18 backdrop-blur-md border border-white/20 p-3 pr-5 transition-all duration-300 group max-w-sm shadow-lg"
                        >
                            <div class="size-24 lg:size-32 shrink-0 rounded-xl bg-white p-2 flex items-center justify-center shadow-md overflow-hidden">
                                <img
                                    src="{{ $slide['image'] }}"
                                    alt="{{ $slide['product_name'] }}"
                                    @if($index === 0) fetchpriority="high" @else loading="lazy" @endif
                                    decoding="async"
                                    class="h-full w-full object-contain transition-transform duration-300 group-hover:scale-108"
                                >
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-blue-200">À la une</span>
                                <p class="text-sm lg:text-base font-bold text-white line-clamp-2 leading-snug mt-0.5">
                                    {{ $slide['product_name'] }}
                                </p>
                                <p class="text-sm lg:text-lg font-extrabold text-amber-300 mt-1.5">
                                    {{ $slide['product_price'] }}
                                </p>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Prev / Next Controls --}}
        <button
            type="button"
            @click="prev()"
            aria-label="Slide précédente"
            class="hidden sm:flex absolute left-2.5 top-1/2 -translate-y-1/2 z-20 size-8 items-center justify-center rounded-full bg-black/30 hover:bg-black/55 text-white backdrop-blur-sm transition-colors cursor-pointer"
        >
            <flux:icon.chevron-left variant="micro" />
        </button>
        <button
            type="button"
            @click="next()"
            aria-label="Slide suivante"
            class="hidden sm:flex absolute right-2.5 top-1/2 -translate-y-1/2 z-20 size-8 items-center justify-center rounded-full bg-black/30 hover:bg-black/55 text-white backdrop-blur-sm transition-colors cursor-pointer"
        >
            <flux:icon.chevron-right variant="micro" />
        </button>

        {{-- Synchronized Progress Indicators --}}
        <div class="absolute bottom-2.5 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
            @foreach($slides as $index => $slide)
                <button
                    type="button"
                    @click="goTo({{ $index }})"
                    aria-label="Afficher la slide {{ $index + 1 }}"
                    class="relative h-1.5 rounded-full overflow-hidden transition-all duration-300 cursor-pointer"
                    :class="active === {{ $index }} ? 'w-9 bg-white/30' : 'w-2.5 bg-white/35 hover:bg-white/60'"
                >
                    <span
                        x-show="active === {{ $index }}"
                        class="absolute inset-y-0 left-0 bg-white rounded-full transition-all duration-150"
                        :style="`width: ${progress}%`"
                    ></span>
                </button>
            @endforeach
        </div>
    </div>
</section>
