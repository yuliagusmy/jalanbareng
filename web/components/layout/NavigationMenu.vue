<template>
  <div class="d-none d-md-flex align-center">
    <v-btn to="/" variant="text" :color="textColor" class="mx-1 nav-link" rounded="pill">
      Home
    </v-btn>

    <!-- Split Button for Aktivasi: Link to /aktivasi + Hover/Click Dropdown Chevron -->
    <div
      class="nav-split-group d-inline-flex align-center mx-1"
      :class="{ 'is-menu-open': isAktivasiMenuOpen }"
      @mouseleave="scheduleCloseMenu"
    >
      <v-btn
        to="/aktivasi"
        variant="text"
        :color="textColor"
        class="nav-link nav-split-main"
        rounded="pill"
      >
        Aktivasi
      </v-btn>

      <v-menu
        v-model="isAktivasiMenuOpen"
        :close-on-content-click="false"
        attach="body"
        content-class="nav-dropdown-menu"
        location="bottom start"
        offset="10"
        transition="slide-y-transition"
      >
        <template v-slot:activator="{ props }">
          <v-btn
            v-bind="props"
            variant="text"
            :color="textColor"
            class="nav-split-arrow"
            :class="{ 'is-active': isAktivasiMenuOpen }"
            rounded="pill"
            aria-label="Pilihan Aktivasi"
            @mouseenter="openMenu"
            @mouseleave="scheduleCloseMenu"
            @click.stop="toggleMenu"
          >
            <v-icon size="16" :class="{ 'rotate-180': isAktivasiMenuOpen }">mdi-chevron-down</v-icon>
          </v-btn>
        </template>

        <div
          class="nav-dropdown-card"
          @mouseenter="openMenu"
          @mouseleave="scheduleCloseMenu"
        >
          <!-- Top Header Info Bar -->
          <div class="dropdown-top-header d-flex align-center justify-space-between px-5 py-3">
            <div class="d-flex align-center ga-2">
              <v-icon size="18" color="#DC2626">mdi-compass-outline</v-icon>
              <span class="text-caption font-weight-bold text-grey-darken-3 tracking-wider">JEJARING AKTIVASI</span>
            </div>
            <span class="count-pill">{{ activations.length }} Inisiatif</span>
          </div>

          <!-- Loading State -->
          <div v-if="isLoadingActivations && activations.length === 0" class="text-center py-6">
            <v-progress-circular indeterminate size="24" width="2" color="#DC2626"></v-progress-circular>
            <div class="text-caption text-grey mt-2">Memuat daftar aktivasi...</div>
          </div>

          <!-- Scrollable Group Container -->
          <div v-else class="nav-dropdown-scrollable">
            <!-- City Activations -->
            <div v-if="groupedActivations.city.length > 0" class="group-wrapper">
              <div class="group-header-pill city">
                <v-icon size="14" class="mr-1">mdi-city-variant-outline</v-icon>
                <span>Aktivasi Kota</span>
              </div>
              <v-list density="compact" class="py-1 bg-transparent">
                <v-list-item
                  v-for="activation in groupedActivations.city"
                  :key="activation.id"
                  :to="`/aktivasi/${activation.slug}`"
                  rounded="xl"
                  class="mx-2 mb-1 dropdown-rich-item"
                  @click="isAktivasiMenuOpen = false"
                >
                  <template v-slot:prepend>
                    <div class="item-avatar-box">
                      <img
                        v-if="activation.icon"
                        :src="`${apiBase}/storage/${activation.icon}`"
                        :alt="activation.name"
                        class="item-icon-img"
                      />
                      <v-icon v-else size="18" color="#DC2626">mdi-city-variant</v-icon>
                    </div>
                  </template>
                  <v-list-item-title class="font-weight-bold text-grey-darken-4 item-title">
                    {{ activation.name }}
                  </v-list-item-title>
                  <v-list-item-subtitle class="text-caption text-grey-darken-1 d-flex align-center ga-1 mt-0-5">
                    <span v-if="activation.city" class="city-micro-chip">
                      <v-icon size="11" class="mr-0-5">mdi-map-marker-outline</v-icon>
                      {{ activation.city }}
                    </span>
                    <span v-else-if="activation.tagline" class="text-truncate">{{ activation.tagline }}</span>
                  </v-list-item-subtitle>
                  <template v-slot:append>
                    <v-icon size="16" class="item-hover-arrow">mdi-arrow-right</v-icon>
                  </template>
                </v-list-item>
              </v-list>
            </div>

            <!-- Theme Activations -->
            <div v-if="groupedActivations.theme.length > 0" class="group-wrapper">
              <div class="group-header-pill theme">
                <v-icon size="14" class="mr-1">mdi-palette-outline</v-icon>
                <span>Aktivasi Tematik</span>
              </div>
              <v-list density="compact" class="py-1 bg-transparent">
                <v-list-item
                  v-for="activation in groupedActivations.theme"
                  :key="activation.id"
                  :to="`/aktivasi/${activation.slug}`"
                  rounded="xl"
                  class="mx-2 mb-1 dropdown-rich-item"
                  @click="isAktivasiMenuOpen = false"
                >
                  <template v-slot:prepend>
                    <div class="item-avatar-box theme">
                      <img
                        v-if="activation.icon"
                        :src="`${apiBase}/storage/${activation.icon}`"
                        :alt="activation.name"
                        class="item-icon-img"
                      />
                      <v-icon v-else size="18" color="#EA580C">mdi-palette</v-icon>
                    </div>
                  </template>
                  <v-list-item-title class="font-weight-bold text-grey-darken-4 item-title">
                    {{ activation.name }}
                  </v-list-item-title>
                  <v-list-item-subtitle class="text-caption text-grey-darken-1 text-truncate mt-0-5">
                    {{ activation.tagline || 'Inisiatif tematik pejalan kaki' }}
                  </v-list-item-subtitle>
                  <template v-slot:append>
                    <v-icon size="16" class="item-hover-arrow">mdi-arrow-right</v-icon>
                  </template>
                </v-list-item>
              </v-list>
            </div>

            <!-- Space Activations -->
            <div v-if="groupedActivations.space.length > 0" class="group-wrapper">
              <div class="group-header-pill space">
                <v-icon size="14" class="mr-1">mdi-storefront-outline</v-icon>
                <span>Ruang Kreatif &amp; Komunitas</span>
              </div>
              <v-list density="compact" class="py-1 bg-transparent">
                <v-list-item
                  v-for="activation in groupedActivations.space"
                  :key="activation.id"
                  :to="`/aktivasi/${activation.slug}`"
                  rounded="xl"
                  class="mx-2 mb-1 dropdown-rich-item"
                  @click="isAktivasiMenuOpen = false"
                >
                  <template v-slot:prepend>
                    <div class="item-avatar-box space">
                      <img
                        v-if="activation.icon"
                        :src="`${apiBase}/storage/${activation.icon}`"
                        :alt="activation.name"
                        class="item-icon-img"
                      />
                      <v-icon v-else size="18" color="#9333EA">mdi-storefront</v-icon>
                    </div>
                  </template>
                  <v-list-item-title class="font-weight-bold text-grey-darken-4 item-title">
                    {{ activation.name }}
                  </v-list-item-title>
                  <v-list-item-subtitle class="text-caption text-grey-darken-1 text-truncate mt-0-5">
                    {{ activation.tagline || 'Ruang temu dan kolaborasi' }}
                  </v-list-item-subtitle>
                  <template v-slot:append>
                    <v-icon size="16" class="item-hover-arrow">mdi-arrow-right</v-icon>
                  </template>
                </v-list-item>
              </v-list>
            </div>
          </div>

          <!-- Pinned Footer Action (Always Visible) -->
          <div class="dropdown-pinned-footer pa-3 border-t">
            <v-btn
              to="/aktivasi"
              block
              color="#DC2626"
              variant="flat"
              rounded="pill"
              size="default"
              class="footer-cta-btn font-weight-bold"
              @click="isAktivasiMenuOpen = false"
            >
              Lihat Semua Aktivasi
              <v-icon end size="16">mdi-arrow-right</v-icon>
            </v-btn>
          </div>
        </div>
      </v-menu>
    </div>

    <v-btn to="/events" variant="text" :color="textColor" class="mx-1 nav-link" rounded="pill">
      Event
    </v-btn>
    <v-btn to="/destinations" variant="text" :color="textColor" class="mx-1 nav-link" rounded="pill">
      Destinasi
    </v-btn>
    <v-btn to="/cerita" variant="text" :color="textColor" class="mx-1 nav-link" rounded="pill">
      Cerita
    </v-btn>
    <v-btn to="/mitra" variant="text" :color="textColor" class="mx-1 nav-link" rounded="pill">
      Mitra
    </v-btn>

    <!-- Admin & Community Admin Menu -->
    <v-menu v-if="authStore.isAdmin || authStore.isCommunityAdmin" offset-y>
      <template v-slot:activator="{ props }">
        <v-btn v-bind="props" variant="text" :color="adminColor" class="mx-1 nav-link admin-nav-btn" rounded="pill">
          <v-icon start size="18">mdi-shield-crown-outline</v-icon>
          {{ authStore.isAdmin ? 'Admin' : 'Kelola' }}
          <v-icon end size="16">mdi-chevron-down</v-icon>
        </v-btn>
      </template>
      <v-list min-width="240" rounded="xl" class="py-2 admin-dropdown-card">
        <v-list-item to="/manage/activations" rounded="lg" class="mx-2 mb-1 dropdown-rich-item">
          <template v-slot:prepend>
            <v-icon color="#DC2626" class="mr-2">mdi-star-four-points-outline</v-icon>
          </template>
          <v-list-item-title class="font-weight-bold">Kelola Aktivasi</v-list-item-title>
          <v-list-item-subtitle class="text-caption">Manage semua aktivasi</v-list-item-subtitle>
        </v-list-item>

        <v-list-item to="/manage/events" rounded="lg" class="mx-2 mb-1 dropdown-rich-item">
          <template v-slot:prepend>
            <v-icon color="#EA580C" class="mr-2">mdi-calendar-multiple</v-icon>
          </template>
          <v-list-item-title class="font-weight-bold">Kelola Event</v-list-item-title>
          <v-list-item-subtitle class="text-caption">Manage semua event</v-list-item-subtitle>
        </v-list-item>

        <v-list-item to="/manage/destinations" rounded="lg" class="mx-2 mb-1 dropdown-rich-item">
          <template v-slot:prepend>
            <v-icon color="#0284C7" class="mr-2">mdi-map-marker-multiple-outline</v-icon>
          </template>
          <v-list-item-title class="font-weight-bold">Kelola Destinasi</v-list-item-title>
          <v-list-item-subtitle class="text-caption">Manage semua destinasi</v-list-item-subtitle>
        </v-list-item>

        <template v-if="authStore.isAdmin">
          <v-divider class="my-2"></v-divider>

          <v-list-item to="/manage/users" rounded="lg" class="mx-2 mb-1 dropdown-rich-item">
            <template v-slot:prepend>
              <v-icon color="#059669" class="mr-2">mdi-account-multiple-outline</v-icon>
            </template>
            <v-list-item-title class="font-weight-bold">Kelola User</v-list-item-title>
            <v-list-item-subtitle class="text-caption">Manage semua user</v-list-item-subtitle>
          </v-list-item>

          <v-list-item to="/manage/categories" rounded="lg" class="mx-2 mb-1 dropdown-rich-item">
            <template v-slot:prepend>
              <v-icon color="#D97706" class="mr-2">mdi-tag-multiple-outline</v-icon>
            </template>
            <v-list-item-title class="font-weight-bold">Kelola Kategori</v-list-item-title>
            <v-list-item-subtitle class="text-caption">Manage kategori</v-list-item-subtitle>
          </v-list-item>

          <v-list-item to="/manage/pages" rounded="lg" class="mx-2 mb-1 dropdown-rich-item">
            <template v-slot:prepend>
              <v-icon color="#0D9488" class="mr-2">mdi-file-document-multiple-outline</v-icon>
            </template>
            <v-list-item-title class="font-weight-bold">Kelola Halaman</v-list-item-title>
            <v-list-item-subtitle class="text-caption">Manage halaman statis</v-list-item-subtitle>
          </v-list-item>

          <v-list-item to="/manage/stories" rounded="lg" class="mx-2 mb-1 dropdown-rich-item">
            <template v-slot:prepend>
              <v-icon color="#E11D48" class="mr-2">mdi-feather</v-icon>
            </template>
            <v-list-item-title class="font-weight-bold">Kurasi Tulisan</v-list-item-title>
            <v-list-item-subtitle class="text-caption">Moderasi cerita Jalan Bareng</v-list-item-subtitle>
          </v-list-item>

          <v-divider class="my-2"></v-divider>

          <v-list-item to="/manage/settings" rounded="lg" class="mx-2 dropdown-rich-item">
            <template v-slot:prepend>
              <v-icon color="grey-darken-1" class="mr-2">mdi-cog-outline</v-icon>
            </template>
            <v-list-item-title class="font-weight-bold">Pengaturan</v-list-item-title>
            <v-list-item-subtitle class="text-caption">Pengaturan website</v-list-item-subtitle>
          </v-list-item>
        </template>
      </v-list>
    </v-menu>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig } from '#app'

interface Props {
  textColor?: string
  adminColor?: string
}

const props = withDefaults(defineProps<Props>(), {
  textColor: 'primary',
  adminColor: 'secondary'
})

const authStore = useAuthStore()
const { api } = useApi()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const activations = ref<any[]>([])
const isAktivasiMenuOpen = ref(false)
const isLoadingActivations = ref(false)
let closeTimer: ReturnType<typeof setTimeout> | null = null

const openMenu = () => {
  if (closeTimer) {
    clearTimeout(closeTimer)
    closeTimer = null
  }
  isAktivasiMenuOpen.value = true
}

const scheduleCloseMenu = () => {
  if (closeTimer) clearTimeout(closeTimer)
  closeTimer = setTimeout(() => {
    isAktivasiMenuOpen.value = false
  }, 220)
}

const toggleMenu = () => {
  if (isAktivasiMenuOpen.value) {
    if (closeTimer) clearTimeout(closeTimer)
    isAktivasiMenuOpen.value = false
  } else {
    openMenu()
  }
}

const fetchActivations = async () => {
  if (activations.value.length > 0 && !isLoadingActivations.value) return
  isLoadingActivations.value = true
  try {
    const response = await api.get('/activations')
    activations.value = Array.isArray(response.data) ? response.data : (response.data?.data || [])
  } catch (error) {
    console.error('Error fetching activations:', error)
  } finally {
    isLoadingActivations.value = false
  }
}

watch(isAktivasiMenuOpen, (isOpen) => {
  if (isOpen && activations.value.length === 0) {
    fetchActivations()
  }
})

const groupedActivations = computed(() => {
  const grouped: Record<string, any[]> = {
    city: [],
    theme: [],
    space: [],
    other: []
  }

  activations.value.forEach(a => {
    if (a.is_active) {
      if (grouped[a.category]) {
        grouped[a.category].push(a)
      } else {
        grouped.other.push(a)
      }
    }
  })

  return grouped
})

onMounted(() => {
  fetchActivations()
})
</script>

<style scoped>
/* Nav Links Modern Styling */
.nav-link {
  text-transform: none !important;
  font-weight: 600;
  letter-spacing: 0.2px;
  border-radius: 9999px !important;
  padding: 0 16px !important;
  height: 40px !important;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.nav-link:hover {
  background: #FEF2F2 !important;
  color: #DC2626 !important;
  transform: translateY(-1px);
}

.nav-link.router-link-active {
  background: #FEF2F2 !important;
  color: #DC2626 !important;
  font-weight: 700 !important;
}

/* Split Button Styling */
.nav-split-group {
  display: inline-flex;
  align-items: center;
  border-radius: 9999px;
  background: transparent;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  padding: 2px;
}

.nav-split-group:hover,
.nav-split-group.is-menu-open {
  background: #FEF2F2;
}

.nav-split-group:hover .nav-split-main,
.nav-split-group.is-menu-open .nav-split-main {
  color: #DC2626 !important;
}

.nav-split-group:hover .nav-split-arrow,
.nav-split-group.is-menu-open .nav-split-arrow {
  color: #DC2626 !important;
}

.nav-split-main {
  padding-left: 16px !important;
  padding-right: 4px !important;
  height: 38px !important;
  border-top-right-radius: 0 !important;
  border-bottom-right-radius: 0 !important;
}

.nav-split-arrow {
  min-width: 28px !important;
  width: 28px !important;
  height: 38px !important;
  padding: 0 !important;
  margin-left: 0 !important;
  border-top-left-radius: 0 !important;
  border-bottom-left-radius: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer;
  transition: all 0.2s ease !important;
}

.rotate-180 {
  transform: rotate(180deg);
  transition: transform 0.25s ease;
}

.admin-nav-btn:hover {
  background: #FEF2F2 !important;
  color: #DC2626 !important;
}
</style>

<style>
/* Global overlay styling for teleported menu */
.nav-dropdown-menu {
  z-index: 99999 !important;
}

.nav-dropdown-card {
  min-width: 380px;
  max-width: 440px;
  background-color: #FFFFFF !important;
  background: #FFFFFF !important;
  opacity: 1 !important;
  color: #111827 !important;
  border: 1px solid #E5E7EB !important;
  box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.18), 0 4px 12px -2px rgba(0, 0, 0, 0.06) !important;
  overflow: hidden;
  border-radius: 22px !important;
}

/* Top Header */
.dropdown-top-header {
  background: #F8FAFC;
  border-bottom: 1px solid #F1F5F9;
}

.count-pill {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 9999px;
  background: #FEF2F2;
  color: #DC2626;
  border: 1px solid #FEE2E2;
}

/* Scrollable Container */
.nav-dropdown-scrollable {
  max-height: 390px;
  overflow-y: auto;
  overscroll-behavior: contain;
  padding-bottom: 6px;
}

.nav-dropdown-scrollable::-webkit-scrollbar {
  width: 5px;
}

.nav-dropdown-scrollable::-webkit-scrollbar-track {
  background: #F8FAFC;
}

.nav-dropdown-scrollable::-webkit-scrollbar-thumb {
  background: #CBD5E1;
  border-radius: 9999px;
}

.nav-dropdown-scrollable::-webkit-scrollbar-thumb:hover {
  background: #94A3B8;
}

/* Group Headers */
.group-header-pill {
  display: inline-flex;
  align-items: center;
  margin: 10px 16px 4px;
  padding: 3px 10px;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.03em;
  text-transform: uppercase;
}

.group-header-pill.city {
  background: #FEF2F2;
  color: #DC2626;
}

.group-header-pill.theme {
  background: #FFF7ED;
  color: #EA580C;
}

.group-header-pill.space {
  background: #FAF5FF;
  color: #9333EA;
}

/* Rich Dropdown Item */
.dropdown-rich-item {
  transition: all 0.18s ease !important;
  padding: 8px 12px !important;
}

.dropdown-rich-item:hover {
  background-color: #FEF2F2 !important;
  transform: translateX(2px);
}

.dropdown-rich-item:hover .item-title {
  color: #DC2626 !important;
}

.dropdown-rich-item:hover .item-hover-arrow {
  color: #DC2626 !important;
  transform: translateX(3px);
}

.item-avatar-box {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #FEF2F2;
  overflow: hidden;
  margin-right: 12px;
  border: 1.5px solid #FEE2E2;
}

.item-avatar-box.theme {
  background: #FFF7ED;
  border-color: #FFEDD5;
}

.item-avatar-box.space {
  background: #FAF5FF;
  border-color: #F3E8FF;
}

.item-icon-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-title {
  font-size: 0.92rem !important;
  line-height: 1.3 !important;
  letter-spacing: -0.01em;
}

.city-micro-chip {
  display: inline-flex;
  align-items: center;
  font-size: 0.72rem;
  font-weight: 600;
  color: #6B7280;
  background: #F3F4F6;
  padding: 1px 6px;
  border-radius: 4px;
}

.item-hover-arrow {
  color: #9CA3AF;
  transition: all 0.2s ease;
}

/* Pinned Footer */
.dropdown-pinned-footer {
  background: #FFFFFF;
  border-top: 1px solid #F1F5F9;
}

.footer-cta-btn {
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2) !important;
  letter-spacing: 0.2px;
  text-transform: none !important;
}

.footer-cta-btn:hover {
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.35) !important;
}

.admin-dropdown-card {
  box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.16) !important;
  border: 1px solid #E5E7EB !important;
}
</style>
