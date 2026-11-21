<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import {
  ChevronLeft,
  ChevronRight,
  Play,
  Pause,
  ArrowRight,
  X,
  Award,
  CheckCircle2,
  CalendarCheck,
} from 'lucide-vue-next';
import { CONVENIOS, type Convenio } from '~/data/convenios';

const props = withDefaults(
  defineProps<{
    title?: string;
    intro?: string;
  }>(),
  {
    title: 'Convenios estratégicos',
    intro: 'Alianzas con colegios profesionales e instituciones que respaldan la validez de nuestras certificaciones a nivel nacional.',
  }
);

const activeConvenio = ref<Convenio | null>(null);
const currentIndex = ref(0);
const isPaused = ref(false);
const visibleCount = ref(3);
const trackRef = ref<HTMLDivElement | null>(null);

const total = CONVENIOS.length;
const maxIndex = computed(() => Math.max(0, total - visibleCount.value));
const rangeText = computed(() => `${currentIndex.value + 1}–${Math.min(total, currentIndex.value + visibleCount.value)} de ${total}`);

let autoTimer: ReturnType<typeof setInterval> | null = null;

const updateVisibleCount = () => {
  if (typeof window === 'undefined') return;
  if (window.innerWidth < 768) visibleCount.value = 1;
  else if (window.innerWidth < 1024) visibleCount.value = 2;
  else visibleCount.value = 3;
  if (currentIndex.value > maxIndex.value) {
    currentIndex.value = maxIndex.value;
  }
};

const nextSlide = () => {
  currentIndex.value = currentIndex.value >= maxIndex.value ? 0 : currentIndex.value + 1;
};

const prevSlide = () => {
  currentIndex.value = currentIndex.value <= 0 ? maxIndex.value : currentIndex.value - 1;
};

watch(currentIndex, () => {
  if (trackRef.value) {
    const cardWidth = trackRef.value.scrollWidth / total;
    trackRef.value.scrollTo({
      left: currentIndex.value * cardWidth,
      behavior: 'smooth',
    });
  }
});

watch(activeConvenio, (val) => {
  if (typeof document === 'undefined') return;
  document.body.style.overflow = val ? 'hidden' : '';
});

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && activeConvenio.value) {
    activeConvenio.value = null;
  }
};

onMounted(() => {
  updateVisibleCount();
  window.addEventListener('resize', updateVisibleCount);
  window.addEventListener('keydown', handleKeyDown);
  autoTimer = setInterval(() => {
    if (!isPaused.value && !activeConvenio.value) {
      nextSlide();
    }
  }, 6000);
});

onUnmounted(() => {
  if (autoTimer) clearInterval(autoTimer);
  if (typeof document !== 'undefined') document.body.style.overflow = '';
  if (typeof window !== 'undefined') {
    window.removeEventListener('resize', updateVisibleCount);
    window.removeEventListener('keydown', handleKeyDown);
  }
});
</script>

<template>
  <section id="convenios" class="scroll-mt-6 py-16 md:py-20 bg-gradient-to-b from-[#0A2540] to-[#0A1929]">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex flex-wrap items-end justify-between gap-6 mb-10">
        <div>
          <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight text-white leading-tight">
            {{ props.title }}
          </h2>
          <div class="mt-4 h-[3px] w-14 rounded-full bg-gold" />
          <p v-if="props.intro" class="mt-4 max-w-xl text-sm sm:text-base leading-relaxed text-white/80">
            {{ props.intro }}
          </p>
        </div>

        <div class="flex items-center gap-2.5 self-start md:self-auto">
          <span class="min-w-[5.5ch] text-right text-xs sm:text-sm font-semibold text-white/80 font-mono tabular-nums mr-1">
            {{ rangeText }}
          </span>

          <button
            type="button"
            aria-label="Convenios anteriores"
            class="inline-flex h-[2.9rem] w-[2.9rem] items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white hover:bg-gold hover:text-[#0A2540] hover:border-gold transition-colors cursor-pointer"
            @click="prevSlide"
          >
            <ChevronLeft class="h-5 w-5" />
          </button>

          <button
            type="button"
            aria-label="Convenios siguientes"
            class="inline-flex h-[2.9rem] w-[2.9rem] items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white hover:bg-gold hover:text-[#0A2540] hover:border-gold transition-colors cursor-pointer"
            @click="nextSlide"
          >
            <ChevronRight class="h-5 w-5" />
          </button>

          <button
            type="button"
            :aria-label="isPaused ? 'Reanudar rotación automática' : 'Pausar rotación automática'"
            class="inline-flex h-[2.9rem] w-[2.9rem] items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white hover:bg-gold hover:text-[#0A2540] hover:border-gold transition-colors cursor-pointer"
            @click="isPaused = !isPaused"
          >
            <Play v-if="isPaused" class="h-4 w-4" />
            <Pause v-else class="h-4 w-4" />
          </button>
        </div>
      </div>

      <div
        ref="trackRef"
        class="flex gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
        @mouseenter="isPaused = true"
        @mouseleave="isPaused = false"
      >
        <div
          v-for="(item, index) in CONVENIOS"
          :key="item.id"
          :class="[
            'snap-start flex-shrink-0 w-[85%] sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] transition-all duration-300',
            index >= currentIndex && index < currentIndex + visibleCount ? 'ring-1 ring-gold/35 rounded-[1.1rem]' : '',
          ]"
        >
          <article class="flex flex-col w-full h-full overflow-hidden rounded-[1.1rem] border border-white/12 bg-[#153A59] text-white text-left transition-all duration-300 hover:border-gold hover:shadow-[0_18px_44px_rgba(3,12,22,0.28)] group">
            <div v-if="item.imagen" class="relative aspect-[16/10] w-full overflow-hidden bg-[#0A2540]">
              <img
                :src="item.imagen"
                :alt="item.nombre"
                class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-700"
                :style="{ objectPosition: item.posicion || 'center' }"
              >
              <div class="absolute inset-0 bg-gradient-to-t from-[#153A59] via-transparent to-transparent pointer-events-none" />
            </div>
            <div v-else class="relative aspect-[16/10] w-full grid place-items-center bg-gradient-to-br from-[#0A2540] to-[#153A59]">
              <b class="relative z-10 text-3xl font-bold text-white/90">{{ item.siglas }}</b>
            </div>

            <div class="flex flex-1 flex-col p-6 pt-0">
              <div class="relative z-10 flex items-end justify-between gap-3 -mt-8 mb-4">
                <div class="grid place-items-center w-[4.25rem] h-[4.25rem] p-2 rounded-2xl bg-white shadow-[0_8px_22px_rgba(3,12,22,0.25)] flex-none">
                  <img
                    :src="item.logo"
                    :alt="`Logo ${item.siglas}`"
                    class="w-full h-full object-contain"
                  >
                </div>
                <span class="pb-1 text-xs font-bold tracking-wider text-gold">
                  {{ item.siglas }}
                </span>
              </div>

              <h3 class="text-lg font-semibold text-white leading-snug line-clamp-2 min-h-[2.8em] group-hover:text-gold transition-colors">
                {{ item.nombre }}
              </h3>

              <div class="mt-4 pt-4 border-t border-white/12 flex items-center gap-3">
                <span class="h-2 w-2 rounded-full bg-gold ring-4 ring-gold/20 flex-shrink-0" />
                <p class="text-xs text-white/80">
                  Vigente hasta <strong class="text-white font-semibold block sm:inline">{{ item.vigencia }}</strong>
                </p>
              </div>

              <button
                type="button"
                class="inline-flex self-start items-center gap-2 mt-5 py-1 text-sm font-semibold text-gold hover:underline underline-offset-4 group/btn cursor-pointer"
                @click="activeConvenio = item"
              >
                <span>Ver convenio</span>
                <ArrowRight class="h-3.5 w-3.5 transition-transform duration-200 group-hover/btn:translate-x-1" aria-hidden="true" />
              </button>
            </div>
          </article>
        </div>
      </div>
    </div>

    <div
      v-if="activeConvenio"
      role="dialog"
      aria-modal="true"
      aria-labelledby="convenio-modal-title"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/75 backdrop-blur-sm animate-fade-in"
      @click.self="activeConvenio = null"
    >
      <div class="relative w-full max-w-2xl max-h-[90vh] flex flex-col bg-white text-slate-900 rounded-3xl shadow-2xl border border-slate-100 overflow-hidden animate-zoom-in">
        <button
          type="button"
          class="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-white/95 hover:bg-white text-slate-700 hover:text-slate-900 flex items-center justify-center shadow-md border border-slate-200/80 transition-all hover:rotate-90 cursor-pointer"
          aria-label="Cerrar detalle del convenio"
          @click="activeConvenio = null"
        >
          <X class="h-5 w-5" />
        </button>

        <div class="relative w-full shrink-0 overflow-hidden">
          <div v-if="activeConvenio.imagen" class="w-full h-56 sm:h-72 bg-slate-900 relative">
            <img
              :src="activeConvenio.imagen"
              :alt="activeConvenio.nombre"
              class="h-full w-full object-cover"
              :style="{ objectPosition: activeConvenio.posicion || 'center' }"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent" />
          </div>
          <div v-else class="w-full h-44 sm:h-52 bg-gradient-to-br from-[#0A2540] via-[#102d49] to-[#153a59] flex items-center justify-center p-6 relative overflow-hidden">
            <div class="w-20 h-20 rounded-2xl bg-white p-3 shadow-xl flex items-center justify-center">
              <img
                :src="activeConvenio.logo"
                :alt="activeConvenio.nombre"
                class="w-full h-full object-contain"
              >
            </div>
          </div>
        </div>

        <div class="p-6 sm:p-8 overflow-y-auto space-y-4">
          <div class="flex flex-wrap items-center gap-3">
            <span class="px-3 py-1 rounded-full text-xs sm:text-sm font-bold tracking-wide bg-slate-100 text-[#0A2540] border border-slate-200">
              {{ activeConvenio.siglas }}
            </span>
            <span class="text-xs sm:text-sm font-medium text-slate-600 flex items-center gap-1.5">
              <CalendarCheck class="h-4 w-4 text-slate-500" aria-hidden="true" />
              <span>Vigente hasta</span>
              <strong class="text-slate-800">{{ activeConvenio.vigencia }}</strong>
            </span>
          </div>

          <h3 id="convenio-modal-title" class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug">
            {{ activeConvenio.nombre }}
          </h3>

          <div class="bg-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-200">
            <h4 class="text-sm sm:text-base font-semibold text-slate-900 mb-2.5 flex items-center gap-2">
              <Award class="h-5 w-5 text-[#0A2540]" aria-hidden="true" />
              <span>Beneficios para participantes</span>
            </h4>
            <ul class="text-xs sm:text-sm text-slate-700 space-y-2">
              <li class="flex items-start gap-2">
                <CheckCircle2 class="h-4 w-4 text-[#0A2540] mt-0.5 shrink-0" aria-hidden="true" />
                <span>Certificados oficiales con firma y sello de Decanatura y representantes de Horus Group.</span>
              </li>
              <li class="flex items-start gap-2">
                <CheckCircle2 class="h-4 w-4 text-[#0A2540] mt-0.5 shrink-0" aria-hidden="true" />
                <span>Validez acreditada para postulaciones y convocatorias en entidades públicas y privadas a nivel nacional.</span>
              </li>
            </ul>
          </div>

          <div>
            <h4 class="text-sm sm:text-base font-semibold text-slate-900 mb-2">Alcance del convenio</h4>
            <p class="text-xs sm:text-sm leading-relaxed text-slate-600">
              {{ activeConvenio.descripcion }}
            </p>
          </div>

          <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <div class="text-xs text-slate-500">
              Horus Group SRL © 2026 Alianzas Estratégicas
            </div>
            <button
              type="button"
              class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-[#0A2540] text-white hover:bg-slate-800 transition-colors shadow-sm cursor-pointer"
              @click="activeConvenio = null"
            >
              Entendido
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
