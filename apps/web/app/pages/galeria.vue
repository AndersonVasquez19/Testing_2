<script setup lang="ts">
import { ref, computed } from 'vue';
import { GALERIA_CATEGORIAS, GALERIA_FOTOS } from '~/data/galeria';

useHead({ title: 'Galería' });

const categoriaActiva = ref<string>('all');
const fotosFiltradas = computed(() =>
  categoriaActiva.value === 'all'
    ? GALERIA_FOTOS
    : GALERIA_FOTOS.filter((f) => f.cat === categoriaActiva.value)
);
</script>

<template>
  <div class="pt-20">
    <section class="py-16 sm:py-24 bg-gradient-to-br from-[#0A2540] via-[#102d49] to-[#1a2f45] text-white text-center">
      <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-5xl font-bold tracking-tight">
          Nuestra <span class="text-gold">galería</span>
        </h1>
        <p class="mt-3 text-base sm:text-lg text-white/80 font-light">
          Momentos que inspiran en capacitaciones, convenios y talleres prácticos.
        </p>
      </div>
    </section>

    <section class="bg-[#F7F9FC] py-12 sm:py-16 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center gap-2 mb-10">
          <button
            v-for="cat in GALERIA_CATEGORIAS"
            :key="cat.id"
            type="button"
            :class="[
              'rounded-full px-4 py-2 text-sm font-semibold transition cursor-pointer',
              categoriaActiva === cat.id
                ? 'bg-[#0A2540] text-gold shadow'
                : 'bg-white border border-slate-200 text-slate-700 hover:border-gold',
            ]"
            @click="categoriaActiva = cat.id"
          >
            {{ cat.label }}
          </button>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <figure
            v-for="foto in fotosFiltradas"
            :key="foto.id"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
          >
            <img
              :src="foto.src"
              :alt="foto.alt"
              class="h-60 w-full object-cover"
              :style="{ objectPosition: foto.pos || 'center' }"
            >
            <figcaption class="p-4">
              <span class="text-[11px] font-bold uppercase tracking-wider text-gold-text">{{ foto.label }}</span>
              <p class="mt-1 text-sm text-slate-700">{{ foto.alt }}</p>
            </figcaption>
          </figure>
        </div>
      </div>
    </section>
  </div>
</template>
