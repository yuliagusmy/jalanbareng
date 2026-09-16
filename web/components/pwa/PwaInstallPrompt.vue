<template>
  <v-slide-y-reverse-transition>
    <div v-if="showPrompt" class="pwa-install-banner">
      <v-card elevation="12" rounded="xl" class="pa-4 pa-sm-5 bg-surface install-card">
        <div class="d-flex align-center ga-3 mb-3">
          <v-avatar size="52" rounded="xl" class="elevation-2 pwa-app-icon">
            <v-img src="/icons/icon-192.png" alt="Jalan Bareng"></v-img>
          </v-avatar>
          <div class="flex-grow-1 overflow-hidden">
            <div class="font-weight-bold text-subtitle-1 text-truncate">Pasang Aplikasi Jalan Bareng</div>
            <div class="text-caption text-grey line-clamp-1">
              Tambahkan ke layar utama untuk pengalaman lebih cepat & praktis
            </div>
          </div>
          <v-btn icon size="x-small" variant="text" color="grey" @click="dismissPrompt" class="ml-1">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </div>

        <div class="d-flex align-center justify-end ga-2">
          <v-btn variant="text" size="small" rounded="pill" color="grey-darken-1" @click="dismissPrompt">
            Nanti Saja
          </v-btn>
          <v-btn
            variant="flat"
            size="small"
            rounded="pill"
            color="primary"
            class="px-5 font-weight-bold"
            elevation="2"
            @click="installPwa"
          >
            <v-icon start size="small">mdi-download</v-icon>
            Pasang Sekarang
          </v-btn>
        </div>
      </v-card>
    </div>
  </v-slide-y-reverse-transition>
</template>

<script setup lang="ts">
const showPrompt = ref(false)
const deferredPrompt = ref<any>(null)

onMounted(() => {
  if (typeof window === 'undefined') return

  // Register service worker if supported
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').then((reg) => {
      console.log('[PWA] Service Worker registered with scope:', reg.scope)
    }).catch((err) => {
      console.warn('[PWA] Service Worker registration failed:', err)
    })
  }

  // Check if dismissed within last 3 days
  const dismissedTime = localStorage.getItem('pwa_prompt_dismissed')
  if (dismissedTime) {
    const hoursSince = (Date.now() - parseInt(dismissedTime, 10)) / (1000 * 60 * 60)
    if (hoursSince < 72) {
      return
    }
  }

  // Listen for beforeinstallprompt event
  window.addEventListener('beforeinstallprompt', (e: any) => {
    e.preventDefault()
    deferredPrompt.value = e
    showPrompt.value = true
  })

  // Detect if already installed
  window.addEventListener('appinstalled', () => {
    showPrompt.value = false
    deferredPrompt.value = null
    console.log('[PWA] Jalan Bareng was successfully installed!')
  })
})

const installPwa = async () => {
  if (!deferredPrompt.value) return
  deferredPrompt.value.prompt()
  const choiceResult = await deferredPrompt.value.userChoice
  if (choiceResult.outcome === 'accepted') {
    showPrompt.value = false
  }
  deferredPrompt.value = null
}

const dismissPrompt = () => {
  showPrompt.value = false
  if (typeof window !== 'undefined') {
    localStorage.setItem('pwa_prompt_dismissed', Date.now().toString())
  }
}
</script>

<style scoped>
.pwa-install-banner {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  max-width: 420px;
  width: calc(100% - 48px);
}

@media (max-width: 600px) {
  .pwa-install-banner {
    bottom: 16px;
    right: 16px;
    left: 16px;
    width: auto;
  }
}

.install-card {
  border: 1px solid rgba(102, 126, 234, 0.25);
  box-shadow: 0 12px 32px rgba(102, 126, 234, 0.18) !important;
}

.pwa-app-icon {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
