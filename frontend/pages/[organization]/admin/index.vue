// pages/[organization]/admin/index.vue
<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />
    
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Events</h1>
            <p class="mt-2 text-sm text-gray-700">Manage your organization's events</p>
          </div>
          <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <NuxtLink 
              :to="`/${organization.slug}/admin/events/new`"
              class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
            >
              Add event
            </NuxtLink>
          </div>
        </div>
        
        <div class="mt-8 flex flex-col">
          <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
              <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Title</th>
                      <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                      <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Venue</th>
                      <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Attendees</th>
                      <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Edit</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="event in events" :key="event.id">
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                        {{ event.title }}
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        {{ formatDate(event.date) }}
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        {{ event.venue }}
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        {{ event.attendees_count }} / {{ event.max_attendees }}
                      </td>
                      <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <NuxtLink 
                          :to="`/${organization.slug}/admin/events/${event.id}`"
                          class="text-indigo-600 hover:text-indigo-900 mr-4"
                        >
                          Edit
                        </NuxtLink>
                        <button 
                          @click="deleteEvent(event.id)"
                          class="text-red-600 hover:text-red-900"
                        >
                          Delete
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { fetchAdminEvents, deleteEvent } from '~/services/eventService'
import { useAuthStore } from '~/stores/auth'

const authStore = useAuthStore()
const organization = computed(() => authStore.organization)

const events = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    events.value = await fetchAdminEvents(organization.value.slug)
  } catch (error) {
    console.error('Failed to fetch events:', error)
  } finally {
    loading.value = false
  }
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const handleDeleteEvent = async (eventId) => {
  if (confirm('Are you sure you want to delete this event?')) {
    try {
      await deleteEvent(organization.value.slug, eventId)
      events.value = events.value.filter(event => event.id !== eventId)
    } catch (error) {
      console.error('Failed to delete event:', error)
    }
  }
}
</script>