<template>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
     
      <Navbar />
      <!-- Main Content -->
      <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Organization Selector -->
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
  
        <!-- Events Grid -->
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
                <p class="text-sm text-gray-600 line-clamp-2">{{ event.description }}</p>
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
                  :to="`/${event.organization_slug}/events/${event.id}`"
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
  import Navbar from '~/components/Navbar.vue'
  // Dummy data
  const organizations = ref([
    { id: 1, name: 'Tech Innovators', slug: 'tech-innovators' },
    { id: 2, name: 'Business Network', slug: 'business-network' },
    { id: 3, name: 'Creative Minds', slug: 'creative-minds' }
  ])
  
  const allEvents = ref([
    {
      id: 1,
      organization_id: 1,
      organization_slug: 'tech-innovators',
      title: 'Future of AI Conference',
      description: 'Join us to explore the latest advancements in artificial intelligence and machine learning.',
      venue: 'Convention Center, Nairobi',
      date: '2025-06-15T09:00:00',
      price: 50,
      max_attendees: 200
    },
    {
      id: 2,
      organization_id: 2,
      organization_slug: 'business-network',
      title: 'Startup Pitch Night',
      description: 'Local startups pitch their ideas to potential investors.',
      venue: 'Innovation Hub, Westlands',
      date: '2025-06-20T18:00:00',
      price: 20,
      max_attendees: 150
    },
    {
      id: 3,
      organization_id: 1,
      organization_slug: 'tech-innovators',
      title: 'Web Development Workshop',
      description: 'Hands-on workshop covering modern web development techniques.',
      venue: 'Tech Campus, Kilimani',
      date: '2025-07-05T10:00:00',
      price: 0,
      max_attendees: 50
    },
    {
      id: 4,
      organization_id: 3,
      organization_slug: 'creative-minds',
      title: 'Art Exhibition Opening',
      description: 'Showcasing works from emerging African artists.',
      venue: 'National Gallery',
      date: '2025-06-25T19:00:00',
      price: 10,
      max_attendees: 300
    }
  ])
  
  // State
  const selectedOrganization = ref('')
  const loading = ref(false)
  const filteredEvents = ref([...allEvents.value])
  
  // Filter events based on selected organization
  const filterEvents = () => {
    loading.value = true
    setTimeout(() => {
      if (!selectedOrganization.value) {
        filteredEvents.value = [...allEvents.value]
      } else {
        filteredEvents.value = allEvents.value.filter(
          event => event.organization_slug === selectedOrganization.value
        )
      }
      loading.value = false
    }, 500)
  }
  
  // Helper function to format date
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