<template>
  <div v-if="canManage" class="admin-nav-dropdown-root d-inline-flex align-center">
    <v-menu
      v-model="isMenuOpen"
      offset="10"
      location="bottom end"
      transition="slide-y-transition"
      content-class="admin-dropdown-menu-sheet"
    >
      <template v-slot:activator="{ props }">
        <!-- Desktop Pill Button -->
        <v-btn
          v-if="$vuetify.display.mdAndUp"
          v-bind="props"
          variant="flat"
          class="admin-topbar-pill mr-2 font-weight-bold"
          rounded="pill"
          size="default"
          :class="{ 'menu-is-active': isMenuOpen }"
          aria-label="Menu Aksi Admin"
        >
          <v-icon start size="18" color="#DC2626">mdi-shield-crown</v-icon>
          <span class="admin-pill-text">{{ authStore.isAdmin ? 'Admin' : 'Kelola' }}</span>
          <v-icon end size="16" color="#DC2626" :class="{ 'rotate-180': isMenuOpen }">mdi-chevron-down</v-icon>
        </v-btn>

        <!-- Mobile Circular Icon Button (for 390px and mobile screens) -->
        <v-btn
          v-else
          v-bind="props"
          icon
          variant="flat"
          class="admin-mobile-icon-btn mr-1"
          :class="{ 'menu-is-active': isMenuOpen }"
          aria-label="Menu Aksi Admin"
        >
          <v-icon color="#DC2626" size="20">mdi-shield-crown</v-icon>
        </v-btn>
      </template>

      <!-- Dropdown Card -->
      <v-card elevation="8" rounded="xl" class="admin-dropdown-card py-2 border-subtle">
        <!-- Header: Quick link to main management overview -->
        <div class="px-4 py-3 d-flex align-center justify-space-between border-b-subtle">
          <div class="d-flex align-center ga-2">
            <div class="admin-shield-badge">
              <v-icon size="16" color="#DC2626">mdi-shield-crown</v-icon>
            </div>
            <div>
              <div class="text-caption font-weight-black text-grey-darken-4 text-uppercase tracking-wider">
                {{ authStore.isAdmin ? 'Super Admin' : 'Admin Komunitas' }}
              </div>
              <div class="text-caption text-grey-darken-1 line-clamp-1" style="font-size: 0.72rem !important;">
                Panel Manajemen
              </div>
            </div>
          </div>

          <v-btn
            to="/manage"
            variant="text"
            color="#DC2626"
            size="x-small"
            rounded="pill"
            class="px-2 font-weight-bold"
            @click="isMenuOpen = false"
          >
            Dashboard
            <v-icon end size="12">mdi-arrow-right</v-icon>
          </v-btn>
        </div>

        <!-- Section: Operasional Komunitas -->
        <div class="section-label px-4 pt-2 pb-1 text-grey-darken-1">
          OPERASIONAL
        </div>
        <v-list density="compact" class="py-0 bg-transparent">
          <v-list-item
            to="/manage/activations"
            rounded="lg"
            class="mx-2 mb-1 dropdown-admin-item"
            @click="isMenuOpen = false"
          >
            <template v-slot:prepend>
              <div class="item-icon-box bg-red-subtle">
                <v-icon size="16" color="#DC2626">mdi-star-four-points-outline</v-icon>
              </div>
            </template>
            <v-list-item-title class="font-weight-bold text-grey-darken-4">
              Kelola Aktivasi
            </v-list-item-title>
            <v-list-item-subtitle class="text-caption text-grey-darken-1">
              Chapter kota &amp; inisiatif tematik
            </v-list-item-subtitle>
          </v-list-item>

          <v-list-item
            to="/manage/events"
            rounded="lg"
            class="mx-2 mb-1 dropdown-admin-item"
            @click="isMenuOpen = false"
          >
            <template v-slot:prepend>
              <div class="item-icon-box bg-orange-subtle">
                <v-icon size="16" color="#EA580C">mdi-calendar-multiple</v-icon>
              </div>
            </template>
            <v-list-item-title class="font-weight-bold text-grey-darken-4">
              Kelola Event
            </v-list-item-title>
            <v-list-item-subtitle class="text-caption text-grey-darken-1">
              Agenda &amp; registrasi pejalan
            </v-list-item-subtitle>
          </v-list-item>

          <v-list-item
            to="/manage/destinations"
            rounded="lg"
            class="mx-2 mb-1 dropdown-admin-item"
            @click="isMenuOpen = false"
          >
            <template v-slot:prepend>
              <div class="item-icon-box bg-blue-subtle">
                <v-icon size="16" color="#0284C7">mdi-map-marker-multiple-outline</v-icon>
              </div>
            </template>
            <v-list-item-title class="font-weight-bold text-grey-darken-4">
              Kelola Destinasi
            </v-list-item-title>
            <v-list-item-subtitle class="text-caption text-grey-darken-1">
              Spot rute &amp; ruang kota
            </v-list-item-subtitle>
          </v-list-item>
        </v-list>

        <!-- Section: Super Admin Only -->
        <template v-if="authStore.isAdmin">
          <v-divider class="my-1-5 mx-3"></v-divider>
          <div class="section-label px-4 pt-1 pb-1 text-grey-darken-1">
            ADMINISTRASI SISTEM
          </div>

          <v-list density="compact" class="py-0 bg-transparent">
            <v-list-item
              to="/manage/users"
              rounded="lg"
              class="mx-2 mb-1 dropdown-admin-item"
              @click="isMenuOpen = false"
            >
              <template v-slot:prepend>
                <div class="item-icon-box bg-green-subtle">
                  <v-icon size="16" color="#059669">mdi-account-multiple-outline</v-icon>
                </div>
              </template>
              <v-list-item-title class="font-weight-bold text-grey-darken-4">
                Kelola User &amp; Peran
              </v-list-item-title>
              <v-list-item-subtitle class="text-caption text-grey-darken-1">
                Data anggota &amp; hak akses
              </v-list-item-subtitle>
            </v-list-item>

            <v-list-item
              to="/manage/categories"
              rounded="lg"
              class="mx-2 mb-1 dropdown-admin-item"
              @click="isMenuOpen = false"
            >
              <template v-slot:prepend>
                <div class="item-icon-box bg-amber-subtle">
                  <v-icon size="16" color="#D97706">mdi-tag-multiple-outline</v-icon>
                </div>
              </template>
              <v-list-item-title class="font-weight-bold text-grey-darken-4">
                Kelola Kategori
              </v-list-item-title>
              <v-list-item-subtitle class="text-caption text-grey-darken-1">
                Kategori destinasi &amp; event
              </v-list-item-subtitle>
            </v-list-item>

            <v-list-item
              to="/manage/partners"
              rounded="lg"
              class="mx-2 mb-1 dropdown-admin-item"
              @click="isMenuOpen = false"
            >
              <template v-slot:prepend>
                <div class="item-icon-box bg-teal-subtle">
                  <v-icon size="16" color="#0D9488">mdi-handshake-outline</v-icon>
                </div>
              </template>
              <v-list-item-title class="font-weight-bold text-grey-darken-4">
                Kelola Mitra
              </v-list-item-title>
              <v-list-item-subtitle class="text-caption text-grey-darken-1">
                Brand, komunitas &amp; media partner
              </v-list-item-subtitle>
            </v-list-item>

            <v-list-item
              to="/manage/stories"
              rounded="lg"
              class="mx-2 mb-1 dropdown-admin-item"
              @click="isMenuOpen = false"
            >
              <template v-slot:prepend>
                <div class="item-icon-box bg-pink-subtle">
                  <v-icon size="16" color="#E11D48">mdi-feather</v-icon>
                </div>
              </template>
              <v-list-item-title class="font-weight-bold text-grey-darken-4">
                Kurasi Tulisan
              </v-list-item-title>
              <v-list-item-subtitle class="text-caption text-grey-darken-1">
                Moderasi cerita komunitas
              </v-list-item-subtitle>
            </v-list-item>

            <v-list-item
              to="/manage/reports"
              rounded="lg"
              class="mx-2 mb-1 dropdown-admin-item"
              @click="isMenuOpen = false"
            >
              <template v-slot:prepend>
                <div class="item-icon-box bg-red-subtle">
                  <v-icon size="16" color="#DC2626">mdi-file-chart-outline</v-icon>
                </div>
              </template>
              <v-list-item-title class="font-weight-bold text-grey-darken-4">
                Laporan &amp; Ekspor
              </v-list-item-title>
              <v-list-item-subtitle class="text-caption text-grey-darken-1">
                Data member &amp; partisipasi
              </v-list-item-subtitle>
            </v-list-item>

            <v-list-item
              to="/manage/cashouts"
              rounded="lg"
              class="mx-2 mb-1 dropdown-admin-item"
              @click="isMenuOpen = false"
            >
              <template v-slot:prepend>
                <div class="item-icon-box bg-green-subtle">
                  <v-icon size="16" color="#059669">mdi-hand-coin</v-icon>
                </div>
              </template>
              <v-list-item-title class="font-weight-bold text-grey-darken-4">
                Pencairan Poin
                <v-chip
                  v-if="pendingCashouts > 0"
                  color="#DC2626"
                  size="x-small"
                  variant="flat"
                  rounded="pill"
                  class="ml-2 text-white font-weight-black"
                  style="font-size: 0.62rem; height: 16px; padding: 0 5px;"
                >
                  {{ pendingCashouts }}
                </v-chip>
              </v-list-item-title>
              <v-list-item-subtitle class="text-caption text-grey-darken-1">
                Verifikasi transfer kontributor
              </v-list-item-subtitle>
            </v-list-item>

            <v-list-item
              to="/manage/settings"
              rounded="lg"
              class="mx-2 mb-1 dropdown-admin-item"
              @click="isMenuOpen = false"
            >
              <template v-slot:prepend>
                <div class="item-icon-box bg-grey-subtle">
                  <v-icon size="16" color="#4B5563">mdi-cog-outline</v-icon>
                </div>
              </template>
              <v-list-item-title class="font-weight-bold text-grey-darken-4">
                Pengaturan Web
              </v-list-item-title>
              <v-list-item-subtitle class="text-caption text-grey-darken-1">
                Kontak, footer &amp; SEO
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </template>
      </v-card>
    </v-menu>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'

const authStore = useAuthStore()
const { api } = useApi()
const isMenuOpen = ref(false)
const pendingCashouts = ref(0)

const canManage = computed(() => {
  return authStore.isAdmin || authStore.isCommunityAdmin
})

// Fetch pending cashout count (admin only)
const fetchPendingCashouts = async () => {
  if (!authStore.isAdmin) return
  try {
    const res = await api.get('/admin/cashouts', { params: { status: 'pending' } })
    pendingCashouts.value = res.data?.data?.counts?.pending ?? 0
  } catch {
    // silent — badge tidak kritis
  }
}

onMounted(() => {
  if (canManage.value) fetchPendingCashouts()
})
</script>

<style scoped>
.admin-nav-dropdown-root {
  position: relative;
}

/* Desktop Pill Button */
.admin-topbar-pill {
  background: #FEF2F2 !important;
  border: 1px solid #FEE2E2 !important;
  color: #DC2626 !important;
  height: 40px !important;
  padding: 0 16px !important;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.admin-topbar-pill:hover,
.admin-topbar-pill.menu-is-active {
  background: #FEE2E2 !important;
  border-color: #FECACA !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.12) !important;
}

.admin-pill-text {
  font-size: 0.88rem;
  letter-spacing: 0.01em;
}

/* Mobile Icon Button */
.admin-mobile-icon-btn {
  width: 40px !important;
  height: 40px !important;
  background: #FEF2F2 !important;
  border: 1px solid #FEE2E2 !important;
  border-radius: 50% !important;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.admin-mobile-icon-btn:hover,
.admin-mobile-icon-btn.menu-is-active {
  background: #FEE2E2 !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(220, 38, 38, 0.15) !important;
}

/* Dropdown Card */
.admin-dropdown-card {
  width: 290px;
  max-width: 90vw;
  background: #FFFFFF !important;
  border: 1px solid #F1F5F9;
  box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.12), 0 4px 12px rgba(0, 0, 0, 0.05) !important;
}

.admin-shield-badge {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  background: #FEF2F2;
  display: flex;
  align-items: center;
  justify-content: center;
}

.section-label {
  font-size: 0.68rem;
  font-weight: 800;
  letter-spacing: 0.06em;
}

.border-subtle {
  border: 1px solid #F1F5F9;
}

.border-b-subtle {
  border-bottom: 1px solid #F1F5F9;
}

.item-icon-box {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
  flex-shrink: 0;
}

.bg-red-subtle { background: #FEF2F2; }
.bg-orange-subtle { background: #FFF7ED; }
.bg-blue-subtle { background: #F0F9FF; }
.bg-green-subtle { background: #F0FDF4; }
.bg-amber-subtle { background: #FFFBEB; }
.bg-pink-subtle { background: #FFF1F2; }
.bg-grey-subtle { background: #F3F4F6; }
.bg-teal-subtle { background: #F0FDFA; }

.dropdown-admin-item {
  transition: all 0.2s ease;
  min-height: 48px;
}

.dropdown-admin-item:hover {
  background: #F8FAFC !important;
  transform: translateX(3px);
}

.rotate-180 {
  transform: rotate(180deg);
  transition: transform 0.25s ease;
}
</style>
