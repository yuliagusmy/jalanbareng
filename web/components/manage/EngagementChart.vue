<template>
  <div class="engagement-chart-wrapper">
    <!-- Chart Header & Metric Filter -->
    <v-card class="chart-card pa-6 mb-6" rounded="xl" elevation="0">
      <div class="d-flex align-center justify-space-between flex-wrap ga-3 mb-6">
        <div>
          <div class="d-flex align-center ga-2 mb-1">
            <v-icon color="#DC2626" size="22">mdi-chart-bell-curve-cumulative</v-icon>
            <h3 class="text-h6 font-weight-black text-grey-darken-4">
              Grafik Engagement Komunitas
            </h3>
          </div>
          <p class="text-caption text-grey-darken-1 mb-0">
            Aktivitas interaksi pejalan kaki, penambahan destinasi, komentar, & apresiasi suka
          </p>
        </div>

        <!-- Metric Switcher Chips -->
        <div class="metric-filter-chips">
          <v-chip
            v-for="filter in filterOptions"
            :key="filter.key"
            size="small"
            rounded="pill"
            :variant="selectedMetric === filter.key ? 'flat' : 'outlined'"
            :color="selectedMetric === filter.key ? filter.color : 'grey-darken-1'"
            class="font-weight-bold cursor-pointer"
            @click="selectedMetric = filter.key"
          >
            <v-icon start size="14">{{ filter.icon }}</v-icon>
            {{ filter.label }}
          </v-chip>
        </div>
      </div>

      <!-- Chart Display Area -->
      <div v-if="trends.length > 0" class="chart-display-container">
        <div class="bar-chart-grid">
          <div
            v-for="(item, idx) in trends"
            :key="item.period"
            class="bar-column-group"
          >
            <!-- Bar Container with percentage height -->
            <div class="bar-track">
              <!-- Target Bar -->
              <div
                class="bar-pill"
                :style="{
                  height: getBarHeight(item) + '%',
                  backgroundColor: currentFilterColor
                }"
              >
                <!-- Tooltip Badge on Hover -->
                <div class="bar-hover-tooltip">
                  <strong>{{ getMetricValue(item) }}</strong>
                  <span class="tooltip-label">{{ currentFilterLabel }}</span>
                </div>
              </div>
            </div>

            <!-- Value on top of / inside column -->
            <div class="bar-value-text font-weight-bold">
              {{ getMetricValue(item) }}
            </div>

            <!-- X-axis Label -->
            <div class="bar-x-label text-caption font-weight-medium">
              {{ item.label }}
            </div>
          </div>
        </div>

        <!-- Chart Legend -->
        <div class="d-flex align-center justify-center ga-6 mt-6 pt-4 border-t flex-wrap">
          <div class="d-flex align-center ga-2">
            <span class="legend-indicator" :style="{ backgroundColor: currentFilterColor }"></span>
            <span class="text-caption font-weight-bold text-grey-darken-3">{{ currentFilterLabel }}</span>
          </div>
          <div class="text-caption text-grey-darken-1">
            Rata-rata: <strong>{{ averageValue }}</strong> / bulan
          </div>
          <div class="text-caption text-grey-darken-1">
            Tertinggi: <strong>{{ peakValue }}</strong>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-10">
        <v-icon size="48" color="grey-lighten-1">mdi-chart-line-variant</v-icon>
        <p class="text-body-2 text-grey-darken-1 mt-2 mb-0">Belum ada data interaksi pada periode ini</p>
      </div>
    </v-card>

    <!-- Monthly Breakdown Table -->
    <v-card class="breakdown-card pa-6" rounded="xl" elevation="0">
      <div class="d-flex align-center justify-space-between mb-4">
        <div>
          <h4 class="text-subtitle-1 font-weight-black text-grey-darken-4">
            Rincian Data Bulanan
          </h4>
          <p class="text-caption text-grey-darken-1 mb-0">
            Tabel rekapitulasi performa per bulan
          </p>
        </div>
      </div>

      <div class="table-responsive">
        <table class="modern-table">
          <thead>
            <tr>
              <th>Periode</th>
              <th class="text-center">Destinasi Baru</th>
              <th class="text-center">Komentar</th>
              <th class="text-center">Suka (Likes)</th>
              <th class="text-center">Peserta Event</th>
              <th class="text-right">Total Interaksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in trendsReversed" :key="item.period">
              <td class="font-weight-bold text-grey-darken-4">
                <v-icon size="16" color="#DC2626" class="mr-1">mdi-calendar-month</v-icon>
                {{ item.label }}
              </td>
              <td class="text-center">
                <v-chip size="x-small" color="#16A34A" variant="tonal" rounded="pill" class="font-weight-bold">
                  +{{ item.destinations }}
                </v-chip>
              </td>
              <td class="text-center text-body-2 text-grey-darken-3">
                {{ item.comments }}
              </td>
              <td class="text-center text-body-2 text-grey-darken-3">
                {{ item.likes }}
              </td>
              <td class="text-center">
                <v-chip size="x-small" color="#0284C7" variant="tonal" rounded="pill" class="font-weight-bold">
                  {{ item.participants }}
                </v-chip>
              </td>
              <td class="text-right font-weight-black text-primary-red">
                {{ item.total_engagement }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </v-card>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

export interface TrendItem {
  period: string
  label: string
  comments: number
  likes: number
  destinations: number
  members: number
  participants: number
  total_engagement: number
}

const props = defineProps<{
  trends: TrendItem[]
}>()

type MetricKey = 'all' | 'destinations' | 'comments' | 'likes' | 'participants'

const selectedMetric = ref<MetricKey>('all')

const filterOptions = [
  { key: 'all' as MetricKey, label: 'Total Interaksi', icon: 'mdi-lightning-bolt', color: '#DC2626' },
  { key: 'destinations' as MetricKey, label: 'Destinasi Baru', icon: 'mdi-map-marker-plus', color: '#16A34A' },
  { key: 'comments' as MetricKey, label: 'Komentar', icon: 'mdi-comment-outline', color: '#EA580C' },
  { key: 'likes' as MetricKey, label: 'Suka', icon: 'mdi-heart-outline', color: '#E11D48' },
  { key: 'participants' as MetricKey, label: 'Peserta Event', icon: 'mdi-account-check-outline', color: '#0284C7' },
]

const currentFilter = computed(() => {
  return filterOptions.find(f => f.key === selectedMetric.value) || filterOptions[0]
})

const currentFilterColor = computed(() => currentFilter.value.color)
const currentFilterLabel = computed(() => currentFilter.value.label)

const getMetricValue = (item: TrendItem): number => {
  switch (selectedMetric.value) {
    case 'destinations':
      return item.destinations
    case 'comments':
      return item.comments
    case 'likes':
      return item.likes
    case 'participants':
      return item.participants
    case 'all':
    default:
      return item.total_engagement
  }
}

const maxMetricValue = computed(() => {
  if (!props.trends || props.trends.length === 0) return 1
  const vals = props.trends.map(t => getMetricValue(t))
  return Math.max(...vals, 1)
})

const getBarHeight = (item: TrendItem): number => {
  const val = getMetricValue(item)
  if (val === 0) return 4 // minimum baseline height
  const height = Math.round((val / maxMetricValue.value) * 100)
  return Math.max(6, Math.min(100, height))
}

const peakValue = computed(() => {
  if (!props.trends.length) return 0
  return Math.max(...props.trends.map(t => getMetricValue(t)))
})

const averageValue = computed(() => {
  if (!props.trends.length) return 0
  const sum = props.trends.reduce((acc, curr) => acc + getMetricValue(curr), 0)
  return Math.round(sum / props.trends.length)
})

const trendsReversed = computed(() => {
  return [...props.trends].reverse()
})
</script>

<style scoped>
.chart-card, .breakdown-card {
  background: #FFFFFF;
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
}

.metric-filter-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.chart-display-container {
  padding-top: 1.5rem;
}

.bar-chart-grid {
  display: flex;
  align-items: flex-end;
  justify-content: space-around;
  height: 220px;
  gap: 12px;
  border-bottom: 2px solid #F3F4F6;
  padding-bottom: 8px;
}

.bar-column-group {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  min-width: 44px;
  height: 100%;
  justify-content: flex-end;
}

.bar-track {
  width: 100%;
  max-width: 44px;
  height: 160px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  position: relative;
}

.bar-pill {
  width: 100%;
  border-radius: 8px 8px 3px 3px;
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  cursor: pointer;
}

.bar-pill:hover {
  filter: brightness(1.1);
  transform: scaleY(1.02);
}

.bar-hover-tooltip {
  display: none;
  position: absolute;
  top: -38px;
  left: 50%;
  transform: translateX(-50%);
  background: #111827;
  color: #FFFFFF;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.72rem;
  white-space: nowrap;
  z-index: 10;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.bar-hover-tooltip::after {
  content: '';
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -4px;
  border-width: 4px;
  border-style: solid;
  border-color: #111827 transparent transparent transparent;
}

.bar-pill:hover .bar-hover-tooltip {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.tooltip-label {
  font-size: 0.65rem;
  color: #9CA3AF;
}

.bar-value-text {
  font-size: 0.8rem;
  color: #374151;
  margin-top: 6px;
}

.bar-x-label {
  color: #6B7280;
  margin-top: 4px;
  text-align: center;
}

.legend-indicator {
  width: 12px;
  height: 12px;
  border-radius: 3px;
  display: inline-block;
}

.text-primary-red {
  color: #DC2626;
}

/* Modern HTML Table Styling */
.table-responsive {
  overflow-x: auto;
}

.modern-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
}

.modern-table th {
  background-color: #F9FAFB;
  color: #4B5563;
  font-weight: 700;
  padding: 12px 16px;
  border-bottom: 2px solid #E5E7EB;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.04em;
}

.modern-table td {
  padding: 14px 16px;
  border-bottom: 1px solid #F3F4F6;
}

.modern-table tr:hover td {
  background-color: #FEF2F2;
}

@media (max-width: 600px) {
  .bar-chart-grid {
    height: 180px;
    gap: 6px;
  }
  .bar-track {
    max-width: 24px;
    height: 130px;
  }
  .bar-x-label {
    font-size: 0.65rem !important;
  }
}
</style>
