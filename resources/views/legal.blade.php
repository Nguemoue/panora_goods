@extends('layouts.app')

@section('title', 'Mentions Légales')

@section('content')
    <div class="min-h-screen bg-slate-50 py-12 lg:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- En-tête -->
            <div class="mb-10">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-950 tracking-tight mb-4">Mentions Légales</h1>
                <p class="text-base text-slate-500">
                    Informations légales et réglementaires concernant l'exploitation du site PanoraGoods. En vigueur au {{ date('d/m/Y') }}.
                </p>
            </div>

            <!-- Conteneur principal (Design "Card" shadcn) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <div class="p-6 sm:p-10 space-y-10 text-slate-700 text-sm leading-relaxed">

                    <!-- 1. Éditeur du site (Informations de l'entreprise) -->
                    <section>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs text-slate-600">1</span>
                            Éditeur du site
                        </h2>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Raison sociale</dt>
                                    <dd class="font-medium text-slate-900">SOCIETE FORESTIERE INDUSTRIELLE DE TRANSFORMATION DES PRODUITS AGRO-INDUSTRIELS ET MINIERS SARL</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Sigle</dt>
                                    <dd class="font-medium text-slate-900">SOFITRAPAM SARL</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Forme juridique</dt>
                                    <dd class="font-medium text-slate-900">Société à Responsabilité Limitée (SARL)</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Capital social</dt>
                                    <dd class="font-medium text-slate-900">1 000 000 FCFA</dd>
                                </div>
                                <div class="sm:col-span-2 pt-2 border-t border-slate-200">
                                    <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Siège social</dt>
                                    <dd class="font-medium text-slate-900">Quartier Djemoun, Bafoussam, Cameroun</dd>
                                </div>
                            </dl>
                        </div>
                    </section>

                    <!-- 2. Identifiants Légaux et Activité -->
                    <section>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs text-slate-600">2</span>
                            Immatriculation & Activité
                        </h2>
                        <ul class="space-y-3 list-disc pl-5 marker:text-blue-600">
                            <li><strong>Numéro RCCM :</strong> CM-BFX-01-2026-B13-00009</li>
                            <li><strong>Numéro d'Identifiant Unique (NIU) :</strong> M0126183474775</li>
                            <li><strong>Activité principale :</strong> Commerce général, import-export, commercialisation de produits.</li>
                        </ul>
                    </section>

                    <!-- 3. Lien avec la marque (TRÈS IMPORTANT) -->
                    <section>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs text-slate-600">3</span>
                            Marque et Plateforme
                        </h2>
                        <div class="bg-blue-50/50 border-l-4 border-blue-600 p-4 rounded-r-lg text-blue-900">
                            <p class="font-medium mb-2">
                                <strong>PanoraGoods</strong> est une marque commerciale exclusivement exploitée par la société <strong>SOFITRAPAM SARL</strong>.
                            </p>
                            <p class="text-sm text-blue-800">
                                SOFITRAPAM SARL est l'entité juridique responsable de toutes les activités commerciales, des offres, et des services réalisés via la plateforme web PanoraGoods.
                            </p>
                        </div>
                    </section>

                    <!-- 4. Hébergement -->
                    <section>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs text-slate-600">4</span>
                            Hébergement du site
                        </h2>
                        <p class="mb-2">Le site PanoraGoods est hébergé par :</p>
                        <ul class="space-y-1 list-none bg-slate-50 p-4 rounded-lg border border-slate-100">
                            <li><strong>Nom de l'hébergeur :</strong> [OVH]</li>
                            <li><strong>Adresse :</strong> [Rue Kellermann, 59100 Roubaix, France]</li>
                            <li><strong>Contact :</strong> [ovhcloud.com]</li>
                        </ul>
                    </section>

                    <!-- 5. Responsabilité -->
                    <section>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs text-slate-600">5</span>
                            Limitation de responsabilité
                        </h2>
                        <p class="mb-3">
                            <strong>SOFITRAPAM SARL</strong> s'efforce de fournir sur le site PanoraGoods des informations aussi précises que possible. Toutefois, elle ne pourra être tenue responsable des omissions, des inexactitudes et des carences dans la mise à jour, qu'elles soient de son fait ou du fait des tiers partenaires qui lui fournissent ces informations.
                        </p>
                        <p>
                            SOFITRAPAM SARL est responsable des activités commerciales réalisées via la plateforme PanoraGoods, dans la limite des lois en vigueur applicables au Cameroun.
                        </p>
                    </section>

                    <!-- 6. Propriété Intellectuelle -->
                    <section>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs text-slate-600">6</span>
                            Propriété Intellectuelle
                        </h2>
                        <p>
                            L'ensemble de ce site relève de la législation sur le droit d'auteur et la propriété intellectuelle. Tous les droits de reproduction sont réservés (textes, images, photographies, logos). La reproduction de tout ou partie de ce site sur quelque support que ce soit est formellement interdite sauf autorisation expresse de <strong>SOFITRAPAM SARL</strong>.
                        </p>
                    </section>

                    <!-- 7. Contact -->
                    <section>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight mb-4 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-xs text-slate-600">7</span>
                            Nous contacter
                        </h2>
                        <p>
                            Pour toute question concernant les mentions légales ou les produits présentés sur le catalogue, vous pouvez nous contacter :
                        </p>
                        <ul class="mt-3 space-y-2 font-medium">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <a href="mailto:{{config('project_configuration.contact_email')}}" class="hover:text-blue-600 transition-colors">{{config('project_configuration.contact_email')}}</a> <!-- Remplacez par votre email -->
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>{{config('project_configuration.phone_number')}}</span> <!-- Remplacez par votre numéro -->
                            </li>
                        </ul>
                    </section>

                </div>
            </div>

        </div>
    </div>
@endsection
