@extends('layouts.app')

@section('full_width')
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
        {{-- Axe 1: Bannière Hero 16:3 Glotelho / Apple Style --}}
        <x-frontend.hero-banner :featured-products="$featuredProducts" />

        {{-- Axe 1: Bento Grid Réassurance & Accès Rapide Catégories --}}
        <x-frontend.bento-highlights :categories="$categories" />

        {{-- Axes 1, 2 & 4: Catalogue Livewire + Loaders Synchronisés + Infinite Scroll --}}
        <livewire:product-catalog />

        {{-- Section dédiée : Télécharger / Installer l'Application --}}
        <x-frontend.pwa-install-card />
    </div>
@endsection
