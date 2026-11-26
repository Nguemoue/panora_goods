@extends('layouts.app')

@section('title', 'Témoignages Clients')

@php
    $reviews = [
        [
            'name' => 'Christian M.',
            'initials' => 'CM',
            'city' => 'Douala',
            'zone' => 'Akwa',
            'category' => 'Smartphones',
            'product' => 'iPhone 15 Pro Max 256 Go',
            'date' => 'Il y a 4 jours',
            'quote' => 'J\'ai vérifié le matricule du conseiller directement sur le site avant notre rendez-vous. Téléphone neuf scellé, facture avec QR code remise immédiatement et suivi impeccable.',
            'avatar_bg' => 'bg-blue-100 text-primary',
        ],
        [
            'name' => 'Nadine T.',
            'initials' => 'NT',
            'city' => 'Yaoundé',
            'zone' => 'Bastos',
            'category' => 'Smartphones',
            'product' => 'Samsung Galaxy S24 Ultra',
            'date' => 'Il y a 1 semaine',
            'quote' => 'Le panier WhatsApp groupé m\'a permis de commander mon téléphone, la coque et le chargeur en un seul message. Livraison le jour même dans ma zone par un vendeur très courtois.',
            'avatar_bg' => 'bg-emerald-100 text-emerald-700',
        ],
        [
            'name' => 'Brice K.',
            'initials' => 'BK',
            'city' => 'Douala',
            'zone' => 'Bonapriso',
            'category' => 'Smartphones',
            'product' => 'iPhone 14 & AirPods Pro',
            'date' => 'Il y a 2 semaines',
            'quote' => 'Pouvoir suivre chacun de mes acomptes en ligne avec le code de suivi et télécharger ma facture PDF est un vrai gage de sérieux. Zéro mauvaise surprise.',
            'avatar_bg' => 'bg-amber-100 text-amber-800',
        ],
        [
            'name' => 'Stéphanie N.',
            'initials' => 'SN',
            'city' => 'Yaoundé',
            'zone' => 'Essos',
            'category' => 'Accessoires',
            'product' => 'Pack Chargeur Rapide + Écouteurs',
            'date' => 'Il y a 2 semaines',
            'quote' => 'Produits d\'origine testés devant moi à la livraison. Le comparateur sur le site m\'a aidée à choisir sans me tromper.',
            'avatar_bg' => 'bg-indigo-100 text-indigo-700',
        ],
        [
            'name' => 'Hervé D.',
            'initials' => 'HD',
            'city' => 'Douala',
            'zone' => 'Makepe',
            'category' => 'Smartphones',
            'product' => 'Samsung Galaxy A55 5G',
            'date' => 'Il y a 3 semaines',
            'quote' => 'Très bon rapport qualité-prix en FCFA et réponse sur WhatsApp en moins de 5 minutes. Le conseiller de Makepe a été ponctuel et professionnel.',
            'avatar_bg' => 'bg-sky-100 text-sky-700',
        ],
        [
            'name' => 'Aïcha B.',
            'initials' => 'AB',
            'city' => 'Yaoundé',
            'zone' => 'Mvan',
            'category' => 'Smartphones',
            'product' => 'iPhone 13 128 Go',
            'date' => 'Il y a 1 mois',
            'quote' => 'Deuxième commande chez PanoraGoods et toujours la même qualité de service. J\'apprécie particulièrement la transparence sur les garanties et les prix.',
            'avatar_bg' => 'bg-rose-100 text-rose-700',
        ],
    ];
@endphp

@section('content')
    <div
        class="space-y-10 pb-8"
        x-data="{
            filter: 'all',
            matches(city, category) {
                return this.filter === 'all' || this.filter === city || this.filter === category;
            }
        }"
    >
        {{-- Breadcrumbs --}}
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" icon="home">Accueil</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Témoignages</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        {{-- Hero Header + KPI Strip --}}
        <section class="rounded-3xl bg-gradient-to-br from-slate-950 via-blue-950 to-primary text-white p-7 sm:p-10 shadow-lg relative overflow-hidden">
            <div class="pointer-events-none absolute -right-16 -top-16 size-64 rounded-full bg-secondary/20 blur-3xl"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-3">
                    <flux:badge size="sm" color="amber" variant="solid">Avis Clients Vérifiés</flux:badge>
                    <h1 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight">
                        La confiance de nos clients, notre meilleure garantie
                    </h1>
                    <p class="text-sm sm:text-base text-slate-200 max-w-2xl leading-relaxed">
                        Découvrez les retours d'expérience de nos acheteurs livrés par nos conseillers certifiés à travers nos zones de couverture.
                    </p>
                </div>

                <div class="lg:col-span-5 grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 p-4 text-center">
                        <p class="font-display text-2xl sm:text-3xl font-extrabold text-amber-300">4,9/5</p>
                        <p class="text-[11px] text-slate-200 mt-1">Note moyenne</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 p-4 text-center">
                        <p class="font-display text-2xl sm:text-3xl font-extrabold text-white">98%</p>
                        <p class="text-[11px] text-slate-200 mt-1">Recommandent</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 p-4 text-center">
                        <p class="font-display text-2xl sm:text-3xl font-extrabold text-emerald-300">100%</p>
                        <p class="text-[11px] text-slate-200 mt-1">Vendeurs vérifiés</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Interactive Filter Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2">
                @foreach([
                    'all' => 'Tous les avis',
                    'Douala' => 'Douala',
                    'Yaoundé' => 'Yaoundé',
                    'Smartphones' => 'Smartphones',
                    'Accessoires' => 'Accessoires',
                ] as $key => $label)
                    <button
                        type="button"
                        @click="filter = '{{ $key }}'"
                        class="rounded-full px-4 py-1.5 text-xs font-semibold border transition-all cursor-pointer"
                        :class="filter === '{{ $key }}' ? 'bg-primary text-white border-primary shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:border-primary/40 hover:text-primary'"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <flux:button href="{{ route('employee-verification') }}" variant="ghost" size="sm" icon="identification">
                Vérifier un conseiller
            </flux:button>
        </div>

        {{-- Reviews Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($reviews as $review)
                <article
                    x-show="matches('{{ $review['city'] }}', '{{ $review['category'] }}')"
                    x-transition.opacity.duration.200ms
                    class="bento-card flex flex-col justify-between rounded-2xl bg-white border border-slate-200/80 p-6 shadow-xs"
                >
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <div class="flex items-center gap-0.5 text-amber-400">
                                @for($s = 0; $s < 5; $s++)
                                    <flux:icon.star variant="solid" class="size-4" />
                                @endfor
                            </div>
                            <flux:badge size="sm" color="emerald" icon="check-badge">Achat vérifié</flux:badge>
                        </div>

                        <p class="text-sm text-slate-700 leading-relaxed">
                            « {{ $review['quote'] }} »
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                            <span class="font-semibold text-primary">{{ $review['product'] }}</span>
                            <span>{{ $review['date'] }}</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-full {{ $review['avatar_bg'] }} flex items-center justify-center font-display text-xs font-extrabold shrink-0">
                                {{ $review['initials'] }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-950 truncate">{{ $review['name'] }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $review['city'] }} — {{ $review['zone'] }}</p>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Bottom Conversion Callout --}}
        <section class="rounded-3xl bg-white border border-slate-200/90 p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-xs">
            <div class="max-w-xl">
                <flux:heading size="lg" class="!font-extrabold text-slate-950">
                    Prêt à commander votre prochain smartphone en toute sécurité ?
                </flux:heading>
                <flux:text class="text-sm text-slate-500 mt-1">
                    Parcourez nos modèles disponibles, comparez leurs fiches techniques et passez commande en 1 clic sur WhatsApp.
                </flux:text>
            </div>
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <flux:button href="{{ route('home') }}#catalog-section" variant="primary" icon-trailing="arrow-right" class="!bg-primary hover:!bg-primary-hover">
                    Explorer le catalogue
                </flux:button>
                <flux:button href="{{ route('contact') }}" variant="outline" icon="chat-bubble-left-ellipsis">
                    Nous contacter
                </flux:button>
            </div>
        </section>
    </div>
@endsection
