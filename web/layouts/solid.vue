<template>
  <v-app>
    <!-- Solid Header - Always with background -->
    <v-app-bar elevation="0" height="0" class="solid-header" :class="appBarClass">
      <template v-slot:extension>
        <v-container class="d-flex align-center header-container" style="height: 70px;">
          <!-- Mobile Menu Icon -->
          <v-app-bar-nav-icon class="d-md-none" @click="drawer = !drawer"
            color="primary"></v-app-bar-nav-icon>

          <!-- Logo/Brand -->
          <NuxtLink to="/" class="text-decoration-none d-flex align-center mr-4">
            <img
              src="/images/logo-jalan-bareng.png"
              alt="Jalan Bareng"
              style="height: 38px; width: auto; object-fit: contain;"
            />
          </NuxtLink>

          <v-spacer></v-spacer>

          <!-- Desktop Navigation -->
          <NavigationMenu text-color="secondary" admin-color="primary" />

          <v-spacer></v-spacer>

          <!-- User Menu / Login Button -->
          <template v-if="authStore.isLoggedIn">
            <NotificationBell class="mr-1" />
            <v-menu offset-y>
              <template v-slot:activator="{ props }">
                <v-btn icon v-bind="props" class="ml-3 avatar-btn">
                  <v-avatar size="40" class="avatar-shadow">
                    <v-img v-if="authStore.user?.photo" :src="getImageUrl(authStore.user.photo)"
                      :alt="authStore.user.name"></v-img>
                    <v-icon v-else color="primary">mdi-account-circle</v-icon>
                  </v-avatar>
                </v-btn>
              </template>
              <v-list min-width="280" rounded="xl" class="py-2">
                <v-list-item class="px-4 py-3">
                  <template v-slot:prepend>
                    <v-avatar size="48">
                      <v-img v-if="authStore.user?.photo" :src="getImageUrl(authStore.user.photo)"></v-img>
                      <v-icon v-else size="large">mdi-account-circle</v-icon>
                    </v-avatar>
                  </template>
                  <v-list-item-title class="font-weight-bold">{{ authStore.user?.name }}</v-list-item-title>
                  <v-list-item-subtitle class="text-caption">{{ authStore.user?.email }}</v-list-item-subtitle>
                </v-list-item>

                <v-divider class="my-2"></v-divider>

                <v-list-item to="/profile" rounded="lg" class="mx-2">
                  <template v-slot:prepend>
                    <v-icon>mdi-account</v-icon>
                  </template>
                  <v-list-item-title>Profil Saya</v-list-item-title>
                </v-list-item>

                <v-list-item to="/destinations/my-posts" rounded="lg" class="mx-2">
                  <template v-slot:prepend>
                    <v-icon color="primary">mdi-post</v-icon>
                  </template>
                  <v-list-item-title>Postingan Saya</v-list-item-title>
                </v-list-item>

                <v-divider class="my-2"></v-divider>

                <v-list-item @click="handleLogout" rounded="lg" class="mx-2">
                  <template v-slot:prepend>
                    <v-icon color="error">mdi-logout</v-icon>
                  </template>
                  <v-list-item-title class="text-error">Keluar</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </template>
          <template v-else>
            <v-btn to="/login" variant="flat" color="primary" class="ml-3 login-btn" rounded="pill" size="default">
              Masuk
            </v-btn>
          </template>
        </v-container>
      </template>
    </v-app-bar>

    <MobileNavigation v-model="drawer" />

    <v-main>
      <slot />
    </v-main>

    <!-- Modern Footer with Gradient -->
    <!-- Centralized Modern Footer Component -->
    <Footer />
  </v-app>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onBeforeUnmount, computed } from 'vue'
import { useWindowScroll } from '@vueuse/core'
import { useAuthStore } from '~/stores/auth'

const authStore = useAuthStore()
const drawer = ref(false)

const { y } = useWindowScroll()
const { getImageUrl } = useImageUrl()
const isAppBarVisible = ref(true)
let hideTimeout: NodeJS.Timeout | null = null

watch(y, (newY) => {
  // Clear any existing timeout
  if (hideTimeout) {
    clearTimeout(hideTimeout)
  }

  // Make the bar visible whenever scrolling happens
  isAppBarVisible.value = true

  // If we are at the top, just keep it visible and don't set a timeout to hide it
  if (newY === 0) {
    return
  }

  // Set a new timeout to hide the app bar after 3 seconds of no scrolling
  hideTimeout = setTimeout(() => {
    isAppBarVisible.value = false
  }, 3000)
}, { immediate: true })

const appBarClass = computed(() => {
  const classes = ['app-bar-solid']

  if (!isAppBarVisible.value && y.value > 50) {
    classes.push('app-bar-hidden')
  }

  return classes.join(' ')
})

const handleLogout = async () => {
  await authStore.logout()
}

// Fetch user on mount if token exists
onMounted(async () => {
  const authStore = useAuthStore()
  const authToken = useCookie('auth_token')
  if (authToken.value && !authStore.isLoggedIn) {
    await authStore.fetchUser()
  }
  await fetchFooterPages()
  await fetchFooterSettings()
})

const footerPages = ref<any[]>([])
const footerSettings = ref<any>({})
const api = useApi()

const fetchFooterPages = async () => {
  try {
    const response = await api.get('/pages/footer')
    footerPages.value = response.data.pages || []
  } catch (error) {
    console.error('Error fetching footer pages:', error)
  }
}

const fetchFooterSettings = async () => {
  try {
    const response = await api.get('/settings/footer')
    footerSettings.value = response.data.settings || {}
  } catch (error) {
    console.error('Error fetching footer settings:', error)
  }
}

const brandTitle = computed(() => footerSettings.value.footer_brand_title || 'Jalan Bareng')
const brandDescription = computed(() => footerSettings.value.footer_brand_description || 'Platform komunitas untuk berbagi dan menemukan destinasi menarik.')

onBeforeUnmount(() => {
  if (hideTimeout) {
    clearTimeout(hideTimeout)
  }
})
</script>

<style>
/* Solid Header Styles - Always with white background */
.solid-header {
  background: transparent !important;
  background-color: transparent !important;
  position: fixed !important;
  top: 0 !important;
  z-index: 1000 !important;
  transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
}

.solid-header.app-bar-hidden {
  transform: translateY(-100%);
}

.solid-header .v-toolbar__content {
  display: none !important;
}

.solid-header .v-toolbar__extension {
  padding: 0 !important;
  height: 70px !important;
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
  transition: background 0.3s ease, box-shadow 0.3s ease, backdrop-filter 0.3s ease;
}

.header-container {
  background: transparent !important;
}

/* Ensure main content has proper top spacing */
.v-main {
  padding-top: 70px !important;
}
</style>

<style scoped>
.brand-text {
  letter-spacing: -0.5px;
  transition: all 0.3s ease;
}

/* Prevent brand text truncation on mobile */
.v-toolbar-title {
  max-width: none !important;
  width: auto !important;
  overflow: visible !important;
  flex-shrink: 0 !important;
}

@media (max-width: 600px) {
  .brand-text {
    font-size: 0.95rem !important;
    white-space: nowrap !important;
  }

  .v-toolbar-title {
    min-width: auto !important;
  }
}

.nav-link {
  text-transform: none !important;
  font-weight: 500;
  letter-spacing: 0.3px;
  transition: all 0.2s ease;
}

.nav-link:hover {
  background: rgba(103, 58, 183, 0.08) !important;
}

.login-btn {
  text-transform: none !important;
  font-weight: 600;
  padding: 0 24px !important;
  transition: all 0.3s ease;
  text-shadow: none;
}

.login-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(103, 58, 183, 0.3);
}

.avatar-btn {
  background: rgba(103, 58, 183, 0.08);
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
}

.avatar-btn:hover {
  background: rgba(103, 58, 183, 0.15);
  transform: scale(1.05);
}

.avatar-shadow {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  border: 2px solid rgba(103, 58, 183, 0.2);
}

/* Modern Footer Styles */
.modern-footer {
  position: relative;
  overflow: hidden;
  border: none !important;
}

.w-100 {
  width: 100%;
}

.footer-gradient {
  background: #09090B;
  position: relative;
  overflow: hidden;
  width: 100%;
}

.footer-gradient::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 0.3;
  pointer-events: none;
}

.footer-gradient .v-container {
  position: relative;
  z-index: 1;
}

.footer-links {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.footer-link {
  color: white;
  text-decoration: none;
  opacity: 0.9;
  transition: all 0.2s ease;
  display: inline-block;
  font-size: 15px;
}

.footer-link:hover {
  opacity: 1;
  transform: translateX(4px);
  color: white;
}

.social-btn {
  transition: all 0.3s ease;
  border-width: 2px;
}

.social-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-3px);
}
</style>
