<template>
  <div class="stats-section-wrapper mb-6 mb-md-16">
    <!-- Desktop View: 4 Individual Cards -->
    <v-row class="d-none d-md-flex" dense>
      <v-col cols="12" md="3" v-for="(stat, index) in stats" :key="'desk-' + index">
        <v-card
          elevation="0"
          rounded="xl"
          class="h-100 pa-6 text-center stat-item-card border"
        >
          <div class="stat-icon-badge mx-auto mb-3" :style="{ backgroundColor: getBadgeBg(stat.color) }">
            <v-icon :color="stat.color" size="28">{{ stat.icon }}</v-icon>
          </div>
          <div
            class="font-weight-black stat-number mb-1 text-h4"
            :style="{ color: stat.color }"
          >
            {{ stat.value }}
          </div>
          <div class="font-weight-bold text-grey-darken-2 text-body-2">
            {{ stat.label }}
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Mobile View: 4 Individual Rounded Cards in 2x2 Grid (Icon on Left) -->
    <div class="d-grid d-md-none stats-mobile-grid">
      <div
        v-for="(stat, index) in stats"
        :key="'mob-' + index"
        class="stat-box-card"
      >
        <div class="stat-box-icon" :style="{ backgroundColor: getBadgeBg(stat.color) }">
          <v-icon :color="stat.color" size="18">{{ stat.icon }}</v-icon>
        </div>
        <div class="stat-box-content">
          <div class="stat-box-value" :style="{ color: stat.color }">{{ stat.value }}</div>
          <div class="stat-box-label">{{ stat.label }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  stats: {
    type: Array as () => any[],
    required: true
  }
})

const getBadgeBg = (hexColor: string) => {
  return `${hexColor}15`
}
</script>

<style scoped>
/* Desktop Styles */
.stat-item-card {
  background-color: #FFFFFF;
  border-color: #E2E8F0 !important;
  transition: all 0.25s ease;
}

.stat-item-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08) !important;
  border-color: #CBD5E1 !important;
}

.stat-icon-badge {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-number {
  line-height: 1.2;
  letter-spacing: -0.5px;
}

/* Mobile Compact Styles (Individual Rounded Boxes with Left Icon) */
.stats-mobile-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  width: 100%;
}

.stat-box-card {
  background-color: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  padding: 8px 10px;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  transition: transform 0.2s ease;
  min-height: 56px;
}

.stat-box-icon {
  width: 32px;
  height: 32px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-box-content {
  display: flex;
  flex-direction: column;
  min-width: 0;
  text-align: left;
}

.stat-box-value {
  font-size: 1.05rem;
  font-weight: 800;
  line-height: 1.15;
  letter-spacing: -0.02em;
}

.stat-box-label {
  font-size: 0.65rem;
  font-weight: 600;
  color: #64748B;
  line-height: 1.15;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}
</style>

