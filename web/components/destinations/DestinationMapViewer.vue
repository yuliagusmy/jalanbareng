<template>
  <div ref="mapContainer" style="height: 100%; width: 100%; border-radius: 12px; overflow: hidden;"></div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'

interface Destination {
  id: number;
  name: string;
  slug: string;
  latitude: number;
  longitude: number;
  primary_photo: string;
  category_name: string;
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

const getImageUrl = (path: string) => {
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
    const position = { lat: Number(dest.latitude), lng: Number(dest.longitude) }

    const marker = new google.maps.marker.AdvancedMarkerElement({
      position,
      map,
      title: dest.name,
    })

    const destinationUrl = `/destinations/${dest.id}`
    const contentString = `
      <div style="width: 250px; font-family: sans-serif;">
        <img src="${getImageUrl(dest.primary_photo)}" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px;">
        <h3 style="margin: 10px 0 5px; font-size: 16px;">${dest.name}</h3>
        <p style="margin: 0 0 10px; font-size: 14px; color: #555;">${dest.category_name || 'Destinasi'}</p>
        <button id="info-window-link-${dest.id}" style="background: none; border: none; padding: 0; color: #1976D2; font-weight: bold; font-size: 14px; cursor: pointer;">Lihat Detail →</button>
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
