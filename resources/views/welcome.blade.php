@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">Catalogue</h1>
            <p class="mt-1 text-sm text-muted-foreground">Parcourez notre sélection — images haute résolution et fiches techniques complètes.</p>
        </div>

        <!-- Simple Search (shadcn-like) -->
        <form action="#" method="GET" class="flex items-center gap-2">
            <label for="search" class="sr-only">Rechercher</label>
            <div class="relative">
                <input id="search" name="q" type="search" placeholder="Rechercher un produit..." class="h-10 w-72 rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:border-transparent">
                <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 rounded px-2 py-1 text-sm text-white bg-primary hover:bg-primary/90">🔎</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar (filters) -->
        <aside class="hidden lg:block">
            <div class="space-y-4">
                <div class="rounded-lg border bg-card p-4">
                    <h3 class="text-sm font-medium">Catégories</h3>
                    <div class="mt-3 flow-root">
                        <ul role="list" class="-my-2 divide-y">
                            <li class="py-2 flex items-center justify-between text-sm">
                                <a href="{{ route('home') }}" class="text-sm font-medium {{ !$selectedCategory ? 'text-primary' : 'text-gray-700' }}">Toutes</a>
                                <span class="text-xs text-muted-foreground">{{ $products->total() }}</span>
                            </li>
                            @foreach($categories as $category)
                                <li class="py-2 flex items-center justify-between text-sm">
                                    <a href="{{ route('home', ['category' => $category->id]) }}" class="truncate {{ $selectedCategory?->id === $category->id ? 'text-primary font-semibold' : 'text-gray-700' }}">{{ $category->name }}</a>
                                    <span class="text-xs text-muted-foreground">{{ $category->products_count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-4">
                    <h3 class="text-sm font-medium">Affiner</h3>
                    <div class="mt-3 space-y-2 text-sm text-muted-foreground">
                        <div class="flex items-center justify-between">
                            <span>En stock</span>
                            <span class="text-xs">{{ __('Affichage uniquement') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Prix</span>
                            <span class="text-xs">Filtre</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-4 text-sm">
                    <h3 class="font-medium">À propos</h3>
                    <p class="mt-2 text-muted-foreground">Glotelho — Catalogue produit. Pour acheter, contactez notre équipe.</p>
                </div>
            </div>
        </aside>

        <!-- Products grid -->
        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-sm text-muted-foreground">@if($selectedCategory) {{ $products->total() }} produit(s) dans « {{ $selectedCategory->name }} » @else {{ $products->total() }} produits @endif</p>
                </div>

                <div class="flex items-center gap-3">
                    <label class="text-sm text-muted-foreground">Afficher</label>
                    <a href="{{ route('home', array_merge(request()->query(), ['per_page' => 12])) }}" class="px-3 py-1 rounded-md text-sm {{ request('per_page', 12) == 12 ? 'bg-primary text-white' : 'bg-muted/50 text-muted-foreground' }}">12</a>
                    <a href="{{ route('home', array_merge(request()->query(), ['per_page' => 24])) }}" class="px-3 py-1 rounded-md text-sm {{ request('per_page', 12) == 24 ? 'bg-primary text-white' : 'bg-muted/50 text-muted-foreground' }}">24</a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <article class="group rounded-lg border bg-card p-4 transition-shadow hover:shadow-lg">
                        <a href="{{ route('products.show', $product) }}" class="block aspect-square overflow-hidden rounded-md bg-muted/10 p-2 flex items-center justify-center">
                            @php $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first(); @endphp
                            <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->path) : 'https://placehold.co/400x400?text=' . urlencode($product->name) }}" alt="{{ $product->name }}" class="max-h-full object-contain transition-transform group-hover:scale-105">
                        </a>

                        <div class="mt-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900 truncate"><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
                                <span class="text-xs text-muted-foreground">{{ $product->brand?->name ?? '—' }}</span>
                            </div>

                            <p class="mt-2 text-sm text-muted-foreground line-clamp-2">{{ $product->category?->name ?? '' }}</p>

                            @if($product->specifications->count())
                                <ul class="mt-3 flex flex-wrap gap-2">
                                    @foreach($product->specifications->take(3) as $spec)
                                        <li class="rounded-md bg-muted/10 px-2 py-1 text-xs text-muted-foreground">{{ $spec->name }}: <span class="font-medium text-gray-900">{{ $spec->pivot->value }}</span></li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="mt-4 flex items-center justify-between">
                                <div>
                                    <div class="text-lg font-extrabold">{{ Number::currency($product->selling_price, 'XAF') }}</div>
                                    @if($product->supplier_price)
                                        <div class="text-xs line-through text-muted-foreground">{{ Number::currency($product->supplier_price, 'XAF') }}</div>
                                    @endif
                                </div>

                                <div class="text-right">
                                    @if($product->stock_quantity > 0)
                                        <span class="inline-flex items-center gap-2 rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700">● En stock</span>
                                    @else
                                        <span class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700">● Rupture</span>
                                    @endif
                                    <a href="{{ route('products.show', $product) }}" class="mt-2 inline-block rounded-md border px-3 py-1 text-xs font-semibold bg-primary text-white">Détails</a>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <p class="text-muted-foreground">Aucun produit disponible.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
