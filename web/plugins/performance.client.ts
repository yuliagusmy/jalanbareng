/**
 * Performance monitoring plugin
 * Auto-initializes Core Web Vitals tracking on client side
 */

export default defineNuxtPlugin(() => {
  // Only run in production or if explicitly enabled
  const enablePerformanceMonitoring = process.env.NODE_ENV === 'production' || 
                                      process.env.ENABLE_PERFORMANCE_MONITORING === 'true'

  if (!enablePerformanceMonitoring) {
    return
  }

  const { initCoreWebVitals, logSummary } = usePerformance()

  // Initialize Core Web Vitals tracking
  initCoreWebVitals()

  // Log summary before page unload (for debugging)
  if (process.env.NODE_ENV === 'development') {
    window.addEventListener('beforeunload', () => {
      logSummary()
    })
  }

  // Optional: Send metrics to analytics service
  // Example: Google Analytics, Vercel Analytics, or custom endpoint
  // const { metrics } = usePerformance()
  // watch(metrics, (newMetrics) => {
  //   const lastMetric = newMetrics[newMetrics.length - 1]
  //   if (lastMetric) {
  //     // Send to analytics
  //     // gtag('event', lastMetric.name, { value: lastMetric.value })
  //   }
  // })
})
