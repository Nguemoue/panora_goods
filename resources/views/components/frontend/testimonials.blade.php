@php
    $testimonials = [
        [
            'name' => 'Christian M.',
            'initials' => 'CM',
            'location' => 'Douala — Akwa',
            'product' => 'iPhone 15 Pro Max',
            'quote' => 'J\'ai vérifié le matricule du conseiller directement sur le site avant ma rencontre. Téléphone scellé, facture avec QR code remise sur place et suivi impeccable. Je recommande à 100 %.',
            'avatar_bg' => 'bg-blue-100 dark:bg-sky-500/20 text-primary dark:text-sky-300',
        ],
        [
            'name' => 'Nadine T.',
            'initials' => 'NT',
            'location' => 'Yaoundé — Bastos',
            'product' => 'Samsung Galaxy S24 Ultra',
            'quote' => 'La commande groupée via WhatsApp est ultra pratique : j\'ai pris mon téléphone et les accessoires dans un seul message, et j\'ai été livrée le jour même dans ma zone.',
            'avatar_bg' => 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300',
        ],
        [
            'name' => 'Brice K.',
            'initials' => 'BK',
            'location' => 'Douala — Bonapriso',
            'product' => 'iPhone 14 & AirPods Pro',
            'quote' => 'J\'ai adoré pouvoir suivre mes acomptes en temps réel avec mon code de suivi et télécharger ma facture PDF à tout moment. Sérieux, transparent et très professionnel.',
            'avatar_bg' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300',
        ],
    ];
@endphp

<section aria-label="Témoignages clients" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-5 sm:p-8 lg:p-10 shadow-xs">
        {{-- Section Header --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 mb-8">
            <div class="max-w-2xl">
                <flux:badge size="sm" color="amber" class="mb-3">
                    Avis Clients Vérifiés
                </flux:badge>
                <flux:heading size="xl" class="!font-extrabold text-slate-950 dark:text-white">
                    Ils nous font confiance pour leurs achats High-Tech
                </flux:heading>
                <flux:text class="text-sm text-slate-500 dark:text-slate-400 mt-1.5">
                    Des centaines de clients livrés en toute sérénité grâce à nos vendeurs certifiés par zone et notre suivi transparent.
                </flux:text>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <div class="flex items-center gap-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700 px-4 py-3">
                    <div class="flex items-center gap-0.5 text-amber-400" aria-label="Note moyenne 4,9 sur 5">
                        @for($s = 0; $s < 5; $s++)
                            <flux:icon.star variant="solid" class="size-4" />
                        @endfor
                    </div>
                    <div class="text-xs">
                        <span class="font-extrabold text-slate-950 dark:text-white text-sm">4,9 / 5</span>
                        <span class="text-slate-500 dark:text-slate-400 block">Satisfaction client</span>
                    </div>
                </div>
                <flux:button href="{{ route('testimonials') }}" variant="outline" size="sm" icon-trailing="arrow-right">
                    Tous les avis
                </flux:button>
            </div>
        </div>

        {{-- Testimonials Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
            @foreach($testimonials as $testimonial)
                <article class="bento-card flex flex-col justify-between rounded-2xl bg-slate-50/70 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-800 p-5 sm:p-6">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <div class="flex items-center gap-0.5 text-amber-400">
                                @for($s = 0; $s < 5; $s++)
                                    <flux:icon.star variant="solid" class="size-3.5" />
                                @endfor
                            </div>
                            <flux:badge size="sm" color="emerald" icon="check-badge">
                                Achat vérifié
                            </flux:badge>
                        </div>

                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">
                            « {{ $testimonial['quote'] }} »
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-200/70 dark:border-slate-700/80 flex items-center gap-3">
                        <div class="size-10 rounded-full {{ $testimonial['avatar_bg'] }} flex items-center justify-center font-display text-xs font-extrabold shrink-0">
                            {{ $testimonial['initials'] }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-950 dark:text-white truncate">{{ $testimonial['name'] }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                {{ $testimonial['location'] }} · <span class="font-medium text-slate-700 dark:text-slate-300">{{ $testimonial['product'] }}</span>
                            </p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
