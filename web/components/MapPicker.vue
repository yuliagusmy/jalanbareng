<template>
  <div class="map-picker">
    <!-- Search Box -->
    <div class="search-box-container pa-4">
      <v-text-field
        ref="searchInput"
        v-model="searchQuery"
        placeholder="Cari lokasi di Makassar..."
        prepend-inner-icon="mdi-magnify"
        variant="outlined"
        rounded="lg"
        density="comfortable"
        clearable
        hide-details
        @keyup.enter="searchLocation"
      >
        <template v-slot:append>
          <v-btn
            color="primary"
            variant="flat"
            size="small"
            rounded="lg"
            @click="searchLocation"
          >
            Cari
          </v-btn>
        </template>
      </v-text-field>
    </div>

    <!-- Map Container -->
    <div ref="mapContainer" class="map-container"></div>

    <!-- Selected Location Info -->
    <div v-if="selectedLocation" class="location-info pa-4">
      <v-card elevation="0" rounded="lg" class="pa-4" color="blue-lighten-5">
        <div class="d-flex align-center">
          <v-icon color="primary" class="mr-3">mdi-map-marker-check</v-icon>
          <div class="flex-grow-1">
            <div class="text-subtitle-2 font-weight-bold">Lokasi Dipilih</div>
            <div class="text-caption text-grey-darken-1">
              {{ selectedLocation.lat.toFixed(6) }}, {{ selectedLocation.lng.toFixed(6) }}
            </div>
            <div v-if="selectedLocation.address" class="text-caption mt-1">
              {{ selectedLocation.address }}
            </div>
          </div>
          <v-btn
            icon
            size="small"
            variant="text"
            @click="clearSelection"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </div>
      </v-card>
    </div>

    <!-- Quick Location Buttons -->
    <div class="quick-locations pa-4 d-flex flex-wrap ga-2">
      <v-chip
        v-for="location in quickLocations"
        :key="location.name"
        variant="outlined"
        color="primary"
        @click="goToLocation(location.lat, location.lng)"
        class="cursor-pointer"
      >
        <v-icon start size="small">mdi-map-marker</v-icon>
        {{ location.name }}
      </v-chip>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'

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

const mapContainer = ref<HTMLElement | null>(null)
const searchInput = ref<any>(null)
const searchQuery = ref('')
const selectedLocation = ref<{ lat: number; lng: number; address?: string } | null>(props.modelValue)

let map: google.maps.Map | null = null
let marker: google.maps.Marker | null = null
let geocoder: google.maps.Geocoder | null = null

// Quick access locations in Makassar
const quickLocations = ref([
  { name: 'Pantai Losari', lat: -5.1376, lng: 119.4028 },
  { name: 'Fort Rotterdam', lat: -5.1347, lng: 119.4089 },
  { name: 'Trans Studio', lat: -5.1456, lng: 119.4372 },
  { name: 'Mal Panakkukang', lat: -5.1489, lng: 119.4323 },
])

const loadGoogleMapsScript = () => {
  return new Promise((resolve, reject) => {
    if (typeof google !== 'undefined' && google.maps) {
      resolve(google)
      return
    }

    const config = useRuntimeConfig()
    const apiKey = config.public.googleMapsApiKey

    if (!apiKey) {
      reject(new Error('Google Maps API key not configured'))
      return
    }

    const script = document.createElement('script')
    script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places`
    script.async = true
    script.defer = true
    script.onload = () => resolve(google)
    script.onerror = reject
    document.head.appendChild(script)
  })
}

const initMap = async () => {
  try {
    await loadGoogleMapsScript()

    if (!mapContainer.value) return

    // Initialize map
    map = new google.maps.Map(mapContainer.value, {
      center: props.modelValue || props.center,
      zoom: props.zoom,
      mapTypeControl: true,
      streetViewControl: false,
      fullscreenControl: true,
      styles: [
        {
          featureType: 'poi',
          elementType: 'labels',
          stylers: [{ visibility: 'on' }]
        }
      ]
    })

    // Initialize services
    geocoder = new google.maps.Geocoder()

    // Add click listener to map
    map.addListener('click', (e: google.maps.MapMouseEvent) => {
      if (e.latLng) {
        const lat = e.latLng.lat()
        const lng = e.latLng.lng()
        setMarker(lat, lng)
        reverseGeocode(lat, lng)
      }
    })

    // Set initial marker if location is provided
    if (props.modelValue) {
      setMarker(props.modelValue.lat, props.modelValue.lng, true) // fromExternal = true
      reverseGeocode(props.modelValue.lat, props.modelValue.lng)
    }

  } catch (error) {
    console.error('Error loading Google Maps:', error)
  }
}

const setMarker = (lat: number, lng: number, fromExternal = false) => {
  if (!map) return

  const position = { lat, lng }

  // Remove existing marker
  if (marker) {
    marker.setMap(null)
  }

  // Create new marker
  marker = new google.maps.Marker({
    position,
    map,
    animation: google.maps.Animation.DROP,
    draggable: true,
    icon: {
      url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
    }
  })

  // Add drag listener
  marker.addListener('dragend', (e: google.maps.MapMouseEvent) => {
    if (e.latLng) {
      const newLat = e.latLng.lat()
      const newLng = e.latLng.lng()
      // Update selected location immediately
      selectedLocation.value = { lat: newLat, lng: newLng }
      emit('update:modelValue', { lat: newLat, lng: newLng })
      // Then get address
      reverseGeocode(newLat, newLng)
    }
  })

  // Center map on marker
  map.panTo(position)

  // Update selected location (only emit if not from external watch)
  selectedLocation.value = { lat, lng }
  if (!fromExternal) {
    emit('update:modelValue', { lat, lng })
    emit('location-selected', { lat, lng })
  }
}

const reverseGeocode = async (lat: number, lng: number) => {
  if (!geocoder) return

  try {
    const response = await geocoder.geocode({ location: { lat, lng } })

    if (response.results && response.results.length > 0) {
      const address = response.results[0].formatted_address
      selectedLocation.value = { lat, lng, address }
      emit('location-selected', { lat, lng, address })
    } else {
      // If no address found, just set coordinates
      selectedLocation.value = { lat, lng }
      emit('location-selected', { lat, lng })
    }
  } catch (error: any) {
    console.error('Geocoding error:', error)
    // Even if geocoding fails, still set the location coordinates
    selectedLocation.value = { lat, lng }
    emit('location-selected', { lat, lng })
  }
}

const searchLocation = async () => {
  if (!searchQuery.value || !map || !geocoder) return

  try {
    // Bias search to Makassar area
    const searchAddress = searchQuery.value.includes('Makassar')
      ? searchQuery.value
      : `${searchQuery.value}, Makassar, Indonesia`

    const response = await geocoder.geocode({
      address: searchAddress,
      region: 'ID',
      componentRestrictions: {
        country: 'ID'
      }
    })

    if (response.results && response.results.length > 0) {
      const location = response.results[0].geometry.location
      const lat = location.lat()
      const lng = location.lng()

      setMarker(lat, lng)
      await reverseGeocode(lat, lng)

      // Zoom in a bit
      if (map) {
        map.setZoom(15)
      }
    } else {
      alert('Lokasi tidak ditemukan. Coba kata kunci yang berbeda.')
    }
  } catch (error: any) {
    console.error('Search error:', error)
    const errorMessage = error.message || 'Terjadi kesalahan saat mencari lokasi.'
    alert(errorMessage)
  }
}

const goToLocation = (lat: number, lng: number) => {
  if (!map) return

  setMarker(lat, lng)
  reverseGeocode(lat, lng)
  map.setZoom(15)
}

const clearSelection = () => {
  if (marker) {
    marker.setMap(null)
    marker = null
  }
  selectedLocation.value = null
  emit('update:modelValue', null)
}

// Watch for external changes (with deep compare to prevent loops)
watch(() => props.modelValue, (newValue, oldValue) => {
  // Only update if value actually changed and we have a map
  if (newValue && map) {
    const isSame = oldValue &&
                   oldValue.lat === newValue.lat &&
                   oldValue.lng === newValue.lng

    if (!isSame) {
      setMarker(newValue.lat, newValue.lng, true) // fromExternal = true
    }
  }
}, { deep: true })

// Watch for center prop changes to update map center
watch(() => props.center, (newCenter) => {
  console.log('🗺️ MapPicker: Center prop changed to:', newCenter)
  if (map && newCenter) {
    console.log('✅ MapPicker: Panning map to new center')
    map.panTo(newCenter)
    map.setZoom(13) // Reset zoom when center changes
  } else {
    console.warn('⚠️ MapPicker: Map not ready or no center provided')
  }
}, { deep: true })

onMounted(() => {
  initMap()
})
</script>

<style scoped>
.map-picker {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.search-box-container {
  background: white;
  border-bottom: 1px solid #e0e0e0;
  z-index: 10;
}

.map-container {
  flex: 1;
  min-height: 400px;
  background: #e0e0e0;
  position: relative;
}

.location-info {
  background: white;
  border-top: 1px solid #e0e0e0;
}

.quick-locations {
  background: white;
  border-top: 1px solid #e0e0e0;
  max-height: 80px;
  overflow-x: auto;
}

.cursor-pointer {
  cursor: pointer;
}

/* Hide scrollbar but keep functionality */
.quick-locations::-webkit-scrollbar {
  height: 4px;
}

.quick-locations::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.quick-locations::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

.quick-locations::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
