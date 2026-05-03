<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Catalogue') | {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-950 font-sans antialiased flex flex-col min-h-screen">

<!-- Top Bar (Style minimaliste shadcn) -->
<div class="bg-blue-950 text-blue-50 py-1.5 text-xs font-medium hidden md:block">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <div class="flex items-center gap-6">
                <span class="flex items-center gap-1.5 opacity-90">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    +237 6XX XXX XXX
                </span>
            <span class="flex items-center gap-1.5 opacity-90">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    contact@tonsite.com
                </span>
        </div>
        <div class="flex gap-4">
            <a href="#" class="hover:text-white transition-colors">Aide & FAQ</a>
            <a href="{{ route('contact') }}" class="hover:text-white transition-colors">Nous contacter</a>
        </div>
    </div>
</div>

<!-- Navbar Principal (Bordure subtile, fond blanc) -->
<nav class="bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 sticky top-0 z-40 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="bg-blue-600 text-white p-1.5 rounded-md shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="text-lg font-bold tracking-tight text-slate-900">{{ config('app.name') }}</span>
            </a>

            <!-- Desktop Menu (Boutons Ghost façon shadcn) -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-slate-100 hover:text-slate-900 text-slate-600 h-9 px-4 py-2">
                    Accueil
                </a>

                <!-- Dropdown Catégories -->
                <div>
                    <button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-slate-100 hover:text-slate-900 text-slate-600 h-9 px-4 py-2">
                        Catégories
                        <svg class="w-4 h-4 ml-1 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <!-- Dropdown Menu (shadcn Popover style) -->
                    <div id="dropdownNavbar" class="z-50 hidden bg-white rounded-md border border-slate-200 shadow-md w-56 p-1">
                        <ul class="text-sm text-slate-700" aria-labelledby="dropdownNavbarLink">
                            @foreach(\App\Models\Category::query()->get() as $category)
                                <li>
                                    <a href="{{ request()->fullUrlWithQuery(['cat' => $category->id]) }}" class="relative flex cursor-default select-none items-center rounded-sm px-3 py-2 text-sm outline-none hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <a href="{{ route('track.order') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-slate-100 hover:text-slate-900 text-slate-600 h-9 px-4 py-2">
                    Suivi
                </a>
                <a href="{{ url('/seller') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-slate-100 hover:text-slate-900 text-slate-600 h-9 px-4 py-2">
                    Espace Employé
                </a>

                <div class="pl-2">
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-white transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-blue-600 text-white hover:bg-blue-600/90 h-9 px-4 py-2 shadow-sm">
                        Contact
                    </a>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button type="button" data-drawer-target="mobile-sidebar" data-drawer-show="mobile-sidebar" aria-controls="mobile-sidebar" class="inline-flex items-center justify-center rounded-md p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">
                    <span class="sr-only">Ouvrir le menu</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Sidebar Mobile (Drawer style shadcn) -->
<div id="mobile-sidebar" class="fixed top-0 left-0 z-50 w-72 h-screen bg-white border-r border-slate-200 transition-transform -translate-x-full shadow-lg" tabindex="-1" aria-labelledby="drawer-label">

    <div class="flex items-center justify-between p-4 border-b border-slate-100">
        <span class="text-lg font-bold tracking-tight text-slate-900">{{ config('app.name') }}</span>
        <button type="button" data-drawer-hide="mobile-sidebar" aria-controls="mobile-sidebar" class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            <span class="sr-only">Fermer</span>
        </button>
    </div>

    <div class="p-4 overflow-y-auto h-[calc(100vh-4rem)]">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('home') }}" class="flex items-center rounded-md px-3 py-2 text-sm font-medium text-slate-900 hover:bg-slate-100 transition-colors">
                    Accueil
                </a>
            </li>

            <li>
                <button type="button" class="flex w-full items-center justify-between rounded-md px-3 py-2 text-sm font-medium text-slate-900 hover:bg-slate-100 transition-colors" aria-controls="dropdown-categories" data-collapse-toggle="dropdown-categories">
                    Catégories
                    <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <ul id="dropdown-categories" class="hidden py-1 pl-4 space-y-1 border-l border-slate-200 ml-3 mt-1">
                    @foreach(\App\Models\Category::query()->get() as $category)
                        <li>
                            <a href="{{ request()->fullUrlWithQuery(['cat' => $category->id]) }}" class="flex items-center rounded-md px-3 py-2 text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

            <li>
                <a href="{{ route('track.order') }}" class="flex items-center rounded-md px-3 py-2 text-sm font-medium text-slate-900 hover:bg-slate-100 transition-colors">
                    Suivi de commande
                </a>
            </li>
            <li>
                <a href="{{ url('/seller') }}" class="flex items-center rounded-md px-3 py-2 text-sm font-medium text-slate-900 hover:bg-slate-100 transition-colors">
                    Espace Employé
                </a>
            </li>
        </ul>

        <div class="mt-6 pt-6 border-t border-slate-100">
            <a href="{{ route('contact') }}" class="inline-flex w-full items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-blue-600 text-white hover:bg-blue-600/90 h-9 px-4 py-2 shadow-sm">
                Nous contacter
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<main class="flex-grow pt-8 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @yield('content')
        {{ $slot ?? '' }}
    </div>
</main>

<!-- Footer minimaliste -->
<footer class="bg-white border-t border-slate-200 py-12 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-slate-950">{{ config('app.name') }}</h4>
                <p class="text-sm text-slate-500 leading-relaxed">La référence du catalogue produit au Cameroun. Simple, rapide et efficace.</p>
            </div>
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-slate-950">Aide</h4>
                <ul class="space-y-2.5 text-sm text-slate-600">
                    <li><a href="#" class="hover:text-slate-950 hover:underline underline-offset-4 transition-colors">FAQ</a></li>
                    <li><a href="#" class="hover:text-slate-950 hover:underline underline-offset-4 transition-colors">Livraison</a></li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-slate-950">Légal</h4>
                <ul class="space-y-2.5 text-sm text-slate-600">
                    <li><a href="#" class="hover:text-slate-950 hover:underline underline-offset-4 transition-colors">Conditions d'utilisation</a></li>
                    <li><a href="#" class="hover:text-slate-950 hover:underline underline-offset-4 transition-colors">Confidentialité</a></li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-slate-950">Contact</h4>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Douala, Cameroun<br>
                    Tél: +237 600 000 000
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Flowbite Script -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
