const CACHE_NAME = 'offline-tasks-v1';
const urlsToCache = ['/', '/static/js/bundle.js', '/static/css/main.css', '/icon-192.png', '/icon-512.png'];

// Install: Cache the App Shell
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(urlsToCache))
      .then(() => self.skipWaiting())
  );
});

// Activate: Take over and clean old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch: Advanced Strategy (Network First for API, Cache First for App Shell)
self.addEventListener('fetch', (event) => {
  const { request } = event;

  // Strategy: Network First, fall back to Cache for API calls
  if (request.url.includes('/api/')) {
    event.respondWith(
      fetch(request)
        .then((networkResponse) => {
          // Clone response to store it in cache
          const responseClone = networkResponse.clone();
          caches.open(CACHE_NAME)
            .then((cache) => cache.put(request, responseClone));
          return networkResponse;
        })
        .catch(() => caches.match(request)) // Offline: Serve from cache
    );
    return;
  }
