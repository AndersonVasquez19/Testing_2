<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { ComplaintItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['admin'],
});
useHead({ title: 'Libro de Reclamaciones · Admin' });

const complaints = ref<ComplaintItem[]>([]);
const notice = ref('');

const loadComplaints = async () => {
  const res = await $fetch<{ data: ComplaintItem[] }>('/api/bff/admin/complaints');
  complaints.value = res.data;
};

const respond = async (c: ComplaintItem) => {
  const response = window.prompt(`Respuesta oficial para ${c.correlative_code}:`);
  if (!response || response.trim().length < 10) return;
  await $fetch(`/api/bff/admin/complaints/${c.id}/respond`, {
    method: 'POST',
    body: { company_response: response.trim(), status: 'closed' },
  });
  notice.value = `Reclamación ${c.correlative_code} respondida y cerrada.`;
  await loadComplaints();
};

onMounted(loadComplaints);
</script>

<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold text-[#0A2540]">Libro de Reclamaciones Virtual (Gestión Oficial)</h2>
    <p v-if="notice" role="status" class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2 text-sm text-emerald-800">{{ notice }}</p>

    <div class="space-y-3">
      <article v-for="c in complaints" :key="c.id" class="rounded-2xl bg-white border border-slate-200 p-5 space-y-2">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <span class="font-mono text-xs font-bold text-[#0A2540]">{{ c.correlative_code }} · Plazo legal: {{ c.legal_deadline }}</span>
          <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold uppercase">{{ c.status }}</span>
        </div>
        <p class="font-bold text-slate-900">{{ c.consumer_name }} ({{ c.consumer_document_type.toUpperCase() }} {{ c.consumer_document_number }})</p>
        <p class="text-sm text-slate-700">{{ c.detail }}</p>
        <p v-if="c.company_response" class="rounded-xl bg-emerald-50 p-3 text-xs text-emerald-900">
          <strong>Respuesta:</strong> {{ c.company_response }}
        </p>
        <button
          v-else
          type="button"
          class="rounded-xl bg-[#0A2540] px-4 py-2 text-xs font-bold text-white cursor-pointer"
          @click="respond(c)"
        >
          Responder y Cerrar
        </button>
      </article>
    </div>
  </div>
</template>
