// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: false },

  nitro: {
    preset: 'vercel'
  },

  app: {
    head: {
      title: 'Jalan Bareng - Komunitas Pejalan Kaki',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'theme-color', content: '#000000' },
        { name: 'mobile-web-app-capable', content: 'yes' },
        { name: 'apple-mobile-web-app-capable', content: 'yes' },
        { name: 'apple-mobile-web-app-status-bar-style', content: 'default' },
        { name: 'apple-mobile-web-app-title', content: 'Jalan Bareng' },
        { name: 'description', content: 'Platform komunitas untuk menjelajah kota bersama Jalan Bareng, berbagi destinasi menarik, dan mengikuti event seru.' }
      ],
      link: [
        { rel: 'manifest', href: '/manifest.webmanifest' },
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
        { rel: 'icon', type: 'image/png', sizes: '192x192', href: '/icons/icon-192.png' },
        { rel: 'icon', type: 'image/png', sizes: '512x512', href: '/icons/icon-512.png' },
        { rel: 'apple-touch-icon', href: '/icons/icon-192.png' },
        // Preconnect to critical origins (faster DNS/TLS)
        { rel: 'preconnect', href: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8001' },
        { rel: 'dns-prefetch', href: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8001' },
        // Preload critical fonts (Inter from system or CDN)
        // Note: If using system fonts, this can be removed
        // If using web fonts, uncomment and adjust:
        // { rel: 'preload', href: '/fonts/Inter-Regular.woff2', as: 'font', type: 'font/woff2', crossorigin: 'anonymous' },
        // { rel: 'preload', href: '/fonts/Inter-Bold.woff2', as: 'font', type: 'font/woff2', crossorigin: 'anonymous' },
      ],
      style: [
        // Inline critical font-face declarations for faster rendering
        {
          children: `
            /* Use system fonts as fallback (fastest) */
            body {
              font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
              font-display: swap;
            }
          `,
          type: 'text/css'
        }
      ]
    }
  },

  typescript: {
    types: ['@types/node']
  },

  modules: [
    '@pinia/nuxt',
    '@vueuse/nuxt',
    'vuetify-nuxt-module'
  ],

  components: [
    {
      path: '~/components',
      pathPrefix: false,
    },
  ],

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost:8001',
      apiUrl: process.env.NUXT_PUBLIC_API_URL || 'http://localhost:8001/api',
      googleMapsApiKey: process.env.NUXT_PUBLIC_GOOGLE_MAPS_API_KEY || '',
      googleMapsMapId: process.env.NUXT_PUBLIC_GOOGLE_MAPS_MAP_ID || ''
    }
  },

  vuetify: {
    vuetifyOptions: {
      theme: {
        defaultTheme: 'light',
        themes: {
          light: {
            colors: {
              primary: '#DC2626',
              secondary: '#111827',
              accent: '#FACC15',
              warning: '#F59E0B',
              error: '#EF4444',
              info: '#0284C7',
              success: '#16A34A',
              background: '#FAFAF9',
              surface: '#FFFFFF',
            },
          },
          dark: {
            colors: {
              primary: '#2196F3',
              secondary: '#424242',
              accent: '#FF4081',
              error: '#FF5252',
              info: '#2196F3',
              success: '#4CAF50',
              warning: '#FB8C00',
            },
          },
        },
      },
    }
  },

  css: [
    '@mdi/font/css/materialdesignicons.css'
  ],

  vite: {
    server: {
      allowedHosts: true
    },
    build: {
      // Code splitting optimization
      rollupOptions: {
        output: {
          manualChunks: {
            // Vendor chunks
            'vendor-vue': ['vue', 'vue-router', '@vue/runtime-core'],
            'vendor-vuetify': ['vuetify'],
            'vendor-pinia': ['pinia'],
            // Map libraries (heavy, split separately)
            'vendor-maps': ['leaflet'],
          },
        },
      },
      // Chunk size warning limit
      chunkSizeWarningLimit: 1000,
    },
    optimizeDeps: {
      // Pre-bundle these dependencies
      include: [
        'vue',
        'vue-router',
        'pinia',
        'vuetify',
        '@mdi/font/css/materialdesignicons.css',
      ],
    },
  },

  // Performance optimizations
  experimental: {
    payloadExtraction: true, // Extract payload for better caching
    renderJsonPayloads: true, // Optimize JSON payloads
    viewTransition: true, // Enable view transitions API
  },

  // Optimize build
  build: {
    transpile: ['vuetify'],
  },

  // Route rules for caching
  routeRules: {
    // Cache static pages
    '/': { swr: 3600 }, // Stale-while-revalidate for 1 hour
    '/aktivasi': { swr: 1800 },
    '/destinations': { swr: 1800 },
    '/cerita': { swr: 1800 },
    '/events': { swr: 1800 },
    '/mitra': { swr: 3600 },
    // Dynamic pages
    '/aktivasi/**': { swr: 1800 },
    '/destinations/**': { swr: 1800 },
    '/cerita/**': { swr: 1800 },
    '/events/**': { swr: 1800 },
    // Static assets
    '/_nuxt/**': { headers: { 'cache-control': 'public, max-age=31536000, immutable' } },
    '/images/**': { headers: { 'cache-control': 'public, max-age=31536000, immutable' } },
    '/icons/**': { headers: { 'cache-control': 'public, max-age=31536000, immutable' } },
  },

  // Optimize images
  image: {
    // Future: use Nuxt Image module for automatic optimization
    // For now, we use custom LazyImage component with useOptimizedImage composable
  },
})
