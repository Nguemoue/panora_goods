@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 pb-12">

        <!-- En-tête de page (Hero Header minimaliste) -->
        <div class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                    <div class="max-w-2xl">
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-950 tracking-tight">
                            Notre Catalogue
                        </h1>
                        <p class="mt-2 text-base text-slate-500">
                            Explorez notre collection de produits. Filtrez par catégorie et trouvez exactement ce dont vous avez besoin.
                        </p>
                    </div>

                    <!-- Barre de recherche façon "Command Palette" -->
                    <form method="GET" class="w-full md:w-80 shrink-0">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input
                                type="search"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Rechercher un produit..."
                                class="flex h-11 w-full rounded-md border border-slate-200 bg-slate-50/50 px-3 py-2 pl-10 text-sm outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 shadow-sm"
                            >
                            <button type="submit" class="absolute inset-y-1.5 right-1.5 flex items-center justify-center rounded bg-slate-900 px-3 text-xs font-medium text-white transition-colors hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1">
                                Chercher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row gap-8 items-start">

                <!-- Sidebar (Sticky) -->
                <aside class="w-full lg:w-64 shrink-0 lg:sticky lg:top-24 space-y-6">

                    <!-- Bloc Catégories -->
                    <div>
                        <h3 class="text-sm font-semibold text-slate-950 uppercase tracking-wider mb-4">Catégories</h3>
                        <div class="space-y-1">
                            <!-- Option "Toutes" -->
                            <a href="{{ route('home') }}"
                               class="flex items-center justify-between rounded-md px-3 py-2.5 text-sm font-medium transition-colors {{ !$selectedCategory ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <span>Toutes les catégories</span>
                                <span class="inline-flex items-center justify-center rounded-full bg-white px-2.5 py-0.5 text-xs font-semibold text-slate-500 border border-slate-200 shadow-sm">
                                    {{ $products->total() }}
                                </span>
                            </a>

                            <!-- Liste des catégories -->
                            @foreach($categories as $category)
                                <a href="{{ route('home', ['category' => $category->id]) }}"
                                   class="flex items-center justify-between rounded-md px-3 py-2.5 text-sm font-medium transition-colors group {{ $selectedCategory?->id === $category->id ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <span class="truncate pr-2">{{ $category->name }}</span>
                                    <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors {{ $selectedCategory?->id === $category->id ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500 group-hover:bg-white group-hover:border-slate-200 border border-transparent' }}">
                                        {{ $category->products_count }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Carte Info Discrète -->
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-2 text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h3 class="font-semibold text-slate-900">Besoin d'aide ?</h3>
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Vous ne trouvez pas ce que vous cherchez ? Contactez notre équipe commerciale.
                        </p>
                        <a href="{{ route('contact') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-900 shadow-sm transition-colors hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-300">
                            Nous contacter
                        </a>
                    </div>
                </aside>

                <!-- Grille Principale -->
                <div class="flex-1 min-w-0">

                    <!-- Toolbar (Compteur & Vues) -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-slate-200">
                        <div class="text-sm text-slate-500">
                            Affichage de <span class="font-semibold text-slate-900">{{ $products->count() }}</span> sur <span class="font-semibold text-slate-900">{{ $products->total() }}</span> produit(s)
                            @if($selectedCategory)
                                dans <span class="font-medium text-blue-600">{{ $selectedCategory->name }}</span>
                            @endif
                        </div>

                        <!-- Sélecteur Segmenté (Segmented Control UI) -->
                        <div class="inline-flex h-9 items-center justify-center rounded-lg bg-slate-100 p-1 text-slate-500 shadow-inner">
                            <a href="{{ route('home', array_merge(request()->query(), ['per_page' => 12])) }}"
                               class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1 text-sm font-medium ring-offset-white transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 {{ request('per_page', 12) == 12 ? 'bg-white text-slate-950 shadow-sm' : 'hover:bg-slate-200/50 hover:text-slate-900' }}">
                                12 par page
                            </a>
                            <a href="{{ route('home', array_merge(request()->query(), ['per_page' => 24])) }}"
                               class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1 text-sm font-medium ring-offset-white transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 {{ request('per_page', 12) == 24 ? 'bg-white text-slate-950 shadow-sm' : 'hover:bg-slate-200/50 hover:text-slate-900' }}">
                                24 par page
                            </a>
                        </div>
                    </div>

                    <!-- Grille de Produits -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

                        @forelse($products as $product)
                            <div class="group relative flex flex-col rounded-xl border border-slate-200 bg-white shadow-sm transition-all duration-200 hover:shadow-lg hover:border-blue-200 overflow-hidden">

                                <!-- Zone Image -->
                                <a href="{{ route('products.show', $product) }}" class="relative block aspect-[4/3] w-full overflow-hidden bg-slate-50">
                                    @php
                                        $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                    @endphp

                                    <img
                                        src="{{ $primaryImage ? asset('storage/'.$primaryImage->path) : 'https://placehold.co/600x400/f8fafc/94a3b8?text=Image+Non+Disponible' }}"
                                        alt="{{ $product->name }}"
                                        class="h-full w-full object-contain p-6 transition-transform duration-500 group-hover:scale-110"
                                    >

                                    <!-- Badge Stock Flottant -->
                                    <div class="absolute top-3 right-3 z-10">
                                        @if($product->stock_quantity > 0)
                                            <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 backdrop-blur-sm shadow-sm">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                En stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20 backdrop-blur-sm shadow-sm">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                Rupture
                                            </span>
                                        @endif
                                    </div>
                                </a>

                                <!-- Zone Contenu -->
                                <div class="flex flex-1 flex-col p-5">
                                    <div class="flex items-center justify-between gap-2 text-xs mb-2">
                                        <span class="font-medium text-blue-600 truncate">{{ $product->category?->name }}</span>
                                        <span class="text-slate-400 uppercase tracking-wider font-semibold">{{ $product->brand?->name }}</span>
                                    </div>

                                    <h3 class="text-base font-bold text-slate-900 line-clamp-2 leading-tight group-hover:text-blue-600 transition-colors">
                                        <a href="{{ route('products.show', $product) }}">
                                            <span class="absolute inset-0 z-0"></span> <!-- Rend toute la carte cliquable -->
                                            {{ $product->name }}
                                        </a>
                                    </h3>

                                    <!-- Spécifications Minimalistes -->
                                    @if($product->specifications->count())
                                        <div class="flex flex-wrap gap-2 mt-3 z-10 relative">
                                            @foreach($product->specifications->take(2) as $spec)
                                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-[11px] font-medium text-slate-600">
                                                    {{ $spec->pivot->value }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Prix & Action (Poussés vers le bas) -->
                                    <div class="mt-auto pt-6 flex items-end justify-between relative z-10">
                                        <div>
                                            @if($product->supplier_price)
                                                <div class="text-xs font-medium text-slate-400 line-through mb-0.5">
                                                    {{ Number::currency($product->supplier_price, 'XAF') }}
                                                </div>
                                            @endif
                                            <div class="text-xl font-extrabold text-slate-900 tracking-tight">
                                                {{ Number::currency($product->selling_price, 'XAF') }}
                                            </div>
                                        </div>

                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-50 text-slate-400 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <!-- Empty State Design -->
                            <div class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white p-12 text-center">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 mb-4">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-1">Aucun produit trouvé</h3>
                                <p class="text-slate-500 max-w-sm mx-auto mb-6">Nous n'avons pas pu trouver de produits correspondant à votre recherche ou catégorie sélectionnée.</p>
                                <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-6 py-2.5 text-sm font-medium text-white shadow transition-colors hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                                    Effacer les filtres
                                </a>
                            </div>
                        @endforelse

                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="mt-10 border-t border-slate-200 pt-8">
                            {{ $products->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
