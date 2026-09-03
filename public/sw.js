// ══════════════════════════════════════════════════════════════
// SIRANI Portal Wali Murid — Service Worker (PWA Offline Ready)
// ══════════════════════════════════════════════════════════════
const CACHE_NAME = 'sirani-ortu-v2';
const OFFLINE_URL = '/cek-presensi';

// Aset statis yang di-cache saat install (shell app)
const STATIC_ASSETS = [
  '/cek-presensi',
  '/manifest.json',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  '/icons/maskable-icon-512.png',
  '/logo.png',
];

// ── INSTALL: Cache shell statis ──────────────────────────────
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    }).then(() => self.skipWaiting())
  );
});

// ── ACTIVATE: Hapus cache lama ───────────────────────────────
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((key) => key !== CACHE_NAME)
          .map((key) => caches.delete(key))
      )
    ).then(() => self.clients.claim())
  );
});

// ── FETCH: Strategi Jaringan Dulu, Cache sebagai Fallback ────
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // Lewati request non-GET, API eksternal, dan admin routes
  if (event.request.method !== 'GET') return;
  if (!url.origin.includes(self.location.origin)) return;
  if (url.pathname.startsWith('/api/')) return;
  if (url.pathname.startsWith('/admin/')) return;
  if (url.pathname.startsWith('/storage/')) return;

  // Font & CDN → cache-first
  if (
    url.hostname.includes('fonts.googleapis.com') ||
    url.hostname.includes('fonts.gstatic.com') ||
    url.hostname.includes('cdn.jsdelivr.net')
  ) {
    event.respondWith(
      caches.open(CACHE_NAME).then(async (cache) => {
        const cached = await cache.match(event.request);
        if (cached) return cached;
        const response = await fetch(event.request);
        if (response.ok) cache.put(event.request, response.clone());
        return response;
      })
    );
    return;
  }

  // Halaman Portal Orang Tua → Network first, fallback ke cache
  if (
    url.pathname.startsWith('/cek-presensi') ||
    url.pathname.startsWith('/presensi-siswa/')
  ) {
    event.respondWith(
      fetch(event.request)
        .then((response) => {
          if (response.ok) {
            const clone = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
          }
          return response;
        })
        .catch(async () => {
          const cached = await caches.match(event.request);
          if (cached) return cached;
          // Fallback ke halaman utama portal
          return caches.match(OFFLINE_URL);
        })
    );
    return;
  }

  // Aset statis (js, css, img) → cache-first
  if (
    url.pathname.endsWith('.js') ||
    url.pathname.endsWith('.css') ||
    url.pathname.endsWith('.png') ||
    url.pathname.endsWith('.jpg') ||
    url.pathname.endsWith('.svg') ||
    url.pathname.endsWith('.ico') ||
    url.pathname.endsWith('.woff2')
  ) {
    event.respondWith(
      caches.open(CACHE_NAME).then(async (cache) => {
        const cached = await cache.match(event.request);
        if (cached) return cached;
        const response = await fetch(event.request);
        if (response.ok) cache.put(event.request, response.clone());
        return response;
      })
    );
    return;
  }

  // Default: network-first
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request))
  );
});

// ── PUSH NOTIFICATION: Terima push dari server ───────────────
self.addEventListener('push', (event) => {
  if (!event.data) return;

  let data = {};
  try {
    data = event.data.json();
  } catch {
    data = { title: 'SIRANI', body: event.data.text() };
  }

  const options = {
    body: data.body || 'Ada pembaruan data presensi ananda.',
    icon: '/icons/icon-192.png',
    badge: '/icons/icon-192.png',
    image: data.image || null,
    vibrate: [200, 100, 200],
    data: {
      url: data.url || '/cek-presensi',
    },
    actions: [
      { action: 'view', title: '📋 Lihat Presensi' },
      { action: 'close', title: 'Tutup' },
    ],
  };

  event.waitUntil(
    self.registration.showNotification(data.title || 'SIRANI — Presensi Siswa', options)
  );
});

// ── NOTIFICATION CLICK: Buka halaman portal ─────────────────
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'close') return;

  const targetUrl = event.notification.data?.url || '/cek-presensi';

  event.waitUntil(
    clients
      .matchAll({ type: 'window', includeUncontrolled: true })
      .then((clientList) => {
        for (const client of clientList) {
          if (client.url.includes(self.location.origin) && 'focus' in client) {
            client.navigate(targetUrl);
            return client.focus();
          }
        }
        if (clients.openWindow) return clients.openWindow(targetUrl);
      })
  );
});
