<template>
    <div class="min-h-screen bg-gray-50">
      <Navbar />
      
      <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="mb-8 bg-white p-6 rounded-lg shadow">
          <label for="organization" class="block text-sm font-medium text-gray-700">Select Organization</label>
          <select 
            id="organization" 
            v-model="selectedOrganization"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
            @change="filterEvents"
          >
            <option value="">All Organizations</option>
            <option v-for="org in organizations" :key="org.id" :value="org.slug">{{ org.name }}</option>
          </select>
        </div>
  
        <div v-if="loading" class="flex justify-center items-center h-64">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
        </div>
  
        <div v-else-if="filteredEvents.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
          <p class="text-gray-500">No upcoming events found.</p>
        </div>
  
        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div 
            v-for="event in filteredEvents" 
            :key="event.id"
            class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition-shadow duration-300"
          >
            <div class="px-4 py-5 sm:p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                  <CalendarIcon class="h-6 w-6 text-white" />
                </div>
                <div class="ml-5 w-0 flex-1">
                  <h3 class="text-lg font-medium text-gray-900">{{ event.title }}</h3>
                  <p class="text-sm text-gray-500">{{ formatDate(event.date) }}</p>
                </div>
              </div>
              <div class="mt-4">
                <p class="text-sm text-gray-600 line-clamp-2">{{ event.description || 'No description provided' }}</p>
                <div class="mt-3 grid grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm font-medium text-gray-500">Venue</p>
                    <p class="text-sm text-gray-900">{{ event.venue }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-500">Price</p>
                    <p class="text-sm text-gray-900">{{ event.price ? `$${event.price}` : 'Free' }}</p>
                  </div>
                </div>
              </div>
              <div class="mt-5">
                <NuxtLink 
                  :to="`/${event.organization.slug}/events/${event.id}`"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  View Details
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </template>
  
  <script setup>
  import { CalendarIcon } from '@heroicons/vue/24/outline'
  
  const selectedOrganization = ref('')
  const loading = ref(true)
  const allEvents = ref([])
  const filteredEvents = ref([])
  const organizations = ref([])
  
  // Fetch events and organizations in parallel
  const { data: eventsData, error: eventsError } = await useFetch('http://127.0.0.1:8000/api/public/events')
  const { data: orgsData, error: orgsError } = await useFetch('http://127.0.0.1:8000/api/public/organizations')
  
  if (eventsError.value || orgsError.value) {
    console.error('Failed to fetch data:', eventsError.value || orgsError.value)
  } else {
    if (Array.isArray(eventsData.value)) {
      allEvents.value = eventsData.value
      filteredEvents.value = [...eventsData.value]
    }
    
    if (Array.isArray(orgsData.value)) {
      organizations.value = orgsData.value
    }
  }
  
  loading.value = false
  
  // Date formatting utility
  const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }
    return new Date(dateString).toLocaleDateString('en-US', options)
  }
  
  // Filter events based on selected organization
  const filterEvents = () => {
    if (!selectedOrganization.value) {
      filteredEvents.value = [...allEvents.value]
    } else {
      filteredEvents.value = allEvents.value.filter(
        event => event.organization.slug === selectedOrganization.value
      )
    }
  }
  </script>
  