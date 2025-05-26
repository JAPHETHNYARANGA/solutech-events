import tailwindcss from "@tailwindcss/vite";


export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',
  devtools: { enabled: true },
  css: ['~/assets/css/main.css'],
  modules: ['@pinia/nuxt'],
  plugins: ['~/plugins/init-auth.client.js'],
  vite: {
    plugins: [
      tailwindcss(),
    ],
  },
})
