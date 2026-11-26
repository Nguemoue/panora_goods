<div
    x-data="{
        online: true,
        showInstall: false,
        installPlatform: 'native',
        updateAvailable: false,
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
    @pwa:installed.window="showInstall = false"
    @pwa:update-available.window="updateAvailable = true"
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
        class="fixed bottom-4 right-4 z-50 max-w-sm rounded-2xl bg-slate-950 text-white border border-white/10 shadow-2xl p-4 flex items-center gap-3.5"
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
        class="fixed bottom-4 left-4 z-40 max-w-sm rounded-2xl bg-white border border-slate-200 shadow-xl p-4 flex items-start gap-3.5"
        role="dialog"
        aria-label="Installer l'application PanoraGoods"
    >
        <img src="{{ asset('images/logo-mark.svg') }}" alt="{{ config('app.name') }}" class="size-11 shrink-0">
        <div class="min-w-0 flex-1 space-y-2">
            <div>
                <p class="font-display text-sm font-extrabold text-slate-950">Installer PanoraGoods</p>
                <p x-show="installPlatform === 'native'" class="text-xs text-slate-500 mt-0.5">
                    Accès rapide depuis votre écran d'accueil et consultation fluide même sur réseau faible.
                </p>
                <p x-show="installPlatform === 'ios'" x-cloak class="text-xs text-slate-500 mt-0.5">
                    Appuyez sur <strong>Partager</strong> puis sur <strong>Sur l'écran d'accueil</strong> pour installer l'app.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <template x-if="installPlatform === 'native'">
                    <flux:button @click="install()" variant="primary" size="xs" icon="arrow-down-tray" class="!bg-primary hover:!bg-primary-hover">
                        Installer
                    </flux:button>
                </template>
                <flux:button @click="dismiss()" variant="ghost" size="xs" class="text-slate-500">
                    Plus tard
                </flux:button>
            </div>
        </div>
    </div>
</div>
