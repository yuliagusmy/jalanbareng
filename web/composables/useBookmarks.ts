import { ref, computed } from 'vue'

export interface BookmarkedDestination {
  id: number | string
  name: string
  slug?: string
  primary_photo?: string
  category_name?: string
  category?: { id?: number; name?: string }
  likes_count?: number
  comments_count?: number
  saved_at?: string
}

// Global reactive state across components
const bookmarks = ref<BookmarkedDestination[]>([])
const isInitialized = ref(false)

export function useBookmarks() {
  const initBookmarks = () => {
    if (typeof window === 'undefined' || isInitialized.value) return
    try {
      const stored = localStorage.getItem('jb_saved_destinations')
      if (stored) {
        bookmarks.value = JSON.parse(stored)
      }
    } catch (e) {
      console.warn('Failed to parse saved destinations from localStorage', e)
    } finally {
      isInitialized.value = true
    }
  }

  const persist = () => {
    if (typeof window === 'undefined') return
    try {
      localStorage.setItem('jb_saved_destinations', JSON.stringify(bookmarks.value))
    } catch (e) {
      console.warn('Failed to save destinations to localStorage', e)
    }
  }

  const isBookmarked = (id: number | string): boolean => {
    initBookmarks()
    return bookmarks.value.some(item => String(item.id) === String(id))
  }

  const toggleBookmark = (dest: any): boolean => {
    initBookmarks()
    const id = dest.id
    const index = bookmarks.value.findIndex(item => String(item.id) === String(id))

    if (index > -1) {
      bookmarks.value.splice(index, 1)
      persist()
      return false // Removed
    } else {
      const newEntry: BookmarkedDestination = {
        id: dest.id,
        name: dest.name,
        slug: dest.slug,
        primary_photo: dest.primary_photo,
        category_name: dest.category?.name || dest.category_name,
        category: dest.category,
        likes_count: dest.likes_count || 0,
        comments_count: dest.comments_count || 0,
        saved_at: new Date().toISOString()
      }
      bookmarks.value.unshift(newEntry)
      persist()
      return true // Added
    }
  }

  const removeBookmark = (id: number | string) => {
    initBookmarks()
    bookmarks.value = bookmarks.value.filter(item => String(item.id) !== String(id))
    persist()
  }

  const bookmarksCount = computed(() => {
    initBookmarks()
    return bookmarks.value.length
  })

  // Initialize on first call
  initBookmarks()

  return {
    bookmarks,
    bookmarksCount,
    isBookmarked,
    toggleBookmark,
    removeBookmark,
    initBookmarks
  }
}
