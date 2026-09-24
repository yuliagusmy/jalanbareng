export default defineNuxtPlugin(() => {
  if (import.meta.client) {
    const fallbackMap = {
      event: 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=800&auto=format&fit=crop&q=80',
      destination: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&auto=format&fit=crop&q=80',
      user: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
      default: 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?w=800&auto=format&fit=crop&q=80',
    }

    // Capture phase error listener for all <img> tags across the app
    window.addEventListener(
      'error',
      (event) => {
        const target = event.target as HTMLElement | null
        if (target && target.tagName === 'IMG') {
          const img = target as HTMLImageElement
          // Avoid infinite loops if the fallback itself ever fails
          if (!img.dataset.hasFallback) {
            img.dataset.hasFallback = 'true'
            const currentSrc = (img.src || '').toLowerCase()
            
            if (currentSrc.includes('event')) {
              img.src = fallbackMap.event
            } else if (currentSrc.includes('destination') || currentSrc.includes('pantai') || currentSrc.includes('coto')) {
              img.src = fallbackMap.destination
            } else if (currentSrc.includes('user') || currentSrc.includes('avatar') || currentSrc.includes('profile')) {
              img.src = fallbackMap.user
            } else {
              img.src = fallbackMap.default
            }
          }
        }
      },
      true // capture phase is required because resource loading errors do not bubble
    )
  }
})
