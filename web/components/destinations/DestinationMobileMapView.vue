<template>
  <div class="mobile-map-root">
    <!-- Full-Screen Map -->
    <div class="mobile-map-canvas">
      <ClientOnly>
        <DestinationMapViewer
          ref="mapViewerRef"
          :destinations="destinations"
          :map-center="userMapCenter || mapCenter"
        />
      </ClientOnly>
    </div>

    <!-- Top Bar: Filter chips + search -->
    <div class="mobile-map-topbar">
      <!-- Search pill -->
      <div class="topbar-search-wrap">
        <v-text-field
          v-model="search"
          placeholder="Cari destinasi..."
          prepend-inner-icon="mdi-magnify"
          variant="solo"
          rounded="pill"
          density="compact"
          hide-details
          clearable
          flat
          class="topbar-search"
          bg-color="white"
          @update:model-value="$emit('search', search ?? '')"
        />
      </div>

      <!-- Category filter chips (horizontal scroll) -->
      <div class="topbar-chips-row">
        <button
          type="button"
          class="map-chip"
          :class="{ active: selectedCategory === null }"
          @click="$emit('filter-category', null)"
        >
          <v-icon size="13" class="mr-1">mdi-view-grid-outline</v-icon>
          Semua
        </button>
        <button
          v-for="cat in categories"
          :key="cat.id"
          type="button"
          class="map-chip"
          :class="{ active: selectedCategory === cat.id }"
          @click="$emit('filter-category', cat.id)"
        >
          <v-icon size="13" class="mr-1">{{ cat.icon || 'mdi-map-marker' }}</v-icon>
          {{ cat.name }}
        </button>
      </div>
    </div>

    <!-- GPS Button (bottom-left, above directory button) -->
    <div class="mobile-map-gps-btn-wrap">
      <button
        type="button"
        class="map-fab-btn gps-btn"
        :class="{ active: isTrackingGps, error: !!gpsError }"
        :aria-label="isTrackingGps ? 'Matikan GPS' : 'Aktifkan lokasi saya'"
        @click="toggleGps"
      >
        <v-progress-circular
          v-if="gpsLoading"
          indeterminate
          size="20"
          width="2"
          color="white"
        />
        <v-icon v-else size="22" color="white">
          {{ gpsError ? 'mdi-map-marker-alert' : (isTrackingGps ? 'mdi-crosshairs-gps' : 'mdi-crosshairs') }}
        </v-icon>
      </button>

      <!-- GPS error tooltip -->
      <div v-if="gpsError" class="gps-error-pill">
        <v-icon size="12" color="#DC2626" class="mr-1">mdi-alert-circle-outline</v-icon>
        <span>{{ gpsError }}</span>
      </div>
    </div>

    <!-- GPS User Dot Marker (rendered via overlay, actual dot added to map in script) -->

    <!-- Bottom Action Bar -->
    <div class="mobile-map-bottom-bar">
      <!-- Direktori Button -->
      <button
        type="button"
        class="dir-trigger-btn"
        aria-label="Buka direktori destinasi dan landmark"
        @click="showDirectory = true"
      >
        <div class="d-flex align-center ga-2">
          <v-icon size="18" color="#DC2626">mdi-map-marker-multiple-outline</v-icon>
          <div class="text-left">
            <div class="dir-btn-label">Direktori</div>
            <div class="dir-btn-sub">{{ destinations.length }} destinasi · {{ landmarkCount }} landmark</div>
          </div>
        </div>
        <div class="d-flex align-center ga-2">
          <v-chip size="x-small" color="#DC2626" variant="flat" class="text-white font-weight-bold">
            {{ destinations.length + landmarkCount }}
          </v-chip>
          <v-icon size="18" color="grey">mdi-chevron-up</v-icon>
        </div>
      </button>

      <!-- Tambah Destinasi FAB -->
      <button
        type="button"
        class="add-dest-btn map-fab-btn"
        aria-label="Tambah destinasi baru"
        @click="$emit('add-destination')"
      >
        <v-icon size="22" color="white">mdi-plus</v-icon>
      </button>
    </div>

    <!-- Directory Bottom Sheet -->
    <DestinationDirectorySheet
      v-model="showDirectory"
      :destinations="destinations"
      :categories="categories"
      :selected-category="selectedCategory"
      :loading="loading"
      @select-landmark="onSelectLandmark"
      @filter-category="(id) => $emit('filter-category', id)"
      @search="(q) => { search = q; $emit('search', q) }"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { defineAsyncComponent } from 'vue'
import { useGeolocation } from '~/composables/useGeolocation'
import { LANDMARKS_DATA } from '~/utils/landmarks'

const DestinationMapViewer = defineAsyncComponent(() =>
  import('~/components/destinations/DestinationMapViewer.vue')
)
const DestinationDirectorySheet = defineAsyncComponent(() =>
  import('~/components/destinations/DestinationDirectorySheet.vue')
)

const props = defineProps<{
  destinations: any[]
  categories: any[]
  selectedCategory: number | null
  mapCenter: { lat: number; lng: number } | null
  loading?: boolean
}>()

const emit = defineEmits<{
  (e: 'search', q: string): void
  (e: 'filter-category', id: number | null): void
  (e: 'select-landmark', name: string): void
  (e: 'add-destination'): void
}>()

const mapViewerRef = ref<any>(null)
const showDirectory = ref(false)
const search = ref('')
const isTrackingGps = ref(false)

const landmarkCount = computed(() => LANDMARKS_DATA.length)

// Geolocation
const { position: gpsPosition, error: gpsError, loading: gpsLoading, start: startGps, stop: stopGps } = useGeolocation()

// When GPS position updates, fly the map to user location
const userMapCenter = ref<{ lat: number; lng: number } | null>(null)

watch(gpsPosition, (pos) => {
  if (pos) {
    userMapCenter.value = { lat: pos.lat, lng: pos.lng }
  }
})

const toggleGps = async () => {
  if (isTrackingGps.value) {
    stopGps()
    isTrackingGps.value = false
    userMapCenter.value = null
    return
  }
  isTrackingGps.value = true
  startGps()
}

const onSelectLandmark = (name: string) => {
  showDirectory.value = false
  emit('select-landmark', name)
}

// Lock body scroll when component mounts (full-screen map)
onMounted(() => {
  if (typeof document !== 'undefined') {
    document.body.style.overflow = 'hidden'
  }
})

onUnmounted(() => {
  if (typeof document !== 'undefined') {
    document.body.style.overflow = ''
  }
  stopGps()
})
</script>

<style scoped>
/* Root — full screen fixed */
.mobile-map-root {
  position: fixed;
  inset: 0;
  z-index: 90;
  background: #E2E8F0;
}

/* Map canvas fills everything */
.mobile-map-canvas {
  position: absolute;
  inset: 0;
  z-index: 1;
}

.mobile-map-canvas :deep(.maplibre-viewer-wrapper),
.mobile-map-canvas :deep(.maplibre-container) {
  width: 100% !important;
  height: 100% !important;
}

/* ── Top bar ── */
.mobile-map-topbar {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  z-index: 10;
  padding: 12px 12px 8px;
  background: linear-gradient(to bottom, rgba(255,255,255,0.96) 60%, transparent);
  pointer-events: none;
}

.topbar-search-wrap {
  pointer-events: all;
  margin-bottom: 8px;
}

.topbar-search :deep(.v-field) {
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.topbar-chips-row {
  display: flex;
  gap: 6px;
  overflow-x: auto;
  scrollbar-width: none;
  -webkit-overflow-scrolling: touch;
  pointer-events: all;
  padding-bottom: 4px;
}

.topbar-chips-row::-webkit-scrollbar {
  display: none;
}

.map-chip {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 9999px;
  border: 1.5px solid rgba(255,255,255,0.9);
  background: rgba(255,255,255,0.92);
  backdrop-filter: blur(8px);
  color: #374151;
  font-size: 0.72rem;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  box-shadow: 0 1px 4px rgba(0,0,0,0.1);
  transition: all 0.18s ease;
}

.map-chip.active {
  background: #DC2626;
  border-color: #DC2626;
  color: #FFFFFF;
  box-shadow: 0 2px 8px rgba(220,38,38,0.35);
}

/* ── GPS button ── */
.mobile-map-gps-btn-wrap {
  position: absolute;
  left: 14px;
  bottom: 100px;
  z-index: 10;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 6px;
}

.map-fab-btn {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(0,0,0,0.2);
  transition: all 0.22s ease;
}

.gps-btn {
  background: #374151;
}

.gps-btn.active {
  background: #DC2626;
  box-shadow: 0 4px 14px rgba(220,38,38,0.4);
}

.gps-btn.error {
  background: #EF4444;
}

.gps-error-pill {
  display: flex;
  align-items: center;
  padding: 5px 10px;
  background: #FFFFFF;
  border: 1px solid #FEE2E2;
  border-radius: 9999px;
  font-size: 0.68rem;
  color: #DC2626;
  font-weight: 600;
  white-space: nowrap;
  max-width: 200px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* ── Bottom bar ── */
.mobile-map-bottom-bar {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 10;
  padding: 12px 14px 20px;
  background: linear-gradient(to top, rgba(255,255,255,0.97) 60%, transparent);
  display: flex;
  align-items: center;
  gap: 10px;
}

.dir-trigger-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  background: #FFFFFF;
  border: 1.5px solid #F1F5F9;
  border-radius: 14px;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  transition: all 0.2s ease;
  text-align: left;
}

.dir-trigger-btn:active {
  transform: scale(0.98);
  background: #FEF2F2;
}

.dir-btn-label {
  font-size: 0.82rem;
  font-weight: 700;
  color: #111827;
  line-height: 1;
}

.dir-btn-sub {
  font-size: 0.68rem;
  color: #6B7280;
  margin-top: 2px;
  line-height: 1;
}

.add-dest-btn {
  background: #DC2626;
  flex-shrink: 0;
  box-shadow: 0 4px 14px rgba(220,38,38,0.4);
}

.add-dest-btn:active {
  transform: scale(0.95);
}

@media (prefers-reduced-motion: reduce) {
  .map-chip,
  .map-fab-btn,
  .dir-trigger-btn {
    transition: none;
  }
}
</style>
