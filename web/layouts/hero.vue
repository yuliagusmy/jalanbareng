<template>
  <v-app class="hero-layout">
    <!-- Modern Simple Header -->
    <v-app-bar elevation="0" height="0" class="modern-header hero-header" :class="appBarClass">
      <template v-slot:extension>
        <v-container class="d-flex align-center header-container" style="height: 70px;">
          <!-- Mobile Menu Icon -->
          <v-app-bar-nav-icon class="d-md-none" @click="drawer = !drawer"
            :color="isScrolled ? 'secondary' : 'white'"></v-app-bar-nav-icon>

          <!-- Logo/Brand -->
          <NuxtLink to="/" class="text-decoration-none d-flex align-center mr-4">
            <img
              :src="isScrolled ? '/images/logo-jalan-bareng.png' : '/images/logo-jalan-bareng-white.png'"
              alt="Jalan Bareng"
              style="height: 38px; width: auto; object-fit: contain; transition: all 0.25s ease;"
            />
          </NuxtLink>

          <v-spacer></v-spacer>

          <!-- Desktop Navigation -->
          <NavigationMenu :text-color="isScrolled ? 'secondary' : 'white'"
            :admin-color="isScrolled ? 'primary' : 'white'" />

          <v-spacer></v-spacer>

          <!-- User Menu / Login Button -->
          <template v-if="authStore.isLoggedIn">
            <!-- Quick Admin Panel Button -->
            <v-btn
              v-if="authStore.isAdmin || authStore.isCommunityAdmin"
              to="/manage/activations"
              icon
              variant="flat"
              class="mr-1 admin-badge-btn"
              :title="authStore.isAdmin ? 'Panel Admin' : 'Panel Manajemen'"
              :aria-label="authStore.isAdmin ? 'Panel Admin' : 'Panel Manajemen'"
            >
              <v-icon color="#DC2626" size="22">mdi-shield-crown</v-icon>
            </v-btn>

            <NotificationBell class="mr-1" />
            <v-menu offset-y>
              <template v-slot:activator="{ props }">
                <v-btn icon v-bind="props" class="ml-3 avatar-btn">
                  <v-avatar size="40" class="avatar-shadow">
                    <v-img v-if="authStore.user?.photo" :src="getImageUrl(authStore.user.photo)"
                      :alt="authStore.user.name"></v-img>
                    <v-icon v-else color="white">mdi-account-circle</v-icon>
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

                <!-- Admin / Community Admin Link in User Dropdown for Quick Access -->
                <template v-if="authStore.isAdmin || authStore.isCommunityAdmin">
                  <v-divider class="my-2"></v-divider>
                  <v-list-item to="/manage/activations" rounded="lg" class="mx-2 bg-red-lighten-5">
                    <template v-slot:prepend>
                      <v-icon color="#DC2626">mdi-shield-crown</v-icon>
                    </template>
                    <v-list-item-title class="font-weight-bold text-red-darken-3">
                      {{ authStore.isAdmin ? 'Panel Admin' : 'Panel Manajemen' }}
                    </v-list-item-title>
                    <v-list-item-subtitle class="text-caption text-red-darken-1">
                      {{ authStore.isAdmin ? 'Kelola website & konten' : 'Kelola event & aktivasi' }}
                    </v-list-item-subtitle>
                  </v-list-item>
                </template>

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
            <v-btn to="/login" :variant="isScrolled ? 'flat' : 'outlined'" :color="isScrolled ? 'primary' : 'white'"
              class="ml-3 login-btn" rounded="pill" size="default">
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

const isScrolled = computed(() => y.value > 50)

// Basic scroll tracking for styling
watch(y, (newY) => {
  // Logic simplified: Header is always visible
}, { immediate: true })

const appBarClass = computed(() => {
  const classes = []
  if (!isScrolled.value) {
    classes.push('app-bar-top')
  } else {
    classes.push('app-bar-scrolled')
  }

  // Removed app-bar-hidden logic
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




</script>

<style>
/* Hero Layout Header Styles - Scoped strictly to hero layout */
.hero-layout .hero-header {
  background: transparent !important;
  background-color: transparent !important;
  position: fixed !important;
  top: 0 !important;
  z-index: 1000 !important;
  transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
}

.hero-layout .hero-header.app-bar-hidden {
  transform: translateY(-100%);
}

.hero-layout .hero-header .v-toolbar__content {
  display: none !important;
}

.hero-layout .hero-header .v-toolbar__extension {
  padding: 0 !important;
  height: 70px !important;
  transition: background 0.3s ease, box-shadow 0.3s ease, backdrop-filter 0.3s ease;
}

/* Style when scrolled down: clean frosted white glass */
.hero-layout .hero-header.app-bar-scrolled .v-toolbar__extension {
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

/* Style when at the top of the page: dark frosted glass so white logo & links are always 100% visible */
.hero-layout .hero-header.app-bar-top .v-toolbar__extension {
  background: rgba(15, 23, 42, 0.6) !important;
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  box-shadow: none !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}

.hero-layout .header-container {
  background: transparent !important;
}

/* Ensure hero layout content overlays from top 0 */
.hero-layout .v-main {
  padding-top: 0 !important;
}
</style>

<style scoped>
.brand-text {
  letter-spacing: -0.5px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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

:deep(.app-bar-scrolled) .brand-text {
  text-shadow: none;
}

.nav-link {
  text-transform: none !important;
  font-weight: 500;
  letter-spacing: 0.3px;
  transition: all 0.2s ease;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

:deep(.app-bar-scrolled) .nav-link {
  text-shadow: none;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.15) !important;
}

:deep(.app-bar-scrolled) .nav-link:hover {
  background: #FEF2F2 !important;
  color: #DC2626 !important;
}

.login-btn {
  text-transform: none !important;
  font-weight: 600;
  padding: 0 24px !important;
  transition: all 0.3s ease;
  border: 2px solid white !important;
  text-shadow: none;
}

.login-btn:hover {
  background: rgba(255, 255, 255, 0.2) !important;
  transform: translateY(-2px);
}

.avatar-btn {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  transition: all 0.3s ease;
}

.avatar-btn:hover {
  background: rgba(255, 255, 255, 0.25);
  transform: scale(1.05);
}

.avatar-shadow {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  border: 2px solid rgba(255, 255, 255, 0.5);
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
  border-top: 1px solid #1E293B;
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
