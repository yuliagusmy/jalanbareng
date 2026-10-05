<template>
  <div class="admin-reports-page">
    <v-container class="py-6 py-md-8">
      <!-- ── Page Header ── -->
      <div class="d-flex align-center justify-space-between flex-wrap ga-4 mb-8">
        <div>
          <div class="d-flex align-center ga-2 mb-1">
            <v-icon color="#DC2626" size="28">mdi-file-chart-outline</v-icon>
            <h1 class="text-h5 text-sm-h4 font-weight-black text-grey-darken-4">
              Laporan & Ekspor Data
            </h1>
          </div>
          <p class="text-body-2 text-grey-darken-1 mb-0">
            Pantau pertumbuhan interaksi bulanan serta ekspor arsip data member & peserta aktivasi
          </p>
        </div>

        <div class="d-flex align-center flex-wrap ga-3">
          <!-- Time Range Selector -->
          <v-btn-toggle
            v-model="selectedMonths"
            mandatory
            color="#DC2626"
            rounded="pill"
            density="comfortable"
            class="range-toggle elevation-0"
            @update:model-value="fetchReportData"
          >
            <v-btn :value="3" size="small" class="font-weight-bold">3 Bulan</v-btn>
            <v-btn :value="6" size="small" class="font-weight-bold">6 Bulan</v-btn>
            <v-btn :value="12" size="small" class="font-weight-bold">12 Bulan</v-btn>
          </v-btn-toggle>

          <v-btn
            variant="outlined"
            rounded="pill"
            size="small"
            color="grey-darken-2"
            class="font-weight-bold"
            :loading="loading"
            @click="fetchReportData"
          >
            <v-icon start size="16">mdi-refresh</v-icon>
            Segarkan
          </v-btn>
        </div>
      </div>

      <!-- ── Loading Skeleton ── -->
      <div v-if="loading && !reportData">
        <v-row class="mb-6">
          <v-col v-for="i in 4" :key="i" cols="6" sm="6" md="3">
            <v-skeleton-loader type="card" height="110" rounded="xl" />
          </v-col>
        </v-row>
        <v-skeleton-loader type="card" height="360" rounded="xl" class="mb-6" />
        <v-skeleton-loader type="card" height="260" rounded="xl" />
      </div>

      <!-- ── Error State ── -->
      <div v-else-if="error" class="text-center py-16">
        <v-icon size="56" color="error">mdi-alert-circle-outline</v-icon>
        <h3 class="text-h6 font-weight-bold text-grey-darken-3 mt-4 mb-2">Gagal memuat data laporan</h3>
        <p class="text-body-2 text-grey-darken-1 mb-4">{{ error }}</p>
        <v-btn color="#DC2626" rounded="pill" class="text-white font-weight-bold" @click="fetchReportData">
          Coba Lagi
        </v-btn>
      </div>

      <!-- ── Main Content ── -->
      <template v-else-if="reportData">
        <!-- ── Summary KPI Cards ── -->
        <v-row class="mb-6">
          <!-- Total Member -->
          <v-col cols="6" sm="6" md="3">
            <div class="kpi-card pa-4 pa-sm-5">
              <div class="kpi-icon-box bg-blue-subtle mb-3">
                <v-icon size="22" color="#1D4ED8">mdi-account-group-outline</v-icon>
              </div>
              <div class="kpi-value">{{ summary.total_members }}</div>
              <div class="kpi-label">Total Member</div>
              <div class="kpi-trend trend-green mt-1">
                <v-icon size="12">mdi-trending-up</v-icon>
                +{{ summary.new_members_month }} bulan ini
              </div>
            </div>
          </v-col>

          <!-- Destinasi Terdaftar -->
          <v-col cols="6" sm="6" md="3">
            <div class="kpi-card pa-4 pa-sm-5">
              <div class="kpi-icon-box bg-green-subtle mb-3">
                <v-icon size="22" color="#15803D">mdi-map-marker-multiple-outline</v-icon>
              </div>
              <div class="kpi-value">{{ summary.total_destinations }}</div>
              <div class="kpi-label">Destinasi Terdaftar</div>
              <div class="kpi-trend trend-green mt-1">
                <v-icon size="12">mdi-trending-up</v-icon>
                +{{ summary.new_destinations_month }} bulan ini
              </div>
            </div>
          </v-col>

          <!-- Komentar Komunitas -->
          <v-col cols="6" sm="6" md="3">
            <div class="kpi-card pa-4 pa-sm-5">
              <div class="kpi-icon-box bg-orange-subtle mb-3">
                <v-icon size="22" color="#C2410C">mdi-comment-text-multiple-outline</v-icon>
              </div>
              <div class="kpi-value">{{ summary.total_comments }}</div>
              <div class="kpi-label">Total Komentar</div>
              <div class="kpi-trend trend-orange mt-1">
                <v-icon size="12">mdi-forum-outline</v-icon>
                +{{ summary.new_comments_month }} bulan ini
              </div>
            </div>
          </v-col>

          <!-- Pendaftar Event & Aktivasi -->
          <v-col cols="6" sm="6" md="3">
            <div class="kpi-card pa-4 pa-sm-5">
              <div class="kpi-icon-box bg-purple-subtle mb-3">
                <v-icon size="22" color="#7E22CE">mdi-calendar-check-outline</v-icon>
              </div>
              <div class="kpi-value">{{ summary.total_participants }}</div>
              <div class="kpi-label">Pendaftar Aktivasi</div>
              <div class="kpi-trend trend-purple mt-1">
                <v-icon size="12">mdi-account-plus</v-icon>
                +{{ summary.new_participants_month }} bulan ini
              </div>
            </div>
          </v-col>
        </v-row>

        <!-- ── Engagement Chart & Breakdown Component ── -->
        <EngagementChart :trends="reportData.trends" class="mb-8" />

        <!-- ── Data Export Section Header ── -->
        <div class="section-title-wrap mb-4 mt-6">
          <div class="d-flex align-center ga-2">
            <v-icon color="#DC2626" size="22">mdi-database-export-outline</v-icon>
            <h2 class="text-h6 font-weight-black text-grey-darken-4">
              Pusat Ekspor Dokumen Laporan
            </h2>
          </div>
          <p class="text-caption text-grey-darken-1 mb-0">
            Unduh berkas data terstruktur untuk kebutuhan arsip, presentasi mitra sponsor, atau audit internal
          </p>
        </div>

        <!-- ── Report Export Card Component ── -->
        <ReportExportCard class="mb-6" />
      </template>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'
import EngagementChart from '~/components/manage/EngagementChart.vue'
import ReportExportCard from '~/components/manage/ReportExportCard.vue'

definePageMeta({
  middleware: ['auth', 'admin'],
  layout: 'solid'
})

useSeoMeta({
  title: 'Laporan & Ekspor Data Admin - Jalan Bareng',
  ogTitle: 'Laporan & Ekspor Data Admin - Jalan Bareng',
  description: 'Halaman analitik engagement bulanan dan ekspor berkas data Jalan Bareng'
})

const { api } = useApi()

const selectedMonths = ref(6)
const loading = ref(true)
const error = ref<string | null>(null)
const reportData = ref<any>(null)

const summary = computed(() => {
  return reportData.value?.summary || {
    total_members: 0,
    new_members_month: 0,
    total_destinations: 0,
    new_destinations_month: 0,
    total_comments: 0,
    new_comments_month: 0,
    total_likes: 0,
    new_likes_month: 0,
    total_events: 0,
    total_participants: 0,
    new_participants_month: 0
  }
})

const fetchReportData = async () => {
  loading.value = true
  error.value = null
  try {
    const res = await api.get('/admin/reports/engagement', {
      params: { months: selectedMonths.value }
    })
    reportData.value = res.data?.data || null
  } catch (err: any) {
    console.error('Gagal memuat laporan', err)
    error.value = err.response?.data?.message || 'Terjadi kesalahan saat memuat data laporan.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchReportData()
})
</script>

<style scoped>
.admin-reports-page {
  background-color: #FAFAF9;
  min-height: 100vh;
}

.range-toggle {
  border: 1px solid rgba(0, 0, 0, 0.1);
  background: #FFFFFF;
}

.kpi-card {
  background: #FFFFFF;
  border-radius: 20px;
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.kpi-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.bg-blue-subtle { background-color: #EFF6FF; }
.bg-green-subtle { background-color: #F0FDF4; }
.bg-orange-subtle { background-color: #FFF7ED; }
.bg-purple-subtle { background-color: #FAF5FF; }

.kpi-value {
  font-size: 1.5rem;
  font-weight: 900;
  color: #111827;
  line-height: 1.2;
}

.kpi-label {
  font-size: 0.78rem;
  color: #6B7280;
  margin-top: 2px;
}

.kpi-trend {
  font-size: 0.72rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 3px;
}

.trend-green { color: #16A34A; }
.trend-orange { color: #EA580C; }
.trend-purple { color: #7E22CE; }

.section-title-wrap {
  border-bottom: 1px solid rgba(0, 0, 0, 0.06);
  padding-bottom: 0.75rem;
}
</style>
