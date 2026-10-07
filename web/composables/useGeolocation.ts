import { ref, onUnmounted } from 'vue'

export interface GeoPosition {
  lat: number
  lng: number
  accuracy: number
}

export function useGeolocation() {
  const position = ref<GeoPosition | null>(null)
  const error = ref<string | null>(null)
  const loading = ref(false)
  let watchId: number | null = null

  const isSupported = typeof navigator !== 'undefined' && 'geolocation' in navigator

  const start = () => {
    if (!isSupported) {
      error.value = 'Perangkat tidak mendukung GPS'
      return
    }
    loading.value = true
    error.value = null

    watchId = navigator.geolocation.watchPosition(
      (pos) => {
        position.value = {
          lat: pos.coords.latitude,
          lng: pos.coords.longitude,
          accuracy: pos.coords.accuracy,
        }
        loading.value = false
      },
      (err) => {
        loading.value = false
        switch (err.code) {
          case err.PERMISSION_DENIED:
            error.value = 'Akses lokasi ditolak. Aktifkan izin lokasi di pengaturan perangkat.'
            break
          case err.POSITION_UNAVAILABLE:
            error.value = 'Posisi tidak tersedia saat ini.'
            break
          case err.TIMEOUT:
            error.value = 'Timeout saat mengambil lokasi.'
            break
          default:
            error.value = 'Gagal mendapatkan lokasi.'
        }
      },
      {
        enableHighAccuracy: true,
        maximumAge: 10000,
        timeout: 15000,
      }
    )
  }

  const stop = () => {
    if (watchId !== null) {
      navigator.geolocation.clearWatch(watchId)
      watchId = null
    }
  }

  const getCurrentOnce = (): Promise<GeoPosition> => {
    return new Promise((resolve, reject) => {
      if (!isSupported) {
        reject(new Error('GPS tidak didukung'))
        return
      }
      navigator.geolocation.getCurrentPosition(
        (pos) => resolve({
          lat: pos.coords.latitude,
          lng: pos.coords.longitude,
          accuracy: pos.coords.accuracy,
        }),
        reject,
        { enableHighAccuracy: true, timeout: 10000 }
      )
    })
  }

  onUnmounted(() => stop())

  return { position, error, loading, isSupported, start, stop, getCurrentOnce }
}
