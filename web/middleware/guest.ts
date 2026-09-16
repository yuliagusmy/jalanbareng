export default defineNuxtRouteMiddleware((to, from) => {
  const authToken = useCookie('auth_token')

  // If authenticated, redirect to home
  if (authToken.value) {
    return navigateTo('/')
  }
})
