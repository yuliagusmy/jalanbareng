<template>
  <div>
    <div
      ref="mapContainer"
      style="height: 500px; width: 100%; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
    ></div>

    <div class="mt-6">
      <v-alert
        type="info"
        variant="tonal"
        density="compact"
        class="mb-4"
      >
        <v-icon start>mdi-information</v-icon>
        <strong>Cara Menggunakan:</strong> Klik pada peta untuk menambahkan titik rute. Titik-titik akan terhubung secara otomatis.
      </v-alert>

      <v-row align="center" class="mt-4">
        <v-col cols="12" sm="6">
          <div class="d-flex ga-2">
            <v-btn
              @click="undo"
              color="warning"
              variant="outlined"
              prepend-icon="mdi-undo"
              :disabled="routePoints.length === 0"
            >
              Undo Terakhir
            </v-btn>
            <v-btn
              @click="clear"
              color="error"
              variant="outlined"
              prepend-icon="mdi-delete"
              :disabled="routePoints.length === 0"
            >
              Hapus Semua
            </v-btn>
          </div>
        </v-col>
        <v-col cols="12" sm="6" class="text-sm-right">
          <v-chip
            color="primary"
            size="large"
            prepend-icon="mdi-map-marker-distance"
            class="font-weight-bold"
          >
            <span class="text-h6">{{ distance.toFixed(2) }} km</span>
          </v-chip>
        </v-col>
      </v-row>

      <v-card v-if="routePoints.length > 0" class="mt-4" elevation="0" variant="outlined">
        <v-card-text>
          <div class="d-flex align-center mb-2">
            <v-icon color="primary" class="mr-2">mdi-map-marker-multiple</v-icon>
            <span class="font-weight-bold">Titik Rute ({{ routePoints.length }})</span>
          </div>
          <div class="route-points-list">
            <v-chip
              v-for="(point, i) in routePoints"
              :key="i"
              size="small"
              class="ma-1"
              :color="i === 0 ? 'success' : (i === routePoints.length - 1 ? 'error' : 'primary')"
            >
              {{ i === 0 ? 'Start' : (i === routePoints.length - 1 ? 'Finish' : `Point ${i}`) }}:
              {{ point.lat.toFixed(6) }}, {{ point.lng.toFixed(6) }}
            </v-chip>
          </div>
        </v-card-text>
      </v-card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array as () => Array<{ lat: number, lng: number }>,
    default: () => []
  },
  isVisible: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'update:distance'])

const config = useRuntimeConfig()
const mapContainer = ref<HTMLElement | null>(null)

let map: any = null
let markers: any[] = []
let polyline: any = null
let google: any = null

const routePoints = ref<Array<{ lat: number, lng: number }>>(
  props.modelValue ? [...props.modelValue] : []
)

// Calculate distance using Haversine formula
const distance = computed(() => {
  if (routePoints.value.length < 2) return 0
  let total = 0

  for (let i = 0; i < routePoints.value.length - 1; i++) {
    const from = routePoints.value[i]
    const to = routePoints.value[i + 1]

    // Haversine formula
    const R = 6371 // Earth's radius in km
    const dLat = toRad(to.lat - from.lat)
    const dLng = toRad(to.lng - from.lng)

    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(toRad(from.lat)) * Math.cos(toRad(to.lat)) *
      Math.sin(dLng / 2) * Math.sin(dLng / 2)

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
    const distance = R * c

    total += distance
  }

  return total
})

const toRad = (degrees: number) => {
  return degrees * (Math.PI / 180)
}

// Watch distance changes and emit
watch(distance, (newDistance) => {
  emit('update:distance', newDistance)
})

// Watch for external modelValue changes (from edit mode)
watch(() => props.modelValue, (newRoute) => {
  if (newRoute && newRoute.length > 0) {
    routePoints.value = [...newRoute]
    updateMapRoute()
  }
}, { deep: true })

// Watch visibility to resize map
watch(() => props.isVisible, (newValue) => {
  if (newValue && map) {
    setTimeout(() => {
      google?.maps?.event?.trigger(map, 'resize')
      if (routePoints.value.length > 0) {
        fitBounds()
      }
    }, 100)
  }
})

const waitForGoogleMaps = () => {
  return new Promise<void>((resolve) => {
    if (typeof window !== 'undefined' && (window as any).google?.maps) {
      resolve()
      return
    }

    const checkInterval = setInterval(() => {
      if ((window as any).google?.maps) {
        clearInterval(checkInterval)
        resolve()
      }
    }, 100)

    setTimeout(() => {
      clearInterval(checkInterval)
      resolve()
    }, 10000)
  })
}

const initMap = async () => {
  try {
    await waitForGoogleMaps()
    await nextTick()

    if (!mapContainer.value) {
      console.error('Map container not found')
      return
    }

    google = (window as any).google

    // Center map on Makassar
    const center = { lat: -5.1477, lng: 119.4327 }

    map = new google.maps.Map(mapContainer.value, {
      center: center,
      zoom: 13,
      mapId: config.public.googleMapsMapId,
      mapTypeControl: true,
      mapTypeControlOptions: {
        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
        mapTypeIds: ['roadmap', 'satellite', 'hybrid']
      },
      streetViewControl: true,
      fullscreenControl: true,
      zoomControl: true,
      styles: [
        {
          featureType: 'poi',
          elementType: 'labels',
          stylers: [{ visibility: 'on' }]
        }
      ]
    })

    // Add click listener to add route points
    map.addListener('click', (event: any) => {
      addRoutePoint({
        lat: event.latLng.lat(),
        lng: event.latLng.lng()
      })
    })

    // If there are existing route points, display them
    if (routePoints.value.length > 0) {
      updateMapRoute()
      fitBounds()
    }

    console.log('Google Maps initialized for route editor')
  } catch (error) {
    console.error('Error initializing Google Maps:', error)
  }
}

const addRoutePoint = (point: { lat: number, lng: number }) => {
  routePoints.value.push(point)
  updateMapRoute()
  emit('update:modelValue', routePoints.value)
}

const updateMapRoute = () => {
  if (!map || !google) return

  // Clear existing markers
  markers.forEach(marker => marker.map = null)
  markers = []

  // Clear existing polyline
  if (polyline) {
    polyline.setMap(null)
  }

  // Add markers for each point
  routePoints.value.forEach((point, index) => {
    const isStart = index === 0
    const isEnd = index === routePoints.value.length - 1

    let glyphText = `${index + 1}`
    let bgColor = '#2196F3' // Blue
    let borderColor = '#1E88E5' // Darker Blue

    if (isStart) {
      glyphText = 'S'
      bgColor = '#4CAF50' // Green
      borderColor = '#388E3C'
    } else if (isEnd) {
      glyphText = 'F'
      bgColor = '#F44336' // Red
      borderColor = '#D32F2F'
    }

    const pinElement = new google.maps.marker.PinElement({
        glyph: glyphText,
        glyphColor: 'white',
        background: bgColor,
        borderColor: borderColor,
    });

    const marker = new google.maps.marker.AdvancedMarkerElement({
      position: point,
      map: map,
      content: pinElement.element,
      title: isStart ? 'Start' : (isEnd ? `Finish` : `Point ${index + 1}`)
    })

    // Add info window
    const infoWindow = new google.maps.InfoWindow({
      content: `
        <div style="padding: 4px;">
          <strong>${isStart ? 'Start' : (isEnd ? 'Finish' : `Point ${index + 1}`)}</strong><br>
          <small>${point.lat.toFixed(6)}, ${point.lng.toFixed(6)}</small>
        </div>
      `
    })

    marker.addListener('click', () => {
      infoWindow.open({
          anchor: marker,
          map,
      });
    })

    markers.push(marker)
  })

  // Draw polyline if there are multiple points
  if (routePoints.value.length > 1) {
    polyline = new google.maps.Polyline({
      path: routePoints.value,
      geodesic: true,
      strokeColor: '#2196F3',
      strokeOpacity: 0.8,
      strokeWeight: 4,
      map: map
    })
  }
}

const fitBounds = () => {
  if (!map || !google || routePoints.value.length === 0) return

  const bounds = new google.maps.LatLngBounds()
  routePoints.value.forEach(point => {
    bounds.extend(point)
  })
  map.fitBounds(bounds)

  // Adjust zoom if only one point
  if (routePoints.value.length === 1) {
    map.setZoom(15)
  }
}

const undo = () => {
  if (routePoints.value.length > 0) {
    routePoints.value.pop()
    updateMapRoute()
    emit('update:modelValue', routePoints.value)

    if (routePoints.value.length > 0) {
      fitBounds()
    }
  }
}

const clear = () => {
  routePoints.value = []
  updateMapRoute()
  emit('update:modelValue', [])

  // Reset map to Makassar center
  if (map && google) {
    map.setCenter({ lat: -5.1477, lng: 119.4327 })
    map.setZoom(13)
  }
}

onMounted(() => {
  // Load Google Maps script if not already loaded
  if (typeof window !== 'undefined') {
    const existingScript = document.querySelector('script[src*="maps.googleapis.com"]')

    if (!existingScript) {
      const script = document.createElement('script')
      script.src = `https://maps.googleapis.com/maps/api/js?key=${config.public.googleMapsApiKey}&libraries=marker`
      script.async = true
      script.defer = true
      script.onload = () => {
        initMap()
      }
      document.head.appendChild(script)
    } else {
      initMap()
    }
  }
})

onUnmounted(() => {
  // Clean up markers
  markers.forEach(marker => marker.map = null)
  if (polyline) {
    polyline.setMap(null)
  }
})
</script>

<style scoped>
.route-points-list {
  max-height: 200px;
  overflow-y: auto;
}
</style>
