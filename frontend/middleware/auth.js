export default defineNuxtRouteMiddleware(async (to) => {
    const authStore = useAuthStore()
    
    // On client side only
    if (process.client) {
      await authStore.initialize()
    }
  
    const isAdminRoute = to.path.startsWith('/admin') || to.path.includes('/admin')
    
    if (isAdminRoute) {
      if (!authStore.isAuthenticated) {
        return navigateTo('/auth/login')
      }
      
      if (!authStore.organization?.slug) {
        console.error('Organization slug missing')
        return navigateTo('/auth/login')
      }
    }
  })