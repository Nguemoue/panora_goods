<section id="install-app" aria-label="Télécharger l'application PanoraGoods" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div
        x-data="{
            installed: false,
            canNativeInstall: false,
            init() {
                if (window.PanoraPwa) {
                    this.installed = window.PanoraPwa.isStandalone();
                    this.canNativeInstall = Boolean(window.PanoraPwa.deferredPrompt);
                }
            },
            async handleInstall() {
                if (window.PanoraPwa) {
                    const result = await window.PanoraPwa.triggerManualInstall();
                    if (result === 'installed') {
                        this.installed = true;
                    }
                }
            }
        }"
        @pwa:install-available.window="canNativeInstall = ($event.detail.platform === 'native')"
        @pwa:installed.window="installed = true"
        class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-blue-950 to-primary text-white p-6 sm:p-8 lg:p-10 shadow-lg ring-1 ring-white/10"
    >
        <div class="pointer-events-none absolute -right-16 -bottom-16 size-64 rounded-full bg-secondary/25 blur-3xl"></div>
        <div class="pointer-events-none absolute left-1/3 -top-20 size-52 rounded-full bg-sky-400/15 blur-3xl"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5 max-w-2xl">
                <img
                    src="{{ asset('images/logo-mark.svg') }}"
                    alt="{{ config('app.name') }}"
                    class="size-14 sm:size-16 shrink-0 drop-shadow-md"
                >
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <flux:badge size="sm" color="amber" variant="solid">Application Web (PWA)</flux:badge>
                        <span class="text-xs text-blue-200 font-medium">Android · iOS · Windows · macOS</span>
                    </div>
                    <h2 class="font-display text-xl sm:text-2xl font-extrabold tracking-tight">
                        Installez PanoraGoods sur votre téléphone ou ordinateur
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-200/90 leading-relaxed">
                        Accès direct depuis l'écran d'accueil, navigation ultra-rapide, consultation fluide même sur réseau faible et zéro espace gaspillé.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <template x-if="!installed">
                    <flux:button
                        @click="handleInstall()"
                        variant="primary"
                        icon="arrow-down-tray"
                        class="!bg-secondary hover:!bg-secondary-hover !border-0 shadow-md w-full sm:w-auto"
                    >
                        Télécharger l'application
                    </flux:button>
                </template>
                <template x-if="installed">
                    <flux:badge size="lg" color="emerald" icon="check-circle" variant="solid">
                        Application installée
                    </flux:badge>
                </template>
            </div>
        </div>
    </div>
</section>
