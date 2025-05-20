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
              <button
                @click="openModal('create')"
                class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
              >
                Add event
              </button>
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
                          <span class="sr-only">Actions</span>
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <template v-if="loading">
                        <tr>
                          <td colspan="5" class="py-4 text-center text-sm text-gray-500">
                            Loading events...
                          </td>
                        </tr>
                      </template>
                      <template v-else-if="events.length === 0">
                        <tr>
                          <td colspan="5" class="py-4 text-center text-sm text-gray-500">
                            No events found
                          </td>
                        </tr>
                      </template>
                      <template v-else>
                        <tr v-for="event in events" :key="event?.id">
                          <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            {{ event?.title || 'Untitled Event' }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ event?.date ? formatDate(event.date) : 'Date not set' }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ event?.venue || 'Venue not set' }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ event?.attendees_count || 0 }} / {{ event?.max_attendees || 0 }}
                          </td>
                          <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <button
                              @click="openModal('edit', event)"
                              class="text-indigo-600 hover:text-indigo-900 mr-4"
                            >
                              Edit
                            </button>
                            <button 
                              @click="openModal('delete', event)"
                              class="text-red-600 hover:text-red-900 mr-4"
                            >
                              Delete
                            </button>
                            <button 
                              @click="viewAttendees(event)"
                              class="text-green-600 hover:text-green-900"
                            >
                              Attendees
                            </button>
                          </td>
                        </tr>
                      </template>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
  
      <!-- Event Management Modal -->
      <TransitionRoot as="template" :show="isModalOpen">
        <Dialog as="div" class="relative z-50" @close="closeModal">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="ease-in duration-200"
            leave-from="opacity-100"
            leave-to="opacity-0"
          >
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
          </TransitionChild>
  
          <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
              <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to="opacity-100 translate-y-0 sm:scale-100"
                leave="ease-in duration-200"
                leave-from="opacity-100 translate-y-0 sm:scale-100"
                leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              >
                <DialogPanel
                  class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                >
                  <!-- Modal Header -->
                  <div>
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100">
                      <CalendarIcon class="h-6 w-6 text-indigo-600" aria-hidden="true" />
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                      <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                        {{ modalTitle }}
                      </DialogTitle>
                    </div>
                  </div>
  
                  <!-- Create/Edit Form -->
                  <div v-if="modalAction !== 'delete'" class="mt-5">
                    <form @submit.prevent="handleSubmit">
                      <div class="space-y-4">
                        <div>
                          <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                          <input
                            type="text"
                            id="title"
                            v-model="form.title"
                            class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                            required
                          />
                        </div>
  
                        <div>
                          <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                          <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                          />
                        </div>
  
                        <div>
                          <label for="venue" class="block text-sm font-medium text-gray-700">Venue</label>
                          <input
                            type="text"
                            id="venue"
                            v-model="form.venue"
                            class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                            required
                          />
                        </div>
  
                        <div>
                          <label for="date" class="block text-sm font-medium text-gray-700">Date & Time</label>
                          <input
                            type="datetime-local"
                            id="date"
                            v-model="form.date"
                            class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                            required
                          />
                        </div>
  
                        <div>
                          <label for="price" class="block text-sm font-medium text-gray-700">Price ($)</label>
                          <input
                            type="number"
                            id="price"
                            v-model="form.price"
                            min="0"
                            step="0.01"
                            class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                            required
                          />
                        </div>
  
                        <div>
                          <label for="max_attendees" class="block text-sm font-medium text-gray-700">Max Attendees</label>
                          <input
                            type="number"
                            id="max_attendees"
                            v-model="form.max_attendees"
                            min="1"
                            class="mt-1 block w-full rounded-md border border-gray-300 py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                            required
                          />
                        </div>
                      </div>
  
                      <!-- Form Actions -->
                      <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button
                          type="button"
                          class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:col-span-1 sm:mt-0 sm:text-sm"
                          @click="closeModal"
                        >
                          Cancel
                        </button>
                        <button
                          type="submit"
                          :disabled="isSubmitting"
                          class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 sm:col-span-1 sm:text-sm"
                        >
                          <span v-if="isSubmitting">
                            <ArrowPathIcon class="animate-spin h-4 w-4 mr-2 inline" />
                            Processing...
                          </span>
                          <span v-else>{{ modalAction === 'create' ? 'Create' : 'Update' }}</span>
                        </button>
                      </div>
                    </form>
                  </div>
  
                  <!-- Delete Confirmation -->
                  <div v-else class="mt-5">
                    <p class="text-sm text-gray-500">Are you sure you want to delete "{{ selectedEvent?.title }}"? This action cannot be undone.</p>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                      <button
                        type="button"
                        class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:col-span-1 sm:mt-0 sm:text-sm"
                        @click="closeModal"
                      >
                        Cancel
                      </button>
                      <button
                        type="button"
                        @click="handleDelete"
                        :disabled="isSubmitting"
                        class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 sm:col-span-1 sm:text-sm"
                      >
                        <span v-if="isSubmitting">
                          <ArrowPathIcon class="animate-spin h-4 w-4 mr-2 inline" />
                          Deleting...
                        </span>
                        <span v-else>Delete</span>
                      </button>
                    </div>
                  </div>
                </DialogPanel>
              </TransitionChild>
            </div>
          </div>
        </Dialog>
      </TransitionRoot>
  
      <!-- Attendee Management Modal -->
      <TransitionRoot as="template" :show="isAttendeeModalOpen">
        <Dialog as="div" class="relative z-50" @close="closeAttendeeModal">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="ease-in duration-200"
            leave-from="opacity-100"
            leave-to="opacity-0"
          >
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
          </TransitionChild>
  
          <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
              <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to="opacity-100 translate-y-0 sm:scale-100"
                leave="ease-in duration-200"
                leave-from="opacity-100 translate-y-0 sm:scale-100"
                leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              >
                <DialogPanel
                  class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl sm:p-6"
                >
                  <div>
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                      <UsersIcon class="h-6 w-6 text-green-600" aria-hidden="true" />
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                      <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                        Attendees for {{ selectedEvent?.title }}
                      </DialogTitle>
                      <p class="mt-2 text-sm text-gray-500">
                        {{ attendees.length }} registered attendees
                      </p>
                    </div>
                  </div>
  
                  <div class="mt-5">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                      <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                          <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Phone</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Registered At</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                              <span class="sr-only">Actions</span>
                            </th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                          <template v-if="attendeesLoading">
                            <tr>
                              <td colspan="5" class="py-4 text-center text-sm text-gray-500">
                                Loading attendees...
                              </td>
                            </tr>
                          </template>
                          <template v-else-if="attendees.length === 0">
                            <tr>
                              <td colspan="5" class="py-4 text-center text-sm text-gray-500">
                                No attendees found
                              </td>
                            </tr>
                          </template>
                          <template v-else>
                            <tr v-for="attendee in attendees" :key="attendee.id">
                              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                {{ attendee.name }}
                              </td>
                              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {{ attendee.email }}
                              </td>
                              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {{ attendee.phone || 'N/A' }}
                              </td>
                              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {{ formatDate(attendee.created_at) }}
                              </td>
                              <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <button
                                  @click="confirmDeleteAttendee(attendee)"
                                  class="text-red-600 hover:text-red-900"
                                >
                                  Remove
                                </button>
                              </td>
                            </tr>
                          </template>
                        </tbody>
                      </table>
                    </div>
                  </div>
  
                  <div class="mt-5 sm:mt-6">
                    <button
                      type="button"
                      class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:text-sm"
                      @click="closeAttendeeModal"
                    >
                      Close
                    </button>
                  </div>
                </DialogPanel>
              </TransitionChild>
            </div>
          </div>
        </Dialog>
      </TransitionRoot>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue'
  import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
  } from '@headlessui/vue'
  import { CalendarIcon, ArrowPathIcon, UsersIcon } from '@heroicons/vue/24/outline'
  import { fetchAdminEvents, createEvent, updateEvent, deleteEvent } from '~/services/eventService'
  import { fetchEventAttendees, deleteAttendee } from '~/services/attendeeService'
  
  const organization = ref(null)
  
  if (process.client) {
    organization.value = JSON.parse(localStorage.getItem('authOrganization'))
  }
  
  // Events data
  const events = ref([])
  const loading = ref(true)
  
  // Event modal state
  const isModalOpen = ref(false)
  const modalAction = ref('create')
  const selectedEvent = ref(null)
  const isSubmitting = ref(false)
  
  // Attendee modal state
  const isAttendeeModalOpen = ref(false)
  const attendees = ref([])
  const attendeesLoading = ref(false)
  
  // Form data
  const form = ref({
    title: '',
    description: '',
    venue: '',
    date: '',
    price: 0,
    max_attendees: 10
  })
  
  // Computed modal title
  const modalTitle = computed(() => {
    switch (modalAction.value) {
      case 'create': return 'Create New Event'
      case 'edit': return 'Edit Event'
      case 'delete': return 'Delete Event'
      default: return 'Event Management'
    }
  })
  
  onMounted(async () => {
    try {
      if (process.client) {
        const orgData = localStorage.getItem('authOrganization')
        organization.value = orgData ? JSON.parse(orgData) : null
  
        const orgSlug = organization.value?.slug
        if (!orgSlug) {
          console.error('Organization slug is missing — redirecting to login')
          return navigateTo('/auth/login')
        }
  
        const response = await fetchAdminEvents(orgSlug)
        events.value = Array.isArray(response.original) ? response.original : []
      }
    } catch (error) {
      console.error('Failed to fetch events:', error?.message || error)
      events.value = []
    } finally {
      loading.value = false
    }
  })
  
  // Format date for display
  const formatDate = (dateString) => {
    if (!dateString) return 'Date not set'
    
    try {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    } catch (e) {
      console.error('Invalid date format:', dateString)
      return 'Invalid date'
    }
  }
  
  // Event modal methods
  const openModal = (action, event = null) => {
    modalAction.value = action
    selectedEvent.value = event
  
    if (action === 'edit' && event) {
      form.value = {
        title: event.title || '',
        description: event.description || '',
        venue: event.venue || '',
        date: event.date ? event.date.slice(0, 16) : '',
        price: event.price || 0,
        max_attendees: event.max_attendees || 10
      }
    } else if (action === 'create') {
      form.value = {
        title: '',
        description: '',
        venue: '',
        date: '',
        price: 0,
        max_attendees: 10
      }
    }
  
    isModalOpen.value = true
  }
  
  const closeModal = () => {
    isModalOpen.value = false
  }
  
  const handleSubmit = async () => {
    isSubmitting.value = true
  
    try {
      if (!organization.value?.slug) {
        throw new Error('Organization information not available')
      }
  
      // Strip the time part from the date to ensure only the date is sent
      const eventData = { ...form.value }
      if (eventData.date) {
        eventData.date = eventData.date.split('T')[0]  // Extract only the date part (YYYY-MM-DD)
      }
  
      if (modalAction.value === 'create') {
        const newEvent = await createEvent(organization.value.slug, eventData)
        if (newEvent) {
          events.value = [...(events.value || []), newEvent]
        }
      } else if (modalAction.value === 'edit' && selectedEvent.value?.id) {
        const updatedEvent = await updateEvent(
          organization.value.slug, 
          selectedEvent.value.id, 
          eventData
        )
        if (updatedEvent) {
          events.value = (events.value || []).map(e => 
            e?.id === selectedEvent.value?.id ? updatedEvent : e
          )
        }
      }
  
      closeModal()
    } catch (error) {
      console.error('Failed to save event:', error)
      alert(`Failed to save event: ${error.message}`)
    } finally {
      isSubmitting.value = false
    }
  }
  
  const handleDelete = async () => {
    isSubmitting.value = true
  
    try {
      if (!organization.value?.slug || !selectedEvent.value?.id) {
        throw new Error('Missing required information')
      }
  
      await deleteEvent(organization.value.slug, selectedEvent.value.id)
      events.value = (events.value || []).filter(e => e?.id !== selectedEvent.value?.id)
      closeModal()
    } catch (error) {
      console.error('Failed to delete event:', error)
      alert(`Failed to delete event: ${error.message}`)
    } finally {
      isSubmitting.value = false
    }
  }
  
  // Attendee modal methods
  const viewAttendees = async (event) => {
    selectedEvent.value = event
    isAttendeeModalOpen.value = true
    attendeesLoading.value = true
    
    try {
      const response = await fetchEventAttendees(organization.value.slug, event.id)
      attendees.value = response.original || response || []
    } catch (error) {
      console.error('Error fetching attendees:', error)
      attendees.value = []
    } finally {
      attendeesLoading.value = false
    }
  }
  
  const closeAttendeeModal = () => {
    isAttendeeModalOpen.value = false
    attendees.value = []
  }
  
  const confirmDeleteAttendee = async (attendee) => {
    if (confirm(`Are you sure you want to remove ${attendee.name} from this event?`)) {
      try {
        await deleteAttendee(organization.value.slug, selectedEvent.value.id, attendee.id)
        attendees.value = attendees.value.filter(a => a.id !== attendee.id)
      } catch (error) {
        console.error('Error deleting attendee:', error)
        alert('Failed to remove attendee')
      }
    }
  }
  </script>