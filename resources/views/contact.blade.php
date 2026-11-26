@extends('layouts.app')

@section('title', __('frontend.nav.contact'))

@section('content')
    <div class="space-y-8 pb-8">
        {{-- Breadcrumbs --}}
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('home') }}" icon="home">Accueil</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ __('frontend.nav.contact') }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        {{-- Header --}}
        <div class="max-w-2xl space-y-2.5">
            <flux:badge size="sm" color="blue">Support & Conseillers</flux:badge>
            <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-950 tracking-tight">
                {{ __('frontend.contact.title') }}
            </h1>
            <p class="text-sm sm:text-base text-slate-500 leading-relaxed">
                {{ __('frontend.contact.subtitle') }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- Left Column (5 cols): Direct Channels & Office Info --}}
            <div class="lg:col-span-5 space-y-4">
                {{-- Fast-Track WhatsApp Card --}}
                <div class="rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-700 text-white p-6 shadow-md space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="size-10 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
                            <flux:icon.chat-bubble-left-ellipsis class="size-5" />
                        </div>
                        <div>
                            <h2 class="font-display text-base font-extrabold">Réponse immédiate sur WhatsApp</h2>
                            <p class="text-xs text-emerald-100">Temps moyen de réponse : moins de 5 min</p>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-emerald-50/90 leading-relaxed">
                        Besoin d'un conseil sur un modèle, d'une confirmation de disponibilité ou d'un suivi de livraison ? Écrivez-nous directement.
                    </p>
                    <flux:button
                        href="https://wa.me/{{ preg_replace('/[^\d]/', '', (string) config('project_configuration.whatsapp')) }}"
                        target="_blank"
                        rel="noopener"
                        variant="filled"
                        icon-trailing="arrow-up-right"
                        class="w-full !bg-white !text-emerald-800 hover:!bg-emerald-50 font-semibold"
                    >
                        Ouvrir WhatsApp
                    </flux:button>
                </div>

                {{-- Info Cards --}}
                <div class="rounded-2xl bg-white border border-slate-200/80 divide-y divide-slate-100 shadow-xs overflow-hidden">
                    <div class="p-4 sm:p-5 flex items-start gap-3.5">
                        <div class="size-10 rounded-xl bg-blue-50 text-primary flex items-center justify-center shrink-0">
                            <flux:icon.map-pin class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-950">{{ __('frontend.contact.our_office') }}</h3>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ __('frontend.contact.office_address') }}</p>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 flex items-start gap-3.5">
                        <div class="size-10 rounded-xl bg-blue-50 text-primary flex items-center justify-center shrink-0">
                            <flux:icon.phone class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-950">{{ __('frontend.contact.phone') }}</h3>
                            <p class="text-xs sm:text-sm font-semibold text-slate-700 mt-0.5">{{ config('project_configuration.phone_number') }}</p>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 flex items-start gap-3.5">
                        <div class="size-10 rounded-xl bg-blue-50 text-primary flex items-center justify-center shrink-0">
                            <flux:icon.envelope class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-950">{{ __('frontend.contact.email') }}</h3>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 break-all">{{ config('project_configuration.contact_email') }}</p>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5 flex items-start gap-3.5">
                        <div class="size-10 rounded-xl bg-amber-50 text-secondary flex items-center justify-center shrink-0">
                            <flux:icon.clock class="size-5" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-950">{{ __('frontend.contact.opening_hours') }}</h3>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ __('frontend.contact.hours_detail') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column (7 cols): Flux UI Contact Form --}}
            <div class="lg:col-span-7">
                <div class="rounded-3xl bg-white border border-slate-200/90 p-6 sm:p-8 shadow-xs space-y-6">
                    <div>
                        <flux:heading size="lg" class="!font-extrabold text-slate-950">
                            Envoyez-nous un message
                        </flux:heading>
                        <flux:text class="text-xs sm:text-sm text-slate-500 mt-0.5">
                            Notre équipe vous répondra dans les plus brefs délais.
                        </flux:text>
                    </div>

                    @if(session('contact_sent'))
                        <flux:callout variant="success" icon="check-circle" heading="Message envoyé avec succès">
                            Merci pour votre message ! Un conseiller PanoraGoods reviendra vers vous très rapidement.
                        </flux:callout>
                    @endif

                    <form method="POST" action="{{ route('contact.send') }}" class="space-y-5">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <flux:input
                                name="name"
                                label="{{ __('frontend.contact.form_name') }}"
                                value="{{ old('name') }}"
                                placeholder="{{ __('frontend.contact.form_placeholder_name') }}"
                                required
                            />

                            <flux:input
                                type="email"
                                name="email"
                                label="{{ __('frontend.contact.form_email') }}"
                                value="{{ old('email') }}"
                                placeholder="{{ __('frontend.contact.form_placeholder_email') }}"
                                required
                            />
                        </div>

                        <flux:input
                            name="subject"
                            label="{{ __('frontend.contact.form_subject') }}"
                            value="{{ old('subject') }}"
                            placeholder="{{ __('frontend.contact.form_placeholder_subject') }}"
                            required
                        />

                        <flux:textarea
                            name="message"
                            label="{{ __('frontend.contact.form_message') }}"
                            rows="5"
                            placeholder="{{ __('frontend.contact.form_placeholder_message') }}"
                            required
                        >{{ old('message') }}</flux:textarea>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                            <p class="text-xs text-slate-400">
                                Vos informations restent strictement confidentielles.
                            </p>
                            <flux:button
                                type="submit"
                                variant="primary"
                                icon="paper-airplane"
                                class="!bg-primary hover:!bg-primary-hover shadow-sm"
                            >
                                {{ __('frontend.contact.send_message') }}
                            </flux:button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
