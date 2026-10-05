<template>
  <div class="admin-quick-actions-card mb-6">
    <div class="d-flex align-center justify-space-between mb-3 px-1">
      <div class="d-flex align-center ga-2">
        <div class="qa-icon-pill">
          <v-icon size="16" color="#DC2626">mdi-lightning-bolt</v-icon>
        </div>
        <div>
          <h2 class="qa-header-title text-grey-darken-4">Aksi Operasional Cepat</h2>
          <p class="qa-header-sub text-grey-darken-1 mb-0">Akses langsung modul manajemen komunitas</p>
        </div>
      </div>
      <span class="qa-count-badge">{{ actions.length }} Modul</span>
    </div>

    <v-row dense class="qa-grid-row">
      <v-col
        v-for="action in actions"
        :key="action.title"
        cols="6"
        sm="4"
        md="3"
        class="pa-1-5 pa-sm-2"
      >
        <NuxtLink :to="action.to" class="qa-action-tile text-decoration-none">
          <div class="d-flex align-center ga-2-5 ga-sm-3">
            <div class="qa-tile-icon" :style="{ backgroundColor: action.bg, color: action.color }">
              <v-icon :size="iconSize">{{ action.icon }}</v-icon>
            </div>
            <div class="flex-grow-1 min-w-0">
              <div class="qa-tile-title text-truncate">{{ action.title }}</div>
              <div class="qa-tile-desc text-truncate">{{ action.desc }}</div>
            </div>
            <v-chip
              v-if="action.badge"
              color="#DC2626"
              size="x-small"
              variant="flat"
              rounded="pill"
              class="qa-alert-badge font-weight-black"
            >
              {{ action.badge }}
            </v-chip>
          </div>
        </NuxtLink>
      </v-col>
    </v-row>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useDisplay } from 'vuetify'

const props = defineProps<{
  pendingStoriesCount?: number
}>()

const display = useDisplay()
const iconSize = computed(() => (display.xs.value ? 18 : 20))

const actions = computed(() => [
  {
    title: 'Kelola Aktivasi',
    desc: 'Chapter & rute kota',
    to: '/manage/activations',
    icon: 'mdi-compass-outline',
    color: '#DC2626',
    bg: '#FEF2F2'
  },
  {
    title: 'Kelola Destinasi',
    desc: 'Titik ruang & spot jalan',
    to: '/manage/destinations',
    icon: 'mdi-map-marker-outline',
    color: '#059669',
    bg: '#ECFDF5'
  },
  {
    title: 'Moderasi Cerita',
    desc: 'Review tulisan pejalan',
    to: '/manage/stories',
    icon: 'mdi-feather',
    color: '#D97706',
    bg: '#FFFBEB',
    badge: (props.pendingStoriesCount ?? 0) > 0 ? `${props.pendingStoriesCount} new` : undefined
  },
  {
    title: 'Kelola Anggota',
    desc: 'Database & role user',
    to: '/manage/users',
    icon: 'mdi-account-group-outline',
    color: '#2563EB',
    bg: '#EFF6FF'
  },
  {
    title: 'Kelola Kategori',
    desc: 'Taksonomi konten',
    to: '/manage/categories',
    icon: 'mdi-tag-multiple-outline',
    color: '#16A34A',
    bg: '#F0FDF4'
  },
  {
    title: 'Laporan & Ekspor',
    desc: 'KPI bulanan & CSV',
    to: '/manage/reports',
    icon: 'mdi-file-chart-outline',
    color: '#DC2626',
    bg: '#FEF2F2'
  },
  {
    title: 'Pengaturan Web',
    desc: 'Sistem & profil publik',
    to: '/manage/settings',
    icon: 'mdi-cog-outline',
    color: '#7C3AED',
    bg: '#F5F3FF'
  },
  {
    title: 'Kemitraan (Mitra)',
    desc: '80+ Mitra & kolaborasi',
    to: '/mitra',
    icon: 'mdi-handshake-outline',
    color: '#EA580C',
    bg: '#FFF7ED'
  }
])
</script>

<style scoped>
.admin-quick-actions-card {
  background: #FFFFFF;
  border-radius: 20px;
  border: 1px solid #F1F5F9;
  box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
  padding: 16px 14px;
}

@media (min-width: 600px) {
  .admin-quick-actions-card {
    padding: 20px 22px;
  }
}

.qa-icon-pill {
  width: 28px;
  height: 28px;
  border-radius: 9px;
  background: #FEF2F2;
  display: flex;
  align-items: center;
  justify-content: center;
}

.qa-header-title {
  font-size: 0.95rem;
  font-weight: 800;
  letter-spacing: -0.01em;
  line-height: 1.2;
}

.qa-header-sub {
  font-size: 0.75rem;
  line-height: 1.3;
}

.qa-count-badge {
  font-size: 0.7rem;
  font-weight: 700;
  color: #64748B;
  background: #F1F5F9;
  padding: 3px 10px;
  border-radius: 9999px;
}

.qa-action-tile {
  display: block;
  background: #FAFAF9;
  border: 1px solid #E7E5E4;
  border-radius: 14px;
  padding: 10px 10px;
  min-height: 58px;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  box-sizing: border-box;
}

@media (min-width: 600px) {
  .qa-action-tile {
    padding: 12px 14px;
    min-height: 64px;
    border-radius: 16px;
  }
}

.qa-action-tile:hover {
  background: #FFFFFF;
  border-color: #CBD5E1;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
}

.qa-tile-icon {
  width: 32px;
  height: 32px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

@media (min-width: 600px) {
  .qa-tile-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
  }
}

.qa-tile-title {
  font-size: 0.8rem;
  font-weight: 800;
  color: #1E293B;
  line-height: 1.25;
}

@media (min-width: 600px) {
  .qa-tile-title {
    font-size: 0.86rem;
  }
}

.qa-tile-desc {
  font-size: 0.68rem;
  color: #64748B;
  font-weight: 500;
  line-height: 1.3;
  margin-top: 2px;
}

@media (min-width: 600px) {
  .qa-tile-desc {
    font-size: 0.74rem;
  }
}

.qa-alert-badge {
  font-size: 0.62rem !important;
  height: 18px !important;
  padding: 0 5px !important;
}
</style>
