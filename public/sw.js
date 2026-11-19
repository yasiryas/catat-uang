const CACHE_NAME = 'keuanganapp-v3';
const APP_SHELL = [
    '/manifest.webmanifest',
    '/icon-192.png',
    '/icon-512.png',
    '/apple-touch-icon.png',
    '/favicon.svg',
    '/offline',
];
const CACHEABLE_ASSET = /^\/(?:build\/assets\/[^/]+\.(?:js|css|woff2)|images\/[^/]+\.(?:jpg|png|webp|svg))$/;

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'LOGOUT') {
        event.waitUntil(
            caches.keys().then((keys) => Promise.all(keys.map((key) => caches.delete(key))))
        );
    }
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') return;

    const url = new URL(request.url);

    if (url.origin !== location.origin) return;
    if (request.mode === 'navigate' && url.pathname !== '/offline') return;
    if (url.searchParams.has('signature') || url.searchParams.has('expires')) return;
    if (url.pathname.includes('/receipt')) return;

    const isCacheable = APP_SHELL.includes(url.pathname) || CACHEABLE_ASSET.test(url.pathname);

    if (!isCacheable) return;

    event.respondWith(
        caches.match(request).then((cached) => cached || fetch(request).then((response) => {
            if (response.ok) {
                const clone = response.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
            }
            return response;
        }))
    );
});
