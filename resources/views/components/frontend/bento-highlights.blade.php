@props([
    'categories' => collect(),
])

<section aria-label="Avantages et catégories phares" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5">
        {{-- Pillar 1: Fast Delivery & Verified Advisors --}}
        <div class="bento-card md:col-span-4 bg-white border border-slate-200/80 p-4 flex items-center gap-3.5">
            <div class="size-11 shrink-0 rounded-xl bg-blue-50 text-primary flex items-center justify-center">
                <flux:icon.shield-check class="size-6" />
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-bold text-slate-900">Vendeurs Vérifiés par Zone</h2>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                    Contrôlez l'identité de votre conseiller avant paiement.
                </p>
            </div>
        </div>

        {{-- Pillar 2: WhatsApp Multi-Item Quote --}}
        <div class="bento-card md:col-span-4 bg-white border border-slate-200/80 p-4 flex items-center gap-3.5">
            <div class="size-11 shrink-0 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <flux:icon.chat-bubble-left-right class="size-6" />
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-bold text-slate-900">Panier WhatsApp 1-Clic</h2>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                    Groupez vos articles et recevez une réponse immédiate.
                </p>
            </div>
        </div>

        {{-- Pillar 3: Live Tracking & Barcode Invoice --}}
        <div class="bento-card md:col-span-4 bg-white border border-slate-200/80 p-4 flex items-center gap-3.5">
            <div class="size-11 shrink-0 rounded-xl bg-amber-50 text-secondary flex items-center justify-center">
                <flux:icon.truck class="size-6" />
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-bold text-slate-900">Suivi & Facture QR Code</h2>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                    Historique des acomptes et suivi transparent en ligne.
                </p>
            </div>
        </div>
    </div>

    {{-- Quick Category Bento Strip --}}
    @if($categories->isNotEmpty())
        <div class="mt-3.5 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 shrink-0 mr-1">
                Accès rapide :
            </span>
            @foreach($categories->take(8) as $cat)
                <a
                    href="{{ route('home', ['category' => $cat->id]) }}#catalog-section"
                    class="inline-flex items-center gap-1.5 shrink-0 rounded-full bg-white border border-slate-200/90 hover:border-primary/40 hover:bg-blue-50/50 px-3 py-1.5 text-xs font-medium text-slate-700 hover:text-primary transition-colors shadow-2xs"
                >
                    <span>{{ $cat->name }}</span>
                    <span class="rounded-full bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-500">
                        {{ $cat->products_count }}
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</section>
