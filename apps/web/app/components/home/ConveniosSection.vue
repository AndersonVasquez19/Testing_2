<script setup lang="ts">
import { ref } from 'vue';
import { Award, CalendarCheck, X } from 'lucide-vue-next';
import { CONVENIOS, type Convenio } from '~/data/convenios';

const activeConvenio = ref<Convenio | null>(null);
</script>

<template>
  <section id="convenios" class="scroll-mt-6 py-16 md:py-20 bg-gradient-to-b from-[#0A2540] to-[#0A1929]">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mb-10 max-w-2xl">
        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-white">
          Convenios estratégicos
        </h2>
        <p class="mt-2 text-sm sm:text-base text-stone-300">
          Alianzas con colegios profesionales e instituciones que respaldan la validez de nuestras certificaciones a nivel nacional.
        </p>
      </div>

      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <article
          v-for="item in CONVENIOS"
          :key="item.id"
          class="rounded-2xl border border-white/10 bg-white/5 p-6 flex flex-col justify-between hover:border-gold/50 transition-all"
        >
          <div>
            <div class="flex items-center gap-3 mb-4">
              <img :src="item.logo" :alt="item.siglas" class="h-12 w-12 rounded-xl object-contain bg-white p-1">
              <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gold">{{ item.siglas }}</span>
                <h3 class="text-base font-bold text-white leading-snug">{{ item.nombre }}</h3>
              </div>
            </div>
            <p class="text-sm text-stone-300 line-clamp-3 leading-relaxed">
              {{ item.descripcion }}
            </p>
          </div>

          <div class="mt-5 pt-4 border-t border-white/10 flex items-center justify-between text-xs">
            <span class="inline-flex items-center gap-1.5 text-stone-300">
              <CalendarCheck class="h-4 w-4 text-gold" aria-hidden="true" />
              Vigente: {{ item.vigencia }}
            </span>
            <button
              type="button"
              class="font-semibold text-gold hover:underline cursor-pointer"
              @click="activeConvenio = item"
            >
              Ver detalle
            </button>
          </div>
        </article>
      </div>

      <div
        v-if="activeConvenio"
        role="dialog"
        aria-modal="true"
        :aria-label="activeConvenio.nombre"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4"
        @click.self="activeConvenio = null"
      >
        <div class="max-w-lg w-full rounded-3xl border border-gold/30 bg-[#0A2540] p-6 sm:p-8 text-white shadow-2xl">
          <div class="flex items-start justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
              <Award class="h-6 w-6 text-gold shrink-0" />
              <h3 class="text-lg font-bold">{{ activeConvenio.nombre }}</h3>
            </div>
            <button
              type="button"
              aria-label="Cerrar detalle de convenio"
              class="rounded-full p-1 text-stone-300 hover:bg-white/10"
              @click="activeConvenio = null"
            >
              <X class="h-5 w-5" />
            </button>
          </div>
          <img
            v-if="activeConvenio.imagen"
            :src="activeConvenio.imagen"
            :alt="activeConvenio.nombre"
            class="w-full h-48 object-cover rounded-2xl mb-4"
          >
          <p class="text-sm text-stone-200 leading-relaxed">{{ activeConvenio.descripcion }}</p>
          <p class="mt-4 text-xs text-gold font-semibold">Vigencia: {{ activeConvenio.vigencia }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
