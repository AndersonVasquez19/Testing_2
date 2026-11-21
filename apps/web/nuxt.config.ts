import tailwindcss from '@tailwindcss/vite';

export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',
  future: {
    compatibilityVersion: 4,
  },
  devtools: { enabled: false },

  modules: [
    '@pinia/nuxt',
    '@nuxt/image',
    '@nuxt/eslint',
    '@vee-validate/nuxt',
  ],

  css: ['~/assets/css/main.css'],

  vite: {
    plugins: [tailwindcss()],
  },

  runtimeConfig: {
    apiBaseUrl: process.env.HORUS_API_URL || 'http://127.0.0.1:8000/api/v1',
    public: {
      whatsappNumber: process.env.NUXT_PUBLIC_WHATSAPP_NUMBER || '51972021374',
      siteName: 'Horus Academic',
    },
  },

  routeRules: {
    '/': { isr: 60 },
    '/quienes-somos': { isr: 60 },
    '/galeria': { isr: 60 },
    '/contactos': { ssr: true },
    '/educacion/**': { isr: 60 },
    '/legales/**': { isr: 60 },
    '/pagar/**': { ssr: true },
    '/verificar-certificado/**': { ssr: true },
    '/panel/**': { ssr: false },
    '/panel/admin/**': { ssr: false },
    '/panel/secretaria/**': { ssr: false },
    '/panel/coordinador/**': { ssr: false },
    '/panel/estudiante/**': { ssr: false },
  },

  app: {
    head: {
      htmlAttrs: { lang: 'es' },
      titleTemplate: '%s · Horus Academic',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      ],
    },
  },

  image: {
    provider: 'none',
  },
});
