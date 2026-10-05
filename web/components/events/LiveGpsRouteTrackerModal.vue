<template>
  <v-dialog
    :model-value="modelValue"
    fullscreen
    transition="dialog-bottom-transition"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <div class="gps-tracker-screen">
      <!-- ── Top Status Bar ── -->
      <div class="tracker-topbar d-flex align-center justify-space-between px-4 py-3">
        <div class="d-flex align-center ga-3">
          <!-- Live REC Indicator -->
          <div v-if="isTracking" class="rec-badge" :class="{ 'is-paused': isPaused }">
            <span class="rec-dot"></span>
            <span>{{ isPaused ? 'JEDA' : 'MEREKAM GPS' }}</span>
          </div>
          <div v-else class="standby-badge">
            <span>SIAP MEREKAM</span>
          </div>

          <!-- GPS Accuracy Status -->
          <v-chip
            size="x-small"
            :color="accuracyStatus.color"
            variant="tonal"
            rounded="pill"
            class="font-weight-bold d-none d-sm-flex"
          >
            <v-icon start size="12">mdi-crosshairs-gps</v-icon>
            {{ accuracyStatus.label }}
          </v-chip>
        </div>

        <div class="d-flex align-center ga-2">
          <v-btn
            icon
            variant="text"
            size="small"
            color="grey-darken-3"
            aria-label="Tutup Tracker"
            @click="handleClose"
          >
            <v-icon size="24">mdi-close</v-icon>
          </v-btn>
        </div>
      </div>

      <!-- ── HUD Metrics Board (Strava Style) ── -->
      <div class="tracker-hud-panel px-4 py-3">
        <v-row dense class="text-center">
          <!-- Jarak -->
          <v-col cols="4">
            <div class="hud-metric">
              <span class="hud-label">JARAK</span>
              <div class="hud-value text-primary-red">
                {{ totalDistanceKm.toFixed(2) }}
                <span class="hud-unit">km</span>
              </div>
            </div>
          </v-col>

          <!-- Durasi -->
          <v-col cols="4">
            <div class="hud-metric">
              <span class="hud-label">DURASI</span>
              <div class="hud-value text-grey-darken-4 font-mono">
                {{ formattedDuration }}
              </div>
            </div>
          </v-col>

          <!-- Kecepatan Rata-rata -->
          <v-col cols="4">
            <div class="hud-metric">
              <span class="hud-label">RATA-RATA</span>
              <div class="hud-value text-grey-darken-4">
                {{ averageSpeedKmh.toFixed(1) }}
                <span class="hud-unit">km/j</span>
              </div>
            </div>
          </v-col>
        </v-row>
      </div>

      <!-- ── Live Map Canvas ── -->
      <div class="tracker-map-wrapper">
        <div ref="mapContainer" class="tracker-map-canvas"></div>

        <!-- Floating Recenter Button -->
        <button
          type="button"
          class="map-recenter-btn"
          @click="centerOnUser"
          title="Pusatkan ke lokasi saya"
          aria-label="Pusatkan Lokasi"
        >
          <v-icon size="20" color="#DC2626">mdi-crosshairs-gps</v-icon>
        </button>

        <!-- Points & Checkpoint Pill -->
        <div class="floating-stats-pill" v-if="isTracking">
          <span>{{ recordedPoints.length }} Titik</span>
          <span>·</span>
          <span>{{ checkpoints.length }} Checkpoint</span>
        </div>

        <!-- Error Alert -->
        <v-alert
          v-if="gpsError"
          type="error"
          variant="flat"
          density="compact"
          class="gps-error-banner"
          closable
        >
          {{ gpsError }}
        </v-alert>
      </div>

      <!-- ── Bottom Action Control Bar ── -->
      <div class="tracker-bottom-bar px-4 py-3">
        <!-- Belum Mulai -->
        <div v-if="!isTracking" class="d-flex ga-3">
          <v-btn
            color="#DC2626"
            size="large"
            rounded="pill"
            block
            class="font-weight-black text-white elevation-2"
            @click="startRecording"
          >
            <v-icon start size="22">mdi-play-circle-outline</v-icon>
            Mulai Rekam Rute GPS
          </v-btn>
        </div>

        <!-- Sedang Tracking -->
        <div v-else class="d-flex align-center justify-space-between ga-2">
          <!-- Tombol Tambah Checkpoint -->
          <v-btn
            color="#0284C7"
            variant="flat"
            rounded="pill"
            size="default"
            class="font-weight-bold text-white flex-grow-1"
            @click="openCheckpointDialog"
          >
            <v-icon start size="18">mdi-map-marker-plus</v-icon>
            <span class="d-none d-sm-inline">Tandai </span>Checkpoint
          </v-btn>

          <!-- Tombol Jeda / Lanjut -->
          <v-btn
            :color="isPaused ? 'success' : 'warning'"
            variant="flat"
            rounded="pill"
            size="default"
            class="font-weight-bold flex-grow-1"
            @click="isPaused ? resumeTracking() : pauseTracking()"
          >
            <v-icon start size="18">{{ isPaused ? 'mdi-play' : 'mdi-pause' }}</v-icon>
            {{ isPaused ? 'Lanjut' : 'Jeda' }}
          </v-btn>

          <!-- Tombol Selesai & Simpan -->
          <v-btn
            color="#DC2626"
            variant="flat"
            rounded="pill"
            size="default"
            class="font-weight-black text-white flex-grow-1"
            @click="finishAndSave"
          >
            <v-icon start size="18">mdi-flag-checkered</v-icon>
            Selesai
          </v-btn>
        </div>
      </div>

      <!-- ── Dialog Input Nama Checkpoint ── -->
      <v-dialog v-model="checkpointDialog" max-width="380" rounded="xl">
        <v-card class="pa-5" rounded="20">
          <h4 class="text-subtitle-1 font-weight-black text-grey-darken-4 mb-2">
            Tandai Checkpoint Baru
          </h4>
          <p class="text-caption text-grey-darken-1 mb-4">
            Beri nama titik singgah atau spot menarik di posisi GPS Anda sekarang.
          </p>
          <v-text-field
            v-model="newCheckpointName"
            placeholder="Contoh: Titik Kumpul Kopi / Spot Foto Gedung"
            variant="outlined"
            density="comfortable"
            rounded="lg"
            autofocus
            class="mb-3"
            hide-details
          />
          <v-textarea
            v-model="newCheckpointDesc"
            placeholder="Catatan / deskripsi tempat (opsional)"
            variant="outlined"
            density="comfortable"
            rounded="lg"
            rows="2"
            hide-details
            class="mb-4"
          />
          <div class="d-flex justify-end ga-2">
            <v-btn variant="text" rounded="pill" @click="checkpointDialog = false">Batal</v-btn>
            <v-btn color="#DC2626" rounded="pill" class="text-white font-weight-bold" @click="saveCheckpoint">
              Simpan Titik
            </v-btn>
          </div>
        </v-card>
      </v-dialog>
    </div>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { useMapLibre, MAP_STYLES } from '~/composables/useMapLibre'
import { useGpsTracker } from '~/composables/useGpsTracker'

const props = defineProps<{
  modelValue: boolean
  initialTitle?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
  (e: 'saved', payload: { route: any[]; distance: number; duration: number }): void
}>()

const { loadMapLibre } = useMapLibre()
const {
  isTracking,
  isPaused,
  recordedPoints,
  checkpoints,
  currentPosition,
  totalDistanceKm,
  formattedDuration,
  elapsedSeconds,
  averageSpeedKmh,
  accuracyStatus,
  gpsError,
  startTracking,
  pauseTracking,
  resumeTracking,
  stopTracking,
  resetAll,
  addCheckpointAtCurrentPosition,
  restoreFromStorage
} = useGpsTracker()

const mapContainer = ref<HTMLElement | null>(null)
let maplibregl: any = null
let mapInstance: any = null
let userMarker: any = null
let checkpointMarkers: any[] = []

const checkpointDialog = ref(false)
const newCheckpointName = ref('')
const newCheckpointDesc = ref('')

const initMap = async () => {
  if (!mapContainer.value) return

  try {
    maplibregl = await loadMapLibre()
    await nextTick()

    const defaultCenter = currentPosition.value
      ? [currentPosition.value.lng, currentPosition.value.lat]
      : [119.4327, -5.1477]

    mapInstance = new maplibregl.Map({
      container: mapContainer.value,
      style: MAP_STYLES.positron.url,
      center: defaultCenter,
      zoom: 16,
      attributionControl: false
    })

    mapInstance.on('load', () => {
      // Source & Layer untuk live breadcrumb polyline
      mapInstance.addSource('live-route', {
        type: 'geojson',
        data: {
          type: 'Feature',
          properties: {},
          geometry: { type: 'LineString', coordinates: [] }
        }
      })

      // Halo casing
      mapInstance.addLayer({
        id: 'live-route-halo',
        type: 'line',
        source: 'live-route',
        layout: { 'line-join': 'round', 'line-cap': 'round' },
        paint: { 'line-color': '#FFFFFF', 'line-width': 8, 'line-opacity': 0.85 }
      })

      // Main line
      mapInstance.addLayer({
        id: 'live-route-line',
        type: 'line',
        source: 'live-route',
        layout: { 'line-join': 'round', 'line-cap': 'round' },
        paint: { 'line-color': '#DC2626', 'line-width': 5 }
      })

      updateLivePolyline()
    })
  } catch (err) {
    console.error('Failed to init live tracker map:', err)
  }
}

// Update posisi user dan polyline pada peta
const updateLivePolyline = () => {
  if (!mapInstance || !maplibregl) return

  const coords = recordedPoints.value.map(p => [p.lng, p.lat])
  const source = mapInstance.getSource('live-route')
  if (source) {
    source.setData({
      type: 'Feature',
      properties: {},
      geometry: { type: 'LineString', coordinates: coords }
    })
  }

  // Update current user pulsing marker
  if (currentPosition.value) {
    const { lng, lat } = currentPosition.value
    if (!userMarker) {
      const el = document.createElement('div')
      el.className = 'live-user-dot'
      el.innerHTML = '<span class="pulse-ring"></span><span class="center-dot"></span>'
      userMarker = new maplibregl.Marker({ element: el })
        .setLngLat([lng, lat])
        .addTo(mapInstance)
    } else {
      userMarker.setLngLat([lng, lat])
    }
  }
}

const centerOnUser = () => {
  if (!mapInstance || !currentPosition.value) return
  mapInstance.flyTo({
    center: [currentPosition.value.lng, currentPosition.value.lat],
    zoom: 17,
    duration: 800
  })
}

const startRecording = () => {
  const started = startTracking()
  if (started && currentPosition.value) {
    centerOnUser()
  }
}

const openCheckpointDialog = () => {
  newCheckpointName.value = `Checkpoint ${checkpoints.value.length + 1}`
  newCheckpointDesc.value = ''
  checkpointDialog.value = true
}

const saveCheckpoint = () => {
  const cp = addCheckpointAtCurrentPosition(newCheckpointName.value, newCheckpointDesc.value)
  if (cp && mapInstance && maplibregl) {
    const el = document.createElement('div')
    el.className = 'checkpoint-pin'
    el.innerHTML = `<span>${checkpoints.value.length}</span>`

    const marker = new maplibregl.Marker({ element: el })
      .setLngLat([cp.lng, cp.lat])
      .addTo(mapInstance)
    checkpointMarkers.push(marker)
  }
  checkpointDialog.value = false
}

const finishAndSave = () => {
  if (recordedPoints.value.length < 2) {
    alert('Rute terlalu pendek atau belum merekam pergerakan. Silakan berjalan beberapa meter sebelum menyelesaikan.')
    return
  }

  const durationMinutes = Math.max(1, Math.round(elapsedSeconds.value / 60))
  const finalDistance = Math.round(totalDistanceKm.value * 100) / 100

  // Siapkan data route terstruktur
  const cleanRoute = recordedPoints.value.map((p, idx) => ({
    lat: p.lat,
    lng: p.lng,
    name: p.name || (idx === 0 ? 'Start' : idx === recordedPoints.value.length - 1 ? 'Finish' : undefined),
    description: p.description
  }))

  emit('saved', {
    route: cleanRoute,
    distance: finalDistance,
    duration: durationMinutes
  })

  stopTracking()
  resetAll()
  emit('update:modelValue', false)
}

const handleClose = () => {
  if (isTracking.value) {
    if (confirm('Pelacakan rute sedang berlangsung. Hentikan dan batalkan perekaman?')) {
      stopTracking()
      resetAll()
      emit('update:modelValue', false)
    }
  } else {
    emit('update:modelValue', false)
  }
}

// Watchers
watch(currentPosition, () => {
  updateLivePolyline()
})

watch(() => props.modelValue, (isOpen) => {
  if (isOpen) {
    nextTick(() => {
      initMap()
      restoreFromStorage()
    })
  } else {
    checkpointMarkers.forEach(m => m.remove())
    if (userMarker) userMarker.remove()
    if (mapInstance) {
      mapInstance.remove()
      mapInstance = null
    }
  }
})

onUnmounted(() => {
  stopTracking()
})
</script>

<style scoped>
.gps-tracker-screen {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background: #FFFFFF;
  position: relative;
  overflow: hidden;
}

.tracker-topbar {
  background: #FFFFFF;
  border-bottom: 1px solid #E5E7EB;
  z-index: 10;
}

.rec-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #FEF2F2;
  color: #DC2626;
  font-weight: 900;
  font-size: 0.75rem;
  padding: 4px 10px;
  border-radius: 9999px;
  border: 1px solid rgba(220, 38, 38, 0.2);
}

.rec-badge.is-paused {
  background: #FFFBEB;
  color: #D97706;
  border-color: rgba(217, 119, 6, 0.2);
}

.rec-dot {
  width: 8px;
  height: 8px;
  background-color: #DC2626;
  border-radius: 50%;
  animation: blink 1.2s infinite;
}

.is-paused .rec-dot {
  background-color: #D97706;
  animation: none;
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.2; }
}

.standby-badge {
  font-size: 0.75rem;
  font-weight: 800;
  color: #6B7280;
  background: #F3F4F6;
  padding: 4px 10px;
  border-radius: 9999px;
}

.tracker-hud-panel {
  background: #FFFFFF;
  border-bottom: 1px solid #F3F4F6;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
  z-index: 9;
}

.hud-metric {
  display: flex;
  flex-direction: column;
}

.hud-label {
  font-size: 0.65rem;
  font-weight: 800;
  letter-spacing: 0.05em;
  color: #6B7280;
}

.hud-value {
  font-size: 1.55rem;
  font-weight: 900;
  line-height: 1.1;
}

.hud-unit {
  font-size: 0.75rem;
  font-weight: 700;
  color: #6B7280;
}

.font-mono {
  font-family: monospace;
}

.tracker-map-wrapper {
  position: relative;
  flex: 1;
  width: 100%;
}

.tracker-map-canvas {
  width: 100%;
  height: 100%;
}

.map-recenter-btn {
  position: absolute;
  bottom: 20px;
  right: 16px;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #FFFFFF;
  border: 1px solid rgba(0, 0, 0, 0.1);
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 5;
}

.floating-stats-pill {
  position: absolute;
  top: 14px;
  left: 14px;
  background: rgba(17, 24, 39, 0.88);
  color: #FFFFFF;
  font-size: 0.72rem;
  font-weight: 800;
  padding: 5px 12px;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  gap: 6px;
  z-index: 5;
}

.gps-error-banner {
  position: absolute;
  top: 14px;
  left: 14px;
  right: 14px;
  z-index: 6;
}

.tracker-bottom-bar {
  background: #FFFFFF;
  border-top: 1px solid #E5E7EB;
  z-index: 10;
}

/* User Pulsing Dot */
:deep(.live-user-dot) {
  position: relative;
  width: 22px;
  height: 22px;
}

:deep(.center-dot) {
  position: absolute;
  top: 5px;
  left: 5px;
  width: 12px;
  height: 12px;
  background: #0284C7;
  border: 2px solid #FFFFFF;
  border-radius: 50%;
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

:deep(.pulse-ring) {
  position: absolute;
  inset: 0;
  background: rgba(2, 132, 199, 0.35);
  border-radius: 50%;
  animation: pulse-user 1.8s infinite;
}

@keyframes pulse-user {
  0% { transform: scale(0.6); opacity: 1; }
  100% { transform: scale(2.2); opacity: 0; }
}

:deep(.checkpoint-pin) {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #0284C7;
  border: 2px solid #FFFFFF;
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 900;
  box-shadow: 0 2px 8px rgba(0,0,0,0.25);
}
</style>
