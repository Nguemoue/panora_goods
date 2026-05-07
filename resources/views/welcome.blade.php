@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 pb-12">

        <!-- En-tête de page (Hero Header minimaliste) -->
        <div class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                    <div class="max-w-2xl">
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-950 tracking-tight">
                            {{ __('frontend.home.title') }}
                        </h1>
                        <p class="mt-2 text-base text-slate-500">
                            {{ __('frontend.home.subtitle') }}
                        </p>
                    </div>

                    <!-- Barre de recherche façon "Command Palette" -->
                    <form method="GET" class="w-full md:w-80 shrink-0">
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                type="search"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="{{ __('frontend.home.search_placeholder') }}"
                                class="flex h-11 w-full rounded-md border border-slate-200 bg-slate-50/50 px-3 py-2 pl-10 text-sm outline-none transition-all placeholder:text-slate-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 shadow-sm"
                            >
                            <button type="submit"
                                    class="absolute inset-y-1.5 right-1.5 flex items-center justify-center rounded bg-slate-900 px-3 text-xs font-medium text-white transition-colors hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1">
                                {{ __('frontend.home.search_button') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">

                <aside class="w-full lg:w-64 shrink-0 lg:sticky lg:top-24 space-y-4 lg:space-y-6">

                    <div class="lg:hidden">
                        <button type="button" data-collapse-toggle="mobile-categories-menu"
                                class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-900 shadow-sm hover:bg-slate-50 transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                                                       d="M4 6h16M4 12h16M4 18h7"/></svg>
                        {{ __('frontend.nav.categories') }}
                        @if($selectedCategory)
                            <span class="text-slate-400 font-normal ml-1"> | <span
                                    class="text-blue-600 font-medium">{{ $selectedCategory->name }}</span></span>
                        @endif
                    </span>
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    <div id="mobile-categories-menu" class="hidden lg:block">
                        <div
                            class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm lg:p-0 lg:border-none lg:bg-transparent lg:shadow-none">
                            <h3 class="hidden lg:block text-sm font-semibold text-slate-950 uppercase tracking-wider mb-4">{{ __('frontend.nav.categories') }}</h3>
                            <div class="space-y-1">
                                <a href="{{ route('home') }}"
                                   class="flex items-center justify-between rounded-md px-3 py-2.5 text-sm font-medium transition-colors {{ !$selectedCategory ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <span>{{ __('frontend.nav.all_categories') }}</span>
                                    <span
                                        class="inline-flex items-center justify-center rounded-full bg-white px-2.5 py-0.5 text-xs font-semibold text-slate-500 border border-slate-200 shadow-sm">
                                {{ $products->total() }}
                            </span>
                                </a>

                                @foreach($categories as $category)
                                    <a href="{{ route('home', ['category' => $category->id]) }}"
                                       class="flex items-center justify-between rounded-md px-3 py-2.5 text-sm font-medium transition-colors group {{ $selectedCategory?->id === $category->id ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                        <span class="truncate pr-2">{{ $category->name }}</span>
                                        <span
                                            class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors {{ $selectedCategory?->id === $category->id ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500 group-hover:bg-white group-hover:border-slate-200 border border-transparent' }}">
                                    {{ $category->products_count }}
                                </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="hidden lg:block rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center gap-3 mb-2 text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h3 class="font-semibold text-slate-900">{{ __('frontend.home.need_help') }}</h3>
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            {{ __('frontend.home.help_text') }}
                        </p>
                        <a href="{{ route('contact') }}"
                           class="mt-4 inline-flex w-full items-center justify-center rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-900 shadow-sm transition-colors hover:bg-slate-50 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-300">
                            {{ __('frontend.top_bar.contact_us') }}
                        </a>
                    </div>
                </aside>

                <div class="flex-1 min-w-0">

                    <div
                        class="hidden sm:flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-slate-200">
                        <div class="text-sm text-slate-500">
                            {{ __('frontend.home.display_count', ['count' => $products->count(), 'total' => $products->total()]) }}
                            @if($selectedCategory)
                                {{ __('messages.in') }} <span
                                    class="font-medium text-blue-600">{{ $selectedCategory->name }}</span>
                            @endif
                        </div>

                        <div
                            class="inline-flex h-9 items-center justify-center rounded-lg bg-slate-100 p-1 text-slate-500 shadow-inner">
                            <a href="{{ route('home', array_merge(request()->query(), ['per_page' => 12])) }}"
                               class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1 text-sm font-medium ring-offset-white transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 {{ request('per_page', 12) == 12 ? 'bg-white text-slate-950 shadow-sm' : 'hover:bg-slate-200/50 hover:text-slate-900' }}">
                                {{ __('frontend.home.per_page', ['count' => 12]) }}
                            </a>
                            <a href="{{ route('home', array_merge(request()->query(), ['per_page' => 24])) }}"
                               class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1 text-sm font-medium ring-offset-white transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 {{ request('per_page', 12) == 24 ? 'bg-white text-slate-950 shadow-sm' : 'hover:bg-slate-200/50 hover:text-slate-900' }}">
                                {{ __('frontend.home.per_page', ['count' => 24]) }}
                            </a>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-0 sm:gap-6 border-t border-slate-200 sm:border-t-0">

                        @forelse($products as $product)
                            <div
                                class="group relative flex flex-row sm:flex-col my-1 border-b border-slate-200 sm:border sm:rounded-xl bg-white sm:shadow-sm transition-all duration-200 sm:hover:shadow-lg sm:hover:border-blue-200 overflow-hidden py-4 sm:py-0 pr-4 sm:pr-0 pl-1 sm:pl-0 gap-3 sm:gap-0">

                                <div
                                    class="w-[38%] sm:w-full relative shrink-0 flex items-center justify-center bg-transparent sm:bg-slate-50">
                                    <a href="{{ route('products.show', $product) }}"
                                       class="block aspect-[3/4] sm:aspect-[4/3] w-full overflow-hidden bg-gray-50">
                                        @php
                                            $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                                        @endphp

                                        <img
                                            src="{{ $primaryImage ? asset('storage/'.$primaryImage->path) : 'https://placehold.co/600x400/f8fafc/94a3b8?text=Image+Non+Disponible' }}"
                                            alt="{{ $product->name }}"
                                            class="h-full w-full object-contain p-2 sm:p-6 mix-blend-multiply transition-transform duration-500 group-hover:scale-105"
                                        >
                                    </a>

                                    <button type="button"
                                            class="absolute bottom-1 left-2 sm:hidden p-1.5 bg-white/90 backdrop-blur flex items-center gap-1 rounded-sm border border-slate-200 shadow-sm text-slate-700">
                                        <span>Détails</span>
                                        <x-heroicon-s-eye class="h-5 w-5 text-primary" />
                                    </button>
                                </div>

                                <div class="flex flex-1 flex-col sm:p-5 pt-1">

                                    <h3 class="text-sm sm:text-base font-medium text-slate-900 leading-snug line-clamp-3 sm:line-clamp-2 hover:text-blue-600 transition-colors">
                                        <a href="{{ route('products.show', $product) }}">
                                            <span class="absolute inset-0 z-0 sm:hidden"></span> {{ $product->name }}
                                        </a>
                                    </h3>

                                    @if($product->brand)
                                        <div class="text-[11px] sm:text-xs text-blue-600 hover:underline mt-1">
                                            Marque : {{ $product->brand->name }}
                                        </div>
                                    @endif
                                    {{-- description  section --}}
                                    <div class="text-sm text-slate-500 my-4 ">
                                        {{ $product->short_description }}
                                    </div>

                                    <div class="mt-2 flex items-baseline gap-1.5">
                                        <span
                                            class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ Number::currency($product->selling_price, 'XAF') }}</span>

                                    </div>

                                    <div class="mt-1">
                                        @if($product->stock_quantity > 0)
                                            <p class="text-[11px] sm:text-xs font-semibold text-emerald-700">En stock
                                                : {{ $product->stock_quantity }} disponible(s)</p>
                                        @else
                                            <p class="text-[11px] sm:text-xs font-semibold text-rose-700">Rupture de
                                                stock</p>
                                        @endif
                                    </div>

                                    @if($product->specifications->count())
                                        <div class="mt-auto pt-3 flex flex-wrap gap-1.5">
                                            <span class="inline-flex items-center gap-1 rounded border border-slate-200 bg-white px-1.5 py-0.5 text-[10px] font-medium text-slate-700 shadow-sm">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor"
                                                         stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round"
                                                                                                    stroke-linejoin="round"
                                                                                                    d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                {{ $product->category?->name ?? 'Fiche' }}
                                            </span>

                                            @foreach($product->specifications->take(3) as $spec)
                                                <span
                                                    class="inline-flex items-center rounded-full border border-slate-300 bg-slate-100 px-2.5 py-0.5 text-[10px] font-medium text-slate-600">
                                                    {{ $spec->pivot->value }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                </div>
                            </div>

                        @empty
                            <div
                                class="col-span-full flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white p-12 text-center mt-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 mb-4">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor"
                                         stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 mb-1">{{ __('frontend.home.no_products_found') }}</h3>
                                <p class="text-slate-500 max-w-sm mx-auto mb-6">{{ __('frontend.home.no_products_found_text') }}</p>
                                <a href="{{ route('home') }}"
                                   class="inline-flex items-center justify-center rounded-md bg-slate-900 px-6 py-2.5 text-sm font-medium text-white shadow transition-colors hover:bg-slate-800">
                                    {{ __('frontend.home.clear_filters') }}
                                </a>
                            </div>
                        @endforelse

                    </div>

                    @if($products->hasPages())
                        <div class="mt-8 sm:mt-10 sm:border-t sm:border-slate-200 pt-6 sm:pt-8">
                            {{ $products->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
