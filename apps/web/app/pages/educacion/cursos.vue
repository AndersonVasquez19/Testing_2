<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import {
  Award,
  BookOpen,
  Clock,
  GraduationCap,
  Home,
  Laptop,
  Check,
  ArrowRight,
  CalendarDays,
  MessageCircle,
  X,
} from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';
import type { ProgramItem } from '~/types/horus';

useHead({ title: 'Cursos' });

const modalidadFiltro = ref<string>('todos');
const cursoDetalle = ref<ProgramItem | null>(null);

const { data, pending, error } = await useFetch<{ data: ProgramItem[] }>('/api/bff/public/programs', {
  query: { type: 'course' },
});

const programs = computed(() => data.value?.data ?? []);

const modalityLabel = (mod: string) => {
  if (mod === 'presential') return 'Presencial';
  if (mod === 'hybrid') return 'Híbrido / Semipresencial';
  if (mod === 'virtual') return 'Virtual';
  return mod;
};

const filteredPrograms = computed(() => {
  if (modalidadFiltro.value === 'todos') return programs.value;
  return programs.value.filter((p) =>
    p.cohorts?.some((c) => {
      if (modalidadFiltro.value === 'hybrid') return c.modality === 'hybrid';
      return c.modality === modalidadFiltro.value;
    })
  );
});

const selectModalityAndScroll = (mod: 'presential' | 'hybrid' | 'virtual') => {
  modalidadFiltro.value = mod;
  if (typeof document !== 'undefined') {
    document.getElementById('catalogo-cursos')?.scrollIntoView({ behavior: 'smooth' });
  }
};

watch(cursoDetalle, (val) => {
  if (typeof document !== 'undefined') {
    document.body.style.overflow = val ? 'hidden' : '';
  }
});

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && cursoDetalle.value) {
    cursoDetalle.value = null;
  }
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
    <!-- Hero Cursos con 3 estadísticas clave -->
    <section class="relative overflow-hidden py-16 sm:py-24 bg-gradient-to-br from-[#0A2540] via-[#102d49] to-[#1a2f45] text-white text-center">
      <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="inline-flex items-center gap-2.5 px-6 py-2.5 rounded-full border-2 border-gold bg-gold/20 mb-8">
          <Award class="h-5 w-5 text-gold" aria-hidden="true" />
          <span class="text-sm font-semibold tracking-wider text-gold uppercase">CERTIFICACIÓN OFICIAL</span>
        </div>

        <h1 class="text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight mb-6">
          CURSOS CON CERTIFICADOS FÍSICOS Y DIGITALES
        </h1>

        <p class="text-base sm:text-lg text-white/90 leading-relaxed max-w-3xl mx-auto mb-10 text-justify sm:text-center">
          Te ofrecemos cursos teórico-prácticos diseñados según las necesidades del mercado para seguir fortaleciendo tus competencias personales y profesionales, con modalidades flexibles y verificación QR oficial.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 max-w-3xl mx-auto">
          <div class="flex items-center gap-4 p-4 sm:p-5 rounded-xl border-2 border-gold/30 bg-white/10 hover:border-gold hover:-translate-y-1 transition-all">
            <BookOpen class="h-8 w-8 text-gold shrink-0" aria-hidden="true" />
            <div class="text-left">
              <strong class="block text-2xl font-bold text-white tabular-nums">+10</strong>
              <span class="text-xs text-white/80">Cursos Disponibles</span>
            </div>
          </div>

          <div class="flex items-center gap-4 p-4 sm:p-5 rounded-xl border-2 border-gold/30 bg-white/10 hover:border-gold hover:-translate-y-1 transition-all">
            <Clock class="h-8 w-8 text-gold shrink-0" aria-hidden="true" />
            <div class="text-left">
              <strong class="block text-2xl font-bold text-white">FLEXIBLE</strong>
              <span class="text-xs text-white/80">Horarios Adaptables</span>
            </div>
          </div>

          <div class="flex items-center gap-4 p-4 sm:p-5 rounded-xl border-2 border-gold/30 bg-white/10 hover:border-gold hover:-translate-y-1 transition-all">
            <Award class="h-8 w-8 text-gold shrink-0" aria-hidden="true" />
            <div class="text-left">
              <strong class="block text-2xl font-bold text-white">DOBLE</strong>
              <span class="text-xs text-white/80">Certificación Física y Digital</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Sección Modalidades de Cursos -->
    <section class="bg-gradient-to-b from-white to-[#f8f9fa] py-16 sm:py-24 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="relative inline-block pb-4 text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight text-[#0A2540]">
            MODALIDADES DE CURSOS
            <span class="absolute bottom-0 left-1/2 -translate-x-1/2 h-1 w-32 rounded-full animate-line-shine" />
          </h2>
          <p class="mt-4 text-base text-slate-600">
            Elige la modalidad que mejor se adapte a tu ritmo y disponibilidad.
          </p>
        </div>

        <div class="grid gap-6 lg:gap-8 md:grid-cols-3 items-stretch">
          <!-- Presencial -->
          <article class="group rounded-2xl bg-white border border-stone-200/90 p-7 sm:p-8 shadow-[0_6px_24px_rgba(10,37,64,0.06)] hover:-translate-y-1 hover:shadow-[0_14px_34px_rgba(10,37,64,0.1)] hover:border-gold/60 transition-all duration-300 flex flex-col">
            <div class="flex items-center justify-between gap-3 mb-6">
              <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#0A2540]/6 text-[#0A2540] border border-[#0A2540]/10 group-hover:bg-[#0A2540] group-hover:text-gold transition-colors">
                <GraduationCap class="h-6 w-6" aria-hidden="true" />
              </div>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-stone-100 text-[#0A2540] border border-stone-200/80">
                Tradicional
              </span>
            </div>

            <h3 class="text-xl font-bold tracking-tight text-[#0A2540] mb-3">PRESENCIAL</h3>

            <p class="text-sm text-slate-600 leading-relaxed mb-6">
              Sesiones teóricas y prácticas íntegramente presenciales en sede para potenciar tus habilidades con aprendizaje directo y personalizado.
            </p>

            <ul class="space-y-2.5 border-t border-stone-100 pt-5 mb-7 text-sm text-slate-700 font-medium">
              <li class="flex items-center gap-2.5">
                <Check class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                <span>Interacción directa en aula</span>
              </li>
              <li class="flex items-center gap-2.5">
                <Check class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                <span>Práctica inmediata</span>
              </li>
              <li class="flex items-center gap-2.5">
                <Check class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                <span>Networking presencial</span>
              </li>
            </ul>

            <div class="mt-auto pt-2">
              <button
                type="button"
                class="inline-flex w-full items-center justify-between rounded-xl border border-stone-200 bg-stone-50/70 px-4 py-2.5 text-sm font-semibold text-[#0A2540] hover:border-[#0A2540] hover:bg-[#0A2540] hover:text-white transition-colors cursor-pointer"
                @click="selectModalityAndScroll('presential')"
              >
                <span>Ver cursos presenciales</span>
                <ArrowRight class="h-4 w-4" aria-hidden="true" />
              </button>
            </div>
          </article>

          <!-- Híbrido / Semipresencial (Destacada) -->
          <article class="group relative rounded-2xl bg-gradient-to-b from-gold/[0.06] via-white to-white border border-gold/70 p-7 sm:p-8 shadow-[0_10px_30px_rgba(201,169,97,0.16)] hover:-translate-y-1 hover:shadow-[0_16px_40px_rgba(201,169,97,0.22)] transition-all duration-300 flex flex-col overflow-hidden">
            <div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#0A2540] via-gold to-[#0A2540]" />

            <div class="flex items-center justify-between gap-3 mb-6">
              <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#0A2540] text-gold shadow-sm">
                <Home class="h-6 w-6" aria-hidden="true" />
              </div>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gold text-[#0A2540]">
                Más Popular
              </span>
            </div>

            <h3 class="text-xl font-bold tracking-tight text-[#0A2540] mb-3">SEMIPRESENCIAL / HÍBRIDO</h3>

            <p class="text-sm text-slate-600 leading-relaxed mb-6">
              Combina sesiones teóricas virtuales en vivo por Meet/Zoom y grabaciones de repaso con talleres presenciales flexibles según el cronograma de la cohorte.
            </p>

            <ul class="space-y-2.5 border-t border-gold/20 pt-5 mb-7 text-sm text-slate-700 font-medium">
              <li class="flex items-center gap-2.5">
                <Check class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                <span>Lo mejor de ambos mundos</span>
              </li>
              <li class="flex items-center gap-2.5">
                <Check class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                <span>Clases grabadas de repaso</span>
              </li>
              <li class="flex items-center gap-2.5">
                <Check class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                <span>Sesiones híbridas por fecha</span>
              </li>
            </ul>

            <div class="mt-auto pt-2">
              <button
                type="button"
                class="inline-flex w-full items-center justify-between rounded-xl bg-[#0A2540] px-4 py-2.5 text-sm font-semibold text-white hover:bg-gold hover:text-[#0A2540] transition-colors cursor-pointer"
                @click="selectModalityAndScroll('hybrid')"
              >
                <span>Ver cursos híbridos</span>
                <ArrowRight class="h-4 w-4" aria-hidden="true" />
              </button>
            </div>
          </article>

          <!-- Virtual -->
          <article class="group rounded-2xl bg-white border border-stone-200/90 p-7 sm:p-8 shadow-[0_6px_24px_rgba(10,37,64,0.06)] hover:-translate-y-1 hover:shadow-[0_14px_34px_rgba(10,37,64,0.1)] hover:border-gold/60 transition-all duration-300 flex flex-col">
            <div class="flex items-center justify-between gap-3 mb-6">
              <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#0A2540]/6 text-[#0A2540] border border-[#0A2540]/10 group-hover:bg-[#0A2540] group-hover:text-gold transition-colors">
                <Laptop class="h-6 w-6" aria-hidden="true" />
              </div>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-stone-100 text-[#0A2540] border border-stone-200/80">
                100% Online
              </span>
            </div>

            <h3 class="text-xl font-bold tracking-tight text-[#0A2540] mb-3">VIRTUAL</h3>

            <p class="text-sm text-slate-600 leading-relaxed mb-6">
              Estudia desde cualquier ciudad con clases en vivo, acceso a grabaciones y material digital descargable desde tu Aula Virtual.
            </p>

            <ul class="space-y-2.5 border-t border-stone-100 pt-5 mb-7 text-sm text-slate-700 font-medium">
              <li class="flex items-center gap-2.5">
                <Check class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                <span>Desde cualquier lugar</span>
              </li>
              <li class="flex items-center gap-2.5">
                <Check class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                <span>Grabaciones en Aula Virtual</span>
              </li>
              <li class="flex items-center gap-2.5">
                <Check class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                <span>Material descargable PDF</span>
              </li>
            </ul>

            <div class="mt-auto pt-2">
              <button
                type="button"
                class="inline-flex w-full items-center justify-between rounded-xl border border-stone-200 bg-stone-50/70 px-4 py-2.5 text-sm font-semibold text-[#0A2540] hover:border-[#0A2540] hover:bg-[#0A2540] hover:text-white transition-colors cursor-pointer"
                @click="selectModalityAndScroll('virtual')"
              >
                <span>Ver cursos virtuales</span>
                <ArrowRight class="h-4 w-4" aria-hidden="true" />
              </button>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Catálogo de Cursos Vigentes -->
    <section id="catalogo-cursos" class="bg-white py-16 sm:py-24 border-t border-stone-200 scroll-mt-6 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#0A2540] tracking-tight">
            {{ modalidadFiltro !== 'todos' ? `CURSOS: ${modalityLabel(modalidadFiltro).toUpperCase()}` : 'CATÁLOGO DE CURSOS VIGENTES' }}
          </h2>
          <div class="mt-3 h-1 w-20 rounded-full bg-gold mx-auto" />
          <p class="mt-3 text-sm text-slate-600">
            {{ filteredPrograms.length }} {{ filteredPrograms.length === 1 ? 'programa disponible' : 'programas disponibles' }}. Consulta temarios, ediciones o solicita tu matrícula.
          </p>

          <div class="flex flex-wrap justify-center gap-2 mt-6">
            <button
              v-for="mod in [
                { id: 'todos', label: 'Todos' },
                { id: 'presential', label: 'Presencial' },
                { id: 'hybrid', label: 'Híbrido' },
                { id: 'virtual', label: 'Virtual' },
              ]"
              :key="mod.id"
              type="button"
              :class="[
                'rounded-full px-4 py-1.5 text-xs font-semibold transition-all cursor-pointer',
                modalidadFiltro === mod.id
                  ? 'bg-[#0A2540] text-white shadow-sm'
                  : 'bg-stone-100 text-slate-700 hover:bg-stone-200',
              ]"
              @click="modalidadFiltro = mod.id"
            >
              {{ mod.label }}
            </button>
          </div>
        </div>

        <p v-if="pending" class="text-center text-sm text-slate-600 py-8">Cargando catálogo de cursos…</p>
        <div
          v-else-if="error"
          role="alert"
          class="mx-auto max-w-xl rounded-2xl border border-red-200 bg-red-50 px-6 py-6 text-center text-sm text-red-800"
        >
          No pudimos cargar el listado de cursos en este momento. Puedes consultarnos por WhatsApp.
        </div>
        <div
          v-else-if="filteredPrograms.length === 0"
          role="status"
          class="mx-auto max-w-xl rounded-2xl border border-stone-200 bg-stone-50 px-6 py-8 text-center text-sm text-slate-600"
        >
          No hay cursos publicados actualmente para la modalidad seleccionada.
        </div>

        <div v-else class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
          <article
            v-for="program in filteredPrograms"
            :key="program.id"
            class="group rounded-2xl border border-stone-200/90 bg-white shadow-[0_8px_26px_rgba(10,37,64,0.06)] hover:shadow-[0_16px_38px_rgba(10,37,64,0.11)] hover:border-gold/60 transition-all duration-300 overflow-hidden flex flex-col"
          >
            <div class="relative aspect-[16/10] w-full overflow-hidden bg-gradient-to-br from-[#0A2540] via-[#0F3154] to-[#174066]">
              <img
                :src="program.image_path || '/images/capacitaciones/imagen 1.jpg'"
                :alt="program.name"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
              >
              <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-[#0A2540]/65 via-[#0A2540]/10 to-transparent" />
              <div class="absolute inset-x-4 top-3.5 flex items-center justify-between gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-[#0A2540]/90 backdrop-blur-sm border border-gold/40 px-3 py-1 text-xs font-semibold text-gold-light shadow-sm">
                  {{ program.code }}
                </span>
                <span
                  v-if="program.cohorts && program.cohorts[0]"
                  class="inline-flex items-center gap-1 rounded-full bg-[#0A2540]/85 backdrop-blur-sm px-2.5 py-1 text-[11px] font-medium text-white"
                >
                  {{ modalityLabel(program.cohorts[0].modality) }}
                </span>
              </div>
            </div>

            <div class="p-6 sm:p-7 flex-1 flex flex-col">
              <h3 class="text-xl font-bold text-[#0A2540] mb-2.5 leading-snug tracking-tight group-hover:text-[#153A59] transition-colors">
                {{ program.name }}
              </h3>

              <p class="text-sm text-slate-600 leading-relaxed mb-5 line-clamp-3">
                {{ program.description }}
              </p>

              <div class="mt-auto space-y-2 rounded-xl bg-stone-50/90 border border-stone-200/75 p-4 text-[13px] text-slate-700">
                <div class="flex items-center justify-between gap-2">
                  <span class="inline-flex items-center gap-1.5">
                    <Clock class="h-4 w-4 text-gold-text" aria-hidden="true" />
                    <span>{{ program.duration_hours }} horas</span>
                  </span>
                  <span class="font-bold text-[#0A2540] tabular-nums">S/ {{ program.price }}</span>
                </div>
                <div
                  v-for="cohort in (program.cohorts ?? []).slice(0, 2)"
                  :key="cohort.id"
                  class="pt-2 border-t border-stone-200/70 text-xs flex items-center justify-between gap-2"
                >
                  <span class="inline-flex items-center gap-1.5 font-medium text-[#0A2540] truncate">
                    <CalendarDays class="h-3.5 w-3.5 text-gold-text shrink-0" aria-hidden="true" />
                    {{ cohort.name }}
                  </span>
                  <span class="text-slate-600 shrink-0 tabular-nums">{{ cohort.available_slots }} cupos</span>
                </div>
              </div>

              <div class="mt-6 pt-4 border-t border-stone-100 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <button
                  type="button"
                  class="inline-flex h-10 items-center justify-center rounded-xl border border-stone-300 bg-white px-4 text-xs font-semibold text-[#0A2540] hover:border-[#0A2540] hover:bg-stone-50 transition-colors cursor-pointer"
                  @click="cursoDetalle = program"
                >
                  Ver detalles
                </button>
                <a
                  :href="`https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent(`Hola Horus Academic, deseo solicitar matrícula para el curso: ${program.name}.`)}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex h-10 flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-800 px-4 text-xs font-semibold text-white hover:bg-emerald-900 transition-colors shadow-sm"
                >
                  <MessageCircle class="h-3.5 w-3.5" aria-hidden="true" />
                  <span>Solicitar matrícula</span>
                </a>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- Modal detalle de curso y temario -->
    <div
      v-if="cursoDetalle"
      role="dialog"
      aria-modal="true"
      aria-labelledby="curso-modal-title"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/75 backdrop-blur-sm animate-fade-in"
      @click.self="cursoDetalle = null"
    >
      <div class="relative w-full max-w-2xl max-h-[90vh] flex flex-col bg-white text-slate-900 rounded-3xl shadow-2xl border border-slate-100 overflow-hidden animate-zoom-in">
        <button
          type="button"
          aria-label="Cerrar detalle del curso"
          class="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-white/95 hover:bg-white text-slate-700 hover:text-slate-900 flex items-center justify-center shadow-md border border-slate-200 transition-all hover:rotate-90 cursor-pointer"
          @click="cursoDetalle = null"
        >
          <X class="h-5 w-5" />
        </button>

        <div class="relative h-48 sm:h-56 w-full shrink-0 bg-[#0A2540]">
          <img
            :src="cursoDetalle.image_path || '/images/capacitaciones/imagen 1.jpg'"
            :alt="cursoDetalle.name"
            class="h-full w-full object-cover"
          >
          <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent" />
          <span class="absolute bottom-4 left-6 rounded-full bg-gold px-3 py-1 text-xs font-bold text-[#0A2540]">
            {{ cursoDetalle.code }}
          </span>
        </div>

        <div class="p-6 sm:p-8 overflow-y-auto space-y-5">
          <div>
            <h3 id="curso-modal-title" class="text-xl sm:text-2xl font-bold text-[#0A2540] leading-snug">
              {{ cursoDetalle.name }}
            </h3>
            <p class="mt-2 text-sm text-slate-600 leading-relaxed">
              {{ cursoDetalle.description }}
            </p>
          </div>

          <div class="grid gap-3 sm:grid-cols-3 rounded-xl bg-stone-50 border border-stone-200 p-4 text-xs text-slate-700">
            <div>
              <span class="block text-slate-500">Duración</span>
              <strong class="text-sm text-[#0A2540]">{{ cursoDetalle.duration_hours }} horas</strong>
            </div>
            <div>
              <span class="block text-slate-500">Inversión</span>
              <strong class="text-sm text-[#0A2540] tabular-nums">S/ {{ cursoDetalle.price }}</strong>
            </div>
            <div>
              <span class="block text-slate-500">Certificación</span>
              <strong class="text-sm text-[#0A2540]">{{ cursoDetalle.certification_label || 'Física y Digital QR' }}</strong>
            </div>
          </div>

          <div v-if="cursoDetalle.syllabus_json && cursoDetalle.syllabus_json.length > 0">
            <h4 class="text-sm font-bold text-[#0A2540] mb-3">Temario / Módulos</h4>
            <div class="space-y-3">
              <div
                v-for="(mod, idx) in cursoDetalle.syllabus_json"
                :key="idx"
                class="rounded-xl border border-stone-200 bg-white p-3.5"
              >
                <p class="text-xs font-bold text-[#0A2540]">
                  {{ mod.module }}
                  <span v-if="mod.hours" class="text-gold-text font-semibold ml-1">({{ mod.hours }} h)</span>
                </p>
                <ul v-if="mod.topics?.length" class="mt-1.5 space-y-1 text-xs text-slate-600">
                  <li v-for="t in mod.topics" :key="t" class="flex items-start gap-2">
                    <Check class="h-3.5 w-3.5 text-gold-text shrink-0 mt-0.5" aria-hidden="true" />
                    <span>{{ t }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="pt-4 border-t border-stone-100 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <NuxtLink
              to="/registro"
              class="inline-flex h-11 items-center justify-center rounded-xl border border-[#0A2540] bg-white px-5 text-sm font-semibold text-[#0A2540] hover:bg-stone-50 transition-colors"
            >
              Crear cuenta y matricularme
            </NuxtLink>
            <a
              :href="`https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent(`Hola Horus Academic, deseo solicitar matrícula para el curso: ${cursoDetalle.name}.`)}`"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-800 px-5 text-sm font-semibold text-white hover:bg-emerald-900 transition-colors shadow-sm"
            >
              <MessageCircle class="h-4 w-4" aria-hidden="true" />
              <span>Solicitar matrícula por WhatsApp</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
