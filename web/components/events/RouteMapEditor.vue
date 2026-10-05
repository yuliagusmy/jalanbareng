<template>
  <div class="route-map-editor">
    <div class="editor-frame-wrapper">
      <div ref="mapContainer" class="editor-canvas"></div>

      <!-- Instructions Pill -->
      <div class="editor-tip-pill" v-if="mapReady">
        <v-icon size="16" color="#DC2626">mdi-cursor-default-click</v-icon>
        <span>Klik pada peta untuk menambah titik / belokan rute</span>
      </div>

      <!-- Floating Controls -->
      <div class="editor-floating-controls" v-if="mapReady">
        <button
          type="button"
          class="map-ctrl-btn"
          @click="toggleMapStyle"
          :title="`Ganti gaya peta (${currentStyleName})`"
          aria-label="Ganti Gaya Peta"
        >
          <v-icon size="16">{{ currentStyleIcon }}</v-icon>
          <span class="ctrl-label">{{ currentStyleKey === 'positron' ? 'Minimal' : 'Detail' }}</span>
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="map-loading-overlay">
        <v-progress-circular indeterminate color="#DC2626" size="36" width="3" />
        <span class="text-caption font-weight-bold text-grey-darken-2 mt-2">
          Menyiapkan kanvas peta...
        </span>
      </div>
    </div>

    <!-- Actions & Stats Bar -->
    <div class="mt-4">
      <v-row align="center" justify="space-between">
        <v-col cols="12" sm="7">
          <div class="d-flex flex-wrap ga-2">
            <v-btn
              @click="showGpsModal = true"
              color="#DC2626"
              variant="flat"
              size="small"
              rounded="pill"
              class="font-weight-bold text-white elevation-1"
            >
              <v-icon start size="16">mdi-cellphone-marker</v-icon>
              Rekam GPS HP (Live)
            </v-btn>
            <v-btn
              @click="undoLastPoint"
              color="grey-darken-3"
              variant="outlined"
              size="small"
              rounded="pill"
              class="font-weight-bold"
              :disabled="routePoints.length === 0"
            >
              <v-icon start size="16">mdi-undo</v-icon>
              Undo
            </v-btn>
            <v-btn
              @click="clearAllPoints"
              color="grey-darken-1"
              variant="outlined"
              size="small"
              rounded="pill"
              :disabled="routePoints.length === 0"
            >
              <v-icon start size="16">mdi-delete-sweep-outline</v-icon>
              Reset
            </v-btn>
          </div>
        </v-col>

        <v-col cols="12" sm="5" class="text-sm-right">
          <div class="d-inline-flex align-center ga-3 bg-white px-4 py-2 rounded-pill border">
            <div class="text-left">
              <div class="text-caption text-grey">Estimasi Jarak</div>
              <div class="text-subtitle-2 font-weight-black text-primary-red">
                {{ calculatedDistance.toFixed(2) }} km
              </div>
            </div>
            <v-divider vertical class="my-1" />
            <div class="text-left">
              <div class="text-caption text-grey">Jumlah Titik</div>
              <div class="text-subtitle-2 font-weight-black text-grey-darken-4">
                {{ routePoints.length }}
              </div>
            </div>
          </div>
        </v-col>
      </v-row>

      <!-- Waypoints List Preview -->
      <v-card v-if="routePoints.length > 0" class="mt-4 pa-4" elevation="0" rounded="xl" border>
        <div class="d-flex align-center justify-space-between mb-2">
          <span class="text-caption font-weight-bold text-grey-darken-3">
            Titik-Titik Checkpoint Terdata:
          </span>
          <span class="text-caption text-grey">
            Start (Hijau) ➔ Finish (Merah)
          </span>
        </div>
        <div class="points-chips-container">
          <v-chip
            v-for="(p, i) in routePoints"
            :key="i"
            size="small"
            rounded="pill"
            :color="i === 0 ? 'success' : (i === routePoints.length - 1 ? '#DC2626' : 'grey-darken-3')"
            :variant="i === 0 || i === routePoints.length - 1 ? 'flat' : 'outlined'"
            class="font-weight-bold"
          >
            {{ i === 0 ? '🚩 Start' : (i === routePoints.length - 1 ? '🏁 Finish' : `Titik ${i + 1}`) }}:
            {{ p.lat.toFixed(4) }}, {{ p.lng.toFixed(4) }}
          </v-chip>
        </div>
      </v-card>
    </div>

    <!-- Live GPS Route Tracker Modal (Strava Style) -->
    <LiveGpsRouteTrackerModal
      v-model="showGpsModal"
      @saved="handleGpsRouteSaved"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useMapLibre, MAP_STYLES, type MapStyleKey } from '~/composables/useMapLibre'
import LiveGpsRouteTrackerModal from '~/components/events/LiveGpsRouteTrackerModal.vue'

interface Point {
  lat: number
  lng: number
  name?: string
}

const showGpsModal = ref(false)

const handleGpsRouteSaved = (payload: { route: Point[]; distance: number; duration: number }) => {
  routePoints.value = payload.route
  redrawRoute()
  emit('update:modelValue', routePoints.value)
  emit('update:distance', payload.distance)
}

const props = defineProps({
  modelValue: {
    type: Array as () => Point[],
    default: () => []
  },
  isVisible: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'update:distance'])

const { loadMapLibre } = useMapLibre()

const mapContainer = ref<HTMLElement | null>(null)
const mapReady = ref(false)
const loading = ref(true)

let maplibregl: any = null
let mapInstance: any = null
let markers: any[] = []

const currentStyleKey = ref<MapStyleKey>('positron')
const currentStyleName = computed(() => MAP_STYLES[currentStyleKey.value].name)
const currentStyleIcon = computed(() => MAP_STYLES[currentStyleKey.value].icon)

const routePoints = ref<Point[]>(
  props.modelValue && Array.isArray(props.modelValue)
    ? props.modelValue.map(p => ({
        lat: typeof p.lat === 'number' ? p.lat : parseFloat(p.lat as any),
        lng: typeof p.lng === 'number' ? p.lng : parseFloat(p.lng as any),
        name: p.name
      }))
    : []
)

// Hitung jarak via formula Haversine
const calculatedDistance = computed(() => {
  if (routePoints.value.length < 2) return 0
  let total = 0
  for (let i = 0; i < routePoints.value.length - 1; i++) {
    total += haversineKm(routePoints.value[i], routePoints.value[i + 1])
  }
  return total
})

function haversineKm(p1: Point, p2: Point): number {
  const R = 6371
  const dLat = ((p2.lat - p1.lat) * Math.PI) / 180
  const dLng = ((p2.lng - p1.lng) * Math.PI) / 180
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((p1.lat * Math.PI) / 180) *
      Math.cos((p2.lat * Math.PI) / 180) *
      Math.sin(dLng / 2) *
      Math.sin(dLng / 2)
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
}

watch(calculatedDistance, (newDist) => {
  emit('update:distance', newDist)
})

const initMap = async () => {
  if (!mapContainer.value) return
  loading.value = true

  try {
    maplibregl = await loadMapLibre()
    await nextTick()

    // Default ke Makassar atau point pertama
    const defaultCenter = routePoints.value.length > 0
      ? [routePoints.value[0].lng, routePoints.value[0].lat]
      : [119.4327, -5.1477]

    mapInstance = new maplibregl.Map({
      container: mapContainer.value,
      style: MAP_STYLES[currentStyleKey.value].url,
      center: defaultCenter,
      zoom: 14,
      attributionControl: false
    })

    mapInstance.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'bottom-right')

    mapInstance.on('load', () => {
      mapReady.value = true
      loading.value = false
      redrawRoute()
    })

    // Tangkap klik pada peta untuk menambah titik rute
    mapInstance.on('click', (e: any) => {
      addPoint(e.lngLat.lat, e.lngLat.lng)
    })
  } catch (err) {
    console.error('Failed to init MapLibre route editor:', err)
    loading.value = false
  }
}

const addPoint = (lat: number, lng: number) => {
  routePoints.value.push({ lat, lng })
  redrawRoute()
  emit('update:modelValue', routePoints.value)
}

const undoLastPoint = () => {
  if (routePoints.value.length === 0) return
  routePoints.value.pop()
  redrawRoute()
  emit('update:modelValue', routePoints.value)
}

const clearAllPoints = () => {
  routePoints.value = []
  redrawRoute()
  emit('update:modelValue', routePoints.value)
}

const redrawRoute = () => {
  if (!mapInstance || !maplibregl) return

  // Bersihkan marker lama
  markers.forEach(m => m.remove())
  markers = []

  const pts = routePoints.value
  const coords = pts.map(p => [p.lng, p.lat])

  const geojson = {
    type: 'Feature',
    properties: {},
    geometry: {
      type: 'LineString',
      coordinates: coords
    }
  }

  // Update or create source
  if (mapInstance.getSource('editor-route')) {
    mapInstance.getSource('editor-route').setData(geojson)
  } else {
    mapInstance.addSource('editor-route', {
      type: 'geojson',
      data: geojson
    })

    mapInstance.addLayer({
      id: 'editor-route-halo',
      type: 'line',
      source: 'editor-route',
      layout: { 'line-join': 'round', 'line-cap': 'round' },
      paint: { 'line-color': '#FFFFFF', 'line-width': 7, 'line-opacity': 0.8 }
    })

    mapInstance.addLayer({
      id: 'editor-route-line',
      type: 'line',
      source: 'editor-route',
      layout: { 'line-join': 'round', 'line-cap': 'round' },
      paint: { 'line-color': '#DC2626', 'line-width': 4 }
    })
  }

  // Tambahkan visual marker untuk setiap titik
  pts.forEach((p, idx) => {
    const isStart = idx === 0
    const isFinish = idx === pts.length - 1 && pts.length > 1

    const el = document.createElement('div')
    el.className = `editor-point-marker ${isStart ? 'is-start' : isFinish ? 'is-finish' : ''}`
    el.innerHTML = `<span>${isStart ? 'S' : isFinish ? 'F' : idx + 1}</span>`

    const marker = new maplibregl.Marker({ element: el })
      .setLngLat([p.lng, p.lat])
      .addTo(mapInstance)

    markers.push(marker)
  })

  // Fit bounds jika ada lebih dari 1 titik
  if (pts.length > 1) {
    const bounds = new maplibregl.LngLatBounds()
    pts.forEach(p => bounds.extend([p.lng, p.lat]))
    mapInstance.fitBounds(bounds, { padding: 40, maxZoom: 16 })
  }
}

const toggleMapStyle = () => {
  if (!mapInstance) return
  currentStyleKey.value = currentStyleKey.value === 'positron' ? 'liberty' : 'positron'
  mapInstance.setStyle(MAP_STYLES[currentStyleKey.value].url)
  mapInstance.once('style.load', () => redrawRoute())
}

watch(() => props.isVisible, (val) => {
  if (val && mapInstance) {
    setTimeout(() => {
      mapInstance.resize()
      if (routePoints.value.length > 0) redrawRoute()
    }, 150)
  }
})

watch(() => props.modelValue, (newVal) => {
  if (newVal && Array.isArray(newVal)) {
    routePoints.value = newVal.map(p => ({
      lat: typeof p.lat === 'number' ? p.lat : parseFloat(p.lat as any),
      lng: typeof p.lng === 'number' ? p.lng : parseFloat(p.lng as any),
      name: p.name
    }))
    if (mapReady.value) redrawRoute()
  }
}, { deep: true })

onMounted(() => {
  initMap()
})

onUnmounted(() => {
  markers.forEach(m => m.remove())
  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }
})
</script>

<style scoped>
.route-map-editor {
  width: 100%;
}

.editor-frame-wrapper {
  position: relative;
  width: 100%;
  height: 440px;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
}

.editor-canvas {
  width: 100%;
  height: 100%;
}

.editor-tip-pill {
  position: absolute;
  top: 14px;
  left: 14px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 9999px;
  padding: 6px 14px;
  font-size: 0.76rem;
  font-weight: 700;
  color: #1F2937;
  display: flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  z-index: 5;
}

.editor-floating-controls {
  position: absolute;
  top: 14px;
  right: 14px;
  z-index: 5;
}

.map-ctrl-btn {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 9999px;
  padding: 6px 12px;
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.75rem;
  font-weight: 700;
  color: #374151;
  cursor: pointer;
}

.map-loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.88);
  backdrop-filter: blur(4px);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.text-primary-red {
  color: #DC2626;
}

.points-chips-container {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  max-height: 120px;
  overflow-y: auto;
}

:deep(.editor-point-marker) {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: #3B82F6;
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 900;
  border: 2px solid #FFFFFF;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

:deep(.editor-point-marker.is-start) { background: #16A34A; }
:deep(.editor-point-marker.is-finish) { background: #DC2626; }
</style>
