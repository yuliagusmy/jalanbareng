<template>
  <!-- Hanya tampil di bawah md (mobile). Hidden di tablet/desktop. -->
  <nav
    v-if="!display.mdAndUp.value"
    class="bottom-nav-wrapper"
    aria-label="Navigasi Mobile Bawah"
  >
    <!-- Floating Pill Capsule -->
    <div
      class="bottom-nav-pill"
      :class="{ 'is-compact': isCompact }"
    >
      <template v-for="item in navItems" :key="item.id">
        <!-- Item Aktivasi: Membuka Bottom Sheet Interaktif -->
        <button
          v-if="item.id === 'aktivasi'"
          type="button"
          :aria-label="item.label"
          class="nav-item"
          :class="{ 'nav-item--active': isAktivasiActive }"
          @click="toggleAktivasiSheet"
        >
          <div class="nav-icon-wrapper">
            <v-icon
              :size="isCompact ? 18 : 20"
              class="nav-icon"
            >
              {{ item.icon }}
            </v-icon>
          </div>
          <span v-if="isAktivasiActive" class="nav-label">
            {{ item.label }}
          </span>
        </button>

        <!-- Item Biasa (NuxtLink) -->
        <NuxtLink
          v-else
          :to="item.href"
          :aria-label="item.label"
          class="nav-item"
          :class="{ 'nav-item--active': isActive(item) }"
          @click="isSheetOpen = false"
        >
          <div class="nav-icon-wrapper">
            <!-- Avatar khusus item profil jika sudah login -->
            <v-avatar
              v-if="item.id === 'profil' && authStore.user?.photo"
              :size="isCompact ? 18 : 20"
              class="nav-avatar"
            >
              <v-img :src="getImageUrl(authStore.user.photo)" :alt="authStore.user.name" />
            </v-avatar>
            <v-icon
              v-else
              :size="isCompact ? 18 : 20"
              class="nav-icon"
            >
              {{ item.icon }}
            </v-icon>

            <!-- Crown badge jika admin di item profil -->
            <span
              v-if="item.id === 'profil' && authStore.isAdmin"
              class="admin-dot-badge"
              title="Admin"
            ></span>
          </div>

          <!-- Label hanya muncul di item aktif -->
          <span v-if="isActive(item)" class="nav-label">
            {{ item.label }}
          </span>
        </NuxtLink>
      </template>
    </div>

    <!-- Bottom Sheet Jejaring Aktivasi -->
    <Teleport to="body">
      <Transition name="fade">
        <div
          v-if="isSheetOpen"
          class="bottom-sheet-backdrop"
          @click="isSheetOpen = false"
        ></div>
      </Transition>

      <Transition name="slide-up">
        <div
          v-if="isSheetOpen"
          class="bottom-sheet-container"
          role="dialog"
          aria-modal="true"
          aria-label="Jejaring Aktivasi"
        >
          <!-- Pull Handle Bar -->
          <div class="sheet-drag-handle-zone" @click="isSheetOpen = false">
            <div class="sheet-drag-pill"></div>
          </div>

          <!-- Sheet Header -->
          <div class="sheet-header">
            <div class="d-flex align-center ga-2">
              <div class="sheet-header-icon-box">
                <v-icon size="20" color="#DC2626">mdi-compass-outline</v-icon>
              </div>
              <div>
                <h3 class="sheet-title">Jejaring Aktivasi</h3>
                <p class="sheet-subtitle">Pilih inisiatif pejalan kaki & ruang kota</p>
              </div>
            </div>
            <button
              type="button"
              class="sheet-close-btn"
              aria-label="Tutup"
              @click="isSheetOpen = false"
            >
              <v-icon size="20">mdi-close</v-icon>
            </button>
          </div>

          <!-- Shortcut Khusus Admin jika Login sebagai Admin -->
          <div
            v-if="authStore.isAdmin"
            class="sheet-admin-card"
            @click="navigateToUrl('/manage/activations')"
          >
            <div class="d-flex align-center ga-2">
              <div class="admin-icon-box">
                <v-icon size="18" color="#F59E0B">mdi-shield-crown</v-icon>
              </div>
              <div>
                <div class="admin-card-title">Panel Administrator</div>
                <div class="admin-card-desc">Kelola aktivasi, event, destinasi, & user</div>
              </div>
            </div>
            <v-icon size="18" color="#E2E8F0">mdi-chevron-right</v-icon>
          </div>

          <!-- Filter Kategori Chips -->
          <div class="sheet-category-scroll">
            <button
              v-for="cat in categories"
              :key="cat.key"
              type="button"
              class="category-chip"
              :class="{ 'category-chip--active': activeCategory === cat.key }"
              @click="activeCategory = cat.key"
            >
              <span>{{ cat.label }}</span>
              <span class="chip-count">{{ getCategoryCount(cat.key) }}</span>
            </button>
          </div>

          <!-- Loading State -->
          <div v-if="isLoadingActivations && activations.length === 0" class="sheet-loading">
            <v-progress-circular indeterminate size="28" width="2.5" color="#DC2626"></v-progress-circular>
            <span class="mt-2 text-caption text-grey">Memuat jejaring aktivasi...</span>
          </div>

          <!-- Activations List -->
          <div v-else class="sheet-list-scroll">
            <div
              v-for="item in filteredActivations"
              :key="item.id"
              class="sheet-activation-item"
              @click="navigateToUrl('/aktivasi/' + item.slug)"
            >
              <div
                class="item-avatar"
                :class="item.category"
              >
                <img
                  v-if="item.icon"
                  :src="apiBase + '/storage/' + item.icon"
                  :alt="item.name"
                  class="item-avatar-img"
                />
                <v-icon
                  v-else
                  size="20"
                  :color="getCategoryColor(item.category)"
                >
                  {{ getCategoryIcon(item.category) }}
                </v-icon>
              </div>

              <div class="item-info">
                <div class="item-name">{{ item.name }}</div>
                <div class="item-sub">
                  <span v-if="item.city" class="item-city-pill">
                    <v-icon size="11">mdi-map-marker-outline</v-icon>
                    {{ item.city }}
                  </span>
                  <span v-else-if="item.tagline" class="item-tagline">{{ item.tagline }}</span>
                </div>
              </div>

              <v-icon size="18" class="item-arrow">mdi-chevron-right</v-icon>
            </div>

            <!-- Empty State jika kategori kosong -->
            <div v-if="filteredActivations.length === 0" class="text-center py-6 text-grey text-caption">
              Tidak ada aktivasi pada kategori ini.
            </div>
          </div>

          <!-- Pinned Footer Action -->
          <div class="sheet-footer">
            <button
              type="button"
              class="sheet-primary-btn"
              @click="navigateToUrl('/aktivasi')"
            >
              <span>Lihat Semua Aktivasi</span>
              <v-icon size="18">mdi-arrow-right</v-icon>
            </button>
          </div>
        </div>
      </Transition>
    </Teleport>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useDisplay } from 'vuetify'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig } from '#app'
import { useImageUrl } from '~/composables/useImageUrl'

const authStore = useAuthStore()
const route = useRoute()
const router = useRouter()
const display = useDisplay()
const { getImageUrl } = useImageUrl()
const { api } = useApi()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// State Sheet
const isSheetOpen = ref(false)
const activeCategory = ref<'all' | 'city' | 'theme' | 'space'>('all')

// State Activations
const activations = ref<any[]>([])
const isLoadingActivations = ref(false)

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

const toggleAktivasiSheet = () => {
  isSheetOpen.value = !isSheetOpen.value
  if (isSheetOpen.value && activations.value.length === 0) {
    fetchActivations()
  }
}

const navigateToUrl = (url: string) => {
  isSheetOpen.value = false
  router.push(url)
}

// Tutup sheet saat route berganti
watch(() => route.path, () => {
  isSheetOpen.value = false
})

// Scroll lock ketika sheet terbuka
watch(isSheetOpen, (isOpen) => {
  if (typeof document !== 'undefined') {
    if (isOpen) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
})

// Scroll-aware compact mode
const isCompact = ref(false)

onMounted(() => {
  fetchActivations()

  let lastScrollY = window.scrollY
  let accumulatedDelta = 0
  let ticking = false

  const handleScroll = () => {
    if (!ticking) {
      window.requestAnimationFrame(() => {
        const currentScrollY = window.scrollY
        const delta = currentScrollY - lastScrollY

        if (currentScrollY < 30) {
          isCompact.value = false
          accumulatedDelta = 0
        } else {
          if (delta > 0) {
            accumulatedDelta = Math.max(0, accumulatedDelta + delta)
            if (accumulatedDelta > 40) {
              isCompact.value = true
              accumulatedDelta = 0
            }
          } else if (delta < 0) {
            accumulatedDelta = Math.min(0, accumulatedDelta + delta)
            if (accumulatedDelta < -25) {
              isCompact.value = false
              accumulatedDelta = 0
            }
          }
        }

        lastScrollY = currentScrollY
        ticking = false
      })
      ticking = true
    }
  }

  window.addEventListener('scroll', handleScroll, { passive: true })
  onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll)
    if (typeof document !== 'undefined') {
      document.body.style.overflow = ''
    }
  })
})

const navItems = computed(() => [
  {
    id: 'beranda',
    label: 'Beranda',
    href: '/',
    icon: 'mdi-home',
  },
  {
    id: 'aktivasi',
    label: 'Aktivasi',
    href: '/aktivasi',
    icon: 'mdi-compass-outline',
  },
  {
    id: 'event',
    label: 'Event',
    href: '/events',
    icon: 'mdi-calendar',
  },
  {
    id: 'destinasi',
    label: 'Destinasi',
    href: '/destinations',
    icon: 'mdi-map-marker-radius',
  },
  {
    id: 'mitra',
    label: 'Mitra',
    href: '/mitra',
    icon: 'mdi-handshake-outline',
  },
])

const isActive = (item: { id: string; href: string }) => {
  if (item.href === '/') return route.path === '/'
  return route.path.startsWith(item.href)
}

const isAktivasiActive = computed(() => {
  return isSheetOpen.value || route.path.startsWith('/aktivasi')
})

// Filter data aktivasi
const categories = [
  { key: 'all' as const, label: 'Semua' },
  { key: 'city' as const, label: 'Kota' },
  { key: 'theme' as const, label: 'Tematik' },
  { key: 'space' as const, label: 'Ruang Komunal' },
]

const getCategoryCount = (key: 'all' | 'city' | 'theme' | 'space') => {
  if (key === 'all') return activations.value.filter(a => a.is_active).length
  return activations.value.filter(a => a.is_active && a.category === key).length
}

const filteredActivations = computed(() => {
  const activeItems = activations.value.filter(a => a.is_active)
  if (activeCategory.value === 'all') return activeItems
  return activeItems.filter(a => a.category === activeCategory.value)
})

const getCategoryColor = (cat: string) => {
  if (cat === 'city') return '#DC2626'
  if (cat === 'theme') return '#EA580C'
  if (cat === 'space') return '#9333EA'
  return '#4B5563'
}

const getCategoryIcon = (cat: string) => {
  if (cat === 'city') return 'mdi-city-variant'
  if (cat === 'theme') return 'mdi-palette'
  if (cat === 'space') return 'mdi-storefront'
  return 'mdi-star-four-points'
}
</script>

<style scoped>
.bottom-nav-wrapper {
  position: fixed;
  bottom: calc(0.85rem + env(safe-area-inset-bottom, 0px));
  left: 0;
  right: 0;
  z-index: 1000;
  display: flex;
  justify-content: center;
  pointer-events: none;
  padding: 0 16px;
}

.bottom-nav-pill {
  pointer-events: auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 4px;
  max-width: 390px;
  width: 100%;
  padding: 8px 12px;
  background: rgba(255, 255, 255, 0.96);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 9999px;
  box-shadow:
    0 16px 40px -8px rgba(0, 0, 0, 0.20),
    0 4px 12px -4px rgba(0, 0, 0, 0.10);
  transition:
    max-width 0.35s cubic-bezier(0.16, 1, 0.3, 1),
    padding 0.35s cubic-bezier(0.16, 1, 0.3, 1),
    box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.bottom-nav-pill.is-compact {
  max-width: 330px;
  padding: 6px 10px;
}

.nav-item {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  text-decoration: none;
  border-radius: 9999px;
  color: rgba(0, 0, 0, 0.48);
  min-width: 44px;
  min-height: 44px;
  padding: 0 8px;
  flex-shrink: 0;
  border: none;
  background: transparent;
  cursor: pointer;
  outline: none;
  transition:
    color 0.35s cubic-bezier(0.16, 1, 0.3, 1),
    background 0.35s cubic-bezier(0.16, 1, 0.3, 1),
    padding 0.35s cubic-bezier(0.16, 1, 0.3, 1),
    min-width 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.nav-item:active {
  transform: scale(0.92);
  transition: transform 0.1s ease;
}

.nav-item--active {
  background: #DC2626;
  color: #ffffff !important;
  padding: 10px 16px;
  min-width: auto;
}

.nav-item--active:active {
  background: #B91C1C;
}

.nav-icon-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.admin-dot-badge {
  position: absolute;
  top: -2px;
  right: -2px;
  width: 8px;
  height: 8px;
  background: #F59E0B;
  border: 1.5px solid #ffffff;
  border-radius: 50%;
}

.nav-label {
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.01em;
  white-space: nowrap;
  animation: fadeLabel 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@keyframes fadeLabel {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

.nav-icon,
.nav-avatar {
  flex-shrink: 0;
}

.bottom-nav-pill.is-compact .nav-item--active {
  padding: 8px 12px;
}

/* ==========================================================================
   Bottom Sheet Styles
   ========================================================================== */
.bottom-sheet-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.5);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  z-index: 2000;
}

.bottom-sheet-container {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 2001;
  background: #FFFFFF;
  border-radius: 28px 28px 0 0;
  box-shadow: 0 -12px 48px rgba(0, 0, 0, 0.2);
  max-height: 85vh;
  display: flex;
  flex-direction: column;
  padding-bottom: calc(0.5rem + env(safe-area-inset-bottom, 0px));
  overflow: hidden;
}

.sheet-drag-handle-zone {
  padding: 10px 0 6px;
  display: flex;
  justify-content: center;
  cursor: pointer;
}

.sheet-drag-pill {
  width: 36px;
  height: 4px;
  background: #E2E8F0;
  border-radius: 9999px;
}

.sheet-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 4px 20px 14px;
  border-bottom: 1px solid #F1F5F9;
}

.sheet-header-icon-box {
  width: 36px;
  height: 36px;
  border-radius: 12px;
  background: #FEF2F2;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sheet-title {
  font-size: 16px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
  line-height: 1.25;
}

.sheet-subtitle {
  font-size: 11px;
  color: #64748B;
  margin: 0;
}

.sheet-close-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  background: #F1F5F9;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

/* Admin Shortcut Strip */
.sheet-admin-card {
  margin: 12px 18px 4px;
  padding: 10px 14px;
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: white;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
  transition: transform 0.15s ease;
}

.sheet-admin-card:active {
  transform: scale(0.98);
}

.admin-icon-box {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.12);
  display: flex;
  align-items: center;
  justify-content: center;
}

.admin-card-title {
  font-size: 12px;
  font-weight: 700;
  color: #FFFFFF;
}

.admin-card-desc {
  font-size: 10px;
  color: #CBD5E1;
}

/* Category Filter Chips */
.sheet-category-scroll {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 18px 10px;
  overflow-x: auto;
  scrollbar-width: none;
}

.sheet-category-scroll::-webkit-scrollbar {
  display: none;
}

.category-chip {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 9999px;
  border: 1px solid #E2E8F0;
  background: #F8FAFC;
  font-size: 12px;
  font-weight: 600;
  color: #475569;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s ease;
}

.category-chip--active {
  background: #DC2626;
  border-color: #DC2626;
  color: #FFFFFF;
}

.chip-count {
  font-size: 10px;
  opacity: 0.8;
  padding: 1px 5px;
  border-radius: 9999px;
  background: rgba(0, 0, 0, 0.08);
}

.category-chip--active .chip-count {
  background: rgba(255, 255, 255, 0.25);
  color: #FFFFFF;
}

/* List Items */
.sheet-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 0;
}

.sheet-list-scroll {
  flex: 1;
  overflow-y: auto;
  padding: 4px 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-height: 180px;
  max-height: 44vh;
  scrollbar-width: thin;
  scrollbar-color: rgba(0, 0, 0, 0.3) transparent;
}

.sheet-list-scroll::-webkit-scrollbar {
  width: 6px;
}

.sheet-list-scroll::-webkit-scrollbar-track {
  background: transparent;
}

.sheet-list-scroll::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.3);
  border-radius: 3px;
}

.sheet-list-scroll::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.5);
}

.sheet-activation-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 12px;
  border-radius: 16px;
  background: #F8FAFC;
  border: 1px solid #F1F5F9;
  cursor: pointer;
  transition: background 0.15s ease, transform 0.15s ease;
}

.sheet-activation-item:active {
  background: #F1F5F9;
  transform: scale(0.98);
}

.item-avatar {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  overflow: hidden;
  background: #FFFFFF;
  border: 1px solid rgba(0, 0, 0, 0.06);
}

.item-avatar.city {
  background: #FEF2F2;
}

.item-avatar.theme {
  background: #FFF7ED;
}

.item-avatar.space {
  background: #FAF5FF;
}

.item-avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-info {
  flex: 1;
  min-width: 0;
}

.item-name {
  font-size: 13px;
  font-weight: 700;
  color: #0F172A;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-sub {
  margin-top: 2px;
  display: flex;
  align-items: center;
}

.item-city-pill {
  font-size: 10.5px;
  font-weight: 600;
  color: #DC2626;
  background: #FEE2E2;
  padding: 1px 6px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  gap: 2px;
}

.item-tagline {
  font-size: 11px;
  color: #64748B;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-arrow {
  color: #94A3B8;
  flex-shrink: 0;
}

/* Footer Button */
.sheet-footer {
  padding: 10px 18px 6px;
  border-top: 1px solid #F1F5F9;
}

.sheet-primary-btn {
  width: 100%;
  padding: 12px;
  background: #DC2626;
  color: #FFFFFF;
  border: none;
  border-radius: 9999px;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(220, 38, 38, 0.25);
  transition: background 0.15s ease, transform 0.15s ease;
}

.sheet-primary-btn:active {
  background: #B91C1C;
  transform: scale(0.98);
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active {
  transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-up-leave-active {
  transition: transform 0.22s cubic-bezier(0.4, 0, 1, 1);
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}
</style>
