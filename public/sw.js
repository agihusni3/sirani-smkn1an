// ══════════════════════════════════════════════════════════════
// SIRANI Portal Wali Murid — Service Worker (PWA Offline Ready)
// ══════════════════════════════════════════════════════════════
const CACHE_NAME = 'sirani-ortu-v6';
const OFFLINE_URL = '/cek-presensi';

// Aset statis yang di-cache saat install (shell app)
const STATIC_ASSETS = [
  '/cek-presensi',
  '/manifest.json',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  '/icons/maskable-icon-512.png',
  '/logo.png',
  '/sounds/notif-chime.wav',
  '/sounds/notif-bell.wav',
  '/sounds/notif-soft.wav',
  '/sounds/notif-alert.wav',
];

// ── INDEXEDDB HELPER UNTUK PESAN & BADGE NOTIFIKASI ───────────
const NOTIF_DB_NAME = 'sirani_pwa_notifs_v1';
const NOTIF_DB_STORE = 'messages';

function openNotifDB() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open(NOTIF_DB_NAME, 1);
    request.onupgradeneeded = (event) => {
      const db = event.target.result;
      if (!db.objectStoreNames.contains(NOTIF_DB_STORE)) {
        const store = db.createObjectStore(NOTIF_DB_STORE, { keyPath: 'id' });
        store.createIndex('time', 'time', { unique: false });
        store.createIndex('is_read', 'is_read', { unique: false });
      }
    };
    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error);
  });
}

async function saveNotificationToDB(notifItem) {
  try {
    const db = await openNotifDB();
    return new Promise((resolve) => {
      const tx = db.transaction(NOTIF_DB_STORE, 'readwrite');
      tx.objectStore(NOTIF_DB_STORE).put(notifItem);
      tx.oncomplete = () => resolve(true);
      tx.onerror = () => resolve(false);
    });
  } catch (e) {
    return false;
  }
}

async function getUnreadCountFromDB() {
  try {
    const db = await openNotifDB();
    return new Promise((resolve) => {
      const tx = db.transaction(NOTIF_DB_STORE, 'readonly');
      const store = tx.objectStore(NOTIF_DB_STORE);
      const req = store.getAll();
      req.onsuccess = () => {
        const items = req.result || [];
        const unread = items.filter((i) => !i.is_read).length;
        resolve(unread);
      };
      req.onerror = () => resolve(0);
    });
  } catch (e) {
    return 0;
  }
}

async function markNotifReadInDB(id = null) {
  try {
    const db = await openNotifDB();
    return new Promise((resolve) => {
      const tx = db.transaction(NOTIF_DB_STORE, 'readwrite');
      const store = tx.objectStore(NOTIF_DB_STORE);
      const req = store.getAll();
      req.onsuccess = () => {
        const items = req.result || [];
        for (const item of items) {
          if (!id || item.id === id) {
            item.is_read = true;
            store.put(item);
          }
        }
      };
      tx.oncomplete = () => resolve(true);
      tx.onerror = () => resolve(false);
    });
  } catch (e) {
    return false;
  }
}

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

  const notifId = data.tag || ('sirani-notif-' + Date.now());
  const notifItem = {
    id: notifId,
    title: data.title || 'SIRANI — Presensi Siswa',
    body: data.body || 'Ada pembaruan data presensi ananda.',
    url: data.url || '/cek-presensi',
    time: data.timestamp ? (data.timestamp * 1000) : Date.now(),
    is_read: false,
    tag: notifId,
  };

  const handlePushEvent = async () => {
    // 1. Simpan pesan ke IndexedDB lokal agar riwayat tidak hilang
    await saveNotificationToDB(notifItem);

    // 2. Hitung jumlah total pesan yang belum dibaca
    const unreadCount = await getUnreadCountFromDB();

    // 3. SET TANDA ANGKA (BADGE) PADA IKON APLIKASI DI HP (App Badging API)
    if ('setAppBadge' in self.navigator) {
      try {
        await self.navigator.setAppBadge(unreadCount);
      } catch (e) {
        console.warn('Set badge error in SW:', e);
      }
    }

    // 4. Siapkan opsi notifikasi standar W3C
    const options = {
      body: notifItem.body,
      icon: '/icons/icon-192.png',
      badge: '/icons/icon-192.png',
      vibrate: [400, 200, 400, 200, 400],
      tag: notifId,
      renotify: true,
      timestamp: notifItem.time,
      data: {
        url: notifItem.url,
        id: notifId,
      },
    };

    if (data.image && typeof data.image === 'string' && data.image.startsWith('http')) {
      options.image = data.image;
    }

    // 5. Tampilkan Notifikasi Sistem di Layar Kunci / Notification Bar (Bulletproof fallback)
    try {
      await self.registration.showNotification(notifItem.title, options);
    } catch (err) {
      console.warn('Full options showNotification failed, trying basic notification:', err);
      try {
        await self.registration.showNotification(notifItem.title, {
          body: notifItem.body,
          icon: '/icons/icon-192.png',
          tag: notifId,
        });
      } catch (e) {}
    }

    // 6. Teruskan pesan ke halaman portal yang sedang terbuka (jika ada)
    const clientList = await clients.matchAll({ type: 'window', includeUncontrolled: true });
    for (const client of clientList) {
      client.postMessage({
        type: 'SIRANI_PUSH_RECEIVED',
        title: notifItem.title,
        body: notifItem.body,
        unreadCount: unreadCount,
        notif: notifItem,
      });
    }
  };

  event.waitUntil(handlePushEvent());
});

// ── MESSAGE LISTENER: Komunikasi antara Frontend Portal & SW ─
self.addEventListener('message', async (event) => {
  if (!event.data) return;

  // Permintaan daftar riwayat notifikasi & jumlah belum dibaca (Hanya Hari Ini)
  if (event.data.type === 'SIRANI_GET_NOTIFS') {
    try {
      const db = await openNotifDB();
      const tx = db.transaction(NOTIF_DB_STORE, 'readwrite');
      const store = tx.objectStore(NOTIF_DB_STORE);
      const req = store.getAll();
      req.onsuccess = () => {
        const now = new Date();
        const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
        const rawItems = req.result || [];
        const items = [];
        for (const item of rawItems) {
          if ((item.time || 0) < startOfToday) {
            store.delete(item.id); // Bersihkan pesan hari kemarin
          } else {
            items.push(item);
          }
        }
        items.sort((a, b) => (b.time || 0) - (a.time || 0));
        const unread = items.filter((i) => !i.is_read).length;
        if (event.source && event.source.postMessage) {
          event.source.postMessage({
            type: 'SIRANI_NOTIFS_LIST',
            items: items,
            unreadCount: unread,
          });
        }
      };
    } catch (e) {}
  }

  // Reset total pesan jika pergantian hari
  if (event.data.type === 'SIRANI_CLEAR_ALL_NOTIFS') {
    try {
      const db = await openNotifDB();
      const tx = db.transaction(NOTIF_DB_STORE, 'readwrite');
      tx.objectStore(NOTIF_DB_STORE).clear();
      if ('clearAppBadge' in self.navigator) {
        try { await self.navigator.clearAppBadge(); } catch(e) {}
      }
    } catch(e) {}
  }

  // Permintaan menandai semua atau salah satu pesan telah dibaca
  if (event.data.type === 'SIRANI_MARK_ALL_READ') {
    await markNotifReadInDB(null);
    if ('clearAppBadge' in self.navigator) {
      try {
        await self.navigator.clearAppBadge();
      } catch (e) {}
    }
    if (event.source && event.source.postMessage) {
      event.source.postMessage({
        type: 'SIRANI_ALL_READ_ACK',
        unreadCount: 0,
      });
    }
  }

  if (event.data.type === 'SIRANI_MARK_SINGLE_READ' && event.data.id) {
    await markNotifReadInDB(event.data.id);
    const unread = await getUnreadCountFromDB();
    if ('setAppBadge' in self.navigator) {
      try {
        if (unread <= 0 && 'clearAppBadge' in self.navigator) {
          await self.navigator.clearAppBadge();
        } else {
          await self.navigator.setAppBadge(unread);
        }
      } catch (e) {}
    }
    if (event.source && event.source.postMessage) {
      event.source.postMessage({
        type: 'SIRANI_SINGLE_READ_ACK',
        id: event.data.id,
        unreadCount: unread,
      });
    }
  }

  // Sinkronisasi preferensi suara dari client
  if (event.data.type === 'SIRANI_SET_SOUND_PREFERENCE') {
    if (event.source && event.source.postMessage) {
      event.source.postMessage({
        type: 'SIRANI_SOUND_PREFERENCE_ACK',
        sound: event.data.sound,
        volume: event.data.volume,
      });
    }
  }
});

// ── NOTIFICATION CLICK: Buka halaman portal & bersihkan badge ─
self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  if (event.action === 'close') return;

  const targetUrl = event.notification.data?.url || '/cek-presensi';
  const notifId   = event.notification.data?.id;

  const handleClickEvent = async () => {
    // 1. Tandai pesan ini telah dibaca di database lokal
    if (notifId) {
      await markNotifReadInDB(notifId);
    }
    const unreadCount = await getUnreadCountFromDB();

    // 2. Perbarui badge angka pada aplikasi
    if ('setAppBadge' in self.navigator) {
      try {
        if (unreadCount <= 0 && 'clearAppBadge' in self.navigator) {
          await self.navigator.clearAppBadge();
        } else {
          await self.navigator.setAppBadge(unreadCount);
        }
      } catch (e) {}
    }

    // 3. Arahkan atau buka halaman target
    const clientList = await clients.matchAll({ type: 'window', includeUncontrolled: true });
    for (const client of clientList) {
      if (client.url.includes(self.location.origin) && 'focus' in client) {
        client.navigate(targetUrl);
        return client.focus();
      }
    }
    if (clients.openWindow) {
      return clients.openWindow(targetUrl);
    }
  };

  event.waitUntil(handleClickEvent());
});
