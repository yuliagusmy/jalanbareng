export default defineNuxtPlugin(async () => {
  const authStore = useAuthStore()
  const authToken = useCookie('auth_token')

  // Kalau ada token di cookie tapi user di store belum terisi, fetch user data dulu
  // Ini penting agar middleware bisa membaca state yang benar (SSR maupun client)
  if (authToken.value && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch {
      // Token tidak valid, biarkan middleware handle redirect
    }
  }
})
