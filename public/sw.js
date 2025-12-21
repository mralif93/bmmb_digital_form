// Service Worker for BMMB Digital Forms - Offline Support
const CACHE_NAME = 'bmmb-forms-v1';
const DYNAMIC_CACHE = 'bmmb-dynamic-v1';

// Assets to cache immediately on install
const STATIC_ASSETS = [
    '/',
    '/login',
    '/register',
    '/forms/dar',
    '/forms/dcr',
    '/forms/raf',
    '/forms/srf',
    '/offline.html'
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('Service Worker: Caching static assets');
            return cache.addAll(STATIC_ASSETS).catch((err) => {
                console.log('Service Worker: Error caching some assets', err);
                // Continue even if some assets fail to cache
                return Promise.resolve();
            });
        })
    );
    self.skipWaiting(); // Activate immediately
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME && cacheName !== DYNAMIC_CACHE) {
                        console.log('Service Worker: Deleting old cache', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Fetch event - handle network requests
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip cross-origin requests and non-GET requests
    if (url.origin !== location.origin || request.method !== 'GET') {
        return;
    }

    // Skip admin routes - let them go through normally without service worker
    if (url.pathname.startsWith('/admin/')) {
        return;
    }

    // Handle different types of requests
    if (isHTMLRequest(request)) {
        // For HTML requests, try network first, fallback to cache
        event.respondWith(networkFirstStrategy(request));
    } else if (isStaticAsset(request.url)) {
        // For static assets (CSS, JS, images), use cache first
        event.respondWith(cacheFirstStrategy(request));
    } else {
        // For API/data requests, try network first
        event.respondWith(networkFirstStrategy(request));
    }
});

// Helper functions
function isHTMLRequest(request) {
    return request.headers.get('accept')?.includes('text/html');
}

function isStaticAsset(url) {
    return url.includes('.css') || 
           url.includes('.js') || 
           url.includes('.png') || 
           url.includes('.jpg') || 
           url.includes('.jpeg') || 
           url.includes('.svg') || 
           url.includes('.woff') || 
           url.includes('.woff2') ||
           url.includes('fonts.googleapis.com') ||
           url.includes('cdn.tailwindcss.com') ||
           url.includes('unpkg.com');
}

// Cache first strategy - for static assets
function cacheFirstStrategy(request) {
    return caches.match(request).then((cachedResponse) => {
        if (cachedResponse) {
            return cachedResponse;
        }

        return fetch(request).then((networkResponse) => {
            // Don't cache if it's not a valid response
            if (!networkResponse || networkResponse.status !== 200) {
                return networkResponse;
            }

            // Clone the response before caching
            const responseToCache = networkResponse.clone();
            
            caches.open(DYNAMIC_CACHE).then((cache) => {
                cache.put(request, responseToCache);
            });

            return networkResponse;
        }).catch(() => {
            // If fetch fails and no cache, show offline page for HTML requests
            if (isHTMLRequest(request)) {
                return caches.match('/offline.html');
            }
            // Return a generic response for other failed requests
            return new Response('Offline', { status: 408 });
        });
    });
}

// Network first strategy - for HTML and API requests
function networkFirstStrategy(request) {
    return fetch(request).then((networkResponse) => {
        // Clone the response before caching
        if (networkResponse.status === 200) {
            const responseToCache = networkResponse.clone();
            
            caches.open(DYNAMIC_CACHE).then((cache) => {
                cache.put(request, responseToCache);
            });
        }

        return networkResponse;
    }).catch(() => {
        // Network failed, try cache
        return caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse;
            }

            // If it's an HTML request and no cache, show offline page
            if (isHTMLRequest(request)) {
                return caches.match('/offline.html');
            }

            // Return a generic offline response
            return new Response('You are offline', { 
                status: 503,
                headers: { 'Content-Type': 'text/plain' }
            });
        });
    });
}

// Background sync for form submissions (if browser supports it)
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-forms') {
        console.log('Service Worker: Background sync triggered');
        event.waitUntil(syncForms());
    }
});

function syncForms() {
    // Get queued forms from IndexedDB and retry submission
    return new Promise((resolve) => {
        console.log('Service Worker: Syncing forms...');
        // TODO: Implement form sync logic with IndexedDB
        resolve();
    });
}

// Push notification handler (for future use)
self.addEventListener('push', (event) => {
    console.log('Service Worker: Push notification received');
    // TODO: Handle push notifications
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('/')
    );
});

