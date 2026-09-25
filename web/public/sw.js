// Jalan Bareng Service Worker
const CACHE_NAME = 'jalan-bareng-cache-v2'
const STATIC_ASSETS = [
  '/',
  '/manifest.webmanifest',
  '/favicon.ico',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  '/icons/icon-maskable.png',
  '/icons/icon.svg',
]

// Install event - Cache static assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS)
    }).then(() => self.skipWaiting())
  )
})

// Activate event - Cleanup old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      )
    }).then(() => self.clients.claim())
  )
})

// Fetch event - Network-first for API & navigations, Cache-first for images/icons
self.addEventListener('fetch', (event) => {
  const request = event.request
  const url = new URL(request.url)

  // Skip non-GET requests and chrome-extension/other schemes
  if (request.method !== 'GET' || !url.protocol.startsWith('http')) {
    return
  }

  // Do not intercept or cache local dev files or _nuxt bundles
  if (url.hostname === 'localhost' || url.hostname === '127.0.0.1' || url.pathname.includes('/_nuxt/')) {
    return
  }

  // Handle static assets with Cache-first strategy
  if (url.pathname.startsWith('/icons/')) {
    event.respondWith(
      caches.match(request).then((cached) => {
        if (cached) return cached
        return fetch(request).then((response) => {
          if (response && response.status === 200) {
            const clone = response.clone()
            caches.open(CACHE_NAME).then((cache) => cache.put(request, clone))
          }
          return response
        }).catch(() => caches.match(request))
      })
    )
    return
  }

  // Network-first for pages and API
  event.respondWith(
    fetch(request)
      .then((response) => {
        return response
      })
      .catch(() => {
        return caches.match(request).then((cached) => {
          if (cached) return cached
          // Fallback to home page if navigation fails offline
          if (request.mode === 'navigate') {
            return caches.match('/')
          }
          return null
        })
      })
  )
})

// Handle background Push Notifications
self.addEventListener('push', (event) => {
  let data = { title: 'Jalan Bareng', body: 'Ada kabar baru dari komunitas!', url: '/' }
  if (event.data) {
    try {
      data = event.data.json()
    } catch (e) {
      data.body = event.data.text()
    }
  }

  const options = {
    body: data.body,
    icon: '/icons/icon-192.png',
    badge: '/icons/icon-192.png',
    vibrate: [100, 50, 100],
    data: {
      url: data.url || '/'
    }
  }

  event.waitUntil(
    self.registration.showNotification(data.title || 'Jalan Bareng', options)
  )
})

// Handle Notification Click
self.addEventListener('notificationclick', (event) => {
  event.notification.close()
  const targetUrl = event.notification.data?.url || '/'

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      for (const client of clientList) {
        if (client.url === targetUrl && 'focus' in client) {
          return client.focus()
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(targetUrl)
      }
    })
  )
})
