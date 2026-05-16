const CACHE_VERSION = 'accesshub-pwa-v2';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const OFFLINE_URL = '/offline';

const STATIC_ASSETS = [
    OFFLINE_URL,
    '/manifest.json',
    '/icons/icon-72.png',
    '/icons/icon-96.png',
    '/icons/icon-128.png',
    '/icons/icon-144.png',
    '/icons/icon-152.png',
    '/icons/icon-192.png',
    '/icons/icon-384.png',
    '/icons/icon-512.png',
    '/icons/icon-512-maskable.png',
    '/icons/shortcut-add.png',
    '/icons/shortcut-star.png',
    '/icons/icon-192.png',
];

const AUTH_EXACT_PATHS = new Set([
    '/login',
    '/logout',
    '/register',
    '/forgot-password',
    '/offline',
]);

const AUTH_PREFIXES = [
    '/reset-password',
    '/email/verification',
    '/verify-email',
    '/admin',
    '/profile',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => cache.addAll(STATIC_ASSETS))
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => ![STATIC_CACHE].includes(key))
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('message', (event) => {
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (shouldBypassCaching(url)) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(handleNavigationRequest(request));
        return;
    }

    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirst(request));
    }
});

function shouldBypassCaching(url) {
    if (AUTH_EXACT_PATHS.has(url.pathname)) {
        return true;
    }

    if (AUTH_PREFIXES.some((prefix) => url.pathname.startsWith(prefix))) {
        return true;
    }

    if (url.pathname.startsWith('/livewire') || url.pathname.startsWith('/broadcasting')) {
        return true;
    }

    return false;
}

function isStaticAsset(pathname) {
    return /\.(?:css|js|png|jpg|jpeg|svg|webp|gif|ico|woff2|woff|ttf)$/i.test(pathname);
}

async function cacheFirst(request) {
    const cached = await caches.match(request);

    if (cached) {
        return cached;
    }

    const response = await fetch(request);

    if (shouldCacheResponse(response)) {
        const cache = await caches.open(STATIC_CACHE);
        cache.put(request, response.clone());
    }

    return response;
}

async function handleNavigationRequest(request) {
    try {
        return await fetch(request);
    } catch (error) {
        const cached = await caches.match(request);

        if (cached) {
            return cached;
        }

        const offline = await caches.match(OFFLINE_URL);
        return offline || Response.error();
    }
}

function shouldCacheResponse(response) {
    if (!response || response.status !== 200 || response.type === 'error') {
        return false;
    }

    const cacheControl = response.headers.get('Cache-Control') || '';

    if (cacheControl.includes('no-store') || cacheControl.includes('private')) {
        return false;
    }

    return true;
}
