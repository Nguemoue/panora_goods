@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-6 py-10">
    <!-- Breadcrumbs -->
    <nav class="text-sm mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center gap-2 text-muted-foreground">
            <li><a href="{{ route('home') }}" class="hover:text-primary">Accueil</a></li>
            <li>›</li>
            <li><a href="{{ route('home', ['category' => $product->category_id]) }}" class="hover:text-primary">{{ $product->category->name }}</a></li>
            <li>›</li>
            <li class="font-semibold text-gray-900 truncate">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Gallery -->
        <div class="lg:col-span-6">
            <div class="rounded-lg border bg-card p-4">
                <div class="aspect-[4/5] w-full rounded-md bg-muted/10 flex items-center justify-center overflow-hidden">
                    @php $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first(); @endphp
                    <img id="product-main-image" src="{{ $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://placehold.co/800x1000?text=' . urlencode($product->name) }}" alt="{{ $product->name }}" class="max-h-full object-contain cursor-zoom-in">
                </div>

                @if($product->images->count() > 1)
                    <div class="mt-4">
                        <div class="relative">
                            <button type="button" class="product-thumb-prev absolute left-2 top-1/2 -translate-y-1/2 z-10 p-1 bg-white rounded-full shadow hidden lg:inline-flex" aria-label="Précédent">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>

                            <div class="product-thumbs-container overflow-x-auto no-scrollbar flex gap-2 py-1 px-8">
                                @foreach($product->images as $image)
                                    <button type="button" data-src="{{ asset('storage/' . $image->path) }}" class="product-thumb flex-shrink-0 h-20 w-20 overflow-hidden rounded-md border bg-muted/5 p-1 flex items-center justify-center transition-transform hover:scale-105">
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->name }}" class="h-full object-contain">
                                    </button>
                                @endforeach
                            </div>

                            <button type="button" class="product-thumb-next absolute right-2 top-1/2 -translate-y-1/2 z-10 p-1 bg-white rounded-full shadow hidden lg:inline-flex" aria-label="Suivant">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Similar products (mobile) -->
            @if($similarProducts->count() > 0)
                <div class="mt-6 lg:hidden">
                    <h3 class="text-sm font-semibold mb-3">Produits similaires</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($similarProducts as $sim)
                            @php $img = $sim->images->where('is_primary', true)->first() ?? $sim->images->first(); @endphp
                            <a href="{{ route('products.show', $sim) }}" class="block rounded-md border bg-card p-2">
                                <div class="aspect-square overflow-hidden rounded-md bg-muted/10 flex items-center justify-center">
                                    <img src="{{ $img ? asset('storage/' . $img->path) : 'https://placehold.co/400x400?text=' . urlencode($sim->name) }}" alt="{{ $sim->name }}" class="object-contain max-h-full">
                                </div>
                                <p class="mt-2 text-xs font-medium text-gray-900 truncate">{{ $sim->name }}</p>
                                <p class="text-xs text-muted-foreground">{{ Number::currency($sim->selling_price, 'XAF') }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Details -->
        <div class="lg:col-span-6">
            <div class="rounded-lg border bg-card p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary">{{ $product->brand?->name ?? '—' }}</span>
                            <span class="inline-flex items-center rounded-full bg-muted/10 px-3 py-1 text-xs font-medium text-muted-foreground">{{ $product->category?->name ?? '—' }}</span>
                        </div>

                        <h1 class="text-2xl font-extrabold text-gray-900">{{ $product->name }}</h1>
                        <p class="mt-2 text-sm text-muted-foreground">Modèle: <span class="font-medium">{{ $product->model_number ?? 'N/A' }}</span></p>

                        <div class="mt-4 flex items-center gap-3">
                            @if($product->stock_quantity > 0)
                                <span class="inline-flex items-center gap-2 rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700">● En stock ({{ $product->stock_quantity }})</span>
                            @else
                                <span class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">● Rupture</span>
                            @endif

                            @if($product->supplier_price)
                                <span class="ml-auto text-sm text-muted-foreground line-through">{{ Number::currency($product->supplier_price, 'XAF') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="shrink-0 text-right">
                        <div class="text-3xl font-extrabold text-secondary">{{ Number::currency($product->selling_price, 'XAF') }}</div>
                        <p class="text-xs text-muted-foreground mt-1">Prix catalogue</p>
                        <div class="mt-4">
                            <a href="#" class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white">Contacter</a>
                        </div>
                    </div>
                </div>

                <!-- Key Specs -->
                @if($product->specifications->count() > 0)
                    <div class="mt-6">
                        <h4 class="text-sm font-semibold mb-3">Caractéristiques clés</h4>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($product->specifications->take(6) as $spec)
                                <div class="rounded-md border bg-muted/5 p-3">
                                    <div class="text-xs text-muted-foreground">{{ $spec->name }}</div>
                                    <div class="mt-1 font-medium text-sm text-gray-900">{{ $spec->pivot->value }} @if($spec->measure)<span class="text-xs text-muted-foreground">{{ $spec->measure }}</span>@endif</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Accordion / Full Specs -->
                @if($product->specifications->count() > 0)
                    <div class="mt-6">
                        <h4 class="text-sm font-semibold mb-3">Fiche technique complète</h4>
                        <div class="space-y-2">
                            @foreach($product->specifications as $spec)
                                <div class="rounded-md border bg-card p-3 flex items-start justify-between">
                                    <div>
                                        <div class="text-xs text-muted-foreground">{{ $spec->name }}</div>
                                        <div class="mt-1 text-sm font-medium text-gray-900">{{ $spec->pivot->value }} @if($spec->measure) <span class="text-xs text-muted-foreground">{{ $spec->measure }}</span>@endif</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Info box -->
                <div class="mt-6 rounded-md border bg-muted/5 p-3 text-sm text-muted-foreground">
                    Cette page présente uniquement un <strong>catalogue</strong>. Les commandes ne sont pas disponibles ici.
                </div>

                <!-- Actions -->
                <div class="mt-6 flex items-center gap-3">
                    <a href="{{ route('home') }}" class="text-sm text-muted-foreground hover:underline">← Retour au catalogue</a>
                    <a href="{{ route('home', ['category' => $product->category_id]) }}" class="ml-auto text-sm font-semibold text-primary">Voir la catégorie</a>
                </div>
            </div>

            <!-- Similar products (desktop) -->
            @if($similarProducts->count() > 0)
                <section class="mt-8 hidden lg:block">
                    <h3 class="text-lg font-semibold mb-4">Produits similaires</h3>
                    <div class="grid grid-cols-4 gap-4">
                        @foreach($similarProducts as $sim)
                            @php $img = $sim->images->where('is_primary', true)->first() ?? $sim->images->first(); @endphp
                            <a href="{{ route('products.show', $sim) }}" class="block rounded-md border bg-card p-3 text-sm">
                                <div class="aspect-square overflow-hidden rounded-md bg-muted/10 flex items-center justify-center">
                                    <img src="{{ $img ? asset('storage/' . $img->path) : 'https://placehold.co/400x400?text=' . urlencode($sim->name) }}" alt="{{ $sim->name }}" class="object-contain max-h-full">
                                </div>
                                <p class="mt-2 font-medium text-gray-900 truncate">{{ $sim->name }}</p>
                                <p class="text-xs text-muted-foreground">{{ Number::currency($sim->selling_price, 'XAF') }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
@endsection
        <!-- Breadcrumbs -->
        <nav class="py-4 mb-6 text-sm">
            <ol class="flex text-xs text-gray-500 gap-2 flex-wrap">
                <li><a href="{{ route('home') }}" class="hover:text-primary font-semibold">Accueil</a></li>
                <li class="text-gray-300">/</li>
                <li><a href="{{ route('home', ['category' => $product->category_id]) }}" class="hover:text-primary font-semibold">{{ $product->category->name }}</a></li>
                <li class="text-gray-300">/</li>
                <li class="font-bold text-gray-800 line-clamp-1">{{ $product->name }}</li>
            </ol>
        </nav>

        <!-- Main Product Card -->
        <div class="card p-8 shadow-lg mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Product Gallery -->
                <div>
                    <div class="border-2 border-gray-200 rounded-lg p-6 mb-6 aspect-square flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 sticky top-24">
                        @php $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first(); @endphp
                        <img id="product-main-image" src="{{ $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://placehold.co/800x800?text=' . urlencode($product->name) }}" 
                             alt="{{ $product->name }}" 
                             class="max-w-full max-h-full object-contain cursor-zoom-in">
                    </div>

                    <!-- Image Thumbnails -->
                    @if($product->images->count() > 1)
                        <div class="mt-4">
                            <div class="relative">
                                <button type="button" class="product-thumb-prev absolute left-2 top-1/2 -translate-y-1/2 z-10 p-1 bg-white rounded-full shadow hidden lg:inline-flex" aria-label="Précédent">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>

                                <div class="product-thumbs-container overflow-x-auto no-scrollbar flex gap-2 py-1 px-8">
                                    @foreach($product->images as $image)
                                        <button type="button" data-src="{{ asset('storage/' . $image->path) }}" class="product-thumb flex-shrink-0 h-20 w-20 overflow-hidden rounded-md border bg-muted/5 p-1 flex items-center justify-center transition-transform hover:scale-105">
                                            <img src="{{ asset('storage/' . $image->path) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="h-full object-contain">
                                        </button>
                                    @endforeach
                                </div>

                                <button type="button" class="product-thumb-next absolute right-2 top-1/2 -translate-y-1/2 z-10 p-1 bg-white rounded-full shadow hidden lg:inline-flex" aria-label="Suivant">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div class="flex flex-col">
                    <!-- Header Info -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <div class="mb-4 flex items-center gap-2">
                            <span class="text-xs font-bold text-white bg-primary px-3 py-1 rounded-full uppercase">{{ $product->brand->name }}</span>
                            <span class="text-xs font-bold text-white bg-secondary px-3 py-1 rounded-full uppercase">{{ $product->category->name }}</span>
                        </div>

                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">{{ $product->name }}</h1>
                        
                        <!-- Stock & SKU Info -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
                            <div class="bg-gray-100 text-gray-700 text-[10px] font-bold px-3 py-2 rounded uppercase">
                                🔖 Modèle: {{ $product->model_number ?? 'N/A' }}
                            </div>
                            @if($product->stock_quantity > 0)
                                <div class="text-green-600 text-xs font-bold flex items-center gap-1 bg-green-50 px-3 py-2 rounded-full">
                                    <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></span>
                                    EN STOCK ({{ $product->stock_quantity }} unité(s))
                                </div>
                            @else
                                <div class="text-red-600 text-xs font-bold bg-red-50 px-3 py-2 rounded-full uppercase">
                                    ⚠ Rupture de stock
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Price Section -->
                    <div class="bg-gradient-to-br from-primary/5 to-secondary/5 p-6 rounded-lg mb-8 border border-primary/10">
                        <p class="text-xs text-gray-500 uppercase font-bold mb-2 tracking-widest">Prix catalogue</p>
                        <div class="flex items-baseline gap-3">
                            <span class="text-5xl font-black text-secondary">{{ Number::currency($product->selling_price, 'XAF') }}</span>
                            @if($product->supplier_price)
                                <span class="text-sm text-gray-400 line-through">{{ Number::currency($product->supplier_price, 'XAF') }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 mt-2">💡 Prix TTC sans frais supplémentaires</p>
                    </div>

                    <!-- Key Specifications -->
                    @if($product->specifications->count() > 0)
                        <div class="mb-8">
                            <h4 class="font-bold text-sm uppercase text-gray-900 mb-4 border-l-4 border-secondary pl-3 flex items-center gap-2">
                                <span>⚡ Caractéristiques principales</span>
                            </h4>
                            <div class="grid grid-cols-2 gap-y-4 gap-x-4">
                                @foreach($product->specifications->take(6) as $spec)
                                    <div class="flex flex-col border-l-2 border-gray-200 pl-3">
                                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $spec->name }}</span>
                                        <span class="text-sm font-semibold text-gray-800 mt-1">
                                            {{ $spec->pivot->value }}
                                            @if($spec->measure)
                                                <span class="text-xs text-gray-500">{{ $spec->measure }}</span>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg text-xs text-gray-700">
                        <p class="flex items-start gap-2">
                            <span class="text-blue-600 font-bold mt-0.5">ℹ️</span>
                            <span>Cette page présente uniquement un <strong>catalogue de produits</strong>. Les commandes ne sont pas disponibles directement ici. Veuillez <strong>contacter notre équipe</strong> pour les demandes d'achat.</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Specifications Table -->
        @if($product->specifications->count() > 0)
            <div class="card p-8 mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                    <span class="w-1 h-8 bg-secondary rounded"></span>
                    📋 Fiche technique complète
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-gray-300 rounded-lg overflow-hidden">
                        <thead class="bg-gradient-to-r from-primary to-blue-600 text-white">
                            <tr>
                                <th class="py-4 px-6 text-left font-bold uppercase text-xs tracking-wider w-1/3">Caractéristique</th>
                                <th class="py-4 px-6 text-left font-bold uppercase text-xs tracking-wider">Valeur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($product->specifications as $spec)
                                <tr class="{{ $loop->even ? 'bg-gray-50 hover:bg-gray-100' : 'bg-white hover:bg-gray-50' }} transition-colors">
                                    <td class="py-4 px-6 font-semibold text-gray-800 uppercase text-[10px] tracking-wider">{{ $spec->name }}</td>
                                    <td class="py-4 px-6 text-gray-700 font-medium">
                                        {{ $spec->pivot->value }}
                                        @if($spec->measure)
                                            <span class="text-gray-500 text-xs ml-1">{{ $spec->measure }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-10 text-center text-gray-400 italic">Détails techniques non disponibles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Product Info Sections -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div class="card p-6 text-center">
                <div class="text-4xl mb-3">🏢</div>
                <h4 class="font-bold text-gray-900 mb-2 uppercase text-sm">Fournisseur</h4>
                <p class="text-sm text-gray-600 font-semibold">{{ $product->supplier?->name ?? 'Non spécifié' }}</p>
            </div>
            <div class="card p-6 text-center">
                <div class="text-4xl mb-3">📱</div>
                <h4 class="font-bold text-gray-900 mb-2 uppercase text-sm">Marque</h4>
                <p class="text-sm text-gray-600 font-semibold">{{ $product->brand?->name ?? 'Sans marque' }}</p>
            </div>
            <div class="card p-6 text-center">
                <div class="text-4xl mb-3">🔐</div>
                <h4 class="font-bold text-gray-900 mb-2 uppercase text-sm">Garantie</h4>
                <p class="text-sm text-gray-600 font-semibold">Selon fournisseur</p>
            </div>
        </div>

        <!-- Similar Products -->
        @if($similarProducts->count() > 0)
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-gray-900 mb-8 flex items-center gap-3">
                    <span class="w-1 h-8 bg-secondary rounded"></span>
                    Produits similaires
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($similarProducts as $similar)
                        <a href="{{ route('products.show', $similar) }}" class="card h-full shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden">
                            <!-- Image -->
                            <div class="p-4 border-b border-gray-100 aspect-square flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 group-hover:from-gray-100 group-hover:to-gray-200 transition-colors">
                                @php
                                    $img = $similar->images->where('is_primary', true)->first() ?? $similar->images->first();
                                @endphp
                                <img src="{{ $img ? asset('storage/' . $img->path) : 'https://placehold.co/400x400?text=' . urlencode($similar->name) }}" 
                                     alt="{{ $similar->name }}" 
                                     class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-300">
                            </div>

                            <!-- Info -->
                            <div class="p-4 flex flex-col flex-grow">
                                <p class="text-[10px] text-primary font-bold uppercase mb-1">{{ $similar->brand->name ?? 'Marque' }}</p>
                                <h5 class="text-sm font-bold text-gray-800 mb-3 line-clamp-2 min-h-[40px]">{{ $similar->name }}</h5>
                                <div class="mt-auto">
                                    <span class="text-lg font-black text-secondary">{{ Number::currency($similar->selling_price, 'XAF') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Back Button -->
        <div class="flex gap-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold px-6 py-3 rounded-lg transition-colors uppercase text-xs">
                ← Retour au catalogue
            </a>
            <a href="{{ route('home', ['category' => $product->category_id]) }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white font-bold px-6 py-3 rounded-lg transition-colors uppercase text-xs">
                Voir la catégorie
            </a>
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div id="product-lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 p-4">
        <button id="product-lightbox-close" class="absolute top-6 right-6 text-white text-3xl leading-none">&times;</button>
        <img id="product-lightbox-img" src="" alt="" class="max-h-[90vh] max-w-[90vw] object-contain rounded-md shadow-lg" />
    </div>

    <!-- Gallery scripts moved to resources/js/product-gallery.js and imported in resources/js/app.js -->

@endsection
