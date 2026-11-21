<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Check, X } from 'lucide-vue-next';
import { moneda, fecha } from '~/utils/panel/formato';
import type { PaymentItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['secretary', 'admin'],
});
useHead({ title: 'Validación de Pagos' });

const payments = ref<PaymentItem[]>([]);
const statusFilter = ref('pending');
const message = ref('');

const loadPayments = async () => {
  const query: Record<string, string> = {};
  if (statusFilter.value !== 'all') query.status = statusFilter.value;
  const res = await $fetch<{ data: PaymentItem[] }>('/api/bff/secretary/payments', { query });
  payments.value = res.data;
};

const approve = async (p: PaymentItem) => {
  await $fetch(`/api/bff/secretary/payments/${p.id}/approve`, { method: 'POST' });
  message.value = `Pago ${p.operation_code ?? p.id} validado. Estudiante enviado a Cola de WhatsApp.`;
  await loadPayments();
};

const reject = async (p: PaymentItem) => {
  const reason = window.prompt('Motivo del rechazo (mínimo 5 caracteres):', 'Operación no encontrada en estado de cuenta');
  if (!reason || reason.trim().length < 5) return;
  await $fetch(`/api/bff/secretary/payments/${p.id}/reject`, {
    method: 'POST',
    body: { reason: reason.trim() },
  });
  message.value = `Pago ${p.operation_code ?? p.id} rechazado.`;
  await loadPayments();
};

onMounted(loadPayments);
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-[#0A2540]">Validación de Comprobantes de Pago</h2>
        <p class="text-sm text-slate-600">Verifica Yape, Plin o transferencias para activar matrículas.</p>
      </div>
      <select v-model="statusFilter" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" @change="loadPayments">
        <option value="pending">Por validar</option>
        <option value="validated">Validados</option>
        <option value="rejected">Rechazados</option>
        <option value="all">Todos</option>
      </select>
    </div>

    <p v-if="message" role="status" class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm text-emerald-800">
      {{ message }}
    </p>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-xs">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-bold uppercase text-slate-600">
          <tr>
            <th class="px-4 py-3">Estudiante</th>
            <th class="px-4 py-3">Programa</th>
            <th class="px-4 py-3">Método / Operación</th>
            <th class="px-4 py-3">Monto</th>
            <th class="px-4 py-3">Fecha</th>
            <th class="px-4 py-3">Estado</th>
            <th class="px-4 py-3 text-right">Acción</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="p in payments" :key="p.id">
            <td class="px-4 py-3 font-semibold">{{ p.enrollment?.student?.name ?? '—' }}</td>
            <td class="px-4 py-3 text-xs">{{ p.enrollment?.cohort?.program?.name ?? '—' }}</td>
            <td class="px-4 py-3 text-xs">
              <span class="uppercase font-bold">{{ p.payment_method }}</span> · {{ p.operation_code ?? '—' }}
            </td>
            <td class="px-4 py-3 font-bold text-[#0A2540]">{{ moneda(p.amount) }}</td>
            <td class="px-4 py-3 text-xs text-slate-600">{{ fecha(p.created_at) }}</td>
            <td class="px-4 py-3 text-xs font-semibold uppercase">{{ p.status }}</td>
            <td class="px-4 py-3 text-right space-x-2">
              <template v-if="p.status === 'pending'">
                <button
                  type="button"
                  class="inline-flex items-center gap-1 rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-800 cursor-pointer"
                  @click="approve(p)"
                >
                  <Check class="h-3.5 w-3.5" />
                  <span>Aprobar</span>
                </button>
                <button
                  type="button"
                  class="inline-flex items-center gap-1 rounded-lg border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-50 cursor-pointer"
                  @click="reject(p)"
                >
                  <X class="h-3.5 w-3.5" />
                  <span>Rechazar</span>
                </button>
              </template>
            </td>
          </tr>
          <tr v-if="payments.length === 0">
            <td colspan="7" class="px-4 py-8 text-center text-slate-500">Sin pagos en este estado.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
