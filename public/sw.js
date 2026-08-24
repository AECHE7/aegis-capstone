// A.E.G.I.S. High-Performance PWA Service Worker — v2.0.0
const CACHE_NAME = 'aegis-static-v2.0.0';

// Only cache static, immutable assets — NEVER cache dynamic HTML pages or auth routes
const STATIC_ASSETS = [
    '/logo.webp',
    '/logo.png',
    '/logo-email.png',
    '/manifest.json'
];

// 1. Install Event: Cache only immutable static brand assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS).catch(() => {
                // Ignore missing assets during build transitions
            });
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event: Purge ALL previous caches (removes stale HTML and cached CSRF tokens)
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Event: Strict passthrough for all dynamic/HTML routes; cache-first ONLY for static assets
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // Completely bypass non-GET, non-HTTP, and ANY HTML/Navigation requests
    // Dynamic Laravel pages MUST ALWAYS come directly from the network to ensure fresh CSRF tokens & session cookies
    if (
        request.method !== 'GET' ||
        !url.protocol.startsWith('http') ||
        request.mode === 'navigate' ||
        request.headers.get('accept')?.includes('text/html') ||
        url.pathname.startsWith('/login') ||
        url.pathname.startsWith('/register') ||
        url.pathname.startsWith('/admin') ||
        url.pathname.startsWith('/superadmin') ||
        url.pathname.startsWith('/student') ||
        url.pathname.startsWith('/master') ||
        url.pathname === '/'
    ) {
        return; // Direct browser network fetch without service worker caching
    }

    // Cache-First strategy ONLY for static build assets, images, and fonts
    const isStaticAsset =
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font' ||
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/logo');

    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                if (cachedResponse) {
                    // Update cache in background
                    fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse));
                        }
                    }).catch(() => {});
                    return cachedResponse;
                }
                return fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, responseToCache));
                    }
                    return networkResponse;
                }).catch(() => {
                    // Fail gracefully for missing static assets without throwing uncaught promise rejections
                    return new Response('', { status: 404, statusText: 'Not Found' });
                });
            })
        );
    }
});
