<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { ChevronLeft, ChevronRight, ArrowRight } from 'lucide-vue-next';
import { HERO_SLIDES, HERO_STATS } from '~/data/hero-slides';

const current = ref(0);
const isPaused = ref(false);
let timer: ReturnType<typeof setInterval> | null = null;

const nextSlide = () => {
  current.value = (current.value + 1) % HERO_SLIDES.length;
};

const prevSlide = () => {
  current.value = (current.value - 1 + HERO_SLIDES.length) % HERO_SLIDES.length;
};

onMounted(() => {
  timer = setInterval(() => {
    if (!isPaused.value) nextSlide();
  }, 6000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>

<template>
  <div
    class="relative flex flex-col bg-[#0A1929] text-white pt-20"
    role="region"
    aria-roledescription="carrusel"
    aria-label="Servicios de Horus Group SRL"
    @mouseenter="isPaused = true"
    @mouseleave="isPaused = false"
  >
    <h1 class="sr-only">
      Horus Academic: formación profesional, capacitaciones y asesoramiento en Cajamarca
    </h1>

    <div class="relative isolate flex flex-col justify-center min-h-[520px] sm:min-h-[580px] lg:min-h-[620px] py-14 lg:py-20 overflow-hidden bg-[#0A2540]">
      <div class="absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div
          v-for="(slide, idx) in HERO_SLIDES"
          :key="idx"
          :class="[
            'absolute inset-0 transition-opacity duration-1000 ease-in-out',
            current === idx ? 'opacity-100' : 'opacity-0 pointer-events-none',
          ]"
        >
          <img
            :src="slide.image"
            alt=""
            class="h-full w-full object-cover"
            :style="{ objectPosition: slide.position || 'center' }"
          >
        </div>
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-r from-[#0A2540]/95 via-[#0A2540]/80 md:via-[#0A2540]/70 to-transparent" />
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-[#0A1929] via-transparent to-[#0A2540]/50" />
      </div>

      <div class="mx-auto max-w-7xl w-full px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-2xl">
          <template v-for="(slide, idx) in HERO_SLIDES" :key="idx">
            <div v-if="current === idx" role="group" aria-roledescription="diapositiva" :aria-label="`${idx + 1} de ${HERO_SLIDES.length}`">
              <span class="inline-flex items-center px-4 py-1.5 rounded-full border border-white/20 bg-white/10 text-xs sm:text-sm font-medium tracking-wide text-white/95 backdrop-blur-md">
                {{ slide.eyebrow }}
              </span>

              <h2 class="mt-4 mb-4 text-3xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-white leading-[1.08]">
                {{ slide.title }}
                <span class="block text-gold">{{ slide.highlight }}</span>
              </h2>

              <p class="max-w-xl text-base sm:text-lg text-white/90 leading-relaxed font-normal">
                {{ slide.text }}
              </p>

              <div class="flex flex-wrap gap-3.5 mt-7">
                <NuxtLink
                  :to="slide.cta.url"
                  class="inline-flex items-center gap-2 rounded-full bg-gold px-6 py-3 text-sm font-bold text-[#0A2540] hover:bg-gold-light transition-all shadow-lg"
                >
                  <span>{{ slide.cta.label }}</span>
                  <ArrowRight class="h-4 w-4" aria-hidden="true" />
                </NuxtLink>
                <NuxtLink
                  :to="slide.cta2.url"
                  class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20 transition-all backdrop-blur-md"
                >
                  {{ slide.cta2.label }}
                </NuxtLink>
              </div>
            </div>
          </template>
        </div>

        <div class="mt-8 flex items-center gap-3">
          <button
            type="button"
            aria-label="Diapositiva anterior"
            class="rounded-full border border-white/25 bg-white/10 p-2 text-white hover:bg-white/20 cursor-pointer"
            @click="prevSlide"
          >
            <ChevronLeft class="h-4 w-4" />
          </button>
          <div class="flex gap-2">
            <button
              v-for="(_, idx) in HERO_SLIDES"
              :key="idx"
              type="button"
              :aria-label="`Ir a diapositiva ${idx + 1}`"
              :class="[
                'h-2.5 rounded-full transition-all cursor-pointer',
                current === idx ? 'w-8 bg-gold' : 'w-2.5 bg-white/40 hover:bg-white/70',
              ]"
              @click="current = idx"
            />
          </div>
          <button
            type="button"
            aria-label="Siguiente diapositiva"
            class="rounded-full border border-white/25 bg-white/10 p-2 text-white hover:bg-white/20 cursor-pointer"
            @click="nextSlide"
          >
            <ChevronRight class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>

    <div class="border-y border-white/10 bg-[#081626] py-6">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
        <div v-for="stat in HERO_STATS" :key="stat.label">
          <p class="text-2xl sm:text-3xl font-extrabold text-gold">{{ stat.value }}</p>
          <p class="text-xs sm:text-sm text-stone-300 mt-1">{{ stat.label }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
