<template>
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">EventHub</h1>
        <div class="space-x-4">
          <NuxtLink to="/auth/login" class="text-indigo-600 hover:text-indigo-800">Login</NuxtLink>
          <NuxtLink to="/auth/register" class="text-indigo-600 hover:text-indigo-800">Register</NuxtLink>
        </div>
      </div>
    </header>
  </template>
  
  <script setup>
  // No script needed for now
  </script>
  