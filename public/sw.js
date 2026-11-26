/**
 * PanoraGoods Service Worker — Strategy Pattern Architecture
 *
 * Caching strategies:
 *  - Bypass (Network-Only): Livewire updates, Filament panels, PDF downloads, mutations.
 *  - Network-First + Offline Fallback: HTML page navigations.
 *  - Cache-First: Immutable Vite hashed assets (/build/assets/*), icons, fonts.
 *  - Stale-While-Revalidate (bounded FIFO): Product images (/storage/*, /images/*).
 */

const CACHE_VERSION = 'v1';
const CACHES = Object.freeze({
    STATIC: `panoragoods-static-${CACHE_VERSION}`,
    PAGES: `panoragoods-pages-${CACHE_VERSION}`,
    IMAGES: `panoragoods-images-${CACHE_VERSION}`,
});

const MAX_IMAGE_ENTRIES = 80;
const MAX_PAGE_ENTRIES = 25;

const PRECACHE_URLS = [
    '/offline',
    '/manifest.webmanifest',
    '/images/logo-mark.svg',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/favicon-32x32.png',
];

const BYPASS_PREFIXES = [
    '/admin',
    '/seller',
    '/client',
    '/livewire',
    '/flux',
    '/sales/',
];

/* --------------------------------------------------------------------------
   Lifecycle Events
   -------------------------------------------------------------------------- */
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches
            .open(CACHES.STATIC)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .catch(() => undefined)
    );
});

self.addEventListener('activate', (event) => {
    const allowedCaches = new Set(Object.values(CACHES));

    event.waitUntil(
        caches
            .keys()
            .then((keys) =>
                Promise.all(
                    keys
                        .filter((key) => key.startsWith('panoragoods-') && !allowedCaches.has(key))
                        .map((key) => caches.delete(key))
                )
            )
            .then(() => self.clients.claim())
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

/* --------------------------------------------------------------------------
   Caching Strategies
   -------------------------------------------------------------------------- */
async function trimCache(cacheName, maxEntries) {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    if (keys.length > maxEntries) {
        await Promise.all(keys.slice(0, keys.length - maxEntries).map((key) => cache.delete(key)));
    }
}

async function cacheFirst(request, cacheName) {
    const cached = await caches.match(request);
    if (cached) {
        return cached;
    }

    const response = await fetch(request);
    if (response && response.ok && response.type === 'basic') {
        const cache = await caches.open(cacheName);
        cache.put(request, response.clone());
    }

    return response;
}

async function staleWhileRevalidate(request, cacheName, maxEntries) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);

    const networkPromise = fetch(request)
        .then((response) => {
            if (response && response.ok && (response.type === 'basic' || response.type === 'cors')) {
                cache.put(request, response.clone()).then(() => trimCache(cacheName, maxEntries));
            }
            return response;
        })
        .catch(() => null);

    if (cached) {
        return cached;
    }

    const networkResponse = await networkPromise;
    return networkResponse || new Response('', { status: 504, statusText: 'Offline' });
}

async function networkFirstNavigation(request) {
    const cache = await caches.open(CACHES.PAGES);

    try {
        const response = await fetch(request);
        if (response && response.ok && response.type === 'basic') {
            cache.put(request, response.clone()).then(() => trimCache(CACHES.PAGES, MAX_PAGE_ENTRIES));
        }
        return response;
    } catch {
        const cachedPage = await cache.match(request);
        if (cachedPage) {
            return cachedPage;
        }

        const offlineFallback = await caches.match('/offline');
        return (
            offlineFallback ||
            new Response('Vous êtes hors ligne. Veuillez vérifier votre connexion internet.', {
                status: 503,
                headers: { 'Content-Type': 'text/plain; charset=utf-8' },
            })
        );
    }
}

/* --------------------------------------------------------------------------
   Fetch Router
   -------------------------------------------------------------------------- */
self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Only manage same-origin requests
    if (url.origin !== self.location.origin) {
        return;
    }

    // Never intercept Livewire, Filament panels, or PDF downloads
    if (BYPASS_PREFIXES.some((prefix) => url.pathname.startsWith(prefix))) {
        return;
    }

    // 1. Page navigations -> Network-First with Offline fallback
    if (request.mode === 'navigate') {
        event.respondWith(networkFirstNavigation(request));
        return;
    }

    // 2. Immutable Vite assets & PWA icons -> Cache-First
    if (url.pathname.startsWith('/build/assets/') || url.pathname.startsWith('/icons/')) {
        event.respondWith(cacheFirst(request, CACHES.STATIC));
        return;
    }

    // 3. Product & UI images -> Stale-While-Revalidate with bounded cache
    if (
        request.destination === 'image' ||
        url.pathname.startsWith('/storage/') ||
        url.pathname.startsWith('/images/')
    ) {
        event.respondWith(staleWhileRevalidate(request, CACHES.IMAGES, MAX_IMAGE_ENTRIES));
    }
});
