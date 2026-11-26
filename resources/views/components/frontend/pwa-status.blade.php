<div
    x-data="{
        online: true,
        showInstall: false,
        installPlatform: 'native',
        updateAvailable: false,
        guideOpen: false,
        guidePlatform: 'browser',
        init() {
            this.online = window.navigator.onLine;
        },
        async install() {
            if (window.PanoraPwa) {
                await window.PanoraPwa.promptInstall();
                this.showInstall = false;
            }
        },
        dismiss() {
            this.showInstall = false;
            if (window.PanoraPwa) {
                window.PanoraPwa.dismissInstall();
            }
        },
        update() {
            if (window.PanoraPwa) {
                window.PanoraPwa.applyUpdate();
            }
        }
    }"
    @pwa:network-change.window="online = $event.detail.online"
    @pwa:install-available.window="installPlatform = $event.detail.platform; showInstall = true"
    @pwa:install-hidden.window="showInstall = false"
    @pwa:installed.window="showInstall = false; guideOpen = false"
    @pwa:update-available.window="updateAvailable = true"
    @pwa:open-guide.window="guidePlatform = $event.detail.platform || 'browser'; guideOpen = true"
>
    {{-- Offline Network Indicator --}}
    <div
        x-show="!online"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed top-16 inset-x-0 z-50 bg-amber-500 text-slate-950 text-xs font-semibold py-2 px-4 text-center shadow-md"
        role="status"
    >
        Vous êtes actuellement hors ligne — certaines pages récemment consultées restent accessibles.
    </div>

    {{-- Service Worker Update Prompt --}}
    <div
        x-show="updateAvailable"
        x-cloak
        x-transition
        class="fixed bottom-4 right-4 z-50 max-w-[calc(100vw-2rem)] sm:max-w-sm rounded-2xl bg-slate-950 text-white border border-white/10 shadow-2xl p-4 flex items-center gap-3.5"
        role="status"
    >
        <img src="{{ asset('images/logo-mark.svg') }}" alt="{{ config('app.name') }}" class="size-9 shrink-0">
        <div class="min-w-0 flex-1">
            <p class="text-xs font-bold">Mise à jour disponible</p>
            <p class="text-[11px] text-slate-300 mt-0.5">Actualisez pour profiter de la dernière version.</p>
        </div>
        <flux:button @click="update()" size="xs" variant="primary" class="!bg-secondary hover:!bg-secondary-hover !border-0 shrink-0">
            Actualiser
        </flux:button>
    </div>

    {{-- Discreet PWA Install Prompt (Desktop/Android + iOS Safari) --}}
    <div
        x-show="showInstall && !updateAvailable"
        x-cloak
        x-transition
        class="fixed bottom-4 left-4 right-4 sm:right-auto z-40 sm:max-w-sm rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xl p-4 flex items-start gap-3.5"
        role="dialog"
        aria-label="Installer l'application PanoraGoods"
    >
        <img src="{{ asset('images/logo-mark.svg') }}" alt="{{ config('app.name') }}" class="size-11 shrink-0">
        <div class="min-w-0 flex-1 space-y-2">
            <div>
                <p class="font-display text-sm font-extrabold text-slate-950 dark:text-white">Installer PanoraGoods</p>
                <p x-show="installPlatform === 'native'" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Accès rapide depuis votre écran d'accueil et consultation fluide même sur réseau faible.
                </p>
                <p x-show="installPlatform === 'ios'" x-cloak class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Appuyez sur <strong>Partager</strong> puis sur <strong>Sur l'écran d'accueil</strong> pour installer l'app.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <template x-if="installPlatform === 'native'">
                    <flux:button @click="install()" variant="primary" size="xs" icon="arrow-down-tray" class="!bg-primary hover:!bg-primary-hover">
                        Installer
                    </flux:button>
                </template>
                <flux:button @click="dismiss()" variant="ghost" size="xs">
                    Plus tard
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Manual Install Guide Modal (when user clicks "Télécharger l'application" on iOS / Desktop without prompt) --}}
    <div
        x-show="guideOpen"
        x-cloak
        x-transition.opacity.duration.200ms
        @keydown.escape.window="guideOpen = false"
        class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4"
        @click.self="guideOpen = false"
        role="dialog"
        aria-modal="true"
        aria-label="Guide d'installation PanoraGoods"
    >
        <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl p-6 space-y-5">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-mark.svg') }}" alt="{{ config('app.name') }}" class="size-10 shrink-0">
                    <div>
                        <h3 class="font-display text-base font-extrabold text-slate-950 dark:text-white">
                            Installer PanoraGoods
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Installation rapide en 2 étapes</p>
                    </div>
                </div>
                <button
                    type="button"
                    @click="guideOpen = false"
                    aria-label="Fermer"
                    class="size-8 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center cursor-pointer"
                >
                    <flux:icon.x-mark variant="micro" />
                </button>
            </div>

            <div class="space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                <div class="rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200/80 dark:border-slate-700 p-3.5 space-y-1.5">
                    <p class="font-bold text-slate-900 dark:text-white">Sur iPhone / iPad (Safari)</p>
                    <p>1. Appuyez sur l'icône <strong>Partager</strong> en bas de l'écran.</p>
                    <p>2. Sélectionnez <strong>« Sur l'écran d'accueil »</strong> puis validez.</p>
                </div>

                <div class="rounded-xl bg-slate-50 dark:bg-slate-800/70 border border-slate-200/80 dark:border-slate-700 p-3.5 space-y-1.5">
                    <p class="font-bold text-slate-900 dark:text-white">Sur Android / PC (Chrome, Edge)</p>
                    <p>1. Ouvrez le menu du navigateur (<strong>⋮</strong>) ou l'icône d'installation dans la barre d'adresse.</p>
                    <p>2. Cliquez sur <strong>« Installer l'application »</strong>.</p>
                </div>
            </div>

            <div class="flex justify-end">
                <flux:button @click="guideOpen = false" variant="primary" size="sm" class="!bg-primary hover:!bg-primary-hover">
                    Compris
                </flux:button>
            </div>
        </div>
    </div>
</div>
