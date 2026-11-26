/**
 * PanoraGoods PWA Lifecycle Manager (Singleton / Observer Pattern)
 * Captures beforeinstallprompt IMMEDIATELY at module evaluation so it is never missed,
 * handles Service Worker registration, update notifications, offline status,
 * and cross-platform install prompts (Android/Desktop + iOS Safari guidance).
 */

const DISMISS_KEY = 'panoragoods_pwa_install_dismissed_at';
const DISMISS_TTL_MS = 7 * 24 * 60 * 60 * 1000; // 7 days

class PwaManager {
    constructor() {
        this.deferredPrompt = null;
        this.waitingWorker = null;
        this.initialized = false;

        // Bind beforeinstallprompt immediately (can fire before DOMContentLoaded)
        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            this.deferredPrompt = event;

            if (!this.isStandalone() && !this.isDismissedRecently()) {
                window.dispatchEvent(
                    new CustomEvent('pwa:install-available', {
                        detail: { platform: 'native' },
                    })
                );
            }
        });

        window.addEventListener('appinstalled', () => {
            this.deferredPrompt = null;
            window.dispatchEvent(new CustomEvent('pwa:installed'));
        });
    }

    init() {
        if (this.initialized) {
            return;
        }
        this.initialized = true;

        this.bindNetworkObservers();
        this.registerServiceWorker();

        // If native prompt already arrived before Alpine mounted, re-emit so UI catches it
        if (this.deferredPrompt && !this.isStandalone() && !this.isDismissedRecently()) {
            window.dispatchEvent(
                new CustomEvent('pwa:install-available', {
                    detail: { platform: 'native' },
                })
            );
        } else if (this.isIosSafari() && !this.isDismissedRecently()) {
            setTimeout(() => {
                window.dispatchEvent(
                    new CustomEvent('pwa:install-available', {
                        detail: { platform: 'ios' },
                    })
                );
            }, 3500);
        }
    }

    isStandalone() {
        return (
            window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone === true
        );
    }

    isIosSafari() {
        const ua = window.navigator.userAgent;
        const isIos = /iPad|iPhone|iPod/.test(ua);
        const isWebkit = /WebKit/.test(ua);
        const isOtherBrowser = /CriOS|FxiOS|OPiOS|EdgiOS/.test(ua);
        return isIos && isWebkit && !isOtherBrowser && !this.isStandalone();
    }

    isDismissedRecently() {
        try {
            const raw = window.localStorage.getItem(DISMISS_KEY);
            if (!raw) {
                return false;
            }
            return Date.now() - Number(raw) < DISMISS_TTL_MS;
        } catch {
            return false;
        }
    }

    dismissInstall() {
        try {
            window.localStorage.setItem(DISMISS_KEY, String(Date.now()));
        } catch {
            // Storage unavailable in private mode
        }
        window.dispatchEvent(new CustomEvent('pwa:install-hidden'));
    }

    async promptInstall() {
        if (!this.deferredPrompt) {
            return false;
        }

        const promptEvent = this.deferredPrompt;
        this.deferredPrompt = null;
        promptEvent.prompt();

        const { outcome } = await promptEvent.userChoice;

        if (outcome === 'accepted') {
            window.dispatchEvent(new CustomEvent('pwa:installed'));
        } else {
            this.dismissInstall();
        }

        return outcome === 'accepted';
    }

    async triggerManualInstall() {
        if (this.isStandalone()) {
            return 'installed';
        }

        if (this.deferredPrompt) {
            const accepted = await this.promptInstall();
            return accepted ? 'installed' : 'dismissed';
        }

        window.dispatchEvent(
            new CustomEvent('pwa:open-guide', {
                detail: { platform: this.isIosSafari() ? 'ios' : 'browser' },
            })
        );

        return 'guide';
    }

    applyUpdate() {
        if (this.waitingWorker) {
            this.waitingWorker.postMessage({ type: 'SKIP_WAITING' });
        }
    }

    bindNetworkObservers() {
        const notify = () => {
            window.dispatchEvent(
                new CustomEvent('pwa:network-change', {
                    detail: { online: window.navigator.onLine },
                })
            );
        };

        window.addEventListener('online', notify);
        window.addEventListener('offline', notify);
    }

    registerServiceWorker() {
        if (!('serviceWorker' in navigator)) {
            return;
        }

        const isSecure =
            window.location.protocol === 'https:' ||
            window.location.hostname === 'localhost' ||
            window.location.hostname === '127.0.0.1';

        if (!isSecure) {
            return;
        }

        let refreshing = false;
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (refreshing) {
                return;
            }
            refreshing = true;
            window.location.reload();
        });

        window.addEventListener('load', async () => {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });

                if (registration.waiting) {
                    this.waitingWorker = registration.waiting;
                    window.dispatchEvent(new CustomEvent('pwa:update-available'));
                }

                registration.addEventListener('updatefound', () => {
                    const installing = registration.installing;
                    if (!installing) {
                        return;
                    }

                    installing.addEventListener('statechange', () => {
                        if (installing.state === 'installed' && navigator.serviceWorker.controller) {
                            this.waitingWorker = installing;
                            window.dispatchEvent(new CustomEvent('pwa:update-available'));
                        }
                    });
                });
            } catch {
                // Silent fallback if SW registration is blocked by browser policy
            }
        });
    }
}

const pwaManager = new PwaManager();
window.PanoraPwa = pwaManager;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => pwaManager.init());
} else {
    pwaManager.init();
}
