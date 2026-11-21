<script setup lang="ts">
import { ref } from 'vue';
import { FileText, CheckCircle2, AlertCircle, Download } from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';
import type { ComplaintItem } from '~/types/horus';

useHead({ title: 'Libro de Reclamaciones Virtual' });

const form = ref({
  complaint_type: 'claim' as 'claim' | 'complaint',
  consumer_document_type: 'dni' as 'dni' | 'ce' | 'passport' | 'ruc',
  consumer_document_number: '',
  consumer_name: '',
  consumer_phone: '',
  consumer_email: '',
  consumer_address: '',
  is_minor: false,
  representative_name: '',
  representative_address: '',
  representative_phone: '',
  representative_email: '',
  item_type: 'service' as 'product' | 'service',
  item_description: '',
  claimed_amount: '',
  detail: '',
  consumer_request: '',
  acepta_privacidad: false,
});

const submitting = ref(false);
const errorMessage = ref('');
const registered = ref<ComplaintItem | null>(null);

const submitComplaint = async () => {
  if (!form.value.acepta_privacidad) {
    errorMessage.value = 'Debes aceptar la declaración de veracidad para registrar tu hoja.';
    return;
  }
  submitting.value = true;
  errorMessage.value = '';
  try {
    const res = await $fetch<{ data: ComplaintItem }>('/api/bff/public/complaints', {
      method: 'POST',
      body: {
        ...form.value,
        claimed_amount: form.value.claimed_amount ? Number(form.value.claimed_amount) : null,
      },
    });
    registered.value = res.data;
  } catch (err: unknown) {
    const e = err as { data?: { message?: string } };
    errorMessage.value = e?.data?.message || 'Revisa los campos obligatorios e inténtalo de nuevo.';
  } finally {
    submitting.value = false;
  }
};
</script>

<template>
  <div class="pt-20">
    <section class="py-14 bg-gradient-to-br from-[#0A2540] to-[#0A1929] text-white text-center">
      <div class="mx-auto max-w-4xl px-4">
        <div class="inline-flex items-center gap-2 rounded-full border border-gold/40 bg-gold/10 px-4 py-1.5 text-xs font-bold text-gold uppercase mb-4">
          <FileText class="h-4 w-4" aria-hidden="true" />
          <span>D.S. N.° 011-2011-PCM · INDECOPI</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-bold">Libro de Reclamaciones Virtual</h1>
        <p class="mt-3 text-sm text-stone-300">
          {{ CONTACT_INFO.name }} · RUC {{ CONTACT_INFO.ruc }} · {{ CONTACT_INFO.address }}
        </p>
      </div>
    </section>

    <section class="bg-[#F7F9FC] py-12 text-slate-900">
      <div class="mx-auto max-w-3xl px-4">
        <div v-if="registered" class="rounded-3xl border border-emerald-300 bg-white p-8 shadow-sm space-y-4">
          <div class="flex items-center gap-3 text-emerald-700">
            <CheckCircle2 class="h-8 w-8 shrink-0" aria-hidden="true" />
            <div>
              <h2 class="text-xl font-bold">Hoja de Reclamación Registrada</h2>
              <p class="text-sm">Código correlativo: <strong class="font-mono">{{ registered.correlative_code }}</strong></p>
            </div>
          </div>
          <p class="text-sm text-slate-700">
            Hemos enviado la constancia PDF a <strong>{{ registered.consumer_email }}</strong>.
            Plazo legal máximo de respuesta (15 días hábiles): <strong>{{ registered.legal_deadline }}</strong>.
          </p>
          <a
            :href="`/api/bff/public/complaints/${encodeURIComponent(registered.correlative_code)}/receipt`"
            class="inline-flex items-center gap-2 rounded-xl bg-[#0A2540] px-5 py-3 text-xs font-bold text-white hover:bg-[#153A59]"
          >
            <Download class="h-4 w-4" aria-hidden="true" />
            <span>Descargar Constancia PDF</span>
          </a>
        </div>

        <form v-else class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6" @submit.prevent="submitComplaint">
          <div v-if="errorMessage" role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 flex items-center gap-2 text-red-700 text-sm">
            <AlertCircle class="h-5 w-5 shrink-0" aria-hidden="true" />
            <span>{{ errorMessage }}</span>
          </div>

          <fieldset class="space-y-4">
            <legend class="text-sm font-bold uppercase tracking-wider text-[#0A2540]">1. Identificación del Consumidor</legend>
            <div class="grid gap-4 sm:grid-cols-3">
              <div>
                <label for="lr_doc_type" class="block text-xs font-semibold text-slate-700 mb-1">Tipo Doc.</label>
                <select id="lr_doc_type" v-model="form.consumer_document_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                  <option value="dni">DNI</option>
                  <option value="ce">Carné Extranjería</option>
                  <option value="passport">Pasaporte</option>
                  <option value="ruc">RUC</option>
                </select>
              </div>
              <div class="sm:col-span-2">
                <label for="lr_doc_num" class="block text-xs font-semibold text-slate-700 mb-1">Número de Documento *</label>
                <input id="lr_doc_num" v-model="form.consumer_document_number" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
              </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label for="lr_name" class="block text-xs font-semibold text-slate-700 mb-1">Nombres y Apellidos / Razón Social *</label>
                <input id="lr_name" v-model="form.consumer_name" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
              </div>
              <div>
                <label for="lr_phone" class="block text-xs font-semibold text-slate-700 mb-1">Teléfono *</label>
                <input id="lr_phone" v-model="form.consumer_phone" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
              </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label for="lr_email" class="block text-xs font-semibold text-slate-700 mb-1">Correo electrónico *</label>
                <input id="lr_email" v-model="form.consumer_email" type="email" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
              </div>
              <div>
                <label for="lr_address" class="block text-xs font-semibold text-slate-700 mb-1">Domicilio *</label>
                <input id="lr_address" v-model="form.consumer_address" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
              </div>
            </div>
          </fieldset>

          <fieldset class="space-y-4 border-t border-slate-200 pt-6">
            <legend class="text-sm font-bold uppercase tracking-wider text-[#0A2540]">2. Bien Contratado y Detalle</legend>
            <div class="grid gap-4 sm:grid-cols-3">
              <div>
                <label for="lr_type" class="block text-xs font-semibold text-slate-700 mb-1">Tipo de Registro</label>
                <select id="lr_type" v-model="form.complaint_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                  <option value="claim">Reclamo</option>
                  <option value="complaint">Queja</option>
                </select>
              </div>
              <div>
                <label for="lr_item_type" class="block text-xs font-semibold text-slate-700 mb-1">Bien Contratado</label>
                <select id="lr_item_type" v-model="form.item_type" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                  <option value="service">Servicio</option>
                  <option value="product">Producto</option>
                </select>
              </div>
              <div>
                <label for="lr_amount" class="block text-xs font-semibold text-slate-700 mb-1">Monto Reclamado (S/)</label>
                <input id="lr_amount" v-model="form.claimed_amount" type="number" step="0.01" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
              </div>
            </div>

            <div>
              <label for="lr_item_desc" class="block text-xs font-semibold text-slate-700 mb-1">Descripción del curso o servicio</label>
              <input id="lr_item_desc" v-model="form.item_description" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
              <label for="lr_detail" class="block text-xs font-semibold text-slate-700 mb-1">Detalle de lo ocurrido *</label>
              <textarea id="lr_detail" v-model="form.detail" rows="4" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>

            <div>
              <label for="lr_request" class="block text-xs font-semibold text-slate-700 mb-1">Pedido del consumidor *</label>
              <textarea id="lr_request" v-model="form.consumer_request" rows="3" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" />
            </div>
          </fieldset>

          <label for="lr_privacy" class="flex items-start gap-2.5 text-xs text-slate-700">
            <input id="lr_privacy" v-model="form.acepta_privacidad" type="checkbox" class="mt-0.5">
            <span>Declaro que los datos consignados son veraces y autorizo su tratamiento conforme a la Ley N.° 29733.</span>
          </label>

          <button
            type="submit"
            :disabled="submitting"
            class="w-full rounded-xl bg-[#0A2540] px-6 py-3.5 text-sm font-bold text-white hover:bg-[#153A59] disabled:opacity-50 cursor-pointer"
          >
            {{ submitting ? 'Registrando hoja…' : 'Registrar Hoja de Reclamación' }}
          </button>
        </form>
      </div>
    </section>
  </div>
</template>
