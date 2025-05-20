<template>
    <div v-if="loading" class="flex justify-center items-center h-screen">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
    </div>
    
    <div v-else-if="error" class="text-center py-12">
      <p class="text-red-500">Error loading event details</p>
      <NuxtLink to="/" class="text-indigo-600 hover:text-indigo-800 mt-4 inline-block">
        Back to home
      </NuxtLink>
    </div>
    
    <div v-else class="min-h-screen bg-gray-50">
      <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <NuxtLink 
            to="/" 
            class="text-indigo-600 hover:text-indigo-800 flex items-center"
            >
            <ArrowLeftIcon class="h-5 w-5 mr-1" />
            Back to events
            </NuxtLink>
        </div>
      </header>
  
      <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden rounded-lg">
          <div class="px-4 py-5 sm:px-6 bg-indigo-700 text-white">
            <h1 class="text-2xl font-bold">{{ event.title }}</h1>
            <p class="mt-1">{{ formatDate(event.date) }}</p>
          </div>
          
          <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ event.description }}</dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Venue</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ event.venue }}</dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ formatDate(event.date) }}</dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Price</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ event.price ? `$${event.price}` : 'Free' }}</dd>
              </div>
              <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                <dt class="text-sm font-medium text-gray-500">Available Spots</dt>
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                  {{ event.max_attendees - registeredAttendees.length }} of {{ event.max_attendees }} remaining
                </dd>
              </div>
            </dl>
          </div>
  
          <div class="px-4 py-4 sm:px-6 bg-gray-50 flex justify-end">
            <NuxtLink 
            :to="`/${organization}/events/${id}/register`"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
            Register Now
            </NuxtLink>
          </div>
        </div>
      </main>
    </div>
  </template>
  
  <script setup>
  import { ArrowLeftIcon } from '@heroicons/vue/24/outline'
  
  const route = useRoute()
  const { organization, id } = route.params
  
  const event = ref(null)
  const loading = ref(true)
  const error = ref(null)
  const registeredAttendees = ref([])
  
  onMounted(async () => {
    try {
      // Correct API endpoint with organization slug
      const { data, error: fetchError } = await useFetch(
        `http://127.0.0.1:8000/api/${organization}/public/events/${id}`
      )
      
      if (fetchError.value) {
        throw fetchError.value
      }
  
      event.value = data.value
      
      // Mock registered attendees (replace with actual API call)
      registeredAttendees.value = Array.from(
        { length: Math.floor(Math.random() * event.value.max_attendees) }, 
        (_, i) => ({
          id: i + 1,
          name: `Attendee ${i + 1}`
        })
      )
    } catch (err) {
      error.value = err
      console.error('Failed to fetch event details:', err)
    } finally {
      loading.value = false
    }
  })
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }
  </script>