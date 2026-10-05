<template>
  <v-row class="admin-stat-cards-row mb-4 mb-md-6" dense>
    <!-- Total Member -->
    <v-col cols="6" sm="6" md="3" class="pa-1-5 pa-sm-2">
      <div class="stat-card">
        <div class="d-flex align-center justify-space-between mb-2 mb-sm-3">
          <div class="stat-icon-box stat-icon-blue">
            <v-icon :size="iconSize" color="#1D4ED8">mdi-account-group</v-icon>
          </div>
          <span class="stat-tag-badge stat-tag-blue">Member</span>
        </div>
        <div class="stat-number">{{ summary.total_users }}</div>
        <div class="stat-label">Total Anggota</div>
        <div class="stat-trend trend-up mt-1 text-truncate">
          <v-icon size="12">mdi-trending-up</v-icon>
          <span>+{{ summary.new_users_week }} mgg ini</span>
        </div>
      </div>
    </v-col>

    <!-- Total Destinasi -->
    <v-col cols="6" sm="6" md="3" class="pa-1-5 pa-sm-2">
      <div class="stat-card">
        <div class="d-flex align-center justify-space-between mb-2 mb-sm-3">
          <div class="stat-icon-box stat-icon-green">
            <v-icon :size="iconSize" color="#15803D">mdi-map-marker-multiple</v-icon>
          </div>
          <span class="stat-tag-badge stat-tag-green">Lokasi</span>
        </div>
        <div class="stat-number">{{ summary.total_destinations }}</div>
        <div class="stat-label">Total Destinasi</div>
        <div class="stat-trend trend-up mt-1 text-truncate">
          <v-icon size="12">mdi-trending-up</v-icon>
          <span>+{{ summary.new_destinations }} bln ini</span>
        </div>
      </div>
    </v-col>

    <!-- Aktivasi Aktif -->
    <v-col cols="6" sm="6" md="3" class="pa-1-5 pa-sm-2">
      <div class="stat-card">
        <div class="d-flex align-center justify-space-between mb-2 mb-sm-3">
          <div class="stat-icon-box stat-icon-red">
            <v-icon :size="iconSize" color="#DC2626">mdi-compass-outline</v-icon>
          </div>
          <span class="stat-tag-badge stat-tag-red">Aktivasi</span>
        </div>
        <div class="stat-number text-primary-red">{{ summary.active_activations }}</div>
        <div class="stat-label">Aktivasi Aktif</div>
        <div class="stat-meta mt-1 text-truncate">
          dari {{ summary.total_activations }} total
        </div>
      </div>
    </v-col>

    <!-- Total Cerita Komunitas -->
    <v-col cols="6" sm="6" md="3" class="pa-1-5 pa-sm-2">
      <div class="stat-card" :class="{ 'stat-card-alert': summary.pending_stories > 0 }">
        <div class="d-flex align-center justify-space-between mb-2 mb-sm-3">
          <div class="stat-icon-box" :class="summary.pending_stories > 0 ? 'stat-icon-alert' : 'stat-icon-orange'">
            <v-icon :size="iconSize" :color="summary.pending_stories > 0 ? '#DC2626' : '#C2410C'">mdi-feather</v-icon>
          </div>
          <v-chip
            v-if="summary.pending_stories > 0"
            color="#DC2626"
            size="x-small"
            variant="flat"
            rounded="pill"
            class="text-white font-weight-black px-1.5"
            style="font-size: 0.65rem;"
          >
            {{ summary.pending_stories }} pending
          </v-chip>
          <span v-else class="stat-tag-badge stat-tag-orange">Cerita</span>
        </div>
        <div class="stat-number">{{ summary.total_stories }}</div>
        <div class="stat-label">Cerita Komunitas</div>
        <div class="stat-meta mt-1 text-truncate">
          {{ summary.published_stories }} terbit
        </div>
      </div>
    </v-col>
  </v-row>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useDisplay } from 'vuetify'

interface SummaryStats {
  total_users: number
  new_users_month: number
  new_users_week: number
  total_activations: number
  active_activations: number
  total_destinations: number
  new_destinations: number
  total_stories: number
  pending_stories: number
  published_stories: number
  new_stories: number
  total_comments?: number
  total_likes?: number
}

defineProps<{
  summary: SummaryStats
}>()

const display = useDisplay()
const iconSize = computed(() => (display.xs.value ? 20 : 24))
</script>

<style scoped>
.stat-card {
  background: #FFFFFF;
  border-radius: 20px;
  border: 1px solid #F1F5F9;
  box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
  padding: 14px 14px;
  height: 100%;
  display: flex;
  flex-direction: column;
  transition: transform 0.22s ease, box-shadow 0.22s ease;
  box-sizing: border-box;
}

@media (min-width: 600px) {
  .stat-card {
    padding: 18px 20px;
  }
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.stat-card-alert {
  border-color: #FECACA !important;
  background: #FEF2F2 !important;
}

.stat-icon-box {
  width: 36px;
  height: 36px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

@media (min-width: 600px) {
  .stat-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 14px;
  }
}

.stat-icon-blue { background-color: #EFF6FF; }
.stat-icon-green { background-color: #F0FDF4; }
.stat-icon-red { background-color: #FEF2F2; }
.stat-icon-orange { background-color: #FFF7ED; }
.stat-icon-alert { background-color: #FEE2E2; }

.stat-tag-badge {
  font-size: 0.65rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 2px 7px;
  border-radius: 9999px;
}

.stat-tag-blue { background: #EFF6FF; color: #1D4ED8; }
.stat-tag-green { background: #F0FDF4; color: #15803D; }
.stat-tag-red { background: #FEF2F2; color: #DC2626; }
.stat-tag-orange { background: #FFF7ED; color: #C2410C; }

.stat-number {
  font-size: 1.55rem;
  font-weight: 900;
  color: #0F172A;
  line-height: 1.1;
  letter-spacing: -0.03em;
  margin-top: 2px;
}

@media (min-width: 600px) {
  .stat-number {
    font-size: 2rem;
  }
}

.stat-label {
  font-size: 0.76rem;
  color: #64748B;
  font-weight: 600;
  margin-top: 3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

@media (min-width: 600px) {
  .stat-label {
    font-size: 0.82rem;
  }
}

.stat-trend {
  font-size: 0.7rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 3px;
}

.trend-up {
  color: #16A34A;
}

.stat-meta {
  font-size: 0.7rem;
  color: #94A3B8;
  font-weight: 500;
}

.text-primary-red {
  color: #DC2626 !important;
}
</style>
