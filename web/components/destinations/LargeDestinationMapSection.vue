<template>
  <section class="large-destinations-map-section py-6 py-md-8">
    <v-container>
      <!-- Map Section Header -->
      <div class="d-flex flex-column flex-md-row align-md-end justify-space-between mb-5 ga-4">
        <div>
          <div class="map-eyebrow d-inline-flex align-center mb-2">
            <v-icon size="14" color="#DC2626" class="mr-1.5">mdi-map-marker-radius-outline</v-icon>
            <span>PETA EKSPLORASI KOTA</span>
          </div>
          <h2 class="map-heading text-grey-darken-4 font-weight-black mb-1">
            Peta Sebaran Destinasi Pejalan
          </h2>
          <p class="map-subheading text-grey-darken-1 mb-0">
            Jelajahi sebaran rute pejalan kaki, titik kumpul komunitas, dan cagar budaya di peta interaktif. Klik pin untuk panduan rute dan detail lengkap.
          </p>
        </div>

        <!-- Quick Status Badges -->
        <div class="d-flex align-center ga-2 flex-wrap">
          <div class="map-badge-item">
            <v-icon size="15" color="#DC2626" class="mr-1.5">mdi-map-marker-multiple</v-icon>
            <span class="font-weight-bold">{{ displayCount }} Titik Terpetakan</span>
          </div>
          <div v-if="selectedActivationName" class="map-badge-item active-city-badge">
            <v-icon size="15" color="#2563EB" class="mr-1.5">mdi-city-variant-outline</v-icon>
            <span class="font-weight-bold">{{ selectedActivationName }}</span>
          </div>
        </div>
      </div>

      <!-- Grand Map Frame -->
      <div class="grand-map-card">
        <div class="grand-map-canvas">
          <ClientOnly>
            <DestinationMapViewer
              :destinations="destinations"
              :map-center="mapCenter"
            />
            <template #fallback>
              <div class="map-fallback-box d-flex flex-column align-center justify-center">
                <v-progress-circular indeterminate color="#DC2626" size="42" width="3"></v-progress-circular>
                <span class="mt-3 text-grey-darken-2 font-weight-bold text-body-2">
                  Menyiapkan peta interaktif Jalan Bareng...
                </span>
              </div>
            </template>
          </ClientOnly>
        </div>

        <!-- Footer Map Quick Tip -->
        <div class="grand-map-footer px-4 py-2.5 d-flex align-center justify-space-between flex-wrap ga-2">
          <div class="d-flex align-center text-caption text-grey-darken-1">
            <v-icon size="14" color="#DC2626" class="mr-1.5">mdi-gesture-tap</v-icon>
            <span>Klik pin mana saja untuk melihat nama, kategori, foto, dan tombol panduan.</span>
          </div>
          <div class="d-none d-sm-flex align-center text-caption text-grey-darken-2 font-weight-medium">
            <v-icon size="14" color="#10B981" class="mr-1">mdi-walk</v-icon>
            <span>Dukungan rute jalan kaki kota</span>
          </div>
        </div>
      </div>
    </v-container>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import DestinationMapViewer from './DestinationMapViewer.vue'

const props = defineProps<{
  destinations: any[]
  mapCenter: { lat: number; lng: number } | null
  selectedActivationName?: string | null
}>()

const displayCount = computed(() => {
  return props.destinations ? props.destinations.length : 0
})
</script>

<style scoped>
.large-destinations-map-section {
  background: #FAFAF9;
  border-bottom: 1px solid #E2E8F0;
}

.map-eyebrow {
  padding: 4px 12px;
  background: #FEF2F2;
  border: 1px solid #FEE2E2;
  border-radius: 9999px;
  color: #DC2626;
  font-size: 0.76rem;
  font-weight: 800;
  letter-spacing: 0.04em;
}

.map-heading {
  font-size: clamp(1.35rem, 2.2vw, 1.85rem);
  letter-spacing: -0.025em;
  line-height: 1.25;
}

.map-subheading {
  font-size: 0.92rem;
  max-width: 640px;
  line-height: 1.5;
}

.map-badge-item {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 9999px;
  font-size: 0.8rem;
  color: #334155;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.active-city-badge {
  background: #EFF6FF;
  border-color: #BFDBFE;
  color: #1D4ED8;
}

.grand-map-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 12px 32px -8px rgba(0, 0, 0, 0.07);
}

.grand-map-canvas {
  width: 100%;
  height: clamp(380px, 52vh, 520px);
  position: relative;
  background: #E2E8F0;
}

.map-fallback-box {
  width: 100%;
  height: 100%;
  background: #F8FAFC;
}

.grand-map-footer {
  background: #FFFFFF;
  border-top: 1px solid #F1F5F9;
}

@media (max-width: 599.98px) {
  .grand-map-canvas {
    height: 350px;
  }
}
</style>
