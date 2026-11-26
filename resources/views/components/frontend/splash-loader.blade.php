{{-- Initial Branded Entry Splash Loader (shown once per session, zero lag on subsequent pages) --}}
<div
    id="pg-splash-loader"
    aria-label="Chargement de PanoraGoods"
    role="status"
    class="fixed inset-0 z-[10000] flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-950 transition-opacity duration-400 ease-out"
>
    <div class="flex flex-col items-center gap-5 px-6 text-center">
        {{-- Animated Logo Mark + Orbit Ring --}}
        <div class="relative flex items-center justify-center">
            <span class="absolute size-24 rounded-full border-2 border-primary/15 dark:border-sky-400/20 animate-ping" style="animation-duration: 1.8s;"></span>
            <span class="absolute size-20 rounded-full border-2 border-transparent border-t-primary border-r-secondary dark:border-t-sky-400 dark:border-r-secondary animate-spin" style="animation-duration: 0.9s;"></span>
            <img
                src="{{ asset('images/logo-mark.svg') }}"
                alt="{{ config('app.name') }}"
                class="relative z-10 size-14 drop-shadow-md"
            >
        </div>

        {{-- Brand Wordmark & Subtle Progress Bar --}}
        <div class="space-y-2.5">
            <p class="font-display text-xl font-extrabold tracking-tight text-slate-950 dark:text-white">
                Panora<span class="text-primary dark:text-sky-400">Goods</span>
            </p>
            <div class="w-44 h-1.5 rounded-full bg-slate-200 dark:bg-slate-800 overflow-hidden mx-auto">
                <div class="h-full w-1/2 rounded-full bg-gradient-to-r from-primary via-sky-500 to-secondary animate-[pg-indeterminate_1.1s_ease-in-out_infinite]"></div>
            </div>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">
                Préparation du catalogue…
            </p>
        </div>
    </div>
</div>

<script>
    (function () {
        var el = document.getElementById('pg-splash-loader');
        if (!el) return;

        var seenKey = 'panoragoods_splash_seen';
        try {
            if (window.sessionStorage.getItem(seenKey) === '1') {
                el.remove();
                return;
            }
        } catch (e) {}

        var hidden = false;
        var hideSplash = function () {
            if (hidden) return;
            hidden = true;
            try { window.sessionStorage.setItem(seenKey, '1'); } catch (e) {}
            el.style.opacity = '0';
            el.style.pointerEvents = 'none';
            setTimeout(function () {
                if (el && el.parentNode) el.parentNode.removeChild(el);
            }, 420);
        };

        var startedAt = Date.now();
        var minDisplayMs = 420;

        window.addEventListener('load', function () {
            var elapsed = Date.now() - startedAt;
            setTimeout(hideSplash, Math.max(0, minDisplayMs - elapsed));
        });

        // Hard safety timeout so splash never blocks slow networks
        setTimeout(hideSplash, 1800);
    })();
</script>
