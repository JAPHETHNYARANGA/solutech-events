import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)
  const organization = ref(null)

  const isAuthenticated = computed(() => !!token.value)

  const login = async (credentials) => {
    const response = await $fetch(`${API_BASE_URL}/auth/login`, {
      method: 'POST',
      body: credentials
    })
    
    token.value = response.access_token
    user.value = response.admin
    organization.value = response.admin.organization

    localStorage.setItem('authToken', response.access_token)
    localStorage.setItem('authUser', JSON.stringify(response.admin))
    localStorage.setItem('authOrganization', JSON.stringify(response.admin.organization))

    return response
  }

  const register = async (userData) => {
    try {
      const response = await $fetch(`${API_BASE_URL}/auth/register`, {
        method: 'POST',
        body: userData  // no need for JSON.stringify with $fetch
      })
  
      user.value = response.admin
      organization.value = response.organization
  

      return response
    } catch (error) {
      throw error
    }
  }

  const logout = async () => {
    await $fetch(`${API_BASE_URL}/auth/logout`, {
      method: 'POST',
      headers: getAuthHeaders()
    })

    token.value = null
    user.value = null
    organization.value = null

    localStorage.clear()
  }


  const initialize = () => {
    const savedToken = localStorage.getItem('authToken')
    const savedUser = localStorage.getItem('authUser')
    const savedOrg = localStorage.getItem('authOrganization')

    if (savedToken && savedUser && savedOrg) {
      token.value = savedToken
      user.value = JSON.parse(savedUser)
      organization.value = JSON.parse(savedOrg)
    }
  }

  return {
    user,
    token,
    organization,
    isAuthenticated,
    login,
    logout,
    initialize,
    register 
  }
})
