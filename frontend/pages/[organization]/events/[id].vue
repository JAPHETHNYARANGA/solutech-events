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
  
          <div class="px-4 py-4 sm:px-6 bg-gray-50 flex justify-end" v-if="event">
            <button 
              @click="showModal = true"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Register Now
            </button>
          </div>
        </div>
      </main>
  
      <!-- Registration Modal -->
    <div v-if="showModal" class="fixed z-50 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showModal = false"></div>
        
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-xl transform transition-all max-w-lg w-full mx-4">
        <div class="px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
            Register for {{ event.title }}
            </h3>
            
            <form @submit.prevent="submitRegistration">
            <div class="space-y-4">
                <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input 
                    type="text" 
                    id="name" 
                    v-model="form.name"
                    class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required
                >
                </div>

                <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    v-model="form.email"
                    class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required
                >
                </div>

                <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input 
                    type="tel" 
                    id="phone" 
                    v-model="form.phone"
                    class="mt-1 block w-full rounded-md border border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required
                >
                </div>

                <div class="flex items-center">
                <input 
                    id="terms" 
                    type="checkbox" 
                    v-model="form.agreeTerms"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    required
                >
                <label for="terms" class="ml-2 block text-sm text-gray-700">
                    I agree to the terms and conditions
                </label>
                </div>
            </div>

            <!-- Success/Error messages -->
            <div v-if="successMessage" class="mt-4 rounded-md bg-green-50 p-4">
                <div class="flex">
                <div class="flex-shrink-0">
                    <CheckCircleIcon class="h-5 w-5 text-green-400" />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
                </div>
                </div>
            </div>

            <div v-if="errorMessage" class="mt-4 rounded-md bg-red-50 p-4">
                <div class="flex">
                <div class="flex-shrink-0">
                    <XCircleIcon class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
                </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button 
                type="button" 
                @click="showModal = false"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                Cancel
                </button>
                <button 
                type="submit" 
                :disabled="registrationLoading"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                >
                <span v-if="registrationLoading">
                    <ArrowPathIcon class="animate-spin h-4 w-4 mr-2 inline" />
                    Processing...
                </span>
                <span v-else>Register</span>
                </button>
            </div>
            </form>
        </div>
        </div>
    </div>
    </div>
    </div>
  </template>
  
  
  <script setup>
import { ArrowLeftIcon, CheckCircleIcon, XCircleIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const { organization, id } = route.params

const event = ref(null)
const loading = ref(true)
const error = ref(null)
const registeredAttendees = ref([])

// Modal state
const showModal = ref(false)

// Form state
const form = reactive({
  name: '',
  email: '',
  phone: '',
  agreeTerms: false
})

const registrationLoading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')


const { data, error: fetchError } = await useFetch(
  `http://localhost:8000/api/${organization}/public/events/${id}`
)

if (fetchError.value) {
  error.value = fetchError.value
  console.error('Failed to fetch event details:', fetchError.value)
} else {
  event.value = data.value
  
  // Mock registered attendees (replace with actual API call)
  registeredAttendees.value = Array.from(
    { length: Math.floor(Math.random() * event.value.max_attendees) }, 
    (_, i) => ({
      id: i + 1,
      name: `Attendee ${i + 1}`
    })
  )
}

loading.value = false



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
const router = useRouter()
const submitRegistration = async () => {
  registrationLoading.value = true
  successMessage.value = ''
  errorMessage.value = ''
  
  try {
    const response = await $fetch(`http://localhost:8000/api/${organization}/public/events/${id}/register `, {
      method: 'POST',
      body: {
        name: form.name,
        email: form.email,
        phone: form.phone
      }
    })
    
    successMessage.value = `Thank you for registering! A confirmation has been sent to ${form.email}.`
    
    // Reset form after successful submission
    setTimeout(() => {
      form.name = ''
      form.email = ''
      form.phone = ''
      form.agreeTerms = false
      showModal.value = false
      registeredAttendees.value.push({
        id: registeredAttendees.value.length + 1,
        name: form.name
      })
      router.push('/')
    }, 2000)
  } catch (err) {
    errorMessage.value = err.data?.message || 'Registration failed. Please try again later.'
  } finally {
    registrationLoading.value = false
  }
}
</script>