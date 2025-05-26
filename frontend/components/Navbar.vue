<script setup>
import { useAuthStore } from '~/stores/auth'
import { navigateTo } from '#imports'

const authStore = useAuthStore()

const handleLogout = async () => {
  try {
    await authStore.logout()
    // Clear all localStorage items
    if (process.client) {
      localStorage.clear()
    }
    navigateTo('/')
  } catch (error) {
    console.error('Logout failed:', error)
  }
}
</script>

<template>
  <header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
      <h1 class="text-3xl font-bold text-gray-900">EventHub</h1>
      <div class="space-x-4">

        <NuxtLink to="/" class="text-indigo-600 hover:text-indigo-800">Home Page</NuxtLink>
        <template v-if="authStore.isAuthenticated">
          <NuxtLink 
            :to="`/${authStore.organization.slug}/admin`" 
            class="text-indigo-600 hover:text-indigo-800"
          >
            Dashboard
          </NuxtLink>
          <button 
            @click="handleLogout"
            class="text-indigo-600 hover:text-indigo-800"
          >
            Logout
          </button>
        </template>
        <template v-else>
          <NuxtLink to="/auth/login" class="text-indigo-600 hover:text-indigo-800">Login</NuxtLink>
          <NuxtLink to="/auth/register" class="text-indigo-600 hover:text-indigo-800">Register</NuxtLink>
          
        </template>
      </div>
    </div>
  </header>
</template>