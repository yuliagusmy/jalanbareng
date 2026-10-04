<template>
  <div ref="mapContainer" style="height: 100%; width: 100%; border-radius: 12px; overflow: hidden;"></div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'

interface Destination {
  id: number;
  name: string;
  slug?: string;
  latitude: number | string;
  longitude: number | string;
  primary_photo?: string;
  photos?: Array<{ photo_path: string }>;
  category_name?: string;
  category?: { id?: number; name?: string; icon?: string };
}

const props = defineProps({
  destinations: {
    type: Array as () => Array<Destination>,
    default: () => []
  },
  mapCenter: {
    type: Object as () => { lat: number; lng: number } | null,
    default: null
  }
})

const router = useRouter()
const config = useRuntimeConfig()
const mapContainer = ref<HTMLElement | null>(null)

let map: any = null
let markers: any[] = []
let google: any = null
let activeInfoWindow: any = null

const getImageUrl = (path?: string) => {
  if (!path) return 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=400&h=300&fit=crop'
  if (path.startsWith('http')) return path
  return `${config.public.apiUrl.replace('/api', '')}/storage/${path}`
}

watch(() => props.destinations, (newDestinations) => {
  if (newDestinations.length > 0 && map) {
    updateMarkers(newDestinations)
  }
}, { deep: true })

// Watch for map center changes
watch(() => props.mapCenter, (newCenter) => {
  if (newCenter && map) {
    map.setCenter(newCenter)
    map.setZoom(12)
  }
}, { deep: true })

const waitForGoogleMaps = () => {
  return new Promise<void>((resolve) => {
    if (typeof window !== 'undefined' && (window as any).google?.maps) {
      resolve();
      return;
    }
    const checkInterval = setInterval(() => {
      if ((window as any).google?.maps) {
        clearInterval(checkInterval);
        resolve();
      }
    }, 100);
  });
}

const initMap = async () => {
  await waitForGoogleMaps()
  await nextTick()

  if (!mapContainer.value) return

  google = (window as any).google

  // Use provided center or default to Makassar
  const initialCenter = props.mapCenter || { lat: -5.1477, lng: 119.4327 }

  map = new google.maps.Map(mapContainer.value, {
    center: initialCenter,
    zoom: 12,
    mapId: config.public.googleMapsMapId,
    mapTypeControl: false,
    streetViewControl: false,
    fullscreenControl: true,
  })

  if (props.destinations.length > 0) {
    updateMarkers(props.destinations)
  }
}

const updateMarkers = (destinations: Destination[]) => {
  if (!map || !google) return

  markers.forEach(marker => marker.map = null)
  markers = []

  if (activeInfoWindow) {
    activeInfoWindow.close()
  }

  activeInfoWindow = new google.maps.InfoWindow()

  destinations.forEach(dest => {
    const lat = Number(dest.latitude)
    const lng = Number(dest.longitude)
    if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) return

    const position = { lat, lng }

    const marker = new google.maps.marker.AdvancedMarkerElement({
      position,
      map,
      title: dest.name,
    })

    const destinationUrl = `/destinations/${dest.id}`
    const photo = dest.primary_photo || (dest.photos && dest.photos[0] ? dest.photos[0].photo_path : '')
    const photoUrl = getImageUrl(photo)
    const categoryName = dest.category_name || dest.category?.name || 'Destinasi Pejalan'

    const contentString = `
      <div style="width: 250px; font-family: Inter, system-ui, -apple-system, sans-serif; padding: 4px;">
        <img src="${photoUrl}" alt="${dest.name}" style="width: 100%; height: 125px; object-fit: cover; border-radius: 12px; margin-bottom: 8px; display: block;" onerror="this.src='https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=400&h=300&fit=crop'">
        <h3 style="margin: 0 0 4px; font-size: 15px; font-weight: 800; color: #111827; line-height: 1.3;">${dest.name}</h3>
        <p style="margin: 0 0 10px; font-size: 12px; color: #6B7280; font-weight: 600;">${categoryName}</p>
        <button id="info-window-link-${dest.id}" style="background: #DC2626; color: #FFFFFF; border: none; padding: 7px 16px; border-radius: 9999px; font-weight: 700; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);">
          <span>Lihat Panduan Detail</span>
          <span>&rarr;</span>
        </button>
      </div>
    `

    marker.addListener('click', () => {
      if (activeInfoWindow) {
        activeInfoWindow.close()
      }
      activeInfoWindow.setContent(contentString)
      activeInfoWindow.open(map, marker)

      google.maps.event.addListenerOnce(activeInfoWindow, 'domready', () => {
        const button = document.getElementById(`info-window-link-${dest.id}`)
        if (button) {
          button.addEventListener('click', () => {
            router.push(destinationUrl)
          })
        }
      })
    })
    markers.push(marker)
  })

  // Auto-center based on destinations
  if (destinations.length > 0) {
    const bounds = new google.maps.LatLngBounds()
    destinations.forEach(dest => {
      bounds.extend({ lat: Number(dest.latitude), lng: Number(dest.longitude) })
    })
    map.fitBounds(bounds)

    // If only one destination, zoom closer
    if (destinations.length === 1) {
      map.setZoom(15)
    }
  }
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    const scriptId = 'google-maps-script'
    const existingScript = document.getElementById(scriptId)

    if (!existingScript) {
      const script = document.createElement('script')
      script.id = scriptId
      script.src = `https://maps.googleapis.com/maps/api/js?key=${config.public.googleMapsApiKey}&libraries=marker`
      script.async = true
      script.defer = true
      script.onload = initMap
      document.head.appendChild(script)
    } else if ((window as any).google) {
      initMap()
    } else {
      existingScript.addEventListener('load', initMap);
    }
  }
})

onUnmounted(() => {
  markers.forEach(marker => marker.map = null)
})
</script>
