// stores/auth.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { API_BASE_URL, getAuthHeaders } from '~/utils/api'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const token = ref(null)
    const organization = ref(null)
    
    // Use computed for isAuthenticated to react to token changes
    const isAuthenticated = computed(() => !!token.value)
  
    const login = async (credentials) => {
      try {
        const response = await $fetch(`${API_BASE_URL}/auth/login`, {
          method: 'POST',
          body: credentials // No need for JSON.stringify with $fetch
        })
        
        // Set all auth state
    token.value = response.access_token
    user.value = response.admin
    organization.value = response.admin.organization  // Make sure this contains the slug
    
        // Store in localStorage - consistent format
        localStorage.setItem('authToken', response.access_token)
        localStorage.setItem('authUser', JSON.stringify(response.admin))
        localStorage.setItem('authOrganization', JSON.stringify(response.admin.organization))
            
        // Return full response for redirect handling
        return response
      } catch (error) {
        // Format error consistently
        throw new Error(error.data?.message || 'Login failed')
      }
    }

    const register = async (userData) => {
        try {
          const response = await $fetch(`${API_BASE_URL}/auth/register`, {
            method: 'POST',
            body: userData  // no need for JSON.stringify with $fetch
          })
      
          // Optional: store admin/organization if needed
          user.value = response.admin
          organization.value = response.organization
      
          // No token provided, so don't set it
          return response
        } catch (error) {
          throw error
        }
      }
      

  const logout = async () => {
    try {
      await $fetch(`${API_BASE_URL}/auth/logout`, {
        method: 'POST',
        headers: getAuthHeaders()
      })
      
      user.value = null
      token.value = null
      organization.value = null
      isAuthenticated.value = false
      
      localStorage.removeItem('authToken')
    } catch (error) {
      throw error
    }
  }

  return { user, token, organization, isAuthenticated, login, register, logout }
})