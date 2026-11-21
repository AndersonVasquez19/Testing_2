<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { ZoomIn, X, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { GALERIA_CATEGORIAS, GALERIA_FOTOS } from '~/data/galeria';
import { CONTACT_INFO } from '~/data/contactos';

useHead({ title: 'Galería' });

const categoriaActiva = ref<string>('all');
const lightboxIndex = ref<number>(-1);

const fotosFiltradas = computed(() =>
  categoriaActiva.value === 'all'
    ? GALERIA_FOTOS
    : GALERIA_FOTOS.filter((f) => f.cat === categoriaActiva.value)
);

const countByCategory = (catId: string) =>
  catId === 'all' ? GALERIA_FOTOS.length : GALERIA_FOTOS.filter((f) => f.cat === catId).length;

const activePhoto = computed(() =>
  lightboxIndex.value >= 0 ? fotosFiltradas.value[lightboxIndex.value] ?? null : null
);

const nextPhoto = () => {
  if (!fotosFiltradas.value.length) return;
  lightboxIndex.value = (lightboxIndex.value + 1) % fotosFiltradas.value.length;
};

const prevPhoto = () => {
  if (!fotosFiltradas.value.length) return;
  lightboxIndex.value = (lightboxIndex.value - 1 + fotosFiltradas.value.length) % fotosFiltradas.value.length;
};

watch(lightboxIndex, (val) => {
  if (typeof document !== 'undefined') {
    document.body.style.overflow = val >= 0 ? 'hidden' : '';
  }
});

const handleKeyDown = (e: KeyboardEvent) => {
  if (lightboxIndex.value < 0) return;
  if (e.key === 'Escape') lightboxIndex.value = -1;
  if (e.key === 'ArrowRight') nextPhoto();
  if (e.key === 'ArrowLeft') prevPhoto();
};

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  if (typeof document !== 'undefined') document.body.style.overflow = '';
  if (typeof window !== 'undefined') window.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
  <div class="pt-20">
    <!-- Hero con ornamento dorado -->
    <section class="relative overflow-hidden py-16 sm:py-24 bg-gradient-to-br from-[#0A2540] via-[#102d49] to-[#1a2f45] text-white text-center">
      <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center gap-3 mb-4" aria-hidden="true">
          <span class="h-0.5 w-8 bg-gold/60" />
          <span class="h-2 w-2 rounded-full bg-gold" />
          <span class="h-0.5 w-8 bg-gold/60" />
        </div>

        <h1 class="text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight">
          Nuestra <span class="text-gold">galería</span>
        </h1>
        <p class="mt-3 text-base sm:text-lg text-white/85 font-light">
          Momentos que inspiran en capacitaciones, convenios y talleres prácticos.
        </p>
      </div>
    </section>

    <!-- Filtros, grilla interactiva, callout Instagram y lightbox -->
    <section class="bg-[#F7F9FC] py-12 sm:py-16 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
          <button
            v-for="cat in GALERIA_CATEGORIAS"
            :key="cat.id"
            type="button"
            :class="[
              'rounded-full px-5 py-2 text-xs sm:text-sm font-semibold transition-all cursor-pointer',
              categoriaActiva === cat.id
                ? 'bg-gold text-[#0A2540] shadow-md scale-105'
                : 'bg-white border border-stone-200 text-slate-700 hover:bg-stone-100 hover:text-[#0A2540]',
            ]"
            @click="categoriaActiva = cat.id; lightboxIndex = -1"
          >
            {{ cat.label }}
            <small :class="['ml-1 text-[11px] font-bold tabular-nums', categoriaActiva === cat.id ? 'text-[#0A2540]' : 'text-gold-text']">
              {{ countByCategory(cat.id) }}
            </small>
          </button>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <button
            v-for="(foto, index) in fotosFiltradas"
            :key="foto.id"
            type="button"
            :aria-label="`Ampliar foto: ${foto.label}`"
            class="group relative h-72 sm:h-80 w-full overflow-hidden rounded-2xl border border-stone-200 bg-white cursor-pointer shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-gold text-left"
            @click="lightboxIndex = index"
          >
            <div class="relative w-full h-full overflow-hidden">
              <img
                :src="foto.src"
                :alt="foto.alt"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                :style="{ objectPosition: foto.pos || 'center' }"
              >

              <div class="absolute top-4 right-4 flex h-9 w-9 items-center justify-center rounded-full bg-black/65 text-white backdrop-blur-md opacity-0 group-hover:opacity-100 transition-opacity">
                <ZoomIn class="h-4 w-4 text-gold" aria-hidden="true" />
              </div>

              <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-5 text-white">
                <h3 class="text-base font-bold leading-snug">{{ foto.label }}</h3>
                <em class="text-xs text-gold not-italic mt-0.5 capitalize">
                  {{ foto.cat.replace('-', ' ') }}
                </em>
              </div>
            </div>
          </button>
        </div>

        <div class="mt-16 text-center">
          <p class="text-base text-slate-600 mb-3 font-medium">¿Quieres ver más?</p>
          <a
            :href="CONTACT_INFO.socials.instagram"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full bg-[#0A2540] border-2 border-gold text-white font-semibold text-sm shadow-md hover:bg-gold hover:text-[#0A2540] hover:-translate-y-0.5 transition-all"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="3" y="3" width="18" height="18" rx="5" />
              <circle cx="12" cy="12" r="4" />
              <circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
            </svg>
            <span>Visita nuestro Instagram</span>
          </a>
        </div>
      </div>
    </section>

    <!-- Lightbox modal accesible -->
    <div
      v-if="activePhoto"
      role="dialog"
      aria-modal="true"
      :aria-label="activePhoto.label"
      class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-sm p-4 animate-fade-in"
      @click.self="lightboxIndex = -1"
    >
      <button
        type="button"
        aria-label="Cerrar galería ampliada"
        class="absolute top-4 right-4 z-20 flex h-11 w-11 items-center justify-center rounded-full bg-white/15 text-white hover:bg-gold hover:text-[#0A2540] transition-colors cursor-pointer"
        @click="lightboxIndex = -1"
      >
        <X class="h-5 w-5" />
      </button>

      <button
        type="button"
        aria-label="Foto anterior"
        class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 z-20 flex h-11 w-11 items-center justify-center rounded-full bg-white/15 text-white hover:bg-gold hover:text-[#0A2540] transition-colors cursor-pointer"
        @click="prevPhoto"
      >
        <ChevronLeft class="h-6 w-6" />
      </button>

      <div class="max-w-4xl w-full flex flex-col items-center">
        <img
          :src="activePhoto.src"
          :alt="activePhoto.alt"
          class="max-h-[75vh] w-auto rounded-2xl object-contain shadow-2xl border border-white/15"
        >
        <div class="mt-4 text-center text-white">
          <p class="text-base font-bold text-gold">{{ activePhoto.label }}</p>
          <p class="text-xs text-stone-300 mt-1">{{ activePhoto.alt }}</p>
          <p class="text-[11px] font-mono tabular-nums text-stone-400 mt-2">
            {{ lightboxIndex + 1 }} / {{ fotosFiltradas.length }}
          </p>
        </div>
      </div>

      <button
        type="button"
        aria-label="Foto siguiente"
        class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 z-20 flex h-11 w-11 items-center justify-center rounded-full bg-white/15 text-white hover:bg-gold hover:text-[#0A2540] transition-colors cursor-pointer"
        @click="nextPhoto"
      >
        <ChevronRight class="h-6 w-6" />
      </button>
    </div>
  </div>
</template>
