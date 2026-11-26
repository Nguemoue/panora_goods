<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="PanoraGoods — Catalogue High-Tech, commande WhatsApp par zone, vérification de vendeurs et suivi de livraison en temps réel.">
    <meta name="theme-color" content="#004b91">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">

    <title>@yield('title', 'Catalogue') | {{ config('app.name') }}</title>

    {{-- PWA Manifest & Brand Icons --}}
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-mark.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">

    {{-- Premium Typography: Plus Jakarta Sans (Display) + Inter (UI/Body) + JetBrains Mono (Codes) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @stack('styles')
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-950 dark:text-slate-100 font-sans antialiased flex flex-col min-h-screen transition-colors duration-200">

{{-- Initial Branded Splash Preloader (once per session) --}}
<x-frontend.splash-loader />

{{-- Global Synchronized Top Loader Bar --}}
<div id="sync-progress-bar" class="sync-progress-bar" data-active="false" aria-hidden="true">
    <div class="sync-progress-bar__fill"></div>
</div>

{{-- Top Bar (Contact + Espace Pro discret pour aérer la navbar principale) --}}
<div class="bg-slate-950 dark:bg-black text-slate-200 py-2 text-xs font-medium hidden md:block border-b border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center gap-4">
        <div class="flex items-center gap-6 min-w-0">
            <span class="flex items-center gap-1.5 opacity-90 shrink-0">
                <flux:icon.phone variant="micro" class="text-secondary" />
                {{ config('project_configuration.phone_number') }}
            </span>
            <span class="flex items-center gap-1.5 opacity-90 truncate">
                <flux:icon.envelope variant="micro" class="text-secondary" />
                {{ config('project_configuration.contact_email') }}
            </span>
        </div>
        <div class="flex items-center gap-4 shrink-0">
            <button
                type="button"
                onclick="window.PanoraPwa && window.PanoraPwa.triggerManualInstall()"
                class="inline-flex items-center gap-1.5 text-amber-300 hover:text-amber-200 transition-colors cursor-pointer"
            >
                <flux:icon.arrow-down-tray variant="micro" />
                <span>Installer l'app</span>
            </button>
            <span class="text-slate-700" aria-hidden="true">|</span>
            <a href="{{ route('employee-verification') }}" class="inline-flex items-center gap-1.5 hover:text-white transition-colors {{ request()->routeIs('employee-verification') ? 'text-white font-semibold' : '' }}">
                <flux:icon.shield-check variant="micro" class="text-emerald-400" />
                <span>Vérifier un vendeur</span>
            </a>
            <span class="text-slate-700" aria-hidden="true">|</span>
            <a href="{{ url('/seller') }}" class="inline-flex items-center gap-1.5 hover:text-white transition-colors">
                <flux:icon.briefcase variant="micro" class="text-slate-400" />
                <span>{{ __('frontend.nav.employee_space') }}</span>
            </a>
        </div>
    </div>
</div>

@php
    $navCategories = app(\App\Services\CatalogCacheService::class)->getNavCategories();
    $activeCategoryId = (int) request()->query('category', 0);
    $isHomeActive = request()->routeIs('home') && $activeCategoryId === 0;
    $isCategoriesActive = (request()->routeIs('home') && $activeCategoryId > 0) || request()->routeIs('products.show');
    $isTrackActive = request()->routeIs('track.order');
    $isVerifyActive = request()->routeIs('employee-verification');
    $isTestimonialsActive = request()->routeIs('testimonials');
    $isContactActive = request()->routeIs('contact');
@endphp

{{-- Main Sticky Navbar (Responsive, Dark-Mode & Airy) --}}
<header class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md supports-[backdrop-filter]:bg-white/85 dark:supports-[backdrop-filter]:bg-slate-900/85 sticky top-0 z-40 border-b border-slate-200/80 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-2 sm:gap-4 lg:gap-6">
            {{-- Brand Logo --}}
            <a href="{{ route('home') }}" class="flex items-center shrink-0 group">
                <x-frontend.brand-logo />
            </a>

            {{-- Desktop Primary Navigation --}}
            <nav class="hidden lg:flex items-center gap-1.5 xl:gap-2.5" aria-label="Navigation principale">
                <a
                    href="{{ route('home') }}"
                    @if($isHomeActive) aria-current="page" @endif
                    class="nav-link {{ $isHomeActive ? 'nav-link-active' : '' }}"
                >
                    {{ __('frontend.nav.home') }}
                </a>

                <flux:dropdown>
                    <button
                        type="button"
                        class="nav-link cursor-pointer {{ $isCategoriesActive ? 'nav-link-active' : '' }}"
                    >
                        <span>{{ __('frontend.nav.categories') }}</span>
                        <flux:icon.chevron-down variant="micro" class="opacity-60" />
                    </button>

                    <flux:menu class="w-56">
                        <flux:menu.item href="{{ route('home') }}#catalog-section" icon="squares-2x2">
                            {{ __('frontend.nav.all_categories') }}
                        </flux:menu.item>
                        <flux:menu.separator />
                        @foreach($navCategories as $category)
                            <flux:menu.item
                                href="{{ route('home', ['category' => $category->id]) }}#catalog-section"
                                :icon="$activeCategoryId === (int) $category->id ? 'check' : null"
                            >
                                {{ $category->name }}
                            </flux:menu.item>
                        @endforeach
                    </flux:menu>
                </flux:dropdown>

                <a
                    href="{{ route('track.order') }}"
                    @if($isTrackActive) aria-current="page" @endif
                    class="nav-link {{ $isTrackActive ? 'nav-link-active' : '' }}"
                >
                    Suivi commande
                </a>

                <a
                    href="{{ route('employee-verification') }}"
                    @if($isVerifyActive) aria-current="page" @endif
                    class="nav-link {{ $isVerifyActive ? 'nav-link-active' : '' }}"
                >
                    Vérification
                </a>

                <a
                    href="{{ route('testimonials') }}"
                    @if($isTestimonialsActive) aria-current="page" @endif
                    class="nav-link {{ $isTestimonialsActive ? 'nav-link-active' : '' }}"
                >
                    Témoignages
                </a>
            </nav>

            {{-- Right Actions: Dark Mode Toggle + Panier + Contact + Mobile Trigger --}}
            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                {{-- Dark Mode Switcher (Flux Appearance) --}}
                <div x-data class="inline-flex">
                    <flux:tooltip content="Changer de thème (Clair / Sombre)">
                        <flux:button
                            x-on:click="$flux.dark = ! $flux.dark"
                            variant="ghost"
                            size="sm"
                            square
                            aria-label="Basculer le mode sombre"
                        >
                            <flux:icon.sun variant="mini" class="hidden dark:block text-amber-400" />
                            <flux:icon.moon variant="mini" class="block dark:hidden text-slate-600" />
                        </flux:button>
                    </flux:tooltip>
                </div>

                <livewire:whats-app-cart />

                <div class="hidden sm:block">
                    <flux:button
                        href="{{ route('contact') }}"
                        variant="primary"
                        size="sm"
                        class="!text-white {{ $isContactActive ? '!bg-secondary hover:!bg-secondary-hover ring-2 ring-secondary/30' : '!bg-primary hover:!bg-primary-hover' }}"
                    >
                        {{ __('frontend.nav.contact') }}
                    </flux:button>
                </div>

                {{-- Mobile Flyout Trigger --}}
                <div class="lg:hidden">
                    <flux:modal.trigger name="mobile-nav-drawer">
                        <flux:button variant="ghost" size="sm" square icon="bars-3" aria-label="{{ __('frontend.nav.open_menu') }}" />
                    </flux:modal.trigger>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Mobile Navigation Drawer (Flux Flyout Modal with Active Page State & PWA Install) --}}
<flux:modal name="mobile-nav-drawer" variant="flyout" position="left" class="w-80 max-w-[88vw]">
    <div class="flex h-full flex-col space-y-5">
        <div class="flex items-center pr-6">
            <x-frontend.brand-logo size="sm" />
        </div>

        <flux:separator />

        <flux:navlist variant="outline" class="flex-1 overflow-y-auto">
            <flux:navlist.item href="{{ route('home') }}" icon="home" :current="$isHomeActive">
                {{ __('frontend.nav.home') }}
            </flux:navlist.item>

            <flux:navlist.group heading="{{ __('frontend.nav.categories') }}" expandable :expanded="$isCategoriesActive">
                @foreach($navCategories as $category)
                    <flux:navlist.item
                        href="{{ route('home', ['category' => $category->id]) }}#catalog-section"
                        :current="$activeCategoryId === (int) $category->id"
                    >
                        {{ $category->name }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist.group>

            <flux:navlist.item href="{{ route('track.order') }}" icon="truck" :current="$isTrackActive">
                Suivi commande
            </flux:navlist.item>

            <flux:navlist.item href="{{ route('employee-verification') }}" icon="identification" :current="$isVerifyActive">
                Vérification vendeur
            </flux:navlist.item>

            <flux:navlist.item href="{{ route('testimonials') }}" icon="star" :current="$isTestimonialsActive">
                Témoignages
            </flux:navlist.item>

            <flux:navlist.item href="{{ url('/seller') }}" icon="briefcase">
                {{ __('frontend.nav.employee_space') }}
            </flux:navlist.item>
        </flux:navlist>

        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2.5">
            <flux:button
                type="button"
                onclick="window.PanoraPwa && window.PanoraPwa.triggerManualInstall()"
                variant="outline"
                icon="arrow-down-tray"
                class="w-full"
            >
                Installer l'application
            </flux:button>
            <flux:button
                href="{{ route('contact') }}"
                variant="primary"
                class="w-full {{ $isContactActive ? '!bg-secondary hover:!bg-secondary-hover' : '!bg-primary hover:!bg-primary-hover' }}"
            >
                {{ __('frontend.nav.contact') }}
            </flux:button>
        </div>
    </div>
</flux:modal>

{{-- Main Content --}}
<main class="grow @hasSection('full_width') pb-10 @else py-6 sm:py-8 lg:py-10 @endif">
    @hasSection('full_width')
        @yield('full_width')
    @else
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
            {{ $slot ?? '' }}
        </div>
    @endif
</main>

{{-- Global Product Comparator Dock & Modal (Deferred in Livewire 4 for faster initial hydration) --}}
<livewire:product-comparator defer />

{{-- Premium Responsive & Dark-Mode Footer --}}
<footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 pt-12 pb-8 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 pb-8">
            <div class="space-y-3">
                <x-frontend.brand-logo size="sm" />
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ __('frontend.footer.description') }}</p>
            </div>
            <div class="space-y-3">
                <h4 class="text-sm font-bold text-slate-950 dark:text-white">{{ __('frontend.footer.help') }}</h4>
                <ul class="space-y-2.5 text-sm text-slate-600 dark:text-slate-400">
                    <li><a href="{{ route('track.order') }}" class="hover:text-primary dark:hover:text-sky-400 transition-colors">{{ __('frontend.nav.track_order') }}</a></li>
                    <li><a href="{{ route('employee-verification') }}" class="hover:text-primary dark:hover:text-sky-400 transition-colors">{{ __('frontend.nav.employee_verification') }}</a></li>
                    <li><a href="{{ route('testimonials') }}" class="hover:text-primary dark:hover:text-sky-400 transition-colors">Témoignages</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-primary dark:hover:text-sky-400 transition-colors">{{ __('frontend.nav.contact') }}</a></li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="text-sm font-bold text-slate-950 dark:text-white">Application & Légal</h4>
                <ul class="space-y-2.5 text-sm text-slate-600 dark:text-slate-400">
                    <li>
                        <button
                            type="button"
                            onclick="window.PanoraPwa && window.PanoraPwa.triggerManualInstall()"
                            class="inline-flex items-center gap-1.5 font-semibold text-primary dark:text-sky-400 hover:underline cursor-pointer"
                        >
                            <flux:icon.arrow-down-tray variant="micro" />
                            <span>Télécharger l'application</span>
                        </button>
                    </li>
                    <li><a href="{{ route('legal.mentions') }}" target="_blank" class="hover:text-primary dark:hover:text-sky-400 transition-colors">{{ __('frontend.footer.legal') }}</a></li>
                </ul>
            </div>
            <div class="space-y-3">
                <h4 class="text-sm font-bold text-slate-950 dark:text-white">{{ __('frontend.footer.contact') }}</h4>
                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    {{ __('frontend.footer.location') }}<br>
                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ config('project_configuration.phone_number') }}</span>
                </p>
                <a
                    href="https://wa.me/{{ preg_replace('/[^\d]/', '', (string) config('project_configuration.whatsapp')) }}"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800"
                >
                    <flux:icon.chat-bubble-left-ellipsis variant="micro" />
                    <span>Discuter sur WhatsApp</span>
                </a>
            </div>
        </div>

        <div class="border-t border-slate-100 dark:border-slate-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-400 dark:text-slate-500">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <p>Paiement sécurisé · Vendeurs certifiés par zone</p>
        </div>
    </div>
</footer>

{{-- PWA Network Status, Install Prompt, Guide Modal & Update Banner --}}
<x-frontend.pwa-status />

<flux:toast position="bottom end" />
@fluxScripts
@stack('scripts')
</body>
</html>
