/**
 * Performance monitoring composable
 * Tracks Core Web Vitals and custom metrics
 */

interface PerformanceMetric {
  name: string
  value: number
  rating: 'good' | 'needs-improvement' | 'poor'
  timestamp: number
}

export const usePerformance = () => {
  const metrics = ref<PerformanceMetric[]>([])

  /**
   * Get rating based on Core Web Vitals thresholds
   */
  const getRating = (name: string, value: number): 'good' | 'needs-improvement' | 'poor' => {
    const thresholds: Record<string, { good: number; poor: number }> = {
      'LCP': { good: 2500, poor: 4000 },
      'FID': { good: 100, poor: 300 },
      'CLS': { good: 0.1, poor: 0.25 },
      'FCP': { good: 1800, poor: 3000 },
      'TTFB': { good: 800, poor: 1800 },
      'INP': { good: 200, poor: 500 },
    }

    const threshold = thresholds[name]
    if (!threshold) return 'good'

    if (value <= threshold.good) return 'good'
    if (value <= threshold.poor) return 'needs-improvement'
    return 'poor'
  }

  /**
   * Record a metric
   */
  const recordMetric = (name: string, value: number) => {
    const metric: PerformanceMetric = {
      name,
      value,
      rating: getRating(name, value),
      timestamp: Date.now(),
    }
    
    metrics.value.push(metric)
    
    // Log to console in development
    if (process.env.NODE_ENV === 'development') {
      console.log(`[Performance] ${name}: ${value.toFixed(2)}ms (${metric.rating})`)
    }
    
    // Send to analytics (implement if needed)
    // sendToAnalytics(metric)
  }

  /**
   * Measure Largest Contentful Paint (LCP)
   */
  const measureLCP = () => {
    if (typeof window === 'undefined' || !('PerformanceObserver' in window)) return

    try {
      const observer = new PerformanceObserver((list) => {
        const entries = list.getEntries()
        const lastEntry = entries[entries.length - 1] as any
        
        if (lastEntry && lastEntry.renderTime) {
          recordMetric('LCP', lastEntry.renderTime)
        }
      })
      
      observer.observe({ entryTypes: ['largest-contentful-paint'] })
    } catch (error) {
      console.warn('LCP measurement failed:', error)
    }
  }

  /**
   * Measure First Input Delay (FID)
   */
  const measureFID = () => {
    if (typeof window === 'undefined' || !('PerformanceObserver' in window)) return

    try {
      const observer = new PerformanceObserver((list) => {
        const entries = list.getEntries()
        entries.forEach((entry: any) => {
          if (entry.processingStart && entry.startTime) {
            const fid = entry.processingStart - entry.startTime
            recordMetric('FID', fid)
          }
        })
      })
      
      observer.observe({ entryTypes: ['first-input'] })
    } catch (error) {
      console.warn('FID measurement failed:', error)
    }
  }

  /**
   * Measure Cumulative Layout Shift (CLS)
   */
  const measureCLS = () => {
    if (typeof window === 'undefined' || !('PerformanceObserver' in window)) return

    try {
      let clsValue = 0
      const observer = new PerformanceObserver((list) => {
        const entries = list.getEntries()
        entries.forEach((entry: any) => {
          if (!entry.hadRecentInput) {
            clsValue += entry.value
            recordMetric('CLS', clsValue)
          }
        })
      })
      
      observer.observe({ entryTypes: ['layout-shift'] })
    } catch (error) {
      console.warn('CLS measurement failed:', error)
    }
  }

  /**
   * Measure First Contentful Paint (FCP)
   */
  const measureFCP = () => {
    if (typeof window === 'undefined' || !('PerformanceObserver' in window)) return

    try {
      const observer = new PerformanceObserver((list) => {
        const entries = list.getEntries()
        entries.forEach((entry: any) => {
          if (entry.name === 'first-contentful-paint') {
            recordMetric('FCP', entry.startTime)
          }
        })
      })
      
      observer.observe({ entryTypes: ['paint'] })
    } catch (error) {
      console.warn('FCP measurement failed:', error)
    }
  }

  /**
   * Measure Time to First Byte (TTFB)
   */
  const measureTTFB = () => {
    if (typeof window === 'undefined' || !window.performance) return

    try {
      const navigationTiming = performance.getEntriesByType('navigation')[0] as any
      if (navigationTiming && navigationTiming.responseStart) {
        const ttfb = navigationTiming.responseStart - navigationTiming.requestStart
        recordMetric('TTFB', ttfb)
      }
    } catch (error) {
      console.warn('TTFB measurement failed:', error)
    }
  }

  /**
   * Measure Interaction to Next Paint (INP)
   */
  const measureINP = () => {
    if (typeof window === 'undefined' || !('PerformanceObserver' in window)) return

    try {
      let maxDuration = 0
      const observer = new PerformanceObserver((list) => {
        const entries = list.getEntries()
        entries.forEach((entry: any) => {
          if (entry.duration > maxDuration) {
            maxDuration = entry.duration
            recordMetric('INP', maxDuration)
          }
        })
      })
      
      observer.observe({ entryTypes: ['event'] })
    } catch (error) {
      console.warn('INP measurement failed:', error)
    }
  }

  /**
   * Custom performance mark
   */
  const mark = (name: string) => {
    if (typeof window === 'undefined' || !window.performance) return
    performance.mark(name)
  }

  /**
   * Measure time between two marks
   */
  const measure = (name: string, startMark: string, endMark?: string) => {
    if (typeof window === 'undefined' || !window.performance) return

    try {
      if (endMark) {
        performance.measure(name, startMark, endMark)
      } else {
        performance.measure(name, startMark)
      }
      
      const measure = performance.getEntriesByName(name)[0]
      if (measure) {
        recordMetric(name, measure.duration)
      }
    } catch (error) {
      console.warn('Performance measure failed:', error)
    }
  }

  /**
   * Get all recorded metrics
   */
  const getMetrics = () => {
    return metrics.value
  }

  /**
   * Get metrics summary (averages, counts by rating)
   */
  const getMetricsSummary = () => {
    const summary: Record<string, {
      avg: number
      min: number
      max: number
      count: number
      good: number
      needsImprovement: number
      poor: number
    }> = {}

    metrics.value.forEach((metric) => {
      if (!summary[metric.name]) {
        summary[metric.name] = {
          avg: 0,
          min: Infinity,
          max: -Infinity,
          count: 0,
          good: 0,
          needsImprovement: 0,
          poor: 0,
        }
      }

      const s = summary[metric.name]
      s.avg = (s.avg * s.count + metric.value) / (s.count + 1)
      s.min = Math.min(s.min, metric.value)
      s.max = Math.max(s.max, metric.value)
      s.count++
      
      if (metric.rating === 'good') s.good++
      else if (metric.rating === 'needs-improvement') s.needsImprovement++
      else s.poor++
    })

    return summary
  }

  /**
   * Clear all metrics
   */
  const clearMetrics = () => {
    metrics.value = []
  }

  /**
   * Log performance summary to console
   */
  const logSummary = () => {
    const summary = getMetricsSummary()
    console.table(summary)
  }

  /**
   * Initialize all Core Web Vitals measurements
   */
  const initCoreWebVitals = () => {
    measureLCP()
    measureFID()
    measureCLS()
    measureFCP()
    measureTTFB()
    measureINP()
  }

  // Auto-initialize on client side
  if (process.client) {
    onMounted(() => {
      initCoreWebVitals()
    })
  }

  return {
    metrics,
    recordMetric,
    mark,
    measure,
    getMetrics,
    getMetricsSummary,
    clearMetrics,
    logSummary,
    initCoreWebVitals,
  }
}
