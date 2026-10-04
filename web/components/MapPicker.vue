<template>
  <div class="map-picker">
    <!-- Search Bar & Controls -->
    <div class="picker-top-bar pa-3 d-flex align-center ga-2">
      <v-text-field
        v-model="searchQuery"
        placeholder="Cari nama tempat, jalan, atau landmark..."
        prepend-inner-icon="mdi-magnify"
        variant="outlined"
        rounded="pill"
        density="compact"
        clearable
        hide-details
        class="search-input"
        :loading="isSearching"
        @keyup.enter="searchLocation"
      ></v-text-field>

      <v-btn
        color="#DC2626"
        variant="flat"
        size="small"
        rounded="pill"
        class="px-4 text-white font-weight-bold"
        :loading="isSearching"
        @click="searchLocation"
      >
        Cari
      </v-btn>

      <v-btn
        icon
        variant="tonal"
        color="#111827"
        size="small"
        rounded="pill"
        title="Gunakan Lokasi Saya Saat Ini"
        aria-label="Gunakan Lokasi Saya Saat Ini"
        :loading="isLocating"
        @click="useCurrentLocation"
      >
        <v-icon size="18">mdi-crosshairs-gps</v-icon>
      </v-btn>
    </div>

    <!-- Quick Location Suggestions Chips -->
    <div class="quick-locations-strip px-3 py-2 d-flex align-center ga-2">
      <span class="text-caption text-grey-darken-1 font-weight-medium">Spot Cepat:</span>
      <div class="chips-scroll d-flex align-center ga-2 flex-grow-1">
        <button
          v-for="spot in quickLocations"
          :key="spot.name"
          type="button"
          class="quick-spot-chip"
          @click="goToLocation(spot.lat, spot.lng, spot.name)"
        >
          <v-icon size="12" class="mr-1" color="#DC2626">mdi-map-marker</v-icon>
          {{ spot.name }}
        </button>
      </div>
    </div>

    <!-- Map Canvas Container -->
    <div class="map-canvas-wrapper">
      <div ref="mapContainer" class="map-container"></div>

      <!-- Map Loading Overlay -->
      <div v-if="loadingMap" class="map-loading-overlay d-flex flex-column align-center justify-center">
        <v-progress-circular indeterminate color="#DC2626" size="44"></v-progress-circular>
        <span class="mt-3 text-caption font-weight-medium text-grey-darken-2">Memuat peta interaktif...</span>
      </div>

      <!-- Floating Guide Tag -->
      <div class="floating-pin-hint">
        <v-icon size="14" color="#DC2626" class="mr-1">mdi-cursor-default-click</v-icon>
        Klik di peta atau geser pin merah untuk menandai lokasi
      </div>
    </div>

    <!-- Selected Location Info Card -->
    <div v-if="selectedLocation" class="location-summary-card pa-3">
      <div class="d-flex align-center ga-3">
        <div class="summary-pin-badge">
          <v-icon color="#DC2626" size="20">mdi-map-marker-check</v-icon>
        </div>
        <div class="flex-grow-1 min-w-0">
          <div class="text-caption font-weight-bold text-grey-darken-4">Titik Koordinat Terpilih</div>
          <div class="text-caption font-family-mono text-grey-darken-2 text-truncate">
            {{ selectedLocation.lat.toFixed(6) }}, {{ selectedLocation.lng.toFixed(6) }}
          </div>
          <div v-if="selectedLocation.address" class="text-caption text-grey-darken-3 text-truncate mt-0-5">
            {{ selectedLocation.address }}
          </div>
        </div>
        <v-btn
          icon
          size="x-small"
          variant="text"
          color="grey"
          @click="clearSelection"
          aria-label="Hapus Lokasi"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import { useMapLibre, MAP_STYLES } from '~/composables/useMapLibre'

const props = defineProps({
  modelValue: {
    type: Object as () => { lat: number; lng: number } | null,
    default: null
  },
  center: {
    type: Object as () => { lat: number; lng: number },
    default: () => ({ lat: -5.1477, lng: 119.4327 }) // Makassar center
  },
  zoom: {
    type: Number,
    default: 13
  }
})

const emit = defineEmits(['update:modelValue', 'location-selected'])

const { loadMapLibre } = useMapLibre()

const mapContainer = ref<HTMLElement | null>(null)
const searchQuery = ref('')
const isSearching = ref(false)
const isLocating = ref(false)
const loadingMap = ref(true)

const selectedLocation = ref<{ lat: number; lng: number; address?: string } | null>(props.modelValue)

let maplibregl: any = null
let mapInstance: any = null
let markerInstance: any = null

// Daftar rekomendasi lokasi populer pejalan kaki
const quickLocations = [
  { name: 'Pantai Losari', lat: -5.1376, lng: 119.4028 },
  { name: 'Benteng Rotterdam', lat: -5.1347, lng: 119.4089 },
  { name: 'Karebosi', lat: -5.1349, lng: 119.4144 },
  { name: 'Taman Macan', lat: -5.1418, lng: 119.4124 },
  { name: 'Monumen Mandala', lat: -5.1384, lng: 119.4172 }
]

// Inisialisasi peta MapLibre GL
const initMap = async () => {
  loadingMap.value = true
  try {
    maplibregl = await loadMapLibre()
    await nextTick()

    if (!mapContainer.value) return

    const initialLat = props.modelValue?.lat || props.center?.lat || -5.1477
    const initialLng = props.modelValue?.lng || props.center?.lng || 119.4327

    mapInstance = new maplibregl.Map({
      container: mapContainer.value,
      style: MAP_STYLES.positron.url,
      center: [initialLng, initialLat],
      zoom: props.zoom || 13,
      attributionControl: false
    })

    // Navigation Controls (zoom in/out)
    mapInstance.addControl(new maplibregl.NavigationControl({ showCompass: false }), 'bottom-right')

    mapInstance.on('load', () => {
      loadingMap.value = false

      // Pasang marker awal jika sudah ada koordinat
      if (props.modelValue?.lat && props.modelValue?.lng) {
        setMarker(props.modelValue.lat, props.modelValue.lng, false)
      }

      // Event klik peta untuk menaruh / memindahkan pin
      mapInstance.on('click', (e: any) => {
        const { lng, lat } = e.lngLat
        setMarker(lat, lng, true)
        reverseGeocode(lat, lng)
      })
    })

  } catch (error) {
    console.error('MapLibre init error:', error)
    loadingMap.value = false
  }
}

// Custom Marker DOM element
const createCustomMarkerElement = () => {
  const el = document.createElement('div')
  el.className = 'custom-map-picker-pin'
  el.innerHTML = `
    <div class="pin-pulse"></div>
    <div class="pin-body">
      <svg viewBox="0 0 24 24" width="16" height="16" fill="white">
        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
      </svg>
    </div>
  `
  return el
}

// Pasang / pindahkan marker
const setMarker = (lat: number, lng: number, emitEvent = true) => {
  if (!mapInstance || !maplibregl) return

  if (markerInstance) {
    markerInstance.setLngLat([lng, lat])
  } else {
    const el = createCustomMarkerElement()
    markerInstance = new maplibregl.Marker({
      element: el,
      draggable: true,
      anchor: 'bottom'
    })
      .setLngLat([lng, lat])
      .addTo(mapInstance)

    // Event ketika marker selesai ditarik (dragend)
    markerInstance.on('dragend', () => {
      const pos = markerInstance.getLngLat()
      selectedLocation.value = { lat: pos.lat, lng: pos.lng }
      emit('update:modelValue', { lat: pos.lat, lng: pos.lng })
      emit('location-selected', { lat: pos.lat, lng: pos.lng })
      reverseGeocode(pos.lat, pos.lng)
    })
  }

  selectedLocation.value = { lat, lng }

  if (emitEvent) {
    emit('update:modelValue', { lat, lng })
    emit('location-selected', { lat, lng })
  }
}

// Reverse geocoding via OpenStreetMap Nominatim
const reverseGeocode = async (lat: number, lng: number) => {
  try {
    const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`
    const res = await fetch(url, {
      headers: {
        'Accept-Language': 'id'
      }
    })
    const data = await res.json()
    if (data && data.display_name) {
      const address = data.display_name
      if (selectedLocation.value) {
        selectedLocation.value.address = address
      }
      emit('location-selected', { lat, lng, address })
    }
  } catch (err) {
    // Tetap gunakan koordinat jika geocoding offline
  }
}

// Geocoding search via OpenStreetMap Nominatim
const searchLocation = async () => {
  if (!searchQuery.value.trim() || !mapInstance) return

  isSearching.value = true
  try {
    const q = searchQuery.value.includes('Indonesia')
      ? searchQuery.value
      : `${searchQuery.value}, Indonesia`

    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&countrycodes=id&limit=1`
    const res = await fetch(url, {
      headers: {
        'Accept-Language': 'id'
      }
    })
    const list = await res.json()

    if (list && list.length > 0) {
      const item = list[0]
      const lat = parseFloat(item.lat)
      const lng = parseFloat(item.lon)

      mapInstance.flyTo({
        center: [lng, lat],
        zoom: 15,
        duration: 1200
      })

      setMarker(lat, lng, true)
      if (selectedLocation.value) {
        selectedLocation.value.address = item.display_name
      }
      emit('location-selected', { lat, lng, address: item.display_name })
    }
  } catch (err) {
    console.error('Search location error:', err)
  } finally {
    isSearching.value = false
  }
}

// Gunakan Geolocation Peramban Pengguna
const useCurrentLocation = () => {
  if (!navigator.geolocation) return

  isLocating.value = true
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const lat = pos.coords.latitude
      const lng = pos.coords.longitude

      if (mapInstance) {
        mapInstance.flyTo({
          center: [lng, lat],
          zoom: 16,
          duration: 1200
        })
      }

      setMarker(lat, lng, true)
      reverseGeocode(lat, lng)
      isLocating.value = false
    },
    (err) => {
      console.warn('Geolocation error:', err)
      isLocating.value = false
    },
    { enableHighAccuracy: true, timeout: 8000 }
  )
}

// Tombol Quick Location
const goToLocation = (lat: number, lng: number, name?: string) => {
  if (!mapInstance) return

  mapInstance.flyTo({
    center: [lng, lat],
    zoom: 15,
    duration: 1000
  })

  setMarker(lat, lng, true)
  if (name && selectedLocation.value) {
    selectedLocation.value.address = `${name}, Makassar`
  }
  reverseGeocode(lat, lng)
}

// Hapus pilihan
const clearSelection = () => {
  if (markerInstance) {
    markerInstance.remove()
    markerInstance = null
  }
  selectedLocation.value = null
  emit('update:modelValue', null)
}

// Watch modelValue dari parent
watch(
  () => props.modelValue,
  (newVal) => {
    if (newVal && newVal.lat && newVal.lng) {
      if (
        !selectedLocation.value ||
        selectedLocation.value.lat !== newVal.lat ||
        selectedLocation.value.lng !== newVal.lng
      ) {
        setMarker(newVal.lat, newVal.lng, false)
      }
    }
  },
  { deep: true }
)

// Watch center jika berganti kota
watch(
  () => props.center,
  (newCenter) => {
    if (newCenter && mapInstance) {
      mapInstance.flyTo({
        center: [newCenter.lng, newCenter.lat],
        zoom: 13,
        duration: 1000
      })
    }
  },
  { deep: true }
)

onMounted(() => {
  initMap()
})

onBeforeUnmount(() => {
  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }
})
</script>

<style scoped>
.map-picker {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  background: #FFFFFF;
}

.picker-top-bar {
  background: #FFFFFF;
  border-bottom: 1px solid #F1F5F9;
}

.quick-locations-strip {
  background: #F8FAFC;
  border-bottom: 1px solid #E2E8F0;
  overflow: hidden;
}

.chips-scroll {
  overflow-x: auto;
  scrollbar-width: none;
}

.chips-scroll::-webkit-scrollbar {
  display: none;
}

.quick-spot-chip {
  display: inline-flex;
  align-items: center;
  padding: 3px 10px;
  border-radius: 9999px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  font-size: 0.72rem;
  font-weight: 600;
  color: #334155;
  white-space: nowrap;
  cursor: pointer;
  transition: all 0.2s ease;
}

.quick-spot-chip:hover {
  border-color: #DC2626;
  color: #DC2626;
  background: #FEF2F2;
}

.map-canvas-wrapper {
  position: relative;
  flex: 1;
  min-height: 420px;
}

.map-container {
  width: 100%;
  height: 100%;
  min-height: 420px;
}

.map-loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(4px);
  z-index: 10;
}

.floating-pin-hint {
  position: absolute;
  top: 12px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  padding: 5px 14px;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  color: #1F2937;
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  pointer-events: none;
  z-index: 5;
  white-space: nowrap;
}

.location-summary-card {
  background: #FFFFFF;
  border-top: 1px solid #E5E7EB;
}

.summary-pin-badge {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: #FEF2F2;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
</style>

<style>
/* Custom Marker Pin Styling for MapLibre */
.custom-map-picker-pin {
  position: relative;
  width: 32px;
  height: 38px;
  cursor: grab;
  transform: translate(-50%, -100%);
}

.custom-map-picker-pin:active {
  cursor: grabbing;
}

.custom-map-picker-pin .pin-body {
  width: 32px;
  height: 38px;
  background: #DC2626;
  border: 2px solid #FFFFFF;
  border-radius: 50% 50% 50% 0;
  transform: rotate(-45deg);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 16px rgba(220, 38, 38, 0.5);
  transition: transform 0.15s ease;
}

.custom-map-picker-pin:hover .pin-body {
  transform: rotate(-45deg) scale(1.1);
}

.custom-map-picker-pin .pin-body svg {
  transform: rotate(45deg);
}

.custom-map-picker-pin .pin-pulse {
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 14px;
  height: 6px;
  background: rgba(0, 0, 0, 0.3);
  border-radius: 50%;
}
</style>
