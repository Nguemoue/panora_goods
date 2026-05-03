@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="min-h-screen bg-slate-50 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Breadcrumbs (Fil d'ariane minimaliste) -->
            <nav class="flex text-sm mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 text-slate-500">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="inline-flex items-center hover:text-slate-900 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Catalogue
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            <a href="{{ route('home', ['category' => $product->category_id]) }}" class="ml-1 md:ml-2 hover:text-slate-900 transition-colors">{{ $product->category->name }}</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            <span class="ml-1 md:ml-2 font-medium text-slate-900 truncate max-w-[200px] sm:max-w-xs">{{ $product->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16">

                <!-- Colonne Gauche : Galerie Sticky -->
                <div class="lg:sticky lg:top-24 lg:h-max mb-10 lg:mb-0">
                    <div class="flex flex-col-reverse lg:flex-row gap-4">

                        <!-- Miniatures (Vertical sur Desktop, Horizontal sur Mobile) -->
                        @if($product->images->count() > 1)
                            <div class="flex lg:flex-col gap-3 overflow-x-auto lg:overflow-y-auto no-scrollbar py-1 lg:w-20 shrink-0">
                                @foreach($product->images as $index => $image)
                                    <button type="button"
                                            data-src="{{ asset('storage/' . $image->path) }}"
                                            class="product-thumb relative flex-shrink-0 w-20 h-20 lg:w-full lg:h-20 rounded-md border-2 overflow-hidden bg-white transition-all duration-200 focus:outline-none {{ $index === 0 ? 'border-blue-600 ring-2 ring-blue-600 ring-offset-2' : 'border-transparent hover:border-slate-300' }}">
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="Miniature" class="w-full h-full object-contain p-2">
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <!-- Image Principale -->
                        <div class="flex-1 w-full bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm aspect-square md:aspect-[4/3] lg:aspect-square relative group">
                            @php $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first(); @endphp

                                <!-- Badge Stock sur l'image -->
                            <div class="absolute top-4 left-4 z-10">
                                @if($product->stock_quantity > 0)
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-100/80 px-2.5 py-1.5 text-xs font-semibold text-emerald-800 backdrop-blur-md ring-1 ring-inset ring-emerald-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> En stock ({{ $product->stock_quantity }})
                                </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-rose-100/80 px-2.5 py-1.5 text-xs font-semibold text-rose-800 backdrop-blur-md ring-1 ring-inset ring-rose-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Rupture
                                </span>
                                @endif
                            </div>

                            <img id="product-main-image" src="{{ $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://placehold.co/800x1000?text=' . urlencode($product->name) }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-8 transition-opacity duration-300">
                        </div>
                    </div>
                </div>

                <!-- Colonne Droite : Informations Produit -->
                <div class="flex flex-col">

                    <!-- En-tête du produit -->
                    <div class="mb-6">
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            @if($product->brand)
                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-800">{{ $product->brand->name }}</span>
                            @endif
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">{{ $product->category?->name ?? 'Catégorie' }}</span>
                            @if($product->model_number)
                                <span class="text-xs text-slate-400 font-mono ml-auto">Réf: {{ $product->model_number }}</span>
                            @endif
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-950 tracking-tight leading-tight">{{ $product->name }}</h1>
                    </div>

                    <!-- Bloc Prix et CTA -->
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm mb-8">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Prix de référence</p>
                                <div class="flex items-end gap-3">
                                    <span class="text-4xl font-black text-slate-950 tracking-tight">{{ Number::currency($product->selling_price, 'XAF') }}</span>
                                    @if($product->supplier_price)
                                        <span class="text-lg text-slate-400 line-through mb-1">{{ Number::currency($product->supplier_price, 'XAF') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 shrink-0">
                                <!-- Bouton Primaire façon shadcn -->
                                <a href="{{ route('contact') }}?product={{ $product->id }}" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-8 py-3.5 text-sm font-medium text-white transition-colors hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 shadow-sm w-full sm:w-auto">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    Demander un devis
                                </a>
                                <p class="text-[11px] text-slate-400 text-center">Catalogue uniquement. Pas d'achat direct.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Système d'onglets pour les spécifications (Dynamique) -->
                    @if($product->specifications->count() > 0)
                        <div class="mb-10">
                            <div class="border-b border-slate-200">
                                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                                    <button type="button" onclick="switchTab('specs-key')" id="tab-specs-key" class="tab-btn whitespace-nowrap border-b-2 border-slate-900 py-4 px-1 text-sm font-semibold text-slate-900 transition-colors">
                                        Caractéristiques clés
                                    </button>
                                    <button type="button" onclick="switchTab('specs-full')" id="tab-specs-full" class="tab-btn whitespace-nowrap border-b-2 border-transparent py-4 px-1 text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700 transition-colors">
                                        Fiche technique complète
                                    </button>
                                </nav>
                            </div>

                            <div class="mt-6">
                                <!-- Contenu Onglet 1 : Clés (Grille) -->
                                <div id="content-specs-key" class="tab-content grid grid-cols-2 gap-4">
                                    @foreach($product->specifications->take(6) as $spec)
                                        <div class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm">
                                            <dt class="text-xs font-medium text-slate-500 mb-1">{{ $spec->name }}</dt>
                                            <dd class="text-sm font-bold text-slate-900">{{ $spec->pivot->value }} <span class="font-normal text-slate-500">{{ $spec->measure }}</span></dd>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Contenu Onglet 2 : Complètes (Tableau structuré) -->
                                <div id="content-specs-full" class="tab-content hidden rounded-xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                                    <dl class="divide-y divide-slate-100">
                                        @foreach($product->specifications as $index => $spec)
                                            <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 {{ $index % 2 == 0 ? 'bg-slate-50/50' : 'bg-white' }}">
                                                <dt class="text-sm font-medium text-slate-500">{{ $spec->name }}</dt>
                                                <dd class="mt-1 text-sm font-semibold text-slate-900 sm:col-span-2 sm:mt-0">{{ $spec->pivot->value }} <span class="font-normal text-slate-500">{{ $spec->measure }}</span></dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Section Produits Similaires -->
            @if($similarProducts->count() > 0)
                <div class="mt-16 pt-10 border-t border-slate-200">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-slate-950 tracking-tight">Produits similaires</h2>
                        <a href="{{ route('home', ['category' => $product->category_id]) }}" class="hidden sm:inline-flex text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors">
                            Voir toute la catégorie →
                        </a>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
                        @foreach($similarProducts as $sim)
                            @php $img = $sim->images->where('is_primary', true)->first() ?? $sim->images->first(); @endphp
                            <a href="{{ route('products.show', $sim) }}" class="group flex flex-col rounded-xl border border-slate-200 bg-white overflow-hidden hover:shadow-md hover:border-blue-200 transition-all">
                                <div class="aspect-square bg-slate-50 p-4 relative">
                                    <img src="{{ $img ? asset('storage/' . $img->path) : 'https://placehold.co/400x400?text=' . urlencode($sim->name) }}" alt="{{ $sim->name }}" class="w-full h-full object-contain mix-blend-multiply transition-transform duration-300 group-hover:scale-105">
                                </div>
                                <div class="p-4 flex flex-col flex-1 border-t border-slate-100">
                                    <h3 class="text-sm font-semibold text-slate-900 line-clamp-2 mb-2 group-hover:text-blue-600 transition-colors">{{ $sim->name }}</h3>
                                    <p class="text-sm font-bold text-slate-900 mt-auto">{{ Number::currency($sim->selling_price, 'XAF') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Scripts pour la dynamisation UI -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* --- 1. Logique de la Galerie d'images --- */
            const mainImage = document.getElementById('product-main-image');
            const thumbnails = document.querySelectorAll('.product-thumb');

            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    // Récupérer la nouvelle source
                    const newSrc = this.getAttribute('data-src');

                    // Effet de fondu simple
                    mainImage.style.opacity = '0.5';
                    setTimeout(() => {
                        mainImage.src = newSrc;
                        mainImage.style.opacity = '1';
                    }, 150);

                    // Gérer les états actifs (Bordures bleues façon shadcn)
                    thumbnails.forEach(t => {
                        t.classList.remove('border-blue-600', 'ring-2', 'ring-blue-600', 'ring-offset-2');
                        t.classList.add('border-transparent');
                    });

                    this.classList.remove('border-transparent');
                    this.classList.add('border-blue-600', 'ring-2', 'ring-blue-600', 'ring-offset-2');
                });
            });
        });

        /* --- 2. Logique des Onglets (Tabs) --- */
        function switchTab(tabId) {
            // Cacher tous les contenus
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));

            // Réinitialiser le style de tous les boutons
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-slate-900', 'text-slate-900', 'font-semibold');
                el.classList.add('border-transparent', 'text-slate-500', 'font-medium');
            });

            // Afficher le contenu ciblé
            document.getElementById('content-' + tabId).classList.remove('hidden');

            // Activer le bouton ciblé
            const activeBtn = document.getElementById('tab-' + tabId);
            activeBtn.classList.remove('border-transparent', 'text-slate-500', 'font-medium');
            activeBtn.classList.add('border-slate-900', 'text-slate-900', 'font-semibold');
        }
    </script>
@endsection
