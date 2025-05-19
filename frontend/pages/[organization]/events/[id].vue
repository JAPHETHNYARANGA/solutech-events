<template>
    <div class="min-h-screen bg-gray-50">
      <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <NuxtLink 
            :to="`/${event.organization_slug}/events`" 
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
                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ event.max_attendees - registeredAttendees.length }} of {{ event.max_attendees }} remaining</dd>
              </div>
            </dl>
          </div>
  
          <div class="px-4 py-4 sm:px-6 bg-gray-50 flex justify-end">
            <NuxtLink 
              :to="`/${event.organization_slug}/events/${event.id}/register`"
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
  
  // Dummy data - in a real app this would come from an API
  const event = {
    id: +id,
    organization_id: 1,
    organization_slug: organization,
    title: 'Future of AI Conference',
    description: 'Join us to explore the latest advancements in artificial intelligence and machine learning. This conference will feature talks from industry leaders, hands-on workshops, and networking opportunities.',
    venue: 'Convention Center, Nairobi',
    date: '2025-06-15T09:00:00',
    price: 50,
    max_attendees: 200
  }
  
  const registeredAttendees = ref([
    { id: 1, name: 'John Doe', email: 'john@example.com', phone: '0712345678' },
    { id: 2, name: 'Jane Smith', email: 'jane@example.com', phone: '0723456789' }
  ])
  
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