<template>
  <v-app class="default-layout">
    <!-- Modern Simple Header -->
    <v-app-bar elevation="0" height="0" class="modern-header default-header" :class="appBarClass">
      <template v-slot:extension>
        <v-container class="d-flex align-center header-container" style="height: 70px;">
          <!-- Mobile Menu Icon -->
          <v-app-bar-nav-icon class="d-md-none" @click="drawer = !drawer"
            color="secondary"></v-app-bar-nav-icon>

          <!-- Logo/Brand -->
          <NuxtLink to="/" class="text-decoration-none d-flex align-center mr-4">
            <img
              src="/images/logo-jalan-bareng.png"
              alt="Jalan Bareng"
              style="height: 38px; width: auto; object-fit: contain; transition: all 0.25s ease;"
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
            <v-btn to="/login" variant="flat" color="primary"
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

    <!-- Footer Component -->
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
  // We keep the watch just in case we need reactive scroll effects later,
  // but for now we basically just track isScrolled via computed
}, { immediate: true })

const appBarClass = computed(() => {
  const classes = []
  if (!isScrolled.value) {
    classes.push('app-bar-top')
  } else {
    classes.push('app-bar-scrolled')
  }

  // Removed app-bar-hidden logic to keep header fixed
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

})

const api = useApi()







</script>

<style>
/* Modern Header Styles - Scoped strictly to default layout */
.default-layout .default-header {
  background: transparent !important;
  background-color: transparent !important;
  position: fixed !important;
  top: 0 !important;
  z-index: 1000 !important;
  transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
}

.default-layout .default-header.app-bar-hidden {
  transform: translateY(-100%);
}

.default-layout .default-header .v-toolbar__content {
  display: none !important;
}

.default-layout .default-header .v-toolbar__extension {
  padding: 0 !important;
  height: 70px !important;
  transition: background 0.3s ease, box-shadow 0.3s ease, backdrop-filter 0.3s ease;
}

/* Style when scrolled down */
.default-layout .default-header.app-bar-scrolled .v-toolbar__extension {
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
  border-bottom: 1px solid #E5E7EB;
}

/* Style when at the top of the page */
.default-layout .default-header.app-bar-top .v-toolbar__extension {
  background: #FFFFFF !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
  border-bottom: 1px solid #E5E7EB;
}

.default-layout .header-container {
  background: transparent !important;
}

/* Ensure main content in default layout has proper top spacing */
.default-layout .v-main {
  padding-top: 74px !important;
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
  font-weight: 600;
  letter-spacing: 0.2px;
  border-radius: 9999px !important;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
  text-shadow: none !important;
}

.nav-link:hover,
:deep(.app-bar-scrolled) .nav-link:hover {
  background: #FEF2F2 !important;
  color: #DC2626 !important;
  transform: translateY(-1px);
}

.nav-link.router-link-active,
:deep(.app-bar-scrolled) .nav-link.router-link-active {
  background: #FEF2F2 !important;
  color: #DC2626 !important;
  font-weight: 700 !important;
}

.login-btn {
  text-transform: none !important;
  font-weight: 700;
  padding: 0 24px !important;
  transition: all 0.25s ease;
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2) !important;
}

.login-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35) !important;
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
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
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
