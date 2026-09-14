const CACHE_NAME = 'lms-dani-v2';
const ASSETS_TO_CACHE = [
  '/css/theme-custom.css',
  '/manifest.json'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE);
    }).catch(() => {
      // Graceful fallback if any asset fails
    })
  );
  self.skipWaiting();
});

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
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  // Only cache static GET requests, NEVER HTML pages
  if (event.request.method === 'GET') {
    const url = new URL(event.request.url);
    const acceptHeader = event.request.headers.get('accept') || '';
    if (acceptHeader.includes('text/html') || url.pathname.startsWith('/student/') || url.pathname.startsWith('/admin/')) {
      return; // Network direct for HTML and dynamic app routes
    }
    event.respondWith(
      fetch(event.request).catch(() => {
        return caches.match(event.request);
      })
    );
  }
});
