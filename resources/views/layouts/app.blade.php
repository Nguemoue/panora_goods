<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Catalogue') | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-bg text-gray-800 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Top Bar (Bootstrap Utility Style) -->
        <div class="bg-gray-100 border-b border-gray-200 py-1 text-[11px]">
            <div class="container mx-auto px-4 flex justify-between items-center text-gray-600">
                <div>Le n°1 du e-commerce au Cameroun</div>
                <div class="flex gap-4">
                    <a href="#" class="hover:text-primary">Aide</a>
                    <a href="#" class="hover:text-primary">Nous contacter</a>
                </div>
            </div>
        </div>

        <!-- Middle Header (Search & Logo) -->
        <header class="bg-white py-4 border-b border-gray-200">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex-shrink-0">
                        <span class="text-3xl font-bold text-primary italic tracking-tight">GLO<span class="text-secondary">TELHO</span></span>
                    </a>

                    <!-- Search Form (Bootstrap Input Group Style) -->
                    <div class="flex-grow max-w-xl">
                        <form action="#" method="GET" class="flex">
                            <input type="text" 
                                   placeholder="Rechercher un produit..." 
                                   class="flex-grow border border-gray-300 rounded-l px-4 py-2 text-sm focus:outline-none focus:border-primary">
                            <button type="submit" class="bg-secondary text-white px-6 py-2 rounded-r hover:bg-secondary-hover transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Right Icons -->
                    <div class="flex items-center gap-6 text-gray-700">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="flex items-center gap-2 hover:text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span class="text-xs font-semibold uppercase">Admin</span>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="flex items-center gap-2 hover:text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                                    <span class="text-xs font-semibold uppercase">Connexion</span>
                                </a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Nav (Glotelho Blue Bar) -->
        <nav class="bg-primary text-white sticky top-0 z-50 shadow-md">
            <div class="container mx-auto px-4">
                <ul class="flex flex-wrap items-center">
                    <li class="bg-secondary">
                        <a href="#" class="px-6 py-3 flex items-center gap-2 font-bold text-sm uppercase">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            Toutes nos catégories
                        </a>
                    </li>
                    <li><a href="#" class="px-4 py-3 hover:bg-primary-hover text-xs font-bold uppercase tracking-tight transition-colors">Téléphones</a></li>
                    <li><a href="#" class="px-4 py-3 hover:bg-primary-hover text-xs font-bold uppercase tracking-tight transition-colors">Informatique</a></li>
                    <li><a href="#" class="px-4 py-3 hover:bg-primary-hover text-xs font-bold uppercase tracking-tight transition-colors">Tablettes</a></li>
                    <li class="ml-auto bg-red-600 animate-pulse"><a href="#" class="px-4 py-3 text-xs font-bold uppercase tracking-tight">Déstockage</a></li>
                </ul>
            </div>
        </nav>

        <main class="flex-grow pt-6">
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-300 mt-12 py-10">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h4 class="font-bold text-sm mb-4 uppercase text-primary">À propos</h4>
                        <p class="text-xs text-gray-500 leading-relaxed">Glotelho est la référence du catalogue produit au Cameroun.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm mb-4 uppercase text-primary">Aide</h4>
                        <ul class="text-xs space-y-2 text-gray-600">
                            <li><a href="#" class="hover:text-secondary">FAQ</a></li>
                            <li><a href="#" class="hover:text-secondary">Livraison</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm mb-4 uppercase text-primary">Légal</h4>
                        <ul class="text-xs space-y-2 text-gray-600">
                            <li><a href="#" class="hover:text-secondary">Conditions d'utilisation</a></li>
                            <li><a href="#" class="hover:text-secondary">Confidentialité</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm mb-4 uppercase text-primary">Contact</h4>
                        <p class="text-xs text-gray-600">Douala, Cameroun<br>Tél: +237 600 000 000</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
