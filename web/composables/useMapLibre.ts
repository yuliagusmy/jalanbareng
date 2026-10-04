/**
 * Composable useMapLibre
 * Mengelola pemuatan library MapLibre GL JS secara dinamis dari CDN
 * dan menyediakan preset style OpenFreeMap (100% gratis, tanpa API key).
 */

let maplibrePromise: Promise<any> | null = null

export const MAP_STYLES = {
  positron: {
    id: 'positron',
    name: 'Minimalis Light',
    icon: 'mdi-white-balance-sunny',
    url: 'https://tiles.openfreemap.org/styles/positron'
  },
  liberty: {
    id: 'liberty',
    name: 'Eksplorasi Kota',
    icon: 'mdi-map-legend',
    url: 'https://tiles.openfreemap.org/styles/liberty'
  },
  bright: {
    id: 'bright',
    name: 'Terang & Jelas',
    icon: 'mdi-layers-outline',
    url: 'https://tiles.openfreemap.org/styles/bright'
  }
} as const

export type MapStyleKey = keyof typeof MAP_STYLES

export function useMapLibre() {
  const loadMapLibre = (): Promise<any> => {
    if (typeof window === 'undefined') {
      return Promise.reject(new Error('MapLibre can only run in the browser'))
    }

    if ((window as any).maplibregl) {
      return Promise.resolve((window as any).maplibregl)
    }

    if (maplibrePromise) {
      return maplibrePromise
    }

    maplibrePromise = new Promise((resolve, reject) => {
      // 1. Inject CSS jika belum ada
      const cssId = 'maplibre-gl-css'
      if (!document.getElementById(cssId)) {
        const link = document.createElement('link')
        link.id = cssId
        link.rel = 'stylesheet'
        link.href = 'https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css'
        document.head.appendChild(link)
      }

      // 2. Inject JS jika belum ada
      const scriptId = 'maplibre-gl-js'
      const existingScript = document.getElementById(scriptId) as HTMLScriptElement | null

      if (existingScript) {
        existingScript.addEventListener('load', () => resolve((window as any).maplibregl))
        existingScript.addEventListener('error', (err) => reject(err))
        return
      }

      const script = document.createElement('script')
      script.id = scriptId
      script.src = 'https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js'
      script.async = true
      script.onload = () => {
        if ((window as any).maplibregl) {
          resolve((window as any).maplibregl)
        } else {
          reject(new Error('MapLibre GL failed to attach to window'))
        }
      }
      script.onerror = () => {
        // Fallback ke cdnjs jika unpkg terkendala
        const fallbackScript = document.createElement('script')
        fallbackScript.src = 'https://cdn.jsdelivr.net/npm/maplibre-gl@4.7.1/dist/maplibre-gl.js'
        fallbackScript.async = true
        fallbackScript.onload = () => resolve((window as any).maplibregl)
        fallbackScript.onerror = (err) => reject(err)
        document.head.appendChild(fallbackScript)
      }

      document.head.appendChild(script)
    })

    return maplibrePromise
  }

  /**
   * Menambahkan layer gedung 3D jika didukung oleh sumber vector tile
   */
  const add3DBuildingsLayer = (map: any) => {
    if (!map) return

    // Cek apakah layer 3D sudah ada
    if (map.getLayer('3d-buildings')) return

    const layers = map.getStyle()?.layers
    if (!layers) return

    // Temukan layer pertama bertipe symbol (label) untuk menaruh gedung 3D di bawahnya
    let labelLayerId: string | undefined
    for (let i = 0; i < layers.length; i++) {
      if (layers[i].type === 'symbol' && layers[i].layout?.['text-field']) {
        labelLayerId = layers[i].id
        break
      }
    }

    try {
      map.addLayer(
        {
          id: '3d-buildings',
          source: 'openmaptiles',
          'source-layer': 'building',
          filter: ['!=', ['get', 'hide_3d'], true],
          type: 'fill-extrusion',
          minzoom: 14,
          paint: {
            'fill-extrusion-color': [
              'interpolate',
              ['linear'],
              ['get', 'render_height'],
              0, '#E5E7EB',
              50, '#CBD5E1',
              100, '#94A3B8'
            ],
            'fill-extrusion-height': [
              'interpolate',
              ['linear'],
              ['zoom'],
              14, 0,
              14.5, ['get', 'render_height']
            ],
            'fill-extrusion-base': [
              'interpolate',
              ['linear'],
              ['zoom'],
              14, 0,
              14.5, ['get', 'render_min_height']
            ],
            'fill-extrusion-opacity': 0.82
          }
        },
        labelLayerId
      )
    } catch {
      // Style mungkin menggunakan source selain 'openmaptiles', abaikan jika tidak cocok
    }
  }

  return {
    loadMapLibre,
    add3DBuildingsLayer,
    MAP_STYLES
  }
}
