export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()

  console.log('Admin middleware - isLoggedIn:', authStore.isLoggedIn)
  console.log('Admin middleware - isAdmin:', authStore.isAdmin)
  console.log('Admin middleware - user:', authStore.user)

  // Check if user is authenticated
  if (!authStore.isLoggedIn) {
    console.log('Admin middleware - redirecting to login')
    return navigateTo('/login')
  }

  // Check if user is admin
  if (!authStore.isAdmin) {
    console.log('Admin middleware - not admin, redirecting to home')
    // Redirect non-admin users to home with error message
    return navigateTo('/')
  }

  console.log('Admin middleware - access granted')
})
