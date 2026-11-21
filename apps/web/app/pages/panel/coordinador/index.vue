<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { CohortItem, ProgramItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['coordinator', 'admin'],
});
useHead({ title: 'Coordinación Académica' });

const cohorts = ref<CohortItem[]>([]);
const programs = ref<ProgramItem[]>([]);
const notice = ref('');

const newCohort = ref({
  program_id: '',
  code: '',
  name: '',
  modality: 'hybrid',
  whatsapp_group_link: '',
  default_meeting_url: '',
  location_name: 'Sede Central Horus Group',
  location_address: 'Jr. José Gálvez 322, Cajamarca',
  start_date: '',
  end_date: '',
  max_capacity: 25,
  status: 'open',
});

const loadData = async () => {
  const [cRes, pRes] = await Promise.all([
    $fetch<{ data: CohortItem[] }>('/api/bff/coordinator/cohorts'),
    $fetch<{ data: ProgramItem[] }>('/api/bff/public/programs'),
  ]);
  cohorts.value = cRes.data;
  programs.value = pRes.data;
};

const createCohort = async () => {
  await $fetch('/api/bff/coordinator/cohorts', {
    method: 'POST',
    body: {
      ...newCohort.value,
      program_id: Number(newCohort.value.program_id),
    },
  });
  notice.value = 'Convocatoria creada correctamente.';
  await loadData();
};

onMounted(loadData);
</script>

<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-[#0A2540]">Coordinación Académica · Convocatorias</h2>
      <p class="text-sm text-slate-600">Abre cohortes híbridas, configura el grupo de WhatsApp y programa sesiones individuales.</p>
    </div>

    <p v-if="notice" role="status" class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm text-emerald-800">
      {{ notice }}
    </p>

    <form class="rounded-2xl bg-white border border-slate-200 p-6 space-y-4" @submit.prevent="createCohort">
      <h3 class="font-bold text-[#0A2540]">Abrir Nueva Convocatoria / Edición</h3>
      <div class="grid gap-3 sm:grid-cols-3">
        <select v-model="newCohort.program_id" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
          <option value="">Programa…</option>
          <option v-for="p in programs" :key="p.id" :value="p.id">{{ p.name }}</option>
        </select>
        <input v-model="newCohort.code" placeholder="Código (ED-2026-004)" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <input v-model="newCohort.name" placeholder="Nombre de edición" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <select v-model="newCohort.modality" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
          <option value="hybrid">Híbrido (sesiones mixtas)</option>
          <option value="virtual">Virtual</option>
          <option value="presential">Presencial</option>
        </select>
        <input v-model="newCohort.start_date" type="date" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <input v-model="newCohort.end_date" type="date" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <input v-model="newCohort.whatsapp_group_link" placeholder="https://chat.whatsapp.com/..." class="rounded-xl border border-slate-300 px-3 py-2 text-sm sm:col-span-2">
        <input v-model="newCohort.max_capacity" type="number" min="1" placeholder="Cupo máximo" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      </div>
      <button type="submit" class="rounded-xl bg-[#0A2540] px-5 py-2.5 text-xs font-bold text-white cursor-pointer">
        Crear Convocatoria
      </button>
    </form>

    <div class="grid gap-4 md:grid-cols-2">
      <article v-for="c in cohorts" :key="c.id" class="rounded-2xl bg-white border border-slate-200 p-5 space-y-2">
        <div class="flex items-center justify-between">
          <span class="text-xs font-mono font-bold text-gold-text">{{ c.code }}</span>
          <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold uppercase">{{ c.modality }} · {{ c.status }}</span>
        </div>
        <h3 class="font-bold text-[#0A2540]">{{ c.name }}</h3>
        <p class="text-xs text-slate-600">{{ c.program?.name }} · Cupos: {{ c.enrolled_count }}/{{ c.max_capacity }}</p>
        <div class="pt-2 flex gap-3 text-xs font-semibold">
          <NuxtLink :to="`/panel/coordinador/sesiones?cohort=${c.id}`" class="text-[#1B4469] hover:underline">
            Gestionar sesiones híbridas
          </NuxtLink>
          <NuxtLink :to="`/panel/coordinador/academico?cohort=${c.id}`" class="text-emerald-700 hover:underline">
            Aprobar / Emitir certificados
          </NuxtLink>
        </div>
      </article>
    </div>
  </div>
</template>
