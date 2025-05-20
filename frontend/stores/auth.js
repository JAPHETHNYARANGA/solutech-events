// stores/auth.js
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { API_BASE_URL, getAuthHeaders } from '~/utils/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)
  const organization = ref(null)
  const isAuthenticated = ref(false)

  const login = async (credentials) => {
    try {
      const response = await $fetch(`${API_BASE_URL}/auth/login`, {
        method: 'POST',
        body: JSON.stringify(credentials)
      })
      
      user.value = response.admin
      token.value = response.access_token
      organization.value = response.admin.organization
      isAuthenticated.value = true
      
      localStorage.setItem('authToken', response.access_token)
      return response
    } catch (error) {
      throw error
    }
  }

  const register = async (userData) => {
    try {
      const response = await $fetch(`${API_BASE_URL}/auth/register`, {
        method: 'POST',
        body: JSON.stringify(userData)
      })
      
      user.value = response.admin
      token.value = response.access_token
      organization.value = response.organization
      isAuthenticated.value = true
      
      localStorage.setItem('authToken', response.access_token)
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