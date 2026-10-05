<template>
  <div class="admin-dashboard-page">
    <v-container class="py-6 py-md-10">

      <!-- ── Page Header ── -->
      <div class="d-flex align-center justify-space-between flex-wrap ga-3 mb-8">
        <div>
          <div class="d-flex align-center ga-2 mb-1">
            <v-icon color="#DC2626" size="28">mdi-view-dashboard-outline</v-icon>
            <h1 class="text-h5 font-weight-black text-grey-darken-4">Dashboard Admin</h1>
          </div>
          <p class="text-body-2 text-grey-darken-1 mb-0">
            Ringkasan aktivitas platform Jalan Bareng hari ini
          </p>
        </div>
        <div class="d-flex align-center ga-2">
          <v-chip
            color="#DC2626"
            variant="tonal"
            rounded="pill"
            size="small"
            class="font-weight-bold"
          >
            <v-icon start size="14">mdi-circle</v-icon>
            Live
          </v-chip>
          <v-btn
            variant="outlined"
            rounded="pill"
            size="small"
            :loading="loading"
            @click="fetchStats"
          >
            <v-icon start size="16">mdi-refresh</v-icon>
            Refresh
          </v-btn>
        </div>
      </div>

      <!-- ── Loading Skeleton ── -->
      <div v-if="loading">
        <v-row class="mb-6">
          <v-col v-for="i in 4" :key="i" cols="6" md="3">
            <v-skeleton-loader type="card" height="120" rounded="xl" />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" md="8">
            <v-skeleton-loader type="card" height="320" rounded="xl" />
          </v-col>
          <v-col cols="12" md="4">
            <v-skeleton-loader type="card" height="320" rounded="xl" />
          </v-col>
        </v-row>
      </div>

      <template v-else-if="stats">
        <!-- ── Row 1: Stat Cards ── -->
        <v-row class="mb-6">
          <!-- Users -->
          <v-col cols="6" sm="6" md="3">
            <div class="stat-card pa-5">
              <div class="stat-icon-box stat-icon-blue mb-3">
                <v-icon size="22" color="#1D4ED8">mdi-account-group</v-icon>
              </div>
              <div class="stat-number">{{ stats.summary.total_users }}</div>
              <div class="stat-label">Total Member</div>
              <div class="stat-trend trend-up mt-1">
                <v-icon size="12">mdi-trending-up</v-icon>
                +{{ stats.summary.new_users_week }} minggu ini
              </div>
            </div>
          </v-col>

          <!-- Destinations -->
          <v-col cols="6" sm="6" md="3">
            <div class="stat-card pa-5">
              <div class="stat-icon-box stat-icon-green mb-3">
                <v-icon size="22" color="#15803D">mdi-map-marker-multiple</v-icon>
              </div>
              <div class="stat-number">{{ stats.summary.total_destinations }}</div>
              <div class="stat-label">Total Destinasi</div>
              <div class="stat-trend trend-up mt-1">
                <v-icon size="12">mdi-trending-up</v-icon>
                +{{ stats.summary.new_destinations }} bulan ini
              </div>
            </div>
          </v-col>

          <!-- Activations -->
          <v-col cols="6" sm="6" md="3">
            <div class="stat-card pa-5">
              <div class="stat-icon-box stat-icon-red mb-3">
                <v-icon size="22" color="#B91C1C">mdi-compass-outline</v-icon>
              </div>
              <div class="stat-number">{{ stats.summary.active_activations }}</div>
              <div class="stat-label">Aktivasi Aktif</div>
              <div class="stat-meta mt-1">
                dari {{ stats.summary.total_activations }} total aktivasi
              </div>
            </div>
          </v-col>

          <!-- Stories -->
          <v-col cols="6" sm="6" md="3">
            <div class="stat-card pa-5" :class="{ 'stat-card-alert': stats.summary.pending_stories > 0 }">
              <div class="stat-icon-box stat-icon-orange mb-3">
                <v-icon size="22" color="#C2410C">mdi-feather</v-icon>
              </div>
              <div class="d-flex align-center ga-2">
                <div class="stat-number">{{ stats.summary.total_stories }}</div>
                <v-chip
                  v-if="stats.summary.pending_stories > 0"
                  color="#DC2626"
                  size="x-small"
                  variant="flat"
                  rounded="pill"
                  class="text-white font-weight-black"
                >
                  {{ stats.summary.pending_stories }} pending
                </v-chip>
              </div>
              <div class="stat-label">Total Cerita</div>
              <div class="stat-meta mt-1">
                {{ stats.summary.published_stories }} dipublikasi
              </div>
            </div>
          </v-col>
        </v-row>

        <!-- ── Row 2: Charts + Pending Stories ── -->
        <v-row class="mb-6">
          <!-- User Growth Chart -->
          <v-col cols="12" md="8">
            <div class="chart-card pa-5 pa-md-6 h-100">
              <div class="d-flex align-center justify-space-between mb-5">
                <div>
                  <h3 class="chart-title">Pertumbuhan Komunitas</h3>
                  <p class="chart-subtitle">Member baru &amp; destinasi baru (7 hari terakhir)</p>
                </div>
              </div>

              <!-- Mini Bar Chart (CSS-based) -->
              <div class="bar-chart-wrapper">
                <div class="bar-chart-group" v-for="(day, i) in stats.user_growth" :key="i">
                  <div class="bars-row">
                    <!-- User bar -->
                    <div
                      class="bar bar-user"
                      :style="{ height: getBarHeight(day.count, maxUserCount) + '%' }"
                      :title="`${day.label}: ${day.count} user baru`"
                    ></div>
                    <!-- Destination bar -->
                    <div
                      class="bar bar-dest"
                      :style="{ height: getBarHeight(stats.destination_growth[i]?.count ?? 0, maxDestCount) + '%' }"
                      :title="`${day.label}: ${stats.destination_growth[i]?.count ?? 0} destinasi baru`"
                    ></div>
                  </div>
                  <div class="bar-label">{{ day.label }}</div>
                </div>
              </div>

              <!-- Legend -->
              <div class="d-flex align-center justify-center ga-5 mt-3">
                <div class="d-flex align-center ga-1">
                  <div class="legend-dot legend-dot-user"></div>
                  <span class="text-caption text-grey-darken-1">Member Baru</span>
                </div>
                <div class="d-flex align-center ga-1">
                  <div class="legend-dot legend-dot-dest"></div>
                  <span class="text-caption text-grey-darken-1">Destinasi Baru</span>
                </div>
              </div>
            </div>
          </v-col>

          <!-- Destinations by Category (Donut-like) -->
          <v-col cols="12" md="4">
            <div class="chart-card pa-5 pa-md-6 h-100">
              <h3 class="chart-title mb-1">Destinasi per Kategori</h3>
              <p class="chart-subtitle mb-4">Distribusi {{ stats.summary.total_destinations }} destinasi</p>

              <div
                v-for="(cat, i) in stats.destinations_by_category.slice(0, 6)"
                :key="i"
                class="category-bar-row mb-3"
              >
                <div class="d-flex justify-space-between align-center mb-1">
                  <span class="text-caption font-weight-medium text-grey-darken-3">{{ cat.category }}</span>
                  <span class="text-caption font-weight-bold text-grey-darken-4">{{ cat.count }}</span>
                </div>
                <div class="progress-track">
                  <div
                    class="progress-fill"
                    :style="{
                      width: ((cat.count / stats.summary.total_destinations) * 100) + '%',
                      backgroundColor: categoryColors[i % categoryColors.length]
                    }"
                  ></div>
                </div>
              </div>
            </div>
          </v-col>
        </v-row>

        <!-- ── Row 3: Pending Stories + Recent Users + Top Destinations ── -->
        <v-row>
          <!-- Pending Stories Queue -->
          <v-col cols="12" md="4">
            <div class="chart-card pa-5 pa-md-6 h-100">
              <div class="d-flex align-center justify-space-between mb-4">
                <div>
                  <h3 class="chart-title">Antrian Moderasi</h3>
                  <p class="chart-subtitle">Cerita menunggu review</p>
                </div>
                <v-chip
                  :color="stats.summary.pending_stories > 0 ? '#DC2626' : 'grey'"
                  variant="flat"
                  rounded="pill"
                  size="small"
                  class="text-white font-weight-black"
                >
                  {{ stats.summary.pending_stories }}
                </v-chip>
              </div>

              <div v-if="stats.pending_stories.length > 0">
                <div
                  v-for="(story, i) in stats.pending_stories"
                  :key="story.id"
                  class="pending-story-row"
                  :class="{ 'mb-3': i < stats.pending_stories.length - 1 }"
                >
                  <div class="d-flex align-center ga-3">
                    <div class="story-badge">
                      <v-icon size="14" color="#B45309">mdi-clock-outline</v-icon>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                      <div class="text-caption font-weight-bold text-grey-darken-4 text-truncate">{{ story.title }}</div>
                      <div class="text-caption text-grey-darken-1">{{ story.author }} · {{ story.submitted }}</div>
                    </div>
                    <v-btn
                      icon
                      variant="text"
                      size="x-small"
                      :to="`/manage/stories`"
                      :aria-label="`Review story: ${story.title}`"
                    >
                      <v-icon size="16" color="#DC2626">mdi-arrow-right</v-icon>
                    </v-btn>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-6">
                <v-icon size="36" color="success">mdi-check-circle-outline</v-icon>
                <p class="text-body-2 text-grey-darken-1 mt-2 mb-0">Tidak ada cerita pending</p>
              </div>

              <div v-if="stats.summary.pending_stories > 0" class="mt-4">
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
                  Moderasi Semua
                </v-btn>
              </div>
            </div>
          </v-col>

          <!-- Recent Registrations -->
          <v-col cols="12" md="4">
            <div class="chart-card pa-5 pa-md-6 h-100">
              <div class="d-flex align-center justify-space-between mb-4">
                <div>
                  <h3 class="chart-title">Member Terbaru</h3>
                  <p class="chart-subtitle">Bergabung baru-baru ini</p>
                </div>
                <v-btn
                  to="/manage/users"
                  variant="text"
                  color="#DC2626"
                  size="small"
                  class="font-weight-bold text-caption"
                >
                  Lihat Semua
                </v-btn>
              </div>

              <div
                v-for="(user, i) in stats.recent_users"
                :key="user.id"
                class="recent-user-row"
                :class="{ 'mb-3': i < stats.recent_users.length - 1 }"
              >
                <div class="d-flex align-center ga-3">
                  <v-avatar size="36" :color="roleColor(user.role)">
                    <span class="text-caption font-weight-black text-white">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </span>
                  </v-avatar>
                  <div class="flex-grow-1 min-w-0">
                    <div class="text-caption font-weight-bold text-grey-darken-4 text-truncate">{{ user.name }}</div>
                    <div class="text-caption text-grey-darken-1 text-truncate">{{ user.email }}</div>
                  </div>
                  <v-chip
                    :color="roleColor(user.role)"
                    variant="flat"
                    size="x-small"
                    rounded="pill"
                    class="text-white font-weight-bold"
                  >
                    {{ roleLabel(user.role) }}
                  </v-chip>
                </div>
              </div>
            </div>
          </v-col>

          <!-- Top Destinations -->
          <v-col cols="12" md="4">
            <div class="chart-card pa-5 pa-md-6 h-100">
              <div class="d-flex align-center justify-space-between mb-4">
                <div>
                  <h3 class="chart-title">Destinasi Terpopuler</h3>
                  <p class="chart-subtitle">Berdasarkan jumlah suka</p>
                </div>
              </div>

              <div
                v-for="(dest, i) in stats.top_destinations"
                :key="dest.id"
                class="top-dest-row"
                :class="{ 'mb-3': i < stats.top_destinations.length - 1 }"
              >
                <div class="d-flex align-center ga-3">
                  <div class="rank-badge" :class="`rank-${i + 1}`">{{ i + 1 }}</div>
                  <div class="flex-grow-1 min-w-0">
                    <div class="text-caption font-weight-bold text-grey-darken-4 text-truncate">{{ dest.name }}</div>
                    <div class="text-caption text-grey-darken-1">
                      {{ dest.category }} · {{ dest.city }}
                    </div>
                  </div>
                  <div class="d-flex align-center ga-1">
                    <v-icon size="12" color="#DC2626">mdi-heart</v-icon>
                    <span class="text-caption font-weight-black text-primary-red">{{ dest.likes }}</span>
                  </div>
                </div>
              </div>
            </div>
          </v-col>
        </v-row>

        <!-- ── Row 4: Quick Actions ── -->
        <div class="quick-actions-bar mt-8">
          <div class="qa-label mb-3">
            <v-icon size="16" color="#6B7280">mdi-lightning-bolt-outline</v-icon>
            Aksi Cepat
          </div>
          <div class="d-flex flex-wrap ga-3">
            <v-btn to="/manage/activations" color="#DC2626" variant="tonal" rounded="pill" size="small" class="font-weight-bold">
              <v-icon start size="16">mdi-compass-outline</v-icon> Kelola Aktivasi
            </v-btn>
            <v-btn to="/manage/destinations" color="#DC2626" variant="tonal" rounded="pill" size="small" class="font-weight-bold">
              <v-icon start size="16">mdi-map-marker</v-icon> Kelola Destinasi
            </v-btn>
            <v-btn to="/manage/stories" color="#B45309" variant="tonal" rounded="pill" size="small" class="font-weight-bold">
              <v-icon start size="16">mdi-feather</v-icon> Moderasi Cerita
            </v-btn>
            <v-btn to="/manage/users" color="#1D4ED8" variant="tonal" rounded="pill" size="small" class="font-weight-bold">
              <v-icon start size="16">mdi-account-group</v-icon> Kelola User
            </v-btn>
            <v-btn to="/manage/categories" color="#15803D" variant="tonal" rounded="pill" size="small" class="font-weight-bold">
              <v-icon start size="16">mdi-tag-multiple</v-icon> Kelola Kategori
            </v-btn>
            <v-btn to="/manage/reports" color="#DC2626" variant="tonal" rounded="pill" size="small" class="font-weight-bold">
              <v-icon start size="16">mdi-file-chart-outline</v-icon> Laporan &amp; Ekspor
            </v-btn>
            <v-btn to="/manage/settings" color="#6B21A8" variant="tonal" rounded="pill" size="small" class="font-weight-bold">
              <v-icon start size="16">mdi-cog-outline</v-icon> Pengaturan
            </v-btn>
          </div>
        </div>
      </template>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-16">
        <v-icon size="56" color="error">mdi-alert-circle-outline</v-icon>
        <h3 class="text-h6 font-weight-bold text-grey-darken-3 mt-4 mb-2">Gagal memuat data</h3>
        <p class="text-body-2 text-grey-darken-1 mb-4">{{ error }}</p>
        <v-btn color="#DC2626" rounded="pill" @click="fetchStats">Coba Lagi</v-btn>
      </div>

    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useSeoMeta, navigateTo } from '#app'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'

definePageMeta({ layout: 'default', middleware: 'admin' })

useSeoMeta({
  title: 'Dashboard Admin - Jalan Bareng',
  ogTitle: 'Dashboard Admin - Jalan Bareng',
})

const authStore = useAuthStore()

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

const categoryColors = [
  '#DC2626', '#1D4ED8', '#15803D', '#B45309', '#6B21A8', '#0369A1'
]

const maxUserCount = computed(() =>
  Math.max(1, ...((stats.value?.user_growth ?? []).map(d => d.count)))
)
const maxDestCount = computed(() =>
  Math.max(1, ...((stats.value?.destination_growth ?? []).map(d => d.count)))
)

function getBarHeight(val: number, max: number): number {
  const minH = 4
  return Math.max(minH, Math.round((val / max) * 100))
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

/* ── Stat Cards ── */
.stat-card {
  background: #FFFFFF;
  border-radius: 20px;
  border: 1px solid rgba(0, 0, 0, 0.07);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  height: 100%;
}
.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.07);
}
.stat-card-alert {
  border-color: #FECACA !important;
  background: #FEF2F2 !important;
}

.stat-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.stat-icon-blue { background-color: #DBEAFE; }
.stat-icon-green { background-color: #DCFCE7; }
.stat-icon-red { background-color: #FEE2E2; }
.stat-icon-orange { background-color: #FFEDD5; }

.stat-number {
  font-size: 2rem;
  font-weight: 900;
  color: #111827;
  line-height: 1;
  letter-spacing: -0.03em;
}
.stat-label {
  font-size: 0.8rem;
  color: #6B7280;
  font-weight: 600;
  margin-top: 0.2rem;
}
.stat-trend {
  font-size: 0.73rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}
.trend-up { color: #16A34A; }
.stat-meta {
  font-size: 0.73rem;
  color: #9CA3AF;
}

/* ── Chart Cards ── */
.chart-card {
  background: #FFFFFF;
  border-radius: 20px;
  border: 1px solid rgba(0, 0, 0, 0.07);
  height: 100%;
}
.chart-title {
  font-size: 0.95rem;
  font-weight: 800;
  color: #111827;
  letter-spacing: -0.01em;
}
.chart-subtitle {
  font-size: 0.78rem;
  color: #6B7280;
  margin-bottom: 0;
}

/* ── Bar Chart (CSS) ── */
.bar-chart-wrapper {
  display: flex;
  align-items: flex-end;
  justify-content: space-around;
  height: 160px;
  gap: 8px;
  padding: 0 4px;
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
  gap: 3px;
  flex: 1;
  width: 100%;
  justify-content: center;
}
.bar {
  flex: 1;
  border-radius: 5px 5px 0 0;
  transition: height 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
  min-height: 4px;
  max-width: 16px;
}
.bar-user { background: linear-gradient(180deg, #DC2626, #EF4444); }
.bar-dest { background: linear-gradient(180deg, #1D4ED8, #3B82F6); }
.bar-label {
  font-size: 0.65rem;
  color: #9CA3AF;
  font-weight: 600;
  margin-top: 6px;
  text-align: center;
  white-space: nowrap;
}

.legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}
.legend-dot-user { background: #DC2626; }
.legend-dot-dest { background: #1D4ED8; }

/* ── Progress Bars (Category) ── */
.progress-track {
  height: 6px;
  background: #F3F4F6;
  border-radius: 9999px;
  overflow: hidden;
}
.progress-fill {
  height: 100%;
  border-radius: 9999px;
  transition: width 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* ── Pending Story Row ── */
.story-badge {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  background: #FEF3C7;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.pending-story-row {
  padding-bottom: 0.75rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}
.pending-story-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

/* ── Recent User Row ── */
.recent-user-row {
  padding-bottom: 0.75rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}
.recent-user-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

/* ── Top Destination Row ── */
.top-dest-row {
  padding-bottom: 0.75rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}
.top-dest-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.rank-badge {
  width: 24px;
  height: 24px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 900;
  flex-shrink: 0;
}
.rank-1 { background: #FEF3C7; color: #B45309; }
.rank-2 { background: #F1F5F9; color: #475569; }
.rank-3 { background: #FEF9C3; color: #854D0E; }
.rank-4, .rank-5 { background: #F3F4F6; color: #6B7280; }

/* ── Quick Actions Bar ── */
.quick-actions-bar {
  background: #FFFFFF;
  border: 1px solid rgba(0, 0, 0, 0.07);
  border-radius: 20px;
  padding: 1.25rem 1.5rem;
}
.qa-label {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.78rem;
  font-weight: 700;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.text-primary-red { color: #DC2626; }
</style>
