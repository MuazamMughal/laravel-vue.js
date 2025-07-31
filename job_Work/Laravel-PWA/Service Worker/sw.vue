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
