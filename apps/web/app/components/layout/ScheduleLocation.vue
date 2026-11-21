<script setup lang="ts">
import { Clock, MapPin } from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';
</script>

<template>
  <section class="py-12 md:py-16 bg-[#F7F9FC] text-slate-900">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="rounded-[30px] border border-gold/20 bg-white p-6 sm:p-8 shadow-lg">
        <h2 class="text-2xl sm:text-3xl font-bold text-[#0A2540] tracking-tight">
          Horarios y Ubicación
        </h2>
        <div class="mt-2 h-1 w-16 rounded-full bg-gold" />

        <div class="mt-8 grid gap-6 lg:grid-cols-2 items-center">
          <div class="space-y-4">
            <div class="rounded-2xl border border-stone-200 bg-stone-50 p-5">
              <div class="mb-3 flex items-center gap-3 text-gold">
                <Clock class="h-6 w-6" aria-hidden="true" />
                <h3 class="text-lg font-semibold text-[#0A2540]">Horario de atención</h3>
              </div>
              <ul class="space-y-2 text-sm text-slate-600">
                <li v-for="row in CONTACT_INFO.hours" :key="row.days">
                  <strong>{{ row.days }}:</strong> {{ row.time }}
                </li>
              </ul>
            </div>

            <div class="rounded-2xl border border-stone-200 bg-stone-50 p-5">
              <div class="mb-3 flex items-center gap-3 text-gold">
                <MapPin class="h-6 w-6" aria-hidden="true" />
                <h3 class="text-lg font-semibold text-[#0A2540]">Sede Central</h3>
              </div>
              <p class="text-sm leading-6 text-slate-600">
                {{ CONTACT_INFO.address }}
              </p>
            </div>
          </div>

          <div class="overflow-hidden rounded-[24px] border border-gold/20 bg-stone-100 shadow-inner">
            <iframe
              title="Ubicación de Horus Group en Cajamarca"
              src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d989.6682159535815!2d-78.5107197!3d-7.1637652!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91b25b809bf9b7e3%3A0x70d5a58b14aa7eff!2sPlazuela%20Bolognesi!5e0!3m2!1ses-419!2spe!4v1772667902105!5m2!1ses-419!2spe"
              class="h-[300px] w-full border-0 block"
              loading="lazy"
              allowfullscreen
              referrerpolicy="no-referrer-when-downgrade"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
