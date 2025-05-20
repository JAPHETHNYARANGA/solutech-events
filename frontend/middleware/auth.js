// middleware/auth.js
export default defineNuxtRouteMiddleware(async (to) => {
    const authStore = useAuthStore()
    
    // If route requires auth and user isn't authenticated
    if (to.path.includes('/admin') && !authStore.isAuthenticated) {
      return navigateTo('/auth/login')
    }
    
    // If user is authenticated but tries to access auth pages
    if ((to.path === '/auth/login' || to.path === '/auth/register') && authStore.isAuthenticated) {
      return navigateTo(`/api/${authStore.organization.slug}/admin`)
    }
  })