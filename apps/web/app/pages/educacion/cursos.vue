<script setup lang="ts">
import { ref, computed } from 'vue';
import { Award, BookOpen, Clock, Laptop, GraduationCap, MessageCircle } from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';
import type { ProgramItem } from '~/types/horus';

useHead({ title: 'Cursos' });

const modalidadFiltro = ref<string>('todos');
const { data, pending, error } = await useFetch<{ data: ProgramItem[] }>('/api/bff/public/programs', {
  query: { type: 'course' },
});

const programs = computed(() => data.value?.data ?? []);
const filteredPrograms = computed(() => {
  if (modalidadFiltro.value === 'todos') return programs.value;
  return programs.value.filter((p) => p.cohorts?.some((c) => c.modality === modalidadFiltro.value));
});
</script>

<template>
  <div class="pt-20">
    <section class="py-16 sm:py-24 bg-gradient-to-br from-[#0A2540] via-[#102d49] to-[#1a2f45] text-white text-center">
      <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="inline-flex items-center gap-2.5 px-6 py-2.5 rounded-full border-2 border-gold bg-gold/20 mb-8">
          <Award class="h-5 w-5 text-gold" />
          <span class="text-sm font-semibold tracking-wider text-gold uppercase">CERTIFICACIÓN OFICIAL</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-bold tracking-tight leading-tight mb-6">
          CURSOS CON CERTIFICADOS FÍSICOS Y DIGITALES
        </h1>
        <p class="text-base sm:text-lg text-white/90 max-w-3xl mx-auto">
          Formación práctica con modalidades virtual, presencial e híbrida y verificación QR.
        </p>
      </div>
    </section>

    <section class="bg-[#F7F9FC] py-14 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-10">
          <h2 class="text-2xl font-bold text-[#0A2540]">Catálogo de Cursos Vigentes</h2>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="mod in [
                { id: 'todos', label: 'Todas las modalidades' },
                { id: 'hybrid', label: 'Híbrido' },
                { id: 'virtual', label: 'Virtual' },
                { id: 'presential', label: 'Presencial' },
              ]"
              :key="mod.id"
              type="button"
              :class="[
                'rounded-full px-4 py-2 text-xs font-semibold transition cursor-pointer',
                modalidadFiltro === mod.id ? 'bg-[#0A2540] text-gold' : 'bg-white border border-slate-200 text-slate-700',
              ]"
              @click="modalidadFiltro = mod.id"
            >
              {{ mod.label }}
            </button>
          </div>
        </div>

        <p v-if="pending" class="text-sm text-slate-600">Cargando catálogo…</p>
        <p v-else-if="error" class="text-sm text-red-700">No se pudo cargar el catálogo en este momento.</p>
        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <article
            v-for="program in filteredPrograms"
            :key="program.id"
            class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm flex flex-col justify-between"
          >
            <div>
              <img
                :src="program.image_path || '/images/capacitaciones/imagen 1.jpg'"
                :alt="program.name"
                class="h-48 w-full object-cover"
              >
              <div class="p-6">
                <span class="text-xs font-bold uppercase tracking-wider text-gold-text">{{ program.code }}</span>
                <h3 class="mt-1 text-lg font-bold text-[#0A2540]">{{ program.name }}</h3>
                <p class="mt-2 text-sm text-slate-600 line-clamp-3">{{ program.description }}</p>

                <div class="mt-4 flex flex-wrap gap-3 text-xs text-slate-700">
                  <span class="inline-flex items-center gap-1"><Clock class="h-4 w-4 text-gold-text" /> {{ program.duration_hours }} h</span>
                  <span class="inline-flex items-center gap-1 font-bold text-[#0A2540]">S/ {{ program.price }}</span>
                </div>

                <div v-if="program.cohorts && program.cohorts.length > 0" class="mt-4 space-y-2">
                  <p class="text-xs font-semibold text-slate-500 uppercase">Próximas ediciones</p>
                  <div
                    v-for="cohort in program.cohorts"
                    :key="cohort.id"
                    class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-xs"
                  >
                    <p class="font-semibold text-[#0A2540]">{{ cohort.name }}</p>
                    <p class="text-slate-600 mt-0.5 flex items-center gap-1.5">
                      <Laptop v-if="cohort.modality === 'virtual'" class="h-3.5 w-3.5" />
                      <GraduationCap v-else class="h-3.5 w-3.5" />
                      <span>Modalidad: {{ cohort.modality }} · Cupos: {{ cohort.available_slots }}</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="p-6 pt-0 flex gap-2">
              <a
                :href="`https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent(`Hola Horus Academic, deseo inscribirme en el curso: ${program.name}`)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-800"
              >
                <MessageCircle class="h-4 w-4" />
                <span>Inscribirme por WhatsApp</span>
              </a>
              <NuxtLink
                to="/registro"
                :aria-label="`Registrarme en ${program.name}`"
                class="inline-flex items-center justify-center rounded-xl border border-[#0A2540] px-4 py-2.5 text-xs font-bold text-[#0A2540] hover:bg-[#0A2540] hover:text-white transition"
              >
                <BookOpen class="h-4 w-4" aria-hidden="true" />
              </NuxtLink>
            </div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>
