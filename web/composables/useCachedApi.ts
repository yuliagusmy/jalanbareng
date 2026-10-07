/**
 * Enhanced API composable with built-in caching
 * Reduces unnecessary API calls and improves performance
 */

interface CacheOptions {
  ttl?: number // Time-to-live in seconds (default: 300 = 5 minutes)
  key?: string // Custom cache key
  force?: boolean // Force refresh, bypass cache
}

interface CacheEntry<T> {
  data: T
  timestamp: number
  ttl: number
}

// In-memory cache store
const cache = new Map<string, CacheEntry<any>>()

export const useCachedApi = () => {
  const { getApiUrl } = useApi()

  /**
   * Generate cache key from URL and params
   */
  const generateCacheKey = (url: string, options?: any): string => {
    const baseKey = url
    const paramsKey = options ? JSON.stringify(options) : ''
    return `${baseKey}:${paramsKey}`
  }

  /**
   * Check if cache entry is still valid
   */
  const isCacheValid = <T>(entry: CacheEntry<T>): boolean => {
    const now = Date.now()
    const age = (now - entry.timestamp) / 1000 // age in seconds
    return age < entry.ttl
  }

  /**
   * Get data from cache
   */
  const getFromCache = <T>(cacheKey: string): T | null => {
    const entry = cache.get(cacheKey)
    if (!entry) return null
    
    if (isCacheValid(entry)) {
      return entry.data as T
    }
    
    // Cache expired, remove it
    cache.delete(cacheKey)
    return null
  }

  /**
   * Save data to cache
   */
  const saveToCache = <T>(cacheKey: string, data: T, ttl: number): void => {
    cache.set(cacheKey, {
      data,
      timestamp: Date.now(),
      ttl,
    })
  }

  /**
   * Clear specific cache or all cache
   */
  const clearCache = (pattern?: string): void => {
    if (!pattern) {
      cache.clear()
      return
    }
    
    // Clear cache matching pattern
    for (const [key] of cache) {
      if (key.includes(pattern)) {
        cache.delete(key)
      }
    }
  }

  /**
   * Fetch data with caching
   */
  const fetchCached = async <T>(
    url: string,
    options?: any,
    cacheOptions: CacheOptions = {}
  ): Promise<T> => {
    const {
      ttl = 300, // Default: 5 minutes
      key,
      force = false,
    } = cacheOptions

    // Generate cache key
    const cacheKey = key || generateCacheKey(url, options)

    // Check cache first (unless force refresh)
    if (!force) {
      const cachedData = getFromCache<T>(cacheKey)
      if (cachedData !== null) {
        return cachedData
      }
    }

    // Fetch from API
    const fullUrl = getApiUrl(url)
    const response = await $fetch<T>(fullUrl, {
      credentials: 'include',
      ...options,
    })

    // Save to cache
    saveToCache(cacheKey, response, ttl)

    return response
  }

  /**
   * Fetch with stale-while-revalidate pattern
   * Returns cached data immediately, then updates in background
   */
  const fetchSWR = async <T>(
    url: string,
    options?: any,
    cacheOptions: CacheOptions = {}
  ): Promise<T> => {
    const {
      ttl = 300,
      key,
    } = cacheOptions

    const cacheKey = key || generateCacheKey(url, options)

    // Check cache
    const cachedData = getFromCache<T>(cacheKey)
    
    if (cachedData !== null) {
      // Return cached data immediately
      // Revalidate in background (non-blocking)
      fetchCached<T>(url, options, { ...cacheOptions, force: true }).catch(() => {
        // Silently fail background revalidation
      })
      
      return cachedData
    }

    // No cache, fetch normally
    return fetchCached<T>(url, options, cacheOptions)
  }

  /**
   * Prefetch and cache data (for critical resources)
   */
  const prefetch = async <T>(
    url: string,
    options?: any,
    cacheOptions: CacheOptions = {}
  ): Promise<void> => {
    try {
      await fetchCached<T>(url, options, cacheOptions)
    } catch (error) {
      // Silently fail prefetch
      console.warn('Prefetch failed:', url, error)
    }
  }

  /**
   * Get cache stats (for debugging)
   */
  const getCacheStats = () => {
    const entries = Array.from(cache.entries())
    const validEntries = entries.filter(([, entry]) => isCacheValid(entry))
    const expiredEntries = entries.filter(([, entry]) => !isCacheValid(entry))

    return {
      total: cache.size,
      valid: validEntries.length,
      expired: expiredEntries.length,
      size: entries.reduce((sum, [, entry]) => {
        return sum + JSON.stringify(entry.data).length
      }, 0),
    }
  }

  /**
   * Cleanup expired cache entries
   */
  const cleanupExpiredCache = (): number => {
    let removed = 0
    for (const [key, entry] of cache) {
      if (!isCacheValid(entry)) {
        cache.delete(key)
        removed++
      }
    }
    return removed
  }

  // Auto cleanup every 5 minutes
  if (typeof window !== 'undefined') {
    setInterval(() => {
      const removed = cleanupExpiredCache()
      if (removed > 0) {
        console.debug(`Cleaned up ${removed} expired cache entries`)
      }
    }, 5 * 60 * 1000)
  }

  return {
    fetchCached,
    fetchSWR,
    prefetch,
    clearCache,
    getCacheStats,
    cleanupExpiredCache,
  }
}
