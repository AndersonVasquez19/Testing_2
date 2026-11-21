<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { AdvisorySlotItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['student', 'admin'],
});
useHead({ title: 'Asesorías 1 a 1' });

const slots = ref<AdvisorySlotItem[]>([]);
const notice = ref('');

const loadSlots = async () => {
  const res = await $fetch<{ data: AdvisorySlotItem[] }>('/api/bff/student/advisories');
  slots.value = res.data;
};

const book = async (slot: AdvisorySlotItem) => {
  const topic = window.prompt('Tema o consulta para la asesoría 1 a 1:');
  if (!topic || topic.trim().length < 5) return;
  await $fetch(`/api/bff/student/advisories/${slot.id}/book`, {
    method: 'POST',
    body: { topic: topic.trim() },
  });
  notice.value = 'Asesoría reservada correctamente.';
  await loadSlots();
};

onMounted(loadSlots);
</script>

<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold text-[#0A2540]">Reservar Asesoramiento 1 a 1</h2>
    <p v-if="notice" role="status" class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm text-emerald-800">
      {{ notice }}
    </p>
    <div class="grid gap-4 md:grid-cols-2">
      <article v-for="slot in slots" :key="slot.id" class="rounded-2xl bg-white border border-slate-200 p-5 space-y-2">
        <p class="font-bold text-[#0A2540]">{{ slot.program?.name ?? 'Asesoría 1 a 1' }}</p>
        <p class="text-xs text-slate-600">{{ slot.slot_date }} · {{ slot.start_time }} - {{ slot.end_time }} · {{ slot.modality }}</p>
        <p class="text-xs text-slate-500">Asesor: {{ slot.advisor?.name }}</p>
        <p v-if="slot.is_booked" class="text-xs font-semibold text-emerald-700">
          Reservada · {{ slot.meeting_or_location }}
        </p>
        <button
          v-else
          type="button"
          class="mt-2 rounded-xl bg-[#0A2540] px-4 py-2 text-xs font-bold text-white cursor-pointer"
          @click="book(slot)"
        >
          Reservar horario
        </button>
      </article>
    </div>
  </div>
</template>
