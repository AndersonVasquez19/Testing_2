<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { ActivityLogItem, CertificateItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['admin'],
});
useHead({ title: 'Certificados y Auditoría' });

const certificates = ref<CertificateItem[]>([]);
const logs = ref<ActivityLogItem[]>([]);

const loadAll = async () => {
  const [cRes, lRes] = await Promise.all([
    $fetch<{ data: CertificateItem[] }>('/api/bff/admin/certificates'),
    $fetch<{ data: ActivityLogItem[] }>('/api/bff/admin/activity-logs'),
  ]);
  certificates.value = cRes.data;
  logs.value = lRes.data;
};

const revoke = async (cert: CertificateItem) => {
  const reason = window.prompt('Motivo de revocación del certificado:');
  if (!reason || reason.trim().length < 5) return;
  await $fetch(`/api/bff/admin/certificates/${cert.id}/revoke`, {
    method: 'POST',
    body: { reason: reason.trim() },
  });
  await loadAll();
};

onMounted(loadAll);
</script>

<template>
  <div class="space-y-8">
    <h2 class="text-2xl font-bold text-[#0A2540]">Certificados Emitidos y Registro de Auditoría</h2>

    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-200 font-bold text-[#0A2540]">Certificados QR</div>
      <div class="divide-y divide-slate-100">
        <div v-for="cert in certificates" :key="cert.id" class="p-4 flex flex-wrap items-center justify-between gap-3 text-sm">
          <div>
            <p class="font-mono font-bold text-[#0A2540]">{{ cert.certificate_code }}</p>
            <p class="text-xs text-slate-600">{{ cert.snapshot_data?.student_name }} · {{ cert.snapshot_data?.program_name }}</p>
          </div>
          <div class="flex items-center gap-2">
            <span :class="cert.is_valid ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'" class="rounded-full px-2.5 py-0.5 text-xs font-bold">
              {{ cert.is_valid ? 'Vigente' : 'Revocado' }}
            </span>
            <button
              v-if="cert.is_valid"
              type="button"
              class="rounded-lg border border-red-300 px-3 py-1 text-xs font-semibold text-red-700 cursor-pointer"
              @click="revoke(cert)"
            >
              Revocar
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-200 font-bold text-[#0A2540]">Auditoría del Sistema (JSONB)</div>
      <div class="divide-y divide-slate-100 text-xs">
        <div v-for="log in logs" :key="log.id" class="p-4 flex flex-wrap justify-between gap-2">
          <div>
            <span class="font-mono font-bold text-[#1B4469]">{{ log.action }}</span>
            <span class="text-slate-500"> · {{ log.user?.name ?? 'Público/Sistema' }}</span>
          </div>
          <span class="text-slate-400">{{ log.created_at }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
