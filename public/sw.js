/*
 * Service Worker — پرده‌خوان (فقط پنل مخاطب /panel)
 *
 * هدف: صفحهٔ «آفلاین» ساده وقتی اتصال اینترنت قطع است.
 * این SW محتوایی را کش نمی‌کند؛ فقط یک صفحهٔ فال‌بک آفلاین + آیکون اپ را
 * پیش‌کش می‌کند و برای درخواست‌های ناوبری (navigate) که شبکه ناموفق باشد،
 * همان صفحهٔ آفلاین را برمی‌گرداند. بقیهٔ درخواست‌ها دست‌نخورده به شبکه می‌روند.
 *
 * برای انتشار نسخهٔ جدید، فقط CACHE_VERSION را بالا ببرید.
 */

const CACHE_VERSION = 'v1';
const CACHE_NAME = 'pardekhan-panel-offline-' + CACHE_VERSION;

const OFFLINE_URL = '/pwa/offline.html';
const PRECACHE_URLS = [
    OFFLINE_URL,
    '/pwa/icon-192.png',
];

// نصب: پیش‌کش صفحهٔ آفلاین و آیکون
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

// فعال‌سازی: پاک‌کردن کش‌های نسخهٔ قبلی این SW
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key.startsWith('pardekhan-panel-offline-') && key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

// fetch: فقط برای ناوبری‌ها network-first + فال‌بک آفلاین. بقیه دست‌نخورده.
self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.mode !== 'navigate') {
        return; // اجازه بده مرورگر عادی رفتار کند (بدون کش)
    }

    event.respondWith(
        fetch(request).catch(() =>
            caches.open(CACHE_NAME).then((cache) => cache.match(OFFLINE_URL))
        )
    );
});
