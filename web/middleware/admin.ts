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

  // Check if user has management access (admin or community_admin)
  const isSuperAdmin = authStore.isAdmin
  const isCommunityAdmin = authStore.isCommunityAdmin

  if (!isSuperAdmin && !isCommunityAdmin) {
    console.log('Admin middleware - no management role, redirecting to home')
    return navigateTo('/')
  }

  // Super-admin only routes: users, categories, stories, settings, pages
  const superAdminOnlyRoutes = [
    '/manage/users',
    '/manage/categories',
    '/manage/stories',
    '/manage/settings',
    '/manage/pages'
  ]

  const isSuperAdminRoute = superAdminOnlyRoutes.some(route => to.path.startsWith(route))

  if (isSuperAdminRoute && !isSuperAdmin) {
    console.log('Admin middleware - community_admin tried to access super-admin route, redirecting to manage/activations')
    return navigateTo('/manage/activations')
  }

  console.log('Admin middleware - access granted')
})
