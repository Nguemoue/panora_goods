/**
 * Synchronized Global Progress Bar for Livewire requests.
 * Implements anti-flicker delay (120ms) and smooth asymptotic progress.
 */
function initSyncLoader() {
    const bar = document.getElementById('sync-progress-bar');
    if (!bar) {
        return;
    }

    let activeRequests = 0;
    let delayTimer = null;
    let trickleTimer = null;
    let currentProgress = 0;

    const setProgress = (value) => {
        currentProgress = value;
        bar.style.setProperty('--sync-progress', `${value}%`);
    };

    const start = () => {
        activeRequests += 1;
        if (activeRequests > 1) {
            return;
        }

        clearTimeout(delayTimer);
        delayTimer = setTimeout(() => {
            if (activeRequests === 0) {
                return;
            }

            bar.dataset.active = 'true';
            setProgress(22);

            clearInterval(trickleTimer);
            trickleTimer = setInterval(() => {
                if (currentProgress < 84) {
                    setProgress(Math.min(84, currentProgress + (90 - currentProgress) * 0.18));
                }
            }, 180);
        }, 120);
    };

    const done = () => {
        activeRequests = Math.max(0, activeRequests - 1);
        if (activeRequests > 0) {
            return;
        }

        clearTimeout(delayTimer);
        clearInterval(trickleTimer);

        if (bar.dataset.active === 'true') {
            setProgress(100);
            setTimeout(() => {
                if (activeRequests === 0) {
                    bar.dataset.active = 'false';
                    setTimeout(() => {
                        if (activeRequests === 0) {
                            setProgress(0);
                        }
                    }, 200);
                }
            }, 160);
        }
    };

    const attachLivewireHooks = () => {
        if (!window.Livewire) {
            return;
        }

        if (typeof window.Livewire.interceptRequest === 'function') {
            window.Livewire.interceptRequest(({ onFinish, onError }) => {
                start();
                onFinish(() => done());
                onError(() => done());
            });
        } else if (typeof window.Livewire.hook === 'function') {
            window.Livewire.hook('request', ({ succeed, fail }) => {
                start();
                succeed(() => done());
                fail(() => done());
            });
        }
    };

    document.addEventListener('livewire:init', attachLivewireHooks);
    if (window.Livewire) {
        attachLivewireHooks();
    }
}

document.addEventListener('DOMContentLoaded', initSyncLoader);
