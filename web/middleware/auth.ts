export default defineNuxtRouteMiddleware((to, from) => {
  const authToken = useCookie('auth_token')

  // If not authenticated, redirect to login
  if (!authToken.value) {
    return navigateTo('/login')
  }
})
