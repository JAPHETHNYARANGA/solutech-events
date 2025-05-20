<template>
    <div class="min-h-screen bg-gray-50">
      <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <!-- <h1 class="text-2xl font-bold text-gray-900">Register for {{ event.title }}</h1> -->
        </div>
      </header>
  
      <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <form @submit.prevent="submitRegistration">
              <div class="space-y-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                  <input 
                    type="text" 
                    id="name" 
                    v-model="form.name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"
                    required
                  >
                </div>
  
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                  <input 
                    type="email" 
                    id="email" 
                    v-model="form.email"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"
                    required
                  >
                </div>
  
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                  <input 
                    type="tel" 
                    id="phone" 
                    v-model="form.phone"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border"
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
                    I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-500">terms and conditions</a>
                  </label>
                </div>
  
                <div v-if="successMessage" class="rounded-md bg-green-50 p-4">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <CheckCircleIcon class="h-5 w-5 text-green-400" />
                    </div>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
                    </div>
                  </div>
                </div>
  
                <div v-if="errorMessage" class="rounded-md bg-red-50 p-4">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <XCircleIcon class="h-5 w-5 text-red-400" />
                    </div>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-red-800">{{ errorMessage }}</p>
                    </div>
                  </div>
                </div>
  
                <div class="flex justify-end">
                  <button 
                    type="submit" 
                    :disabled="loading"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <span v-if="loading">
                      <ArrowPathIcon class="animate-spin h-4 w-4 mr-2" />
                      Processing...
                    </span>
                    <span v-else>Complete Registration</span>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </template>
  

  <script setup>
  import { CheckCircleIcon, XCircleIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
  import { registerForEvent } from '~/services/eventService'
  
  const route = useRoute()
  const { organization, id } = route.params
  
  const form = reactive({
    name: '',
    email: '',
    phone: '',
    agreeTerms: false
  })
  
  const loading = ref(false)
  const successMessage = ref('')
  const errorMessage = ref('')
  
  const submitRegistration = async () => {
    loading.value = true
    successMessage.value = ''
    errorMessage.value = ''
    
    try {
      const response = await registerForEvent(organization, id, {
        name: form.name,
        email: form.email,
        phone: form.phone
      })
      
      successMessage.value = `Thank you for registering! A confirmation has been sent to ${form.email}.`
      
      // Reset form
      form.name = ''
      form.email = ''
      form.phone = ''
      form.agreeTerms = false
    } catch (error) {
      errorMessage.value = error.data?.message || 'Registration failed. Please try again later.'
    } finally {
      loading.value = false
    }
  }
  </script>