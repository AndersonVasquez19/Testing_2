<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { CreditCard, MessageSquare, Users, CheckCircle2 } from 'lucide-vue-next';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['secretary', 'admin'],
});
useHead({ title: 'Panel Secretaría' });

const stats = ref({
  pending_payments: 0,
  whatsapp_pending: 0,
  new_leads: 0,
  active_enrollments: 0,
});

onMounted(async () => {
  stats.value = await $fetch<typeof stats.value>('/api/bff/secretary/dashboard');
});
</script>

<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-[#0A2540]">Bandeja Operativa de Secretaría</h2>
      <p class="text-sm text-slate-600">Validación de pagos, registro de matrículas e incorporación a grupos de WhatsApp.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-xs">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
          <span>Pagos por validar</span>
          <CreditCard class="h-4 w-4 text-amber-600" />
        </div>
        <p class="mt-2 text-3xl font-extrabold text-[#0A2540]">{{ stats.pending_payments }}</p>
      </div>
      <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-xs">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
          <span>Cola de WhatsApp</span>
          <MessageSquare class="h-4 w-4 text-emerald-600" />
        </div>
        <p class="mt-2 text-3xl font-extrabold text-emerald-700">{{ stats.whatsapp_pending }}</p>
      </div>
      <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-xs">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
          <span>Leads nuevos</span>
          <Users class="h-4 w-4 text-sky-600" />
        </div>
        <p class="mt-2 text-3xl font-extrabold text-[#0A2540]">{{ stats.new_leads }}</p>
      </div>
      <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-xs">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold">
          <span>Matrículas activas</span>
          <CheckCircle2 class="h-4 w-4 text-emerald-700" />
        </div>
        <p class="mt-2 text-3xl font-extrabold text-[#0A2540]">{{ stats.active_enrollments }}</p>
      </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
      <NuxtLink to="/panel/secretaria/pagos" class="rounded-2xl bg-white border border-slate-200 p-6 hover:border-[#0A2540] transition">
        <h3 class="font-bold text-[#0A2540]">Validar Comprobantes</h3>
        <p class="mt-1 text-xs text-slate-600">Aprueba Yape, Plin o transferencias para habilitar alumnos.</p>
      </NuxtLink>
      <NuxtLink to="/panel/secretaria/whatsapp" class="rounded-2xl bg-white border border-slate-200 p-6 hover:border-emerald-600 transition">
        <h3 class="font-bold text-emerald-800">Cola de WhatsApp</h3>
        <p class="mt-1 text-xs text-slate-600">Incorpora alumnos validados al grupo de su edición y registra auditoría.</p>
      </NuxtLink>
      <NuxtLink to="/panel/secretaria/matriculas" class="rounded-2xl bg-white border border-slate-200 p-6 hover:border-[#0A2540] transition">
        <h3 class="font-bold text-[#0A2540]">Leads y Matrículas</h3>
        <p class="mt-1 text-xs text-slate-600">Atiende consultas web, registra alumnos y reserva cupos.</p>
      </NuxtLink>
    </div>
  </div>
</template>
