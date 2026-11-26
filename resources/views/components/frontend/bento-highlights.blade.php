@props([
    'categories' => collect(),
])

<section aria-label="Avantages et catégories phares" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
        {{-- Pillar 1: Fast Delivery & Verified Advisors --}}
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 flex items-center gap-3.5">
            <div class="size-11 shrink-0 rounded-xl bg-blue-50 dark:bg-sky-500/15 text-primary dark:text-sky-400 flex items-center justify-center">
                <flux:icon.shield-check class="size-6" />
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Vendeurs Vérifiés par Zone</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                    Contrôlez l'identité de votre conseiller avant paiement.
                </p>
            </div>
        </div>

        {{-- Pillar 2: WhatsApp Multi-Item Quote --}}
        <div class="bento-card bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 flex items-center gap-3.5">
            <div class="size-11 shrink-0 rounded-xl bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                <flux:icon.chat-bubble-left-right class="size-6" />
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Panier WhatsApp 1-Clic</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                    Groupez vos articles et recevez une réponse immédiate.
                </p>
            </div>
        </div>

        {{-- Pillar 3: Live Tracking & Barcode Invoice --}}
        <div class="bento-card sm:col-span-2 lg:col-span-1 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-4 flex items-center gap-3.5">
            <div class="size-11 shrink-0 rounded-xl bg-amber-50 dark:bg-amber-500/15 text-secondary flex items-center justify-center">
                <flux:icon.truck class="size-6" />
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Suivi & Facture QR Code</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                    Historique des acomptes et suivi transparent en ligne.
                </p>
            </div>
        </div>
    </div>

    {{-- Quick Category Bento Strip --}}
    @if($categories->isNotEmpty())
        <div class="mt-3.5 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 shrink-0 mr-1">
                Accès rapide :
            </span>
            @foreach($categories->take(8) as $cat)
                <a
                    href="{{ route('home', ['category' => $cat->id]) }}#catalog-section"
                    class="inline-flex items-center gap-1.5 shrink-0 rounded-full bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 hover:border-primary/40 dark:hover:border-sky-500/50 hover:bg-blue-50/50 dark:hover:bg-slate-800 px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:text-primary dark:hover:text-sky-300 transition-colors shadow-2xs"
                >
                    <span>{{ $cat->name }}</span>
                    <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 text-[10px] font-semibold text-slate-500 dark:text-slate-400">
                        {{ $cat->products_count }}
                    </span>
                </a>
            @endforeach
        </div>
    @endif
</section>
