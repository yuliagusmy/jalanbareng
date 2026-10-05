<template>
  <div class="admin-dashboard-page">
    <v-container class="py-4 py-sm-6 py-md-8 px-3 px-sm-6">

      <!-- ── Page Header ── -->
      <div class="d-flex align-center justify-space-between flex-wrap ga-3 mb-5 mb-sm-6">
        <div>
          <div class="d-flex align-center ga-2 mb-1">
            <div class="header-icon-box">
              <v-icon color="#DC2626" size="22">mdi-view-dashboard-outline</v-icon>
            </div>
            <h1 class="page-main-title text-grey-darken-4 mb-0">Dashboard Manajemen</h1>
          </div>
          <p class="text-caption text-sm-body-2 text-grey-darken-1 mb-0">
            Ringkasan operasional &amp; pertumbuhan platform Jalan Bareng
          </p>
        </div>

        <div class="d-flex align-center ga-2">
          <v-chip
            color="#DC2626"
            variant="tonal"
            rounded="pill"
            size="small"
            class="font-weight-black live-chip"
          >
            <v-icon start size="10" class="pulse-dot">mdi-circle</v-icon>
            Live Sync
          </v-chip>
          <v-btn
            variant="outlined"
            rounded="pill"
            size="small"
            color="grey-darken-2"
            class="font-weight-bold text-caption refresh-btn"
            :loading="loading"
            @click="fetchStats"
          >
            <v-icon start size="15">mdi-refresh</v-icon>
            Segarkan
          </v-btn>
        </div>
      </div>

      <!-- ── Loading Skeleton ── -->
      <div v-if="loading">
        <v-row dense class="mb-4">
          <v-col v-for="i in 4" :key="i" cols="6" sm="6" md="3">
            <v-skeleton-loader type="card" height="110" rounded="xl" />
          </v-col>
        </v-row>
        <v-skeleton-loader type="card" height="180" rounded="xl" class="mb-4" />
        <v-row dense>
          <v-col cols="12" md="8">
            <v-skeleton-loader type="card" height="280" rounded="xl" />
          </v-col>
          <v-col cols="12" md="4">
            <v-skeleton-loader type="card" height="280" rounded="xl" />
          </v-col>
        </v-row>
      </div>

      <!-- ── Dashboard Content ── -->
      <template v-else-if="stats">
        <!-- 1. Core Stat KPI Cards (Mobile 390px Optimized) -->
        <AdminStatCards :summary="stats.summary" />

        <!-- 2. Operational Quick Actions Hub (Grid for fast mobile admin) -->
        <AdminQuickActionsGrid :pending-stories-count="stats.summary.pending_stories" />

        <!-- 3. Community Growth Chart & Category Distribution -->
        <v-row class="mb-5 mb-sm-6" dense>
          <!-- User & Destination Growth Chart -->
          <v-col cols="12" md="8" class="pa-1-5 pa-sm-2">
            <div class="chart-card pa-4 pa-sm-5 pa-md-6 h-100">
              <div class="d-flex align-center justify-space-between mb-4">
                <div>
                  <h3 class="chart-title">Pertumbuhan Komunitas</h3>
                  <p class="chart-subtitle">Member &amp; destinasi baru (7 hari terakhir)</p>
                </div>
              </div>

              <!-- Mini Bar Chart (CSS-based & Responsive) -->
              <div class="bar-chart-wrapper">
                <div v-for="(day, i) in stats.user_growth" :key="i" class="bar-chart-group">
                  <div class="bars-row">
                    <!-- User Bar -->
                    <div
                      class="bar bar-user"
                      :style="{ height: getBarHeight(day.count, maxUserCount) + '%' }"
                      :title="`${day.label}: ${day.count} member baru`"
                    />
                    <!-- Destination Bar -->
                    <div
                      class="bar bar-dest"
                      :style="{ height: getBarHeight(stats.destination_growth[i]?.count ?? 0, maxDestCount) + '%' }"
                      :title="`${day.label}: ${stats.destination_growth[i]?.count ?? 0} destinasi baru`"
                    />
                  </div>
                  <div class="bar-label">{{ day.label }}</div>
                </div>
              </div>

              <!-- Legend -->
              <div class="d-flex align-center justify-center flex-wrap ga-4 mt-3 pt-2 border-t-subtle">
                <div class="d-flex align-center ga-1-5">
                  <div class="legend-dot legend-dot-user" />
                  <span class="text-caption text-grey-darken-1 font-weight-medium">Member Baru</span>
                </div>
                <div class="d-flex align-center ga-1-5">
                  <div class="legend-dot legend-dot-dest" />
                  <span class="text-caption text-grey-darken-1 font-weight-medium">Destinasi Baru</span>
                </div>
              </div>
            </div>
          </v-col>

          <!-- Destinations by Category -->
          <v-col cols="12" md="4" class="pa-1-5 pa-sm-2">
            <div class="chart-card pa-4 pa-sm-5 pa-md-6 h-100">
              <div class="d-flex align-center justify-space-between mb-1">
                <h3 class="chart-title">Sebaran Destinasi</h3>
                <span class="text-caption font-weight-bold text-grey-darken-2">
                  {{ stats.summary.total_destinations }} Total
                </span>
              </div>
              <p class="chart-subtitle mb-4">Distribusi titik kumpul &amp; spot jalan</p>

              <div
                v-for="(cat, i) in stats.destinations_by_category.slice(0, 5)"
                :key="i"
                class="category-bar-row mb-3"
              >
                <div class="d-flex justify-space-between align-center mb-1">
                  <span class="text-caption font-weight-medium text-grey-darken-3 text-truncate max-w-75">
                    {{ cat.category }}
                  </span>
                  <span class="text-caption font-weight-bold text-grey-darken-4">{{ cat.count }}</span>
                </div>
                <div class="progress-track">
                  <div
                    class="progress-fill"
                    :style="{
                      width: ((cat.count / (stats.summary.total_destinations || 1)) * 100) + '%',
                      backgroundColor: categoryColors[i % categoryColors.length]
                    }"
                  />
                </div>
              </div>
            </div>
          </v-col>
        </v-row>

        <!-- 4. Operational Feeds: Pending Stories, Recent Members, Top Destinations -->
        <v-row dense>
          <!-- Pending Stories Queue -->
          <v-col cols="12" md="4" class="pa-1-5 pa-sm-2">
            <div class="chart-card pa-4 pa-sm-5 h-100">
              <div class="d-flex align-center justify-space-between mb-3">
                <div>
                  <h3 class="chart-title">Antrian Moderasi</h3>
                  <p class="chart-subtitle">Cerita menunggu review</p>
                </div>
                <v-chip
                  :color="stats.summary.pending_stories > 0 ? '#DC2626' : 'grey-lighten-1'"
                  variant="flat"
                  rounded="pill"
                  size="x-small"
                  class="text-white font-weight-black"
                >
                  {{ stats.summary.pending_stories }}
                </v-chip>
              </div>

              <div v-if="stats.pending_stories.length > 0">
                <div
                  v-for="(story, i) in stats.pending_stories"
                  :key="story.id"
                  class="feed-item-row"
                  :class="{ 'mb-2-5': i < stats.pending_stories.length - 1 }"
                >
                  <div class="d-flex align-center ga-2-5">
                    <div class="story-badge">
                      <v-icon size="14" color="#B45309">mdi-feather</v-icon>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                      <div class="feed-item-title text-truncate">{{ story.title }}</div>
                      <div class="feed-item-meta text-truncate">{{ story.author }} · {{ story.submitted }}</div>
                    </div>
                    <v-btn
                      icon
                      variant="text"
                      size="x-small"
                      to="/manage/stories"
                      aria-label="Review Cerita"
                    >
                      <v-icon size="16" color="#DC2626">mdi-arrow-right</v-icon>
                    </v-btn>
                  </div>
                </div>

                <div class="mt-4">
                  <v-btn
                    to="/manage/stories"
                    color="#DC2626"
                    variant="tonal"
                    rounded="pill"
                    size="small"
                    block
                    class="font-weight-bold"
                  >
                    <v-icon start size="14">mdi-shield-check-outline</v-icon>
                    Review Semua Cerita
                  </v-btn>
                </div>
              </div>
              <div v-else class="text-center py-6">
                <v-icon size="36" color="#16A34A">mdi-check-circle-outline</v-icon>
                <p class="text-caption text-grey-darken-1 mt-2 mb-0 font-weight-medium">Semua cerita sudah dimoderasi</p>
              </div>
            </div>
          </v-col>

          <!-- Recent Members -->
          <v-col cols="12" md="4" class="pa-1-5 pa-sm-2">
            <div class="chart-card pa-4 pa-sm-5 h-100">
              <div class="d-flex align-center justify-space-between mb-3">
                <div>
                  <h3 class="chart-title">Anggota Terbaru</h3>
                  <p class="chart-subtitle">Bergabung ke komunitas</p>
                </div>
                <v-btn
                  to="/manage/users"
                  variant="text"
                  color="#DC2626"
                  size="x-small"
                  class="font-weight-bold"
                >
                  Lihat Semua
                </v-btn>
              </div>

              <div
                v-for="(user, i) in stats.recent_users.slice(0, 4)"
                :key="user.id"
                class="feed-item-row"
                :class="{ 'mb-2-5': i < Math.min(stats.recent_users.length, 4) - 1 }"
              >
                <div class="d-flex align-center ga-2-5">
                  <v-avatar size="32" :color="roleColor(user.role)">
                    <span class="text-caption font-weight-black text-white">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </span>
                  </v-avatar>
                  <div class="flex-grow-1 min-w-0">
                    <div class="feed-item-title text-truncate">{{ user.name }}</div>
                    <div class="feed-item-meta text-truncate">{{ user.email }}</div>
                  </div>
                  <v-chip
                    :color="roleColor(user.role)"
                    variant="flat"
                    size="x-small"
                    rounded="pill"
                    class="text-white font-weight-bold"
                    style="font-size: 0.62rem;"
                  >
                    {{ roleLabel(user.role) }}
                  </v-chip>
                </div>
              </div>
            </div>
          </v-col>

          <!-- Top Destinations -->
          <v-col cols="12" md="4" class="pa-1-5 pa-sm-2">
            <div class="chart-card pa-4 pa-sm-5 h-100">
              <div class="d-flex align-center justify-space-between mb-3">
                <div>
                  <h3 class="chart-title">Destinasi Terpopuler</h3>
                  <p class="chart-subtitle">Berdasarkan jumlah suka</p>
                </div>
                <v-btn
                  to="/manage/destinations"
                  variant="text"
                  color="#DC2626"
                  size="x-small"
                  class="font-weight-bold"
                >
                  Kelola
                </v-btn>
              </div>

              <div
                v-for="(dest, i) in stats.top_destinations.slice(0, 4)"
                :key="dest.id"
                class="feed-item-row"
                :class="{ 'mb-2-5': i < Math.min(stats.top_destinations.length, 4) - 1 }"
              >
                <div class="d-flex align-center ga-2-5">
                  <div class="rank-badge" :class="`rank-${i + 1}`">{{ i + 1 }}</div>
                  <div class="flex-grow-1 min-w-0">
                    <div class="feed-item-title text-truncate">{{ dest.name }}</div>
                    <div class="feed-item-meta text-truncate">{{ dest.category }} · {{ dest.city }}</div>
                  </div>
                  <div class="d-flex align-center ga-1 flex-shrink-0">
                    <v-icon size="13" color="#DC2626">mdi-heart</v-icon>
                    <span class="text-caption font-weight-black text-primary-red">{{ dest.likes }}</span>
                  </div>
                </div>
              </div>
            </div>
          </v-col>
        </v-row>
      </template>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <v-icon size="50" color="error">mdi-alert-circle-outline</v-icon>
        <h3 class="text-h6 font-weight-bold text-grey-darken-3 mt-3 mb-1">Gagal memuat data</h3>
        <p class="text-caption text-grey-darken-1 mb-4">{{ error }}</p>
        <v-btn color="#DC2626" rounded="pill" size="small" class="font-weight-bold text-none" @click="fetchStats">
          Coba Lagi
        </v-btn>
      </div>

    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSeoMeta } from '#app'
import { useApi } from '~/composables/useApi'
import AdminStatCards from '~/components/manage/AdminStatCards.vue'
import AdminQuickActionsGrid from '~/components/manage/AdminQuickActionsGrid.vue'

definePageMeta({ layout: 'default', middleware: 'admin' })

useSeoMeta({
  title: 'Dashboard Manajemen - Jalan Bareng',
  ogTitle: 'Dashboard Manajemen - Jalan Bareng',
})

interface DayData { date: string; label: string; count: number }
interface TopDest { id: number; name: string; city: string; category: string; likes: number }
interface RecentUser { id: number; name: string; email: string; role: string; joined_at: string }
interface PendingStory { id: number; title: string; author: string; submitted: string; slug: string }
interface CatStat { category: string; count: number }

interface Stats {
  summary: {
    total_users: number; new_users_month: number; new_users_week: number
    total_activations: number; active_activations: number
    total_destinations: number; new_destinations: number
    total_stories: number; pending_stories: number; published_stories: number; new_stories: number
    total_comments: number; total_likes: number
  }
  user_growth: DayData[]
  destination_growth: DayData[]
  top_destinations: TopDest[]
  recent_users: RecentUser[]
  destinations_by_category: CatStat[]
  pending_stories: PendingStory[]
}

const stats = ref<Stats | null>(null)
const loading = ref(true)
const error = ref('')

const categoryColors = ['#DC2626', '#1D4ED8', '#15803D', '#B45309', '#6B21A8', '#0369A1']

const maxUserCount = computed(() =>
  Math.max(1, ...((stats.value?.user_growth ?? []).map(d => d.count)))
)
const maxDestCount = computed(() =>
  Math.max(1, ...((stats.value?.destination_growth ?? []).map(d => d.count)))
)

function getBarHeight(val: number, max: number): number {
  return Math.max(4, Math.round((val / max) * 100))
}

function roleColor(role: string): string {
  const map: Record<string, string> = {
    admin: '#DC2626',
    community_admin: '#1D4ED8',
    member: '#15803D',
  }
  return map[role] ?? '#64748B'
}

function roleLabel(role: string): string {
  const map: Record<string, string> = {
    admin: 'Admin',
    community_admin: 'Comm. Admin',
    member: 'Member',
  }
  return map[role] ?? role
}

async function fetchStats() {
  loading.value = true
  error.value = ''
  try {
    const api = useApi()
    const res = await api.get('/admin/stats')
    stats.value = res.data as Stats
  } catch (err: any) {
    error.value = err?.message ?? 'Terjadi kesalahan saat memuat data.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchStats)
</script>

<style scoped>
.admin-dashboard-page {
  background: #FAFAF9;
  min-height: 100vh;
}

.header-icon-box {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: #FEF2F2;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.page-main-title {
  font-size: 1.25rem;
  font-weight: 900;
  letter-spacing: -0.02em;
  line-height: 1.2;
}

@media (min-width: 600px) {
  .page-main-title {
    font-size: 1.5rem;
  }
}

.live-chip {
  background: #FEF2F2 !important;
  color: #DC2626 !important;
}

.pulse-dot {
  animation: pulse 1.8s infinite;
}

@keyframes pulse {
  0% { opacity: 0.4; }
  50% { opacity: 1; }
  100% { opacity: 0.4; }
}

.refresh-btn {
  background: #FFFFFF;
}

/* ── Chart Cards ── */
.chart-card {
  background: #FFFFFF;
  border-radius: 20px;
  border: 1px solid #F1F5F9;
  box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
  box-sizing: border-box;
}

.chart-title {
  font-size: 0.92rem;
  font-weight: 800;
  color: #0F172A;
  letter-spacing: -0.01em;
  line-height: 1.25;
}

.chart-subtitle {
  font-size: 0.74rem;
  color: #64748B;
  margin-bottom: 0;
  line-height: 1.35;
}

/* ── Bar Chart (CSS) ── */
.bar-chart-wrapper {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  height: 140px;
  gap: 4px;
  padding: 0 2px;
}

@media (min-width: 600px) {
  .bar-chart-wrapper {
    height: 160px;
    gap: 8px;
  }
}

.bar-chart-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100%;
  min-width: 0;
}

.bars-row {
  display: flex;
  align-items: flex-end;
  gap: 2px;
  flex: 1;
  width: 100%;
  justify-content: center;
}

.bar {
  flex: 1;
  border-radius: 4px 4px 0 0;
  transition: height 0.4s ease;
  min-height: 4px;
  max-width: 14px;
}

.bar-user { background: linear-gradient(180deg, #DC2626, #EF4444); }
.bar-dest { background: linear-gradient(180deg, #1D4ED8, #3B82F6); }

.bar-label {
  font-size: 0.62rem;
  color: #94A3B8;
  font-weight: 600;
  margin-top: 4px;
  text-align: center;
  white-space: nowrap;
}

.border-t-subtle {
  border-top: 1px solid #F1F5F9;
}

.legend-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}
.legend-dot-user { background: #DC2626; }
.legend-dot-dest { background: #1D4ED8; }

/* ── Progress Bars (Category) ── */
.progress-track {
  height: 6px;
  background: #F1F5F9;
  border-radius: 9999px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 9999px;
  transition: width 0.5s ease;
}

/* ── Feed Rows ── */
.feed-item-row {
  padding: 8px 10px;
  background: #FAFAF9;
  border: 1px solid #F1F5F9;
  border-radius: 12px;
}

.feed-item-title {
  font-size: 0.78rem;
  font-weight: 700;
  color: #1E293B;
  line-height: 1.25;
}

.feed-item-meta {
  font-size: 0.68rem;
  color: #64748B;
  line-height: 1.3;
}

.story-badge {
  width: 26px;
  height: 26px;
  border-radius: 8px;
  background: #FEF3C7;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.rank-badge {
  width: 22px;
  height: 22px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.65rem;
  font-weight: 900;
  flex-shrink: 0;
}

.rank-1 { background: #FEF3C7; color: #B45309; }
.rank-2 { background: #F1F5F9; color: #475569; }
.rank-3 { background: #FEF9C3; color: #854D0E; }
.rank-4 { background: #F3F4F6; color: #6B7280; }

.text-primary-red { color: #DC2626; }
.max-w-75 { max-width: 75%; }
</style>
