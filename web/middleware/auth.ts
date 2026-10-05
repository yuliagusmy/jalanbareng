export default defineNuxtRouteMiddleware(async (to, from) => {
  const authStore = useAuthStore()
  const authToken = useCookie('auth_token')

  if (!authToken.value) {
    return navigateTo('/login')
  }

  // Jika cookie ada tapi user di store belum terisi, coba fetch dulu
  if (authToken.value && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch {
      return navigateTo('/login')
    }
  }
})
