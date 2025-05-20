<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Create your account
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Already have an account? 
          <NuxtLink to="/auth/login" class="font-medium text-indigo-600 hover:text-indigo-500">
            Sign in
          </NuxtLink>
        </p>
      </div>
  
      <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
          <form class="space-y-6" @submit.prevent="register">
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
  
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
              <div>
                <label for="first-name" class="block text-sm font-medium text-gray-700">First name</label>
                <div class="mt-1">
                  <input 
                    type="text" 
                    id="first-name" 
                    v-model="form.firstName"
                    autocomplete="given-name"
                    required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                </div>
              </div>
  
              <div>
                <label for="last-name" class="block text-sm font-medium text-gray-700">Last name</label>
                <div class="mt-1">
                  <input 
                    type="text" 
                    id="last-name" 
                    v-model="form.lastName"
                    autocomplete="family-name"
                    required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  >
                </div>
              </div>
            </div>
  
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
              <div class="mt-1">
                <input 
                  type="email" 
                  id="email" 
                  v-model="form.email"
                  autocomplete="email"
                  required
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
              </div>
            </div>
  
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700">Phone number</label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 flex items-center">
                  <label for="country" class="sr-only">Country</label>
                  <select id="country" name="country" class="h-full py-0 pl-3 pr-7 border-transparent bg-transparent text-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option>+254</option>
                    <option>+255</option>
                    <option>+256</option>
                  </select>
                </div>
                <input 
                  type="tel" 
                  id="phone" 
                  v-model="form.phone"
                  autocomplete="tel"
                  required
                  class="block w-full pl-16 px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                  placeholder="712 345 678"
                >
              </div>
            </div>
  
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
              <div class="mt-1 relative">
                <input 
                  :type="showPassword ? 'text' : 'password'"
                  id="password" 
                  v-model="form.password"
                  autocomplete="new-password"
                  required
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                <button 
                  type="button" 
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center"
                >
                  <EyeIcon v-if="showPassword" class="h-5 w-5 text-gray-400" />
                  <EyeSlashIcon v-else class="h-5 w-5 text-gray-400" />
                </button>
              </div>
              <p class="mt-2 text-xs text-gray-500">
                Must be at least 8 characters with one uppercase, one number, and one special character
              </p>
            </div>
  
            <div>
              <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm password</label>
              <div class="mt-1">
                <input 
                  :type="showConfirmPassword ? 'text' : 'password'"
                  id="confirm-password" 
                  v-model="form.confirmPassword"
                  autocomplete="new-password"
                  required
                  class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
              </div>
            </div>
  
            <div class="flex items-start">
              <div class="flex items-center h-5">
                <input 
                  id="terms" 
                  v-model="form.acceptTerms"
                  type="checkbox" 
                  class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                  required
                >
              </div>
              <div class="ml-3 text-sm">
                <label for="terms" class="font-medium text-gray-700">I agree to the</label>
                {{ ' ' }}
                <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a>
                {{ ' ' }}
                <span class="text-gray-500">and</span>
                {{ ' ' }}
                <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
              </div>
            </div>
  
            <div>
              <button 
                type="submit" 
                :disabled="loading"
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="loading">
                  <ArrowPathIcon class="animate-spin h-4 w-4 mr-2" />
                  Creating account...
                </span>
                <span v-else>Register</span>
              </button>
            </div>
          </form>
  
          <div class="mt-6">
            <div class="relative">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300" />
              </div>
              <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-white text-gray-500">
                  Or sign up with
                </span>
              </div>
            </div>
  
            <div class="mt-6 grid grid-cols-2 gap-3">
              <div>
                <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                  <span class="sr-only">Sign up with Google</span>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z" />
                  </svg>
                </a>
              </div>
  
              <div>
                <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                  <span class="sr-only">Sign up with Facebook</span>
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
<script setup>
import { XCircleIcon, ArrowPathIcon, EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'
import { useAuthStore } from '~/stores/auth'

const authStore = useAuthStore()

const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
  organization_name: '',
  organization_slug: '',
  acceptTerms: false
})

const showPassword = ref(false)
const showConfirmPassword = ref(false)
const loading = ref(false)
const errorMessage = ref('')

const register = async () => {
  loading.value = true
  errorMessage.value = ''
  
  // Validate password match
  if (form.password !== form.confirmPassword) {
    errorMessage.value = 'Passwords do not match'
    loading.value = false
    return
  }

  // Validate password strength
  const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
  if (!passwordRegex.test(form.password)) {
    errorMessage.value = 'Password must be at least 8 characters with uppercase, number, and special character'
    loading.value = false
    return
  }

  try {
    const registrationData = {
      name: `${form.firstName} ${form.lastName}`,
      email: form.email,
      password: form.password,
      organization_name: form.organization_name,
      organization_slug: form.organization_slug.toLowerCase().replace(/\s+/g, '-')
    }

    const response = await authStore.register(registrationData)
    
    // Redirect to admin dashboard
    navigateTo(response.redirect_to)
  } catch (error) {
    errorMessage.value = error.data?.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>