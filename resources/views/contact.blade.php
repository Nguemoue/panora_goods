@extends('layouts.app')

@section('content')
    <!-- Fond avec un léger dégradé et formes décoratives modernes -->
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 relative overflow-hidden py-12 lg:py-20">

        <!-- Effets de lumière en arrière-plan (Blobs) -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10"></div>
            <div class="absolute bottom-10 -left-20 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

            <!-- En-tête de la page -->
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-4">
                    {{ __('frontend.contact.title') }}
                </h1>
                <p class="text-lg text-slate-500">
                    {{ __('frontend.contact.subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-start">

                <!-- Section Informations de Contact (2 colonnes sur grand écran) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Carte Adresse -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-start space-x-4 hover:shadow-md transition-shadow duration-300">
                        <div class="flex-shrink-0 bg-blue-50 p-3 rounded-xl text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('frontend.contact.our_office') }}</h3>
                            <p class="mt-1 text-slate-500">{{ __('frontend.contact.office_address') }}</p>
                        </div>
                    </div>

                    <!-- Carte Téléphone -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-start space-x-4 hover:shadow-md transition-shadow duration-300">
                        <div class="flex-shrink-0 bg-blue-50 p-3 rounded-xl text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('frontend.contact.phone') }}</h3>
                            <p class="mt-1 text-slate-500">{{config('project_configuration.phone_number')}}</p>
                        </div>
                    </div>

                    <!-- Carte Email -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-start space-x-4 hover:shadow-md transition-shadow duration-300">
                        <div class="flex-shrink-0 bg-blue-50 p-3 rounded-xl text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('frontend.contact.email') }}</h3>
                            <p class="mt-1 text-slate-500">{{config('project_configuration.contact_email')}}</p>
                        </div>
                    </div>

                    <!-- Carte Horaires -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-start space-x-4 hover:shadow-md transition-shadow duration-300">
                        <div class="flex-shrink-0 bg-blue-50 p-3 rounded-xl text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('frontend.contact.opening_hours') }}</h3>
                            <p class="mt-1 text-slate-500">{{ __('frontend.contact.hours_detail') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section Formulaire (3 colonnes sur grand écran) -->
                <div class="lg:col-span-3">
                    <div class="bg-white p-8 md:p-10 rounded-3xl shadow-xl shadow-blue-900/5 border border-slate-100">
                        <form method="POST" action="{{ route('contact.send') }}" class="space-y-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nom -->
                                <div class="space-y-1.5">
                                    <label for="name" class="block text-sm font-semibold text-slate-700">{{ __('frontend.contact.form_name') }}</label>
                                    <input type="text" id="name" name="name" required
                                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-900 placeholder-slate-400"
                                           placeholder="{{ __('frontend.contact.form_placeholder_name') }}">
                                </div>

                                <!-- Email -->
                                <div class="space-y-1.5">
                                    <label for="email" class="block text-sm font-semibold text-slate-700">{{ __('frontend.contact.form_email') }}</label>
                                    <input type="email" id="email" name="email" required
                                           class="w-full px-4 py-3 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-900 placeholder-slate-400"
                                           placeholder="{{ __('frontend.contact.form_placeholder_email') }}">
                                </div>
                            </div>

                            <!-- Sujet -->
                            <div class="space-y-1.5">
                                <label for="subject" class="block text-sm font-semibold text-slate-700">{{ __('frontend.contact.form_subject') }}</label>
                                <input type="text" id="subject" name="subject" required
                                       class="w-full px-4 py-3 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-900 placeholder-slate-400"
                                       placeholder="{{ __('frontend.contact.form_placeholder_subject') }}">
                            </div>

                            <!-- Message -->
                            <div class="space-y-1.5">
                                <label for="message" class="block text-sm font-semibold text-slate-700">{{ __('frontend.contact.form_message') }}</label>
                                <textarea id="message" name="message" rows="5" required
                                          class="w-full px-4 py-3 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-slate-900 placeholder-slate-400 resize-none"
                                          placeholder="{{ __('frontend.contact.form_placeholder_message') }}"></textarea>
                            </div>

                            <!-- Bouton Submit -->
                            <button type="submit"
                                    class="w-full py-4 px-6 text-white text-base font-semibold rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 focus:ring-4 focus:ring-blue-500/30 transform hover:-translate-y-0.5 transition-all duration-200 shadow-lg shadow-blue-600/30">
                                {{ __('frontend.contact.send_message') }}
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
