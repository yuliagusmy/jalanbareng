<template>
  <div class="walking-route-viewer">
    <!-- Map Container with Controls -->
    <div class="map-frame-wrapper">
      <div ref="mapContainer" class="map-canvas"></div>

      <!-- Floating Controls -->
      <div class="map-floating-controls" v-if="mapReady">
        <!-- 2D / 3D Toggle -->
        <button
          type="button"
          class="map-ctrl-btn"
          :class="{ active: is3D }"
          @click="toggle3D"
          :title="is3D ? 'Tampilan 2D' : 'Tampilan 3D Perspektif'"
          aria-label="Toggle 3D"
        >
          <v-icon size="18">{{ is3D ? 'mdi-cube-outline' : 'mdi-vector-square' }}</v-icon>
          <span class="ctrl-label">{{ is3D ? '3D' : '2D' }}</span>
        </button>

        <!-- Style Switcher -->
        <button
          type="button"
          class="map-ctrl-btn"
          @click="toggleMapStyle"
          :title="`Ganti gaya peta (${currentStyleName})`"
          aria-label="Ganti Gaya Peta"
        >
          <v-icon size="18">{{ currentStyleIcon }}</v-icon>
          <span class="ctrl-label">{{ currentStyleKey === 'positron' ? 'Minimal' : 'Detail' }}</span>
        </button>

        <!-- Fit Route -->
        <button
          type="button"
          class="map-ctrl-btn"
          @click="fitToRoute"
          title="Pusatkan seluruh rute"
          aria-label="Pusatkan Rute"
        >
          <v-icon size="18">mdi-crosshairs-gps</v-icon>
        </button>

        <!-- Fullscreen -->
        <button
          type="button"
          class="map-ctrl-btn d-none d-sm-flex"
          @click="toggleFullscreen"
          :title="isFullscreen ? 'Keluar Fullscreen' : 'Layar Penuh'"
          aria-label="Layar Penuh"
        >
          <v-icon size="18">{{ isFullscreen ? 'mdi-fullscreen-exit' : 'mdi-fullscreen' }}</v-icon>
        </button>
      </div>

      <!-- Route Status Tag -->
      <div class="route-status-pill" v-if="mapReady && waypoints.length > 0">
        <span class="pulse-dot"></span>
        <span>{{ calculatedDistance.toFixed(1) }} km · {{ waypoints.length }} Titik Rute</span>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="map-loading-overlay">
        <v-progress-circular indeterminate color="#DC2626" size="36" width="3" />
        <span class="text-caption font-weight-bold text-grey-darken-2 mt-2">
          Memuat rute MapLibre...
        </span>
      </div>

      <!-- Error State -->
      <div v-if="loadError" class="map-error-overlay">
        <v-icon size="36" color="#DC2626">mdi-map-marker-alert-outline</v-icon>
        <span class="text-caption text-grey-darken-2 text-center mt-2 px-4">{{ loadError }}</span>
        <v-btn size="small" variant="outlined" color="#DC2626" class="mt-2" @click="retryInit">
          Coba Lagi
        </v-btn>
      </div>
    </div>

    <!-- Interactive Waypoint / Checkpoint Timeline -->
    <div v-if="keyWaypoints.length > 0" class="waypoints-summary-card mt-4 pa-4 pa-sm-5">
      <div class="d-flex align-center justify-space-between mb-3">
        <div class="d-flex align-center ga-2">
          <v-icon size="18" color="#DC2626">mdi-map-marker-check-outline</v-icon>
          <h4 class="text-subtitle-2 font-weight-black text-grey-darken-4 mb-0">
            Daftar Titik Temu &amp; Checkpoint
          </h4>
        </div>
        <span class="text-caption text-grey">Klik titik untuk melihat di peta</span>
      </div>

      <div class="waypoints-chip-list">
        <div
          v-for="(wp, idx) in keyWaypoints"
          :key="idx"
          class="wp-chip-item"
          :class="{ active: activeWaypointIndex === idx, 'is-start': idx === 0, 'is-finish': idx === keyWaypoints.length - 1 }"
          @click="selectWaypoint(wp, idx)"
        >
          <div class="wp-badge">
            <v-icon v-if="idx === 0" size="14" color="white">mdi-flag-outline</v-icon>
            <v-icon v-else-if="idx === keyWaypoints.length - 1" size="14" color="white">mdi-flag-checkered</v-icon>
            <span v-else>{{ idx + 1 }}</span>
          </div>
          <div class="wp-info">
            <span class="wp-title font-weight-bold">{{ wp.name || `Titik ${idx + 1}` }}</span>
            <span class="wp-meta text-caption text-grey-darken-1">{{ wp.distanceText }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { useMapLibre, MAP_STYLES, type MapStyleKey } from '~/composables/useMapLibre'

export interface RoutePoint {
  lat: number
  lng: number
  name?: string
  description?: string
}

const props = defineProps<{
  route: RoutePoint[]
  startPoint?: { lat: number; lng: number; name?: string }
  finishPoint?: { lat: number; lng: number; name?: string }
  title?: string
  distance?: number | string
  duration?: number | string
}>()

const { loadMapLibre, add3DBuildingsLayer } = useMapLibre()

const mapContainer = ref<HTMLElement | null>(null)
const mapReady = ref(false)
const loading = ref(true)
const loadError = ref<string | null>(null)
const is3D = ref(false)
const isFullscreen = ref(false)
const activeWaypointIndex = ref<number | null>(null)

let maplibregl: any = null
let mapInstance: any = null
let markers: any[] = []
let activePopup: any = null

const currentStyleKey = ref<MapStyleKey>('positron')
const currentStyleName = computed(() => MAP_STYLES[currentStyleKey.value].name)
const currentStyleIcon = computed(() => MAP_STYLES[currentStyleKey.value].icon)

// Normalisasi koordinat rute
const waypoints = computed<RoutePoint[]>(() => {
  if (!props.route || !Array.isArray(props.route)) return []
  return props.route
    .map(p => {
      const lat = typeof p.lat === 'number' ? p.lat : parseFloat(p.lat as any)
      const lng = typeof p.lng === 'number' ? p.lng : parseFloat(p.lng as any)
      return { ...p, lat, lng }
    })
    .filter(p => !isNaN(p.lat) && !isNaN(p.lng) && p.lat !== 0 && p.lng !== 0)
})

// Hitung total jarak jalur jika tidak disediakan prop distance
const calculatedDistance = computed(() => {
  if (props.distance) return parseFloat(String(props.distance))
  if (waypoints.value.length < 2) return 0
  let totalKm = 0
  for (let i = 0; i < waypoints.value.length - 1; i++) {
    totalKm += haversineKm(waypoints.value[i], waypoints.value[i + 1])
  }
  return totalKm
})

// Waypoint utama yang diberi label/checkpoint
const keyWaypoints = computed(() => {
  const pts = waypoints.value
  if (pts.length === 0) return []

  return pts.map((p, idx) => {
    let name = p.name
    if (!name) {
      if (idx === 0) name = props.startPoint?.name || 'Titik Kumpul (Start)'
      else if (idx === pts.length - 1) name = props.finishPoint?.name || 'Titik Akhir (Finish)'
      else name = `Checkpoint ${idx + 1}`
    }

    // Hitung jarak kumulatif dari start
    let cumDist = 0
    for (let j = 0; j < idx; j++) {
      cumDist += haversineKm(pts[j], pts[j + 1])
    }
    const distanceText = idx === 0 ? 'Mulai rute' : `+${cumDist.toFixed(1)} km dari start`

    return { ...p, name, distanceText, index: idx }
  })
})

function haversineKm(p1: RoutePoint, p2: RoutePoint): number {
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

const initMap = async () => {
  if (!mapContainer.value) return
  loading.value = true
  loadError.value = null

  try {
    maplibregl = await loadMapLibre()
    await nextTick()

    // Default center ke waypoint pertama atau Makassar
    const firstPoint = waypoints.value[0] || { lng: 119.4327, lat: -5.1477 }

    mapInstance = new maplibregl.Map({
      container: mapContainer.value,
      style: MAP_STYLES[currentStyleKey.value].url,
      center: [firstPoint.lng, firstPoint.lat],
      zoom: 14,
      pitch: 0,
      bearing: 0,
      attributionControl: false
    })

    mapInstance.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'bottom-right')

    mapInstance.on('load', () => {
      mapReady.value = true
      loading.value = false
      renderRoute()
    })

    mapInstance.on('error', (err: any) => {
      console.warn('MapLibre warning/error:', err)
    })
  } catch (err: any) {
    console.error('Failed to init MapLibre:', err)
    loading.value = false
    loadError.value = 'Peta rute tidak dapat dimuat saat ini.'
  }
}

const renderRoute = () => {
  if (!mapInstance || !maplibregl || waypoints.value.length === 0) return

  clearMarkers()

  // 1. Gambar Polyline Rute via GeoJSON
  const coordinates = waypoints.value.map(p => [p.lng, p.lat])
  const routeGeoJSON = {
    type: 'Feature',
    properties: {},
    geometry: {
      type: 'LineString',
      coordinates
    }
  }

  // Casing / Glow Outer Line
  if (mapInstance.getSource('walking-route')) {
    mapInstance.getSource('walking-route').setData(routeGeoJSON)
  } else {
    mapInstance.addSource('walking-route', {
      type: 'geojson',
      data: routeGeoJSON
    })

    // Halo line putih/merah muda
    mapInstance.addLayer({
      id: 'walking-route-casing',
      type: 'line',
      source: 'walking-route',
      layout: {
        'line-join': 'round',
        'line-cap': 'round'
      },
      paint: {
        'line-color': '#FFFFFF',
        'line-width': 8,
        'line-opacity': 0.8
      }
    })

    // Main line merah primary Jalan Bareng
    mapInstance.addLayer({
      id: 'walking-route-line',
      type: 'line',
      source: 'walking-route',
      layout: {
        'line-join': 'round',
        'line-cap': 'round'
      },
      paint: {
        'line-color': '#DC2626',
        'line-width': 5,
        'line-opacity': 0.95
      }
    })
  }

  // 2. Tambahkan Marker Checkpoint / Waypoints
  keyWaypoints.value.forEach((wp, idx) => {
    const isStart = idx === 0
    const isFinish = idx === keyWaypoints.value.length - 1

    const el = document.createElement('div')
    el.className = `custom-route-marker ${isStart ? 'is-start' : isFinish ? 'is-finish' : 'is-checkpoint'}`

    if (isStart) {
      el.innerHTML = '<span class="marker-icon"><i class="mdi mdi-flag-variant"></i></span>'
    } else if (isFinish) {
      el.innerHTML = '<span class="marker-icon"><i class="mdi mdi-flag-checkered"></i></span>'
    } else {
      el.innerHTML = `<span class="marker-num">${idx + 1}</span>`
    }

    const popupContent = `
      <div class="map-route-popup">
        <div class="popup-badge ${isStart ? 'badge-start' : isFinish ? 'badge-finish' : 'badge-cp'}">
          ${isStart ? 'TITIK START' : isFinish ? 'TITIK FINISH' : `CHECKPOINT ${idx + 1}`}
        </div>
        <h4 class="popup-title">${wp.name}</h4>
        <p class="popup-dist">${wp.distanceText}</p>
        ${wp.description ? `<p class="popup-desc">${wp.description}</p>` : ''}
      </div>
    `

    const popup = new maplibregl.Popup({ offset: 20, closeButton: false }).setHTML(popupContent)

    const marker = new maplibregl.Marker({ element: el })
      .setLngLat([wp.lng, wp.lat])
      .setPopup(popup)
      .addTo(mapInstance)

    el.addEventListener('click', () => {
      activeWaypointIndex.value = idx
    })

    markers.push(marker)
  })

  fitToRoute()
}

const clearMarkers = () => {
  markers.forEach(m => m.remove())
  markers = []
}

const fitToRoute = () => {
  if (!mapInstance || !maplibregl || waypoints.value.length === 0) return
  const bounds = new maplibregl.LngLatBounds()
  waypoints.value.forEach(p => bounds.extend([p.lng, p.lat]))
  mapInstance.fitBounds(bounds, {
    padding: { top: 60, bottom: 60, left: 60, right: 60 },
    maxZoom: 16,
    duration: 1000
  })
}

const selectWaypoint = (wp: RoutePoint, idx: number) => {
  if (!mapInstance) return
  activeWaypointIndex.value = idx
  mapInstance.flyTo({
    center: [wp.lng, wp.lat],
    zoom: 16,
    duration: 800
  })
  if (markers[idx]) {
    markers[idx].togglePopup()
  }
}

const toggle3D = () => {
  if (!mapInstance) return
  is3D.value = !is3D.value
  mapInstance.easeTo({
    pitch: is3D.value ? 55 : 0,
    duration: 800
  })
  if (is3D.value) {
    add3DBuildingsLayer(mapInstance)
  }
}

const toggleMapStyle = () => {
  if (!mapInstance) return
  currentStyleKey.value = currentStyleKey.value === 'positron' ? 'liberty' : 'positron'
  mapInstance.setStyle(MAP_STYLES[currentStyleKey.value].url)
  mapInstance.once('style.load', () => {
    renderRoute()
    if (is3D.value) add3DBuildingsLayer(mapInstance)
  })
}

const toggleFullscreen = () => {
  if (!mapContainer.value) return
  if (!document.fullscreenElement) {
    mapContainer.value.parentElement?.requestFullscreen()
    isFullscreen.value = true
  } else {
    document.exitFullscreen()
    isFullscreen.value = false
  }
}

const retryInit = () => {
  initMap()
}

watch(() => props.route, () => {
  if (mapReady.value) renderRoute()
}, { deep: true })

onMounted(() => {
  initMap()
})

onUnmounted(() => {
  clearMarkers()
  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }
})
</script>

<style scoped>
.walking-route-viewer {
  width: 100%;
}

.map-frame-wrapper {
  position: relative;
  width: 100%;
  height: 480px;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(0, 0, 0, 0.08);
}

.map-canvas {
  width: 100%;
  height: 100%;
}

.map-floating-controls {
  position: absolute;
  top: 14px;
  right: 14px;
  display: flex;
  flex-direction: column;
  gap: 8px;
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
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: all 0.2s ease;
}

.map-ctrl-btn:hover {
  background: #FFFFFF;
  transform: translateY(-1px);
  color: #DC2626;
}

.map-ctrl-btn.active {
  background: #DC2626;
  color: #FFFFFF;
  border-color: #DC2626;
}

.route-status-pill {
  position: absolute;
  bottom: 16px;
  left: 16px;
  background: rgba(255, 255, 255, 0.96);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 9999px;
  padding: 6px 14px;
  font-size: 0.78rem;
  font-weight: 800;
  color: #111827;
  display: flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  z-index: 5;
}

.pulse-dot {
  width: 8px;
  height: 8px;
  background-color: #DC2626;
  border-radius: 50%;
  animation: pulse 1.8s infinite;
}

@keyframes pulse {
  0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(220, 38, 38, 0); }
  100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
}

.map-loading-overlay, .map-error-overlay {
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

/* Waypoints Timeline & Chips */
.waypoints-summary-card {
  background: #FFFFFF;
  border-radius: 18px;
  border: 1px solid rgba(0, 0, 0, 0.08);
}

.waypoints-chip-list {
  display: flex;
  flex-wrap: nowrap;
  overflow-x: auto;
  gap: 10px;
  padding-bottom: 6px;
  scrollbar-width: thin;
}

.wp-chip-item {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  border-radius: 12px;
  background: #F9FAFB;
  border: 1px solid rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: all 0.2s ease;
}

.wp-chip-item:hover {
  background: #FEF2F2;
  border-color: #DC2626;
}

.wp-chip-item.active {
  background: #FEF2F2;
  border-color: #DC2626;
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.15);
}

.wp-badge {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #6B7280;
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.72rem;
  font-weight: 900;
}

.is-start .wp-badge { background: #16A34A; }
.is-finish .wp-badge { background: #DC2626; }

.wp-info {
  display: flex;
  flex-direction: column;
}

.wp-title {
  font-size: 0.82rem;
  color: #1F2937;
  white-space: nowrap;
}

.wp-meta {
  font-size: 0.68rem;
  color: #6B7280;
}

/* Markers on Map */
:deep(.custom-route-marker) {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #FFFFFF;
  font-weight: 900;
  font-size: 11px;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
  border: 2px solid #FFFFFF;
  cursor: pointer;
  transition: transform 0.2s ease;
}

:deep(.custom-route-marker:hover) {
  transform: scale(1.18);
  z-index: 10;
}

:deep(.custom-route-marker.is-start) { background: #16A34A; }
:deep(.custom-route-marker.is-finish) { background: #DC2626; }
:deep(.custom-route-marker.is-checkpoint) { background: #0284C7; }

/* Popups on Map */
:deep(.map-route-popup) {
  padding: 6px 4px;
  max-width: 200px;
}

:deep(.popup-badge) {
  font-size: 9px;
  font-weight: 800;
  display: inline-block;
  padding: 2px 6px;
  border-radius: 4px;
  margin-bottom: 4px;
}

:deep(.badge-start) { background: #DCFCE7; color: #15803D; }
:deep(.badge-finish) { background: #FEF2F2; color: #DC2626; }
:deep(.badge-cp) { background: #E0F2FE; color: #0369A1; }

:deep(.popup-title) {
  font-size: 13px;
  font-weight: 800;
  margin: 0 0 2px 0;
  color: #111827;
}

:deep(.popup-dist) {
  font-size: 11px;
  color: #6B7280;
  margin: 0;
}

:deep(.popup-desc) {
  font-size: 11px;
  color: #374151;
  margin: 4px 0 0 0;
}

@media (max-width: 600px) {
  .map-frame-wrapper {
    height: 380px;
  }
}
</style>
