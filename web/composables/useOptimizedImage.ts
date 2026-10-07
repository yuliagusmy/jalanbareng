/**
 * Composable untuk generate optimized image URLs
 * Supports: WebP format, responsive sizes, quality control
 */

interface ImageOptions {
  width?: number
  height?: number
  quality?: number
  format?: 'webp' | 'jpg' | 'png' | 'auto'
  fit?: 'cover' | 'contain' | 'fill' | 'inside' | 'outside'
}

export const useOptimizedImage = () => {
  const config = useRuntimeConfig()
  const baseUrl = config.public.apiBase || 'http://localhost:8000'

  /**
   * Generate optimized image URL
   * Backend should support image optimization params via query string
   * Or use CDN with transformation capabilities
   */
  const getOptimizedUrl = (
    originalUrl: string,
    options: ImageOptions = {}
  ): string => {
    if (!originalUrl) return ''

    // If already a full URL (from external source), return as-is
    if (originalUrl.startsWith('http://') || originalUrl.startsWith('https://')) {
      return originalUrl
    }

    // If relative path from backend
    let imageUrl = originalUrl.startsWith('/')
      ? `${baseUrl}${originalUrl}`
      : `${baseUrl}/${originalUrl}`

    // Add optimization params (if backend supports it)
    const params = new URLSearchParams()
    
    if (options.width) params.append('w', options.width.toString())
    if (options.height) params.append('h', options.height.toString())
    if (options.quality) params.append('q', options.quality.toString())
    if (options.format) params.append('fm', options.format)
    if (options.fit) params.append('fit', options.fit)

    const queryString = params.toString()
    if (queryString) {
      imageUrl += (imageUrl.includes('?') ? '&' : '?') + queryString
    }

    return imageUrl
  }

  /**
   * Generate srcset for responsive images
   */
  const getSrcSet = (
    originalUrl: string,
    widths: number[] = [320, 640, 960, 1280, 1920],
    options: Omit<ImageOptions, 'width'> = {}
  ): string => {
    return widths
      .map((width) => {
        const url = getOptimizedUrl(originalUrl, { ...options, width })
        return `${url} ${width}w`
      })
      .join(', ')
  }

  /**
   * Generate placeholder image URL (low-res, blurred)
   */
  const getPlaceholder = (originalUrl: string): string => {
    return getOptimizedUrl(originalUrl, {
      width: 20,
      quality: 20,
      format: 'webp',
    })
  }

  /**
   * Check if browser supports WebP
   */
  const supportsWebP = (): boolean => {
    if (typeof window === 'undefined') return false

    const canvas = document.createElement('canvas')
    if (canvas.getContext && canvas.getContext('2d')) {
      return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0
    }
    return false
  }

  /**
   * Get optimal image format based on browser support
   */
  const getOptimalFormat = (): 'webp' | 'jpg' => {
    return supportsWebP() ? 'webp' : 'jpg'
  }

  /**
   * Preload critical images (above the fold)
   */
  const preloadImage = (url: string, options: ImageOptions = {}) => {
    if (typeof window === 'undefined') return

    const optimizedUrl = getOptimizedUrl(url, {
      format: getOptimalFormat(),
      ...options,
    })

    const link = document.createElement('link')
    link.rel = 'preload'
    link.as = 'image'
    link.href = optimizedUrl

    // Add to head if not already exists
    if (!document.querySelector(`link[href="${optimizedUrl}"]`)) {
      document.head.appendChild(link)
    }
  }

  /**
   * Generate sizes attribute for responsive images
   */
  const getSizes = (breakpoints: { [key: string]: string } = {}): string => {
    const defaultBreakpoints = {
      xs: '100vw',
      sm: '100vw',
      md: '50vw',
      lg: '33vw',
      xl: '25vw',
      ...breakpoints,
    }

    return Object.entries(defaultBreakpoints)
      .map(([bp, size]) => {
        const mediaQuery = getMediaQuery(bp)
        return mediaQuery ? `${mediaQuery} ${size}` : size
      })
      .join(', ')
  }

  /**
   * Get media query for Vuetify breakpoints
   */
  const getMediaQuery = (breakpoint: string): string | null => {
    const breakpoints: { [key: string]: string } = {
      xs: '(max-width: 599px)',
      sm: '(min-width: 600px) and (max-width: 959px)',
      md: '(min-width: 960px) and (max-width: 1279px)',
      lg: '(min-width: 1280px) and (max-width: 1919px)',
      xl: '(min-width: 1920px)',
    }
    return breakpoints[breakpoint] || null
  }

  return {
    getOptimizedUrl,
    getSrcSet,
    getPlaceholder,
    supportsWebP,
    getOptimalFormat,
    preloadImage,
    getSizes,
  }
}
