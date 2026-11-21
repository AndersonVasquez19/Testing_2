<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { AdvisorySlotItem, ProgramItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['coordinator', 'admin'],
});
useHead({ title: 'Agenda de Asesorías 1 a 1' });

const slots = ref<AdvisorySlotItem[]>([]);
const programs = ref<ProgramItem[]>([]);
const form = ref({
  program_id: '',
  slot_date: '',
  start_time: '16:00',
  end_time: '17:00',
  modality: 'virtual',
  meeting_or_location: 'https://meet.google.com/horus-asesoria',
});

const loadAll = async () => {
  const [sRes, pRes] = await Promise.all([
    $fetch<{ data: AdvisorySlotItem[] }>('/api/bff/coordinator/advisory-slots'),
    $fetch<{ data: ProgramItem[] }>('/api/bff/public/programs', { query: { type: 'advisory' } }),
  ]);
  slots.value = sRes.data;
  programs.value = pRes.data;
  if (!form.value.program_id && programs.value[0]) form.value.program_id = String(programs.value[0].id);
};

const createSlot = async () => {
  await $fetch('/api/bff/coordinator/advisory-slots', {
    method: 'POST',
    body: { ...form.value, program_id: Number(form.value.program_id) },
  });
  await loadAll();
};

onMounted(loadAll);
</script>

<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold text-[#0A2540]">Agenda de Asesoramientos 1 a 1</h2>

    <form class="rounded-2xl bg-white border border-slate-200 p-6 grid gap-3 sm:grid-cols-3" @submit.prevent="createSlot">
      <select v-model="form.program_id" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <option v-for="p in programs" :key="p.id" :value="p.id">{{ p.name }}</option>
      </select>
      <input v-model="form.slot_date" type="date" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <select v-model="form.modality" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <option value="virtual">Virtual</option>
        <option value="presential">Presencial</option>
      </select>
      <input v-model="form.start_time" type="time" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <input v-model="form.end_time" type="time" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <input v-model="form.meeting_or_location" placeholder="Link Meet o Sede" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <button type="submit" class="sm:col-span-3 rounded-xl bg-[#0A2540] px-5 py-2.5 text-xs font-bold text-white w-fit cursor-pointer">
        Publicar Horario Disponible
      </button>
    </form>

    <div class="grid gap-3 md:grid-cols-2">
      <div v-for="slot in slots" :key="slot.id" class="rounded-2xl bg-white border border-slate-200 p-4 text-sm">
        <p class="font-bold text-[#0A2540]">{{ slot.slot_date }} · {{ slot.start_time }} - {{ slot.end_time }}</p>
        <p class="text-xs text-slate-600">{{ slot.modality }} · {{ slot.meeting_or_location }}</p>
        <p class="mt-2 text-xs font-semibold" :class="slot.is_booked ? 'text-emerald-700' : 'text-amber-700'">
          {{ slot.is_booked ? `Reservado por ${slot.student?.name ?? 'Estudiante'} (${slot.topic ?? ''})` : 'Disponible' }}
        </p>
      </div>
    </div>
  </div>
</template>
