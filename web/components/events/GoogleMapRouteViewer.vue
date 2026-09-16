<template>
  <div>
    <div
      ref="mapContainer"
      style="height: 500px; width: 100%; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
    ></div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'

const props = defineProps({
  route: {
    type: Array as () => Array<{ lat: number, lng: number }>,
    default: () => []
  }
})

const config = useRuntimeConfig()
const mapContainer = ref<HTMLElement | null>(null)

let map: any = null
let markers: any[] = []
let polyline: any = null
let google: any = null

const routePoints = ref<Array<{ lat: number, lng: number }>>(
  props.route ? [...props.route] : []
)

watch(() => props.route, (newRoute) => {
  if (newRoute && newRoute.length > 0) {
    routePoints.value = [...newRoute]
    updateMapRoute()
    fitBounds()
  }
}, { deep: true })


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
      resolve() // Resolve anyway after timeout
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
      streetViewControl: false,
      fullscreenControl: true,
      zoomControl: true,
      clickableIcons: false, // Make it read-only
      styles: [
        {
          featureType: 'poi',
          elementType: 'labels',
          stylers: [{ visibility: 'on' }]
        }
      ]
    })

    if (routePoints.value.length > 0) {
      updateMapRoute()
      fitBounds()
    } else {
        // If no route, just show the center
        map.setCenter(center);
        map.setZoom(13);
    }
    
    // Add a listener to resize the map when it becomes visible
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            google.maps.event.trigger(map, 'resize');
            if (routePoints.value.length > 0) {
                fitBounds();
            }
        }
    });
    if (mapContainer.value) {
        observer.observe(mapContainer.value);
    }


    console.log('Google Maps initialized for route viewer')
  } catch (error) {
    console.error('Error initializing Google Maps:', error)
  }
}

const updateMapRoute = () => {
  if (!map || !google) return

  markers.forEach(marker => marker.map = null)
  markers = []

  if (polyline) {
    polyline.setMap(null)
  }

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
      title: isStart ? 'Start' : (isEnd ? 'Finish' : `Point ${index + 1}`)
    })
    markers.push(marker)
  })

  if (routePoints.value.length > 1) {
    polyline = new google.maps.Polyline({
      path: routePoints.value,
      geodesic: true,
      strokeColor: '#1976D2',
      strokeOpacity: 0.9,
      strokeWeight: 5,
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

  if (routePoints.value.length === 1) {
    map.setZoom(15)
  }
}

onMounted(() => {
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
  markers.forEach(marker => marker.map = null)
  if (polyline) {
    polyline.setMap(null)
  }
})
</script>
