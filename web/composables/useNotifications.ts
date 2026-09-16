export interface AppNotification {
  id: string
  type: 'like' | 'comment' | 'event_reminder' | 'general'
  data: {
    type?: string
    actor_id?: number | null
    actor_name?: string
    actor_photo?: string | null
    item_id?: number
    item_type?: string
    item_title?: string
    target_url?: string
    message?: string
    timeframe?: string
    comment_snippet?: string
  }
  read: boolean
  read_at: string | null
  created_at: string
}

export const useNotifications = () => {
  const { api } = useApi()
  const authStore = useAuthStore()

  const notifications = useState<AppNotification[]>('app_notifications', () => [])
  const unreadCount = useState<number>('app_unread_notifications_count', () => 0)
  const loading = useState<boolean>('app_notifications_loading', () => false)
  const hasBrowserPermission = ref(false)

  // Check browser notification permission
  const checkPermission = () => {
    if (typeof window !== 'undefined' && 'Notification' in window) {
      hasBrowserPermission.value = Notification.permission === 'granted'
    }
  }

  // Request browser notification permission
  const requestPermission = async () => {
    if (typeof window !== 'undefined' && 'Notification' in window) {
      try {
        const perm = await Notification.requestPermission()
        hasBrowserPermission.value = perm === 'granted'
        return perm === 'granted'
      } catch (err) {
        console.error('Error requesting notification permission:', err)
        return false
      }
    }
    return false
  }

  // Trigger native browser notification
  const triggerBrowserNotification = (title: string, body: string, url: string = '/') => {
    if (typeof window !== 'undefined' && 'Notification' in window && Notification.permission === 'granted') {
      try {
        const notif = new Notification(title, {
          body,
          icon: '/favicon.ico',
          badge: '/favicon.ico',
        })
        notif.onclick = () => {
          window.focus()
          navigateTo(url)
        }
      } catch (e) {
        console.error('Browser notification error:', e)
      }
    }
  }

  // Fetch notifications from backend
  const fetchNotifications = async (quiet: boolean = false) => {
    if (!authStore.isLoggedIn) {
      notifications.value = []
      unreadCount.value = 0
      return
    }

    if (!quiet) loading.value = true
    try {
      const prevCount = unreadCount.value
      const res = await api.get('/notifications')
      if (res.data) {
        notifications.value = res.data.notifications || []
        unreadCount.value = res.data.unread_count || 0

        // If new notifications arrived and count increased, notify via browser
        if (quiet && res.data.unread_count > prevCount && notifications.value.length > 0) {
          const newest = notifications.value[0]
          triggerBrowserNotification(
            'Jalan Bareng',
            newest.data?.message || 'Anda memiliki notifikasi baru!',
            newest.data?.target_url || '/'
          )
        }
      }
    } catch (err) {
      console.error('Failed to fetch notifications:', err)
    } finally {
      if (!quiet) loading.value = false
    }
  }

  // Mark single notification as read
  const markAsRead = async (id: string, targetUrl?: string) => {
    try {
      // Optimistic update
      const item = notifications.value.find(n => n.id === id)
      if (item && !item.read) {
        item.read = true
        unreadCount.value = Math.max(0, unreadCount.value - 1)
      }
      await api.post(`/notifications/${id}/read`)
      if (targetUrl) {
        navigateTo(targetUrl)
      }
    } catch (err) {
      console.error('Failed to mark notification as read:', err)
    }
  }

  // Mark all notifications as read
  const markAllAsRead = async () => {
    try {
      notifications.value.forEach(n => n.read = true)
      unreadCount.value = 0
      await api.post('/notifications/mark-all-read')
    } catch (err) {
      console.error('Failed to mark all notifications as read:', err)
    }
  }

  // Delete notification
  const deleteNotification = async (id: string) => {
    try {
      const idx = notifications.value.findIndex(n => n.id === id)
      if (idx !== -1) {
        if (!notifications.value[idx].read) {
          unreadCount.value = Math.max(0, unreadCount.value - 1)
        }
        notifications.value.splice(idx, 1)
      }
      await api.delete(`/notifications/${id}`)
    } catch (err) {
      console.error('Failed to delete notification:', err)
    }
  }

  return {
    notifications,
    unreadCount,
    loading,
    hasBrowserPermission,
    checkPermission,
    requestPermission,
    fetchNotifications,
    markAsRead,
    markAllAsRead,
    deleteNotification,
    triggerBrowserNotification,
  }
}
