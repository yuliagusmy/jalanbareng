<template>
  <div v-if="authStore.isLoggedIn" class="notification-bell-wrapper">
    <v-menu v-model="menuOpen" :close-on-content-click="false" location="bottom end" offset="12" max-width="380" min-width="320">
      <template v-slot:activator="{ props }">
        <v-btn icon variant="text" v-bind="props" class="notification-btn" aria-label="Notifikasi">
          <v-badge
            v-if="unreadCount > 0"
            :content="unreadCount > 99 ? '99+' : unreadCount"
            color="error"
            offset-x="4"
            offset-y="4"
          >
            <v-icon>mdi-bell-outline</v-icon>
          </v-badge>
          <v-icon v-else>mdi-bell-outline</v-icon>
        </v-btn>
      </template>

      <v-card class="notification-card" rounded="xl" elevation="10">
        <!-- Header -->
        <div class="d-flex align-center justify-space-between px-4 py-3 border-b">
          <div class="d-flex align-center ga-2">
            <span class="font-weight-bold text-subtitle-1">Notifikasi</span>
            <v-chip v-if="unreadCount > 0" size="x-small" color="primary" variant="flat">
              {{ unreadCount }} baru
            </v-chip>
          </div>
          <v-btn
            v-if="unreadCount > 0"
            size="small"
            variant="text"
            color="primary"
            class="text-caption font-weight-medium px-2"
            @click="markAllAsRead"
          >
            Tandai Dibaca
          </v-btn>
        </div>

        <!-- Browser Permission Banner -->
        <div v-if="!hasBrowserPermission && canRequestBrowser" class="px-4 py-2 bg-amber-lighten-5 d-flex align-center justify-space-between border-b">
          <div class="d-flex align-center ga-2 text-caption text-amber-darken-4">
            <v-icon size="small" color="amber-darken-3">mdi-bell-ring-outline</v-icon>
            <span>Aktifkan pengingat browser</span>
          </div>
          <v-btn size="x-small" variant="flat" color="amber-darken-3" rounded="pill" @click="handleRequestPermission">
            Aktifkan
          </v-btn>
        </div>

        <!-- Notifications List -->
        <v-list class="py-0 notification-list" max-height="400" lines="two">
          <template v-if="loading && notifications.length === 0">
            <div class="pa-6 text-center text-grey">
              <v-progress-circular indeterminate size="24" color="primary" class="mb-2" />
              <div class="text-caption">Memuat notifikasi...</div>
            </div>
          </template>

          <template v-else-if="notifications.length === 0">
            <div class="pa-8 text-center text-grey-darken-1">
              <v-icon size="48" color="grey-lighten-1" class="mb-2">mdi-bell-sleep-outline</v-icon>
              <div class="text-subtitle-2 font-weight-bold">Belum Ada Notifikasi</div>
              <div class="text-caption text-grey">Aktivitas like, komentar, dan pengingat event akan muncul di sini.</div>
            </div>
          </template>

          <template v-else>
            <v-list-item
              v-for="item in notifications"
              :key="item.id"
              :class="['notification-item', { 'unread': !item.read }]"
              @click="handleItemClick(item)"
            >
              <template v-slot:prepend>
                <v-avatar size="40" :color="getTypeColor(item.type) + '-lighten-5'" class="mr-3">
                  <v-icon :color="getTypeColor(item.type)" size="20">
                    {{ getTypeIcon(item.type) }}
                  </v-icon>
                </v-avatar>
              </template>

              <v-list-item-title class="text-body-2 font-weight-medium line-clamp-2">
                {{ item.data?.message || 'Notifikasi baru' }}
              </v-list-item-title>

              <v-list-item-subtitle class="text-caption text-grey mt-1 d-flex align-center justify-space-between">
                <span>{{ formatTimeAgo(item.created_at) }}</span>
                <span v-if="!item.read" class="unread-dot"></span>
              </v-list-item-subtitle>
            </v-list-item>
          </template>
        </v-list>
      </v-card>
    </v-menu>
  </div>
</template>

<script setup lang="ts">
import { useNotifications, type AppNotification } from '~/composables/useNotifications'

const authStore = useAuthStore()
const {
  notifications,
  unreadCount,
  loading,
  hasBrowserPermission,
  checkPermission,
  requestPermission,
  fetchNotifications,
  markAsRead,
  markAllAsRead,
} = useNotifications()

const menuOpen = ref(false)
const canRequestBrowser = ref(false)

onMounted(() => {
  if (typeof window !== 'undefined' && 'Notification' in window) {
    canRequestBrowser.value = Notification.permission !== 'denied' && Notification.permission !== 'granted'
    checkPermission()
  }

  if (authStore.isLoggedIn) {
    fetchNotifications()
    // Poll every 30 seconds
    const interval = setInterval(() => {
      if (authStore.isLoggedIn) {
        fetchNotifications(true)
      }
    }, 30000)

    onUnmounted(() => clearInterval(interval))
  }
})

watch(() => authStore.isLoggedIn, (isLoggedIn) => {
  if (isLoggedIn) {
    fetchNotifications()
  }
})

const handleRequestPermission = async () => {
  const granted = await requestPermission()
  if (granted) {
    canRequestBrowser.value = false
  }
}

const handleItemClick = (item: AppNotification) => {
  markAsRead(item.id, item.data?.target_url)
  menuOpen.value = false
}

const getTypeIcon = (type: string) => {
  switch (type) {
    case 'like':
      return 'mdi-heart'
    case 'comment':
      return 'mdi-comment-text-outline'
    case 'event_reminder':
      return 'mdi-calendar-clock'
    default:
      return 'mdi-bell'
  }
}

const getTypeColor = (type: string) => {
  switch (type) {
    case 'like':
      return 'error'
    case 'comment':
      return 'info'
    case 'event_reminder':
      return 'warning'
    default:
      return 'primary'
  }
}

const formatTimeAgo = (dateStr: string) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const now = new Date()
  const diffSec = Math.floor((now.getTime() - date.getTime()) / 1000)

  if (diffSec < 60) return 'Baru saja'
  if (diffSec < 3600) return `${Math.floor(diffSec / 60)} mnt lalu`
  if (diffSec < 86400) return `${Math.floor(diffSec / 3600)} jam lalu`
  if (diffSec < 604800) return `${Math.floor(diffSec / 86400)} hari lalu`
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
}
</script>

<style scoped>
.notification-card {
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.08);
}

.notification-list {
  overflow-y: auto;
}

.notification-item {
  cursor: pointer;
  transition: background-color 0.2s ease;
  border-bottom: 1px solid rgba(0, 0, 0, 0.04);
}

.notification-item:hover {
  background-color: rgba(0, 0, 0, 0.03);
}

.notification-item.unread {
  background-color: rgba(102, 126, 234, 0.07);
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.unread-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #667eea;
  display: inline-block;
}
</style>
