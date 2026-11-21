<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { moneda } from '~/utils/panel/formato';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['admin'],
});
useHead({ title: 'Panel Administrador' });

const metrics = ref({
  total_revenue_pen: '0.00',
  pending_payments_count: 0,
  active_enrollments_count: 0,
  whatsapp_pending_count: 0,
  open_complaints_count: 0,
  issued_certificates_count: 0,
  programs_count: 0,
  users_count: 0,
});

onMounted(async () => {
  metrics.value = await $fetch<typeof metrics.value>('/api/bff/admin/metrics');
});
</script>

<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-[#0A2540]">Panel Ejecutivo · Horus Academic</h2>
      <p class="text-sm text-slate-600">Métricas financieras, control académico, Libro de Reclamaciones y auditoría JSONB.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-xs font-semibold text-slate-500">Ingresos Validados</p>
        <p class="mt-2 text-2xl font-extrabold text-emerald-700">{{ moneda(metrics.total_revenue_pen) }}</p>
      </div>
      <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-xs font-semibold text-slate-500">Pagos por Validar</p>
        <p class="mt-2 text-2xl font-extrabold text-amber-700">{{ metrics.pending_payments_count }}</p>
      </div>
      <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-xs font-semibold text-slate-500">Cola WhatsApp Pendiente</p>
        <p class="mt-2 text-2xl font-extrabold text-[#0A2540]">{{ metrics.whatsapp_pending_count }}</p>
      </div>
      <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-xs font-semibold text-slate-500">Reclamos Abiertos</p>
        <p class="mt-2 text-2xl font-extrabold text-red-700">{{ metrics.open_complaints_count }}</p>
      </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-xs font-semibold text-slate-500">Matrículas Activas</p>
        <p class="mt-1 text-xl font-bold text-[#0A2540]">{{ metrics.active_enrollments_count }}</p>
      </div>
      <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-xs font-semibold text-slate-500">Certificados Vigentes</p>
        <p class="mt-1 text-xl font-bold text-[#0A2540]">{{ metrics.issued_certificates_count }}</p>
      </div>
      <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-xs font-semibold text-slate-500">Programas Activos</p>
        <p class="mt-1 text-xl font-bold text-[#0A2540]">{{ metrics.programs_count }}</p>
      </div>
      <div class="rounded-2xl bg-white border border-slate-200 p-5">
        <p class="text-xs font-semibold text-slate-500">Usuarios Activos</p>
        <p class="mt-1 text-xl font-bold text-[#0A2540]">{{ metrics.users_count }}</p>
      </div>
    </div>
  </div>
</template>
