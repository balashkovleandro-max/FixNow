const BON_CACHE = 'bon-pwa-v1';
const BON_ASSET_CACHE = 'bon-assets-v1';

const APP_SHELL = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/favicon.svg',
    '/icons/bon-icon-192.png',
    '/icons/bon-icon-512.png',
    '/icons/bon-maskable-512.png'
];

const SENSITIVE_PREFIXES = [
    '/admin',
    '/business',
    '/dashboard',
    '/freelancer',
    '/login',
    '/logout',
    '/onboarding',
    '/register'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(BON_CACHE)
            .then((cache) => cache.addAll(APP_SHELL))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => ![BON_CACHE, BON_ASSET_CACHE].includes(key))
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(handleNavigation(request, url));
        return;
    }

    if (isCacheableAsset(url)) {
        event.respondWith(cacheFirst(request));
    }
});

async function handleNavigation(request, url) {
    try {
        const response = await fetch(request);
        return response;
    } catch (error) {
        const offline = await caches.match('/offline.html');
        return offline || Response.error();
    }
}

async function cacheFirst(request) {
    const cached = await caches.match(request);

    if (cached) {
        return cached;
    }

    const response = await fetch(request);

    if (response && response.ok) {
        const cache = await caches.open(BON_ASSET_CACHE);
        cache.put(request, response.clone());
    }

    return response;
}

function isCacheableAsset(url) {
    if (SENSITIVE_PREFIXES.some((prefix) => url.pathname.startsWith(prefix))) {
        return false;
    }

    return url.pathname.startsWith('/build/')
        || url.pathname.startsWith('/icons/')
        || url.pathname.startsWith('/images/')
        || /\.(?:css|js|mjs|png|jpg|jpeg|webp|svg|gif|ico|woff2?)$/i.test(url.pathname);
}
