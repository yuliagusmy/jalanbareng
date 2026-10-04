<template>
  <div class="maplibre-viewer-wrapper">
    <!-- Map Canvas Container -->
    <div ref="mapContainer" class="maplibre-container"></div>

    <!-- Map Floating Action Controls -->
    <div class="map-floating-controls" v-if="mapReady">
      <!-- 2D / 3D Perspective Toggle -->
      <button
        type="button"
        class="map-ctrl-btn"
        :class="{ active: is3D }"
        @click="toggle3D"
        :title="is3D ? 'Beralih ke tampilan 2D Atas' : 'Beralih ke tampilan 3D Perspektif'"
        aria-label="Toggle 3D"
      >
        <v-icon size="18">{{ is3D ? 'mdi-cube-outline' : 'mdi-vector-square' }}</v-icon>
        <span class="ctrl-label">{{ is3D ? '3D' : '2D' }}</span>
      </button>

      <!-- Style Switcher (Minimal vs Detail) -->
      <button
        type="button"
        class="map-ctrl-btn"
        @click="toggleMapStyle"
        :title="`Ganti gaya peta (Saat ini: ${currentStyleName})`"
        aria-label="Ganti Gaya Peta"
      >
        <v-icon size="18">{{ currentStyleIcon }}</v-icon>
        <span class="ctrl-label">{{ currentStyleKey === 'positron' ? 'Minimal' : 'Detail' }}</span>
      </button>

      <!-- Fit Bounds / Reset View -->
      <button
        type="button"
        class="map-ctrl-btn"
        @click="fitToAllDestinations"
        title="Pusatkan ke semua destinasi"
        aria-label="Pusatkan Peta"
      >
        <v-icon size="18">mdi-crosshairs-gps</v-icon>
      </button>

      <!-- Fullscreen Toggle -->
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

    <!-- Active Filter or Marker Counter Tag -->
    <div class="map-status-pill" v-if="mapReady && destinations.length > 0">
      <span class="pulse-dot"></span>
      <span>{{ destinations.length }} Titik Terpetakan</span>
    </div>

    <!-- Loading State Overlay -->
    <div v-if="loading" class="map-loading-overlay">
      <v-progress-circular indeterminate color="#DC2626" size="38" width="3"></v-progress-circular>
      <span class="text-caption font-weight-bold text-grey-darken-2 mt-2">
        Memuat peta interaktif...
      </span>
    </div>

    <!-- Error State Overlay -->
    <div v-if="loadError" class="map-error-overlay">
      <v-icon size="36" color="#DC2626">mdi-map-marker-alert-outline</v-icon>
      <span class="text-caption text-grey-darken-2 text-center mt-2 px-4">
        {{ loadError }}
      </span>
      <v-btn size="small" variant="outlined" color="#DC2626" class="mt-3" @click="retryInit">
        Coba Lagi
      </v-btn>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { useMapLibre, MAP_STYLES, type MapStyleKey } from '~/composables/useMapLibre'

interface Destination {
  id: number
  name: string
  slug?: string
  latitude: number | string
  longitude: number | string
  primary_photo?: string
  photos?: Array<{ photo_path: string }>
  category_name?: string
  category?: { id?: number; name?: string; icon?: string }
  likes_count?: number
  comments_count?: number
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
const { loadMapLibre, add3DBuildingsLayer } = useMapLibre()

const mapContainer = ref<HTMLElement | null>(null)
const loading = ref(true)
const loadError = ref<string | null>(null)
const mapReady = ref(false)
const is3D = ref(false)
const isFullscreen = ref(false)
const currentStyleKey = ref<MapStyleKey>('positron')

let maplibregl: any = null
let mapInstance: any = null
let activeMarkers: any[] = []
let activePopup: any = null

const currentStyleName = computed(() => MAP_STYLES[currentStyleKey.value].name)
const currentStyleIcon = computed(() => MAP_STYLES[currentStyleKey.value].icon)

const getImageUrl = (path?: string) => {
  if (!path) return 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=400&h=300&fit=crop'
  if (path.startsWith('http')) return path
  return `${config.public.apiUrl.replace('/api', '')}/storage/${path}`
}

// Watch destinations
watch(() => props.destinations, (newDestinations) => {
  if (mapInstance && mapReady.value) {
    updateMarkers(newDestinations)
  }
}, { deep: true })

// Watch mapCenter
watch(() => props.mapCenter, (newCenter) => {
  if (newCenter && mapInstance && mapReady.value) {
    mapInstance.flyTo({
      center: [Number(newCenter.lng), Number(newCenter.lat)],
      zoom: 13,
      duration: 1200
    })
  }
}, { deep: true })

const initMap = async () => {
  loading.value = true
  loadError.value = null

  try {
    maplibregl = await loadMapLibre()
    await nextTick()

    if (!mapContainer.value) return

    // Titik default Makassar
    const defaultLng = 119.4327
    const defaultLat = -5.1477
    const initialLng = props.mapCenter ? Number(props.mapCenter.lng) : defaultLng
    const initialLat = props.mapCenter ? Number(props.mapCenter.lat) : defaultLat

    mapInstance = new maplibregl.Map({
      container: mapContainer.value,
      style: MAP_STYLES[currentStyleKey.value].url,
      center: [initialLng, initialLat],
      zoom: 12,
      pitch: is3D.value ? 55 : 0,
      bearing: is3D.value ? -20 : 0,
      attributionControl: false
    })

    // Navigation control (zoom & compass) di kanan bawah
    mapInstance.addControl(
      new maplibregl.NavigationControl({ visualizePitch: true }),
      'bottom-right'
    )

    // Attribution control minimal
    mapInstance.addControl(
      new maplibregl.AttributionControl({ compact: true }),
      'bottom-left'
    )

    mapInstance.on('load', () => {
      mapReady.value = true
      loading.value = false

      if (is3D.value) {
        add3DBuildingsLayer(mapInstance)
      }

      if (props.destinations.length > 0) {
        updateMarkers(props.destinations)
      }
    })

    mapInstance.on('error', (e: any) => {
      // Abaikan error minor tile abort
      if (e?.error?.status === 404 || e?.status === 404) return
    })

  } catch (err: any) {
    loading.value = false
    loadError.value = 'Gagal memuat sistem peta interaktif. Pastikan peramban mendukung WebGL.'
    console.error('MapLibre init error:', err)
  }
}

const retryInit = () => {
  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }
  initMap()
}

/**
 * Profil warna dan icon tematik berdasarkan kategori destinasi
 */
const getCategoryMeta = (dest: Destination) => {
  const cat = (dest.category?.slug || dest.category_name || dest.category?.name || '').toLowerCase()

  if (cat.includes('kuliner') || cat.includes('makan') || cat.includes('food')) {
    return {
      bg: '#EA580C',
      shadow: 'rgba(234, 88, 12, 0.45)',
      name: 'Kuliner',
      svg: `<svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>`
    }
  }
  if (cat.includes('alam') || cat.includes('nature')) {
    return {
      bg: '#059669',
      shadow: 'rgba(5, 150, 105, 0.45)',
      name: 'Wisata Alam',
      svg: `<svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M14 6l-3.75 5 2.85 3.8-1.6 1.2C9.81 13.75 7 10 7 10l-6 8h22L14 6z"/></svg>`
    }
  }
  if (cat.includes('sejarah') || cat.includes('history') || cat.includes('heritage') || cat.includes('benteng') || cat.includes('museum')) {
    return {
      bg: '#B45309',
      shadow: 'rgba(180, 83, 9, 0.45)',
      name: 'Sejarah',
      svg: `<svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 1L2 6v2h20V6L12 1zm-7 9v8h2v-8H5zm5 0v8h2v-8h-2zm5 0v8h2v-8h-2zm5 0v8h2v-8h-2zM2 20v2h20v-2H2z"/></svg>`
    }
  }
  if (cat.includes('foto') || cat.includes('spot') || cat.includes('photo')) {
    return {
      bg: '#6366F1',
      shadow: 'rgba(99, 102, 241, 0.45)',
      name: 'Spot Foto',
      svg: `<svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 15.2a3.2 3.2 0 1 0 0-6.4 3.2 3.2 0 0 0 0 6.4z"/><path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/></svg>`
    }
  }
  if (cat.includes('pantai') || cat.includes('beach') || cat.includes('laut')) {
    return {
      bg: '#0284C7',
      shadow: 'rgba(2, 132, 199, 0.45)',
      name: 'Pantai',
      svg: `<svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 2a5 5 0 0 0-5 5c0 1.95 1.13 3.63 2.77 4.45L4 21h16l-5.77-9.55C15.87 10.63 17 8.95 17 7a5 5 0 0 0-5-5zm-1 3.5a1.5 1.5 0 1 1 3 0 1.5 1.5 0 0 1-3 0z"/></svg>`
    }
  }
  if (cat.includes('taman') || cat.includes('park')) {
    return {
      bg: '#16A34A',
      shadow: 'rgba(22, 163, 74, 0.45)',
      name: 'Taman Kota',
      svg: `<svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M13 19h4l-5-7h3l-4-6h-2l-4 6h3l-5 7h4v3h6v-3z"/></svg>`
    }
  }
  if (cat.includes('belanja') || cat.includes('pasar') || cat.includes('shop')) {
    return {
      bg: '#E11D48',
      shadow: 'rgba(225, 29, 72, 0.45)',
      name: 'Belanja',
      svg: `<svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-6-2c1.1 0 2 .9 2 2h-4c0-1.1.9-2 2-2zm6 16H6V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h4v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12z"/></svg>`
    }
  }
  if (cat.includes('seni') || cat.includes('budaya') || cat.includes('art')) {
    return {
      bg: '#7C3AED',
      shadow: 'rgba(124, 58, 237, 0.45)',
      name: 'Seni & Budaya',
      svg: `<svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 3c-4.97 0-9 4.03-9 9 0 2.12.74 4.07 1.97 5.61L4.35 19c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.9-1.9C9.17 19.38 10.53 20 12 20c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-5.5 9c-.83 0-1.5-.67-1.5-1.5S5.67 9 6.5 9s1.5.67 1.5 1.5S7.33 12 6.5 12zm3-4C8.67 8 8 7.33 8 6.5S8.67 5 9.5 5s1.5.67 1.5 1.5S10.33 8 9.5 8zm5 0c-.83 0-1.5-.67-1.5-1.5S13.67 5 14.5 5s1.5.67 1.5 1.5S15.33 8 14.5 8zm3 4c-.83 0-1.5-.67-1.5-1.5S16.67 9 17.5 9s1.5.67 1.5 1.5S18.33 12 17.5 12z"/></svg>`
    }
  }

  return {
    bg: '#DC2626',
    shadow: 'rgba(220, 38, 38, 0.45)',
    name: 'Destinasi',
    svg: `<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>`
  }
}

/**
 * Buat elemen marker custom Jalan Bareng dengan kategori dinamis
 */
const createMarkerElement = (dest: Destination) => {
  const meta = getCategoryMeta(dest)

  const el = document.createElement('div')
  el.className = 'jb-custom-marker'
  el.setAttribute('role', 'button')
  el.setAttribute('aria-label', `${dest.name} (${meta.name})`)

  const pin = document.createElement('div')
  pin.className = 'jb-marker-pin'
  pin.style.background = meta.bg
  pin.style.boxShadow = `0 4px 14px ${meta.shadow}`

  const icon = document.createElement('span')
  icon.className = 'jb-marker-icon'
  icon.innerHTML = meta.svg

  pin.appendChild(icon)
  el.appendChild(pin)

  return el
}

/**
 * Update seluruh marker pada peta
 */
const updateMarkers = (destinations: Destination[]) => {
  if (!mapInstance || !maplibregl) return

  // Bersihkan marker lama
  activeMarkers.forEach(m => m.remove())
  activeMarkers = []

  if (activePopup) {
    activePopup.remove()
    activePopup = null
  }

  const validDestinations: Destination[] = []
  const bounds = new maplibregl.LngLatBounds()

  destinations.forEach(dest => {
    const lat = Number(dest.latitude)
    const lng = Number(dest.longitude)
    if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) return

    validDestinations.push(dest)
    bounds.extend([lng, lat])

    // Buat elemen marker
    const markerEl = createMarkerElement(dest)

    // Buat popup card
    const photo = dest.primary_photo || (dest.photos && dest.photos[0] ? dest.photos[0].photo_path : '')
    const photoUrl = getImageUrl(photo)
    const categoryName = dest.category_name || dest.category?.name || 'Destinasi Pejalan'

    const isSinglePage = props.destinations.length === 1

    const popupContent = document.createElement('div')
    popupContent.className = 'jb-popup-card'
    popupContent.innerHTML = `
      <div class="jb-popup-img-wrap">
        <img src="${photoUrl}" alt="${dest.name}" class="jb-popup-img" onerror="this.src='https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=400&h=300&fit=crop'" />
        <span class="jb-popup-cat" style="background: ${getCategoryMeta(dest).bg}">${categoryName}</span>
      </div>
      <div class="jb-popup-body">
        <h4 class="jb-popup-title">${dest.name}</h4>
        ${isSinglePage 
          ? `<a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" rel="noopener" class="jb-popup-btn">
              <span>Buka Petunjuk Arah</span>
              <span>&nearr;</span>
            </a>`
          : `<button type="button" class="jb-popup-btn" id="btn-dest-${dest.id}">
              <span>Lihat Panduan Detail</span>
              <span>&rarr;</span>
            </button>`
        }
      </div>
    `

    const popup = new maplibregl.Popup({
      anchor: 'bottom',
      offset: [0, -34],
      closeButton: true,
      closeOnClick: false,
      maxWidth: '250px',
      className: 'jb-map-popup'
    }).setDOMContent(popupContent)

    const marker = new maplibregl.Marker({
      element: markerEl,
      anchor: 'bottom'
    })
      .setLngLat([lng, lat])
      .setPopup(popup)
      .addTo(mapInstance)

    // Event saat popup dibuka untuk binding navigasi
    popup.on('open', () => {
      activePopup = popup
      const btn = document.getElementById(`btn-dest-${dest.id}`)
      if (btn) {
        btn.onclick = () => {
          router.push(`/destinations/${dest.id}`)
        }
      }
    })

    // Klik marker langsung flyTo dengan offset agar popup di atas pin tampak lega & utuh
    markerEl.addEventListener('click', () => {
      mapInstance.flyTo({
        center: [lng, lat],
        offset: [0, 90],
        zoom: Math.max(mapInstance.getZoom(), 14),
        duration: 800
      })
    })

    activeMarkers.push(marker)
  })

  // Fit bounds ke semua marker
  if (validDestinations.length > 1) {
    mapInstance.fitBounds(bounds, {
      padding: { top: 60, bottom: 60, left: 50, right: 50 },
      maxZoom: 15,
      duration: 1000
    })
  } else if (validDestinations.length === 1) {
    const single = validDestinations[0]
    mapInstance.flyTo({
      center: [Number(single.longitude), Number(single.latitude)],
      offset: [0, 80],
      zoom: 15,
      duration: 1000
    })
  }
}

/**
 * Toggle 3D Perspective Mode
 */
const toggle3D = () => {
  if (!mapInstance) return
  is3D.value = !is3D.value

  if (is3D.value) {
    mapInstance.easeTo({
      pitch: 55,
      bearing: -20,
      duration: 1200
    })
    add3DBuildingsLayer(mapInstance)
  } else {
    mapInstance.easeTo({
      pitch: 0,
      bearing: 0,
      duration: 1000
    })
  }
}

/**
 * Ganti Style Peta (Positron vs Liberty)
 */
const toggleMapStyle = () => {
  if (!mapInstance) return
  currentStyleKey.value = currentStyleKey.value === 'positron' ? 'liberty' : 'positron'

  mapInstance.setStyle(MAP_STYLES[currentStyleKey.value].url)
  mapInstance.once('style.load', () => {
    if (is3D.value) {
      add3DBuildingsLayer(mapInstance)
    }
    if (props.destinations.length > 0) {
      updateMarkers(props.destinations)
    }
  })
}

/**
 * Pusatkan peta ke semua destinasi
 */
const fitToAllDestinations = () => {
  if (!mapInstance || activeMarkers.length === 0) return

  const bounds = new maplibregl.LngLatBounds()
  props.destinations.forEach(dest => {
    const lat = Number(dest.latitude)
    const lng = Number(dest.longitude)
    if (!isNaN(lat) && !isNaN(lng) && !(lat === 0 && lng === 0)) {
      bounds.extend([lng, lat])
    }
  })

  mapInstance.fitBounds(bounds, {
    padding: 60,
    maxZoom: 15,
    duration: 1000
  })
}

/**
 * Layar penuh container peta
 */
const toggleFullscreen = () => {
  if (!mapContainer.value) return
  const wrapper = mapContainer.value.parentElement

  if (!document.fullscreenElement) {
    wrapper?.requestFullscreen().then(() => {
      isFullscreen.value = true
      mapInstance?.resize()
    }).catch(() => {})
  } else {
    document.exitFullscreen().then(() => {
      isFullscreen.value = false
      mapInstance?.resize()
    }).catch(() => {})
  }
}

onMounted(() => {
  initMap()
})

onUnmounted(() => {
  if (activePopup) {
    activePopup.remove()
  }
  activeMarkers.forEach(m => m.remove())
  activeMarkers = []

  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }
})
</script>

<style scoped>
.maplibre-viewer-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  min-height: 420px;
  border-radius: 16px;
  overflow: hidden;
  background: #E5E7EB;
}

.maplibre-container {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
}

/* Floating Controls */
.map-floating-controls {
  position: absolute;
  top: 14px;
  right: 14px;
  z-index: 10;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.map-ctrl-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  background: #FFFFFF;
  color: #1F2937;
  border: 1px solid #E5E7EB;
  border-radius: 9999px;
  padding: 8px 14px;
  font-size: 0.78rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  user-select: none;
}

.map-ctrl-btn:hover {
  transform: translateY(-2px);
  background: #F9FAFB;
  border-color: #CBD5E1;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

.map-ctrl-btn.active {
  background: #DC2626;
  color: #FFFFFF;
  border-color: #DC2626;
  box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35);
}

.ctrl-label {
  letter-spacing: 0.02em;
}

/* Status Pill */
.map-status-pill {
  position: absolute;
  top: 14px;
  left: 14px;
  z-index: 10;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  border: 1px solid #E5E7EB;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 700;
  color: #1F2937;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.pulse-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #DC2626;
  box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
  animation: pulse-ring 2s infinite;
}

@keyframes pulse-ring {
  0% {
    box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
  }
  70% {
    box-shadow: 0 0 0 7px rgba(220, 38, 38, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
  }
}

/* Loading & Error Overlays */
.map-loading-overlay,
.map-error-overlay {
  position: absolute;
  inset: 0;
  z-index: 20;
  background: rgba(250, 250, 249, 0.92);
  backdrop-filter: blur(4px);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

/* Custom Marker Styling (Global inside MapLibre container) */
:deep(.jb-custom-marker) {
  cursor: pointer;
  transform: translateZ(0);
}

:deep(.jb-marker-pin) {
  width: 32px;
  height: 32px;
  background: #DC2626;
  color: #FFFFFF;
  border: 2.5px solid #FFFFFF;
  border-radius: 50% 50% 50% 0;
  transform: rotate(-45deg);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 14px rgba(220, 38, 38, 0.45);
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
}

:deep(.jb-marker-icon) {
  transform: rotate(45deg);
  display: flex;
  align-items: center;
  justify-content: center;
}

:deep(.jb-custom-marker:hover .jb-marker-pin) {
  transform: rotate(-45deg) scale(1.18);
  box-shadow: 0 8px 20px rgba(220, 38, 38, 0.55);
}

/* Popup Customization */
:deep(.maplibregl-popup-content) {
  padding: 0 !important;
  border-radius: 16px !important;
  overflow: hidden !important;
  box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.18) !important;
  border: 1px solid #E5E7EB !important;
}

:deep(.maplibregl-popup-close-button) {
  padding: 6px 10px !important;
  font-size: 16px !important;
  color: #FFFFFF !important;
  background: rgba(0, 0, 0, 0.4) !important;
  border-radius: 50% !important;
  top: 8px !important;
  right: 8px !important;
  z-index: 10 !important;
  transition: background 0.2s ease;
}

:deep(.maplibregl-popup-close-button:hover) {
  background: rgba(0, 0, 0, 0.7) !important;
}

:deep(.jb-popup-card) {
  font-family: Inter, system-ui, -apple-system, sans-serif;
  width: 224px;
}

:deep(.jb-popup-img-wrap) {
  position: relative;
  width: 100%;
  height: 96px;
  background: #F3F4F6;
  overflow: hidden;
}

:deep(.jb-popup-img) {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

:deep(.jb-popup-cat) {
  position: absolute;
  bottom: 6px;
  left: 6px;
  background: rgba(17, 24, 39, 0.82);
  backdrop-filter: blur(4px);
  color: #FFFFFF;
  font-size: 0.65rem;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 9999px;
}

:deep(.jb-popup-body) {
  padding: 10px 12px 12px 12px;
  background: #FFFFFF;
}

:deep(.jb-popup-title) {
  margin: 0 0 8px 0;
  font-size: 0.86rem;
  font-weight: 800;
  color: #111827;
  line-height: 1.25;
}

:deep(.jb-popup-btn) {
  width: 100%;
  background: #DC2626;
  color: #FFFFFF !important;
  border: none;
  padding: 6px 12px;
  border-radius: 9999px;
  font-weight: 700;
  font-size: 0.72rem;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  text-decoration: none;
  box-shadow: 0 3px 8px rgba(220, 38, 38, 0.25);
  transition: all 0.2s ease;
}

:deep(.jb-popup-btn:hover) {
  background: #B91C1C;
  transform: translateY(-1px);
}

@media (max-width: 600px) {
  .maplibre-viewer-wrapper {
    min-height: 340px;
  }

  .map-floating-controls {
    top: 10px;
    right: 10px;
    gap: 6px;
  }

  .map-ctrl-btn {
    padding: 6px 10px;
    font-size: 0.72rem;
  }

  .map-status-pill {
    top: 10px;
    left: 10px;
    padding: 4px 10px;
    font-size: 0.7rem;
  }
}
</style>
