<template>
  <v-row class="admin-stat-cards-row mb-3 mb-sm-5" dense>
    <!-- Total Member -->
    <v-col cols="6" sm="6" md="3" class="pa-1 pa-sm-2">
      <div class="stat-card">
        <div class="d-flex align-center justify-space-between mb-2 mb-sm-2.5">
          <div class="stat-icon-box stat-icon-blue">
            <v-icon :size="iconSize" color="#1D4ED8">mdi-account-group</v-icon>
          </div>
          <span class="stat-tag-badge stat-tag-blue">Member</span>
        </div>
        <div class="stat-number">{{ summary.total_users }}</div>
        <div class="stat-label">Total Anggota</div>
        <div class="stat-trend trend-up mt-1 text-truncate">
          <v-icon size="11">mdi-trending-up</v-icon>
          <span>+{{ summary.new_users_week }} mgg ini</span>
        </div>
      </div>
    </v-col>

    <!-- Total Destinasi -->
    <v-col cols="6" sm="6" md="3" class="pa-1 pa-sm-2">
      <div class="stat-card">
        <div class="d-flex align-center justify-space-between mb-2 mb-sm-2.5">
          <div class="stat-icon-box stat-icon-green">
            <v-icon :size="iconSize" color="#15803D">mdi-map-marker-multiple</v-icon>
          </div>
          <span class="stat-tag-badge stat-tag-green">Lokasi</span>
        </div>
        <div class="stat-number">{{ summary.total_destinations }}</div>
        <div class="stat-label">Total Destinasi</div>
        <div class="stat-trend trend-up mt-1 text-truncate">
          <v-icon size="11">mdi-trending-up</v-icon>
          <span>+{{ summary.new_destinations }} bln ini</span>
        </div>
      </div>
    </v-col>

    <!-- Aktivasi Aktif -->
    <v-col cols="6" sm="6" md="3" class="pa-1 pa-sm-2">
      <div class="stat-card">
        <div class="d-flex align-center justify-space-between mb-2 mb-sm-2.5">
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
    <v-col cols="6" sm="6" md="3" class="pa-1 pa-sm-2">
      <div class="stat-card" :class="{ 'stat-card-alert': summary.pending_stories > 0 }">
        <div class="d-flex align-center justify-space-between mb-2 mb-sm-2.5">
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
            style="font-size: 0.62rem; height: 18px;"
          >
            {{ summary.pending_stories }} review
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
const iconSize = computed(() => (display.xs.value ? 18 : 22))
</script>

<style scoped>
.stat-card {
  background: #FFFFFF;
  border-radius: 18px;
  border: 1px solid #F1F5F9;
  box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
  padding: 12px 12px;
  height: 100%;
  display: flex;
  flex-direction: column;
  transition: transform 0.22s ease, box-shadow 0.22s ease;
  box-sizing: border-box;
}

@media (min-width: 600px) {
  .stat-card {
    padding: 16px 18px;
    border-radius: 20px;
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
  width: 32px;
  height: 32px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

@media (min-width: 600px) {
  .stat-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 12px;
  }
}

.stat-icon-blue { background-color: #EFF6FF; }
.stat-icon-green { background-color: #F0FDF4; }
.stat-icon-red { background-color: #FEF2F2; }
.stat-icon-orange { background-color: #FFF7ED; }
.stat-icon-alert { background-color: #FEE2E2; }

.stat-tag-badge {
  font-size: 0.62rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 2px 7px;
  border-radius: 9999px;
  flex-shrink: 0;
}

.stat-tag-blue { background: #EFF6FF; color: #1D4ED8; }
.stat-tag-green { background: #F0FDF4; color: #15803D; }
.stat-tag-red { background: #FEF2F2; color: #DC2626; }
.stat-tag-orange { background: #FFF7ED; color: #C2410C; }

.stat-number {
  font-size: 1.45rem;
  font-weight: 900;
  color: #0F172A;
  line-height: 1.15;
  letter-spacing: -0.03em;
  margin-top: 2px;
}

@media (min-width: 600px) {
  .stat-number {
    font-size: 1.9rem;
  }
}

.stat-label {
  font-size: 0.74rem;
  color: #64748B;
  font-weight: 600;
  margin-top: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

@media (min-width: 600px) {
  .stat-label {
    font-size: 0.8rem;
  }
}

.stat-trend {
  font-size: 0.68rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.trend-up {
  color: #16A34A;
}

.stat-meta {
  font-size: 0.68rem;
  color: #94A3B8;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.text-primary-red {
  color: #DC2626 !important;
}
</style>
