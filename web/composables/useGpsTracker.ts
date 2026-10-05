/**
 * Composable useGpsTracker
 * Mengelola live GPS tracking ala Strava menggunakan navigator.geolocation.watchPosition,
 * penyaringan jitter/akurasi, kalkulasi jarak Haversine, timer, wake-lock, dan auto-save LocalStorage.
 */

import { ref, computed } from 'vue'

export interface GpsCoordinate {
  lat: number
  lng: number
  altitude?: number | null
  accuracy?: number | null
  timestamp: number
  name?: string
  description?: string
  isCheckpoint?: boolean
}

export function useGpsTracker() {
  const isTracking = ref(false)
  const isPaused = ref(false)
  const recordedPoints = ref<GpsCoordinate[]>([])
  const checkpoints = ref<GpsCoordinate[]>([])
  const currentPosition = ref<GpsCoordinate | null>(null)
  const gpsAccuracy = ref<number | null>(null)
  const elapsedSeconds = ref(0)
  const gpsError = ref<string | null>(null)

  let watchId: number | null = null
  let timerInterval: any = null
  let wakeLockSentinel: any = null

  const STORAGE_KEY = 'jalan_bareng_active_gps_recording'

  // Haversine distance in kilometers
  const haversineKm = (lat1: number, lon1: number, lat2: number, lon2: number): number => {
    const R = 6371
    const dLat = ((lat2 - lat1) * Math.PI) / 180
    const dLon = ((lon2 - lon1) * Math.PI) / 180
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos((lat1 * Math.PI) / 180) *
        Math.cos((lat2 * Math.PI) / 180) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2)
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
  }

  // Hitung total jarak kumulatif (km)
  const totalDistanceKm = computed(() => {
    const pts = recordedPoints.value
    if (pts.length < 2) return 0
    let total = 0
    for (let i = 0; i < pts.length - 1; i++) {
      total += haversineKm(pts[i].lat, pts[i].lng, pts[i + 1].lat, pts[i + 1].lng)
    }
    return total
  })

  // Format durasi HH:MM:SS atau MM:SS
  const formattedDuration = computed(() => {
    const s = elapsedSeconds.value
    const hrs = Math.floor(s / 3600)
    const mins = Math.floor((s % 3600) / 60)
    const secs = s % 60

    const pad = (n: number) => n.toString().padStart(2, '0')
    if (hrs > 0) {
      return `${pad(hrs)}:${pad(mins)}:${pad(secs)}`
    }
    return `${pad(mins)}:${pad(secs)}`
  })

  // Rata-rata kecepatan (km/jam)
  const averageSpeedKmh = computed(() => {
    const hours = elapsedSeconds.value / 3600
    if (hours <= 0 || totalDistanceKm.value <= 0) return 0
    return totalDistanceKm.value / hours
  })

  // Status kualitas sinyal GPS
  const accuracyStatus = computed(() => {
    if (gpsAccuracy.value === null) return { level: 'unknown', label: 'Mencari GPS...', color: 'grey' }
    if (gpsAccuracy.value <= 10) return { level: 'good', label: 'Sinyal Akurat (±' + Math.round(gpsAccuracy.value) + 'm)', color: 'success' }
    if (gpsAccuracy.value <= 25) return { level: 'fair', label: 'Sinyal Cukup (±' + Math.round(gpsAccuracy.value) + 'm)', color: 'warning' }
    return { level: 'poor', label: 'Sinyal Lemah (±' + Math.round(gpsAccuracy.value) + 'm)', color: 'error' }
  })

  // Request Wake Lock agar layar tidak mati saat tracking
  const requestWakeLock = async () => {
    if (typeof window !== 'undefined' && 'wakeLock' in navigator) {
      try {
        wakeLockSentinel = await (navigator as any).wakeLock.request('screen')
      } catch (err) {
        console.warn('Wake Lock request error:', err)
      }
    }
  }

  const releaseWakeLock = () => {
    if (wakeLockSentinel) {
      try {
        wakeLockSentinel.release()
      } catch {}
      wakeLockSentinel = null
    }
  }

  // Mulai Tracking
  const startTracking = () => {
    if (typeof window === 'undefined' || !navigator.geolocation) {
      gpsError.value = 'Browser atau perangkat tidak mendukung Geolocation GPS.'
      return false
    }

    gpsError.value = null
    isTracking.value = true
    isPaused.value = false

    requestWakeLock()

    // Timer Interval
    clearInterval(timerInterval)
    timerInterval = setInterval(() => {
      if (!isPaused.value) {
        elapsedSeconds.value++
        persistToStorage()
      }
    }, 1000)

    // Watch position
    watchId = navigator.geolocation.watchPosition(
      (pos) => {
        handleGpsUpdate(pos)
      },
      (err) => {
        console.error('GPS error:', err)
        if (err.code === err.PERMISSION_DENIED) {
          gpsError.value = 'Izin lokasi ditolak. Harap izinkan akses lokasi di browser HP Anda.'
        } else {
          gpsError.value = 'Mencari sinyal GPS: ' + err.message
        }
      },
      {
        enableHighAccuracy: true,
        timeout: 20000,
        maximumAge: 0
      }
    )

    return true
  }

  // Handle GPS coordinate update
  const handleGpsUpdate = (pos: GeolocationPosition) => {
    const { latitude, longitude, accuracy, altitude } = pos.coords
    gpsAccuracy.value = accuracy

    const point: GpsCoordinate = {
      lat: latitude,
      lng: longitude,
      accuracy,
      altitude,
      timestamp: pos.timestamp || Date.now()
    }

    currentPosition.value = point

    if (isPaused.value) return

    // Filter akurasi: abaikan jika akurasi GPS terlalu melompat (> 35 meter)
    if (accuracy > 35) {
      return
    }

    // Filter jarak: titik baru harus berjarak minimal 4-5 meter dari titik terakhir
    // untuk mencegah polyline kusut saat tour guide berhenti/bicara di tempat
    const pts = recordedPoints.value
    if (pts.length > 0) {
      const lastPoint = pts[pts.length - 1]
      const distFromLast = haversineKm(lastPoint.lat, lastPoint.lng, latitude, longitude) * 1000 // meter
      if (distFromLast < 4) {
        return
      }
    }

    recordedPoints.value.push(point)
    persistToStorage()
  }

  // Tambahkan checkpoint di posisi saat ini
  const addCheckpointAtCurrentPosition = (name: string, description?: string) => {
    if (!currentPosition.value) return null

    const cp: GpsCoordinate = {
      ...currentPosition.value,
      name: name || `Checkpoint ${checkpoints.value.length + 1}`,
      description: description || '',
      isCheckpoint: true
    }

    checkpoints.value.push(cp)
    // Juga tambahkan ke point rute jika belum ada
    recordedPoints.value.push(cp)
    persistToStorage()
    return cp
  }

  const pauseTracking = () => {
    isPaused.value = true
  }

  const resumeTracking = () => {
    isPaused.value = false
  }

  const stopTracking = () => {
    isTracking.value = false
    isPaused.value = false

    if (watchId !== null) {
      navigator.geolocation.clearWatch(watchId)
      watchId = null
    }

    clearInterval(timerInterval)
    timerInterval = null
    releaseWakeLock()
    clearStorage()
  }

  const persistToStorage = () => {
    if (typeof window === 'undefined') return
    try {
      const data = {
        points: recordedPoints.value,
        checkpoints: checkpoints.value,
        elapsedSeconds: elapsedSeconds.value,
        savedAt: Date.now()
      }
      localStorage.setItem(STORAGE_KEY, JSON.stringify(data))
    } catch {}
  }

  const restoreFromStorage = (): boolean => {
    if (typeof window === 'undefined') return false
    try {
      const raw = localStorage.getItem(STORAGE_KEY)
      if (!raw) return false
      const data = JSON.parse(raw)
      // Restore jika disimpan kurang dari 4 jam yang lalu
      if (Date.now() - data.savedAt < 4 * 3600 * 1000) {
        recordedPoints.value = data.points || []
        checkpoints.value = data.checkpoints || []
        elapsedSeconds.value = data.elapsedSeconds || 0
        return true
      }
    } catch {}
    return false
  }

  const clearStorage = () => {
    if (typeof window !== 'undefined') {
      localStorage.removeItem(STORAGE_KEY)
    }
  }

  const resetAll = () => {
    stopTracking()
    recordedPoints.value = []
    checkpoints.value = []
    currentPosition.value = null
    elapsedSeconds.value = 0
    gpsError.value = null
  }

  return {
    isTracking,
    isPaused,
    recordedPoints,
    checkpoints,
    currentPosition,
    gpsAccuracy,
    elapsedSeconds,
    formattedDuration,
    totalDistanceKm,
    averageSpeedKmh,
    accuracyStatus,
    gpsError,
    startTracking,
    pauseTracking,
    resumeTracking,
    stopTracking,
    resetAll,
    addCheckpointAtCurrentPosition,
    restoreFromStorage
  }
}
