<script setup lang="ts">
import { ref } from 'vue';
import { Phone, Mail, MapPin, MessageCircle, Send, CheckCircle2 } from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';

useHead({ title: 'Contáctanos' });

const form = ref({
  full_name: '',
  phone: '',
  email: '',
  subject: '',
  message: '',
});

const status = ref<'idle' | 'sending' | 'sent' | 'error'>('idle');
const errorMessage = ref('');

const enviarMensaje = async () => {
  status.value = 'sending';
  errorMessage.value = '';
  try {
    await $fetch('/api/bff/public/contact-leads', {
      method: 'POST',
      body: {
        ...form.value,
        source_path: '/contactos',
      },
    });
    status.value = 'sent';
    form.value = { full_name: '', phone: '', email: '', subject: '', message: '' };
  } catch (err: unknown) {
    status.value = 'error';
    const e = err as { data?: { message?: string } };
    errorMessage.value = e?.data?.message || 'Revisa los campos e inténtalo nuevamente.';
  }
};
</script>

<template>
  <div class="pt-20">
    <section class="py-16 sm:py-24 bg-gradient-to-br from-[#0A2540] via-[#102d49] to-[#1a2f45] text-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-10">
        <div class="max-w-xl">
          <h1 class="text-3xl sm:text-5xl font-bold tracking-tight">Contáctanos</h1>
          <p class="mt-4 text-base sm:text-lg text-white/90">
            Estamos aquí para ayudarte. Envíanos tu consulta por correo o escríbenos directamente por WhatsApp.
          </p>
        </div>

        <div class="flex flex-col gap-3 shrink-0">
          <a
            :href="CONTACT_INFO.socials.whatsapp"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-3.5 px-5 py-3 rounded-2xl bg-emerald-800 hover:bg-emerald-900 text-white font-semibold transition shadow-md"
          >
            <MessageCircle class="h-6 w-6" aria-hidden="true" />
            <div>
              <span class="block text-sm">Escribir por WhatsApp</span>
              <small class="text-xs text-white font-normal">{{ CONTACT_INFO.phone }}</small>
            </div>
          </a>
        </div>
      </div>
    </section>

    <section class="bg-[#F7F9FC] py-14 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid gap-10 lg:grid-cols-5">
        <div class="lg:col-span-2 space-y-4">
          <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-[#0A2540] mb-4">Canales directos</h2>
            <ul class="space-y-4 text-sm text-slate-700">
              <li class="flex items-start gap-3">
                <Phone class="h-5 w-5 text-gold-text shrink-0" />
                <span>{{ CONTACT_INFO.phone }}</span>
              </li>
              <li class="flex items-start gap-3">
                <Mail class="h-5 w-5 text-gold-text shrink-0" />
                <span>{{ CONTACT_INFO.email }}</span>
              </li>
              <li class="flex items-start gap-3">
                <MapPin class="h-5 w-5 text-gold-text shrink-0" />
                <span>{{ CONTACT_INFO.address }}</span>
              </li>
            </ul>
          </div>
        </div>

        <form class="lg:col-span-3 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-5" @submit.prevent="enviarMensaje">
          <h2 class="text-xl font-bold text-[#0A2540]">Envíanos un mensaje</h2>

          <div v-if="status === 'sent'" class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-emerald-800 text-sm">
            <CheckCircle2 class="h-5 w-5 shrink-0" />
            <span>Mensaje enviado correctamente. Secretaría se pondrá en contacto contigo pronto.</span>
          </div>

          <div v-if="status === 'error'" role="alert" class="rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700 text-sm">
            {{ errorMessage }}
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1">Nombre completo *</label>
              <input id="full_name" v-model="form.full_name" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
            </div>
            <div>
              <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Teléfono / WhatsApp *</label>
              <input id="phone" v-model="form.phone" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
            </div>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Correo electrónico *</label>
              <input id="email" v-model="form.email" type="email" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
            </div>
            <div>
              <label for="subject" class="block text-sm font-medium text-slate-700 mb-1">Asunto</label>
              <input id="subject" v-model="form.subject" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
            </div>
          </div>

          <div>
            <label for="message" class="block text-sm font-medium text-slate-700 mb-1">Mensaje *</label>
            <textarea id="message" v-model="form.message" rows="4" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm" />
          </div>

          <button
            type="submit"
            :disabled="status === 'sending'"
            class="inline-flex items-center gap-2 rounded-xl bg-[#0A2540] px-6 py-3 text-sm font-bold text-white hover:bg-[#153A59] disabled:opacity-50 cursor-pointer"
          >
            <Send class="h-4 w-4" aria-hidden="true" />
            <span>{{ status === 'sending' ? 'Enviando…' : 'Enviar consulta' }}</span>
          </button>
        </form>
      </div>
    </section>
  </div>
</template>
