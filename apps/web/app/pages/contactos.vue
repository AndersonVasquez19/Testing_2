<script setup lang="ts">
import { ref, computed } from 'vue';
import {
  Phone,
  Mail,
  MapPin,
  Clock,
  Copy,
  Check,
  Send,
  ExternalLink,
  MessageCircle,
  CheckCircle2,
} from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';

useHead({ title: 'Contáctanos' });

const copiedField = ref<'tel' | 'mail' | null>(null);
const form = ref({
  full_name: '',
  phone: '',
  email: '',
  subject: '',
  message: '',
});

const status = ref<'idle' | 'sending' | 'sent' | 'error'>('idle');
const errorMessage = ref('');

const copyToClipboard = async (text: string, field: 'tel' | 'mail') => {
  if (typeof navigator !== 'undefined' && navigator.clipboard) {
    await navigator.clipboard.writeText(text).catch(() => null);
  }
  copiedField.value = field;
  setTimeout(() => {
    if (copiedField.value === field) copiedField.value = null;
  }, 2000);
};

const whatsappFormUrl = computed(() => {
  const lines = [
    form.value.full_name ? `Nombre: ${form.value.full_name}` : '',
    form.value.phone ? `Teléfono: ${form.value.phone}` : '',
    form.value.email ? `Correo: ${form.value.email}` : '',
    form.value.subject ? `Asunto: ${form.value.subject}` : '',
    form.value.message ? `\nMensaje:\n${form.value.message}` : '',
  ].filter(Boolean);
  const text = lines.join('\n') || 'Hola Horus Group, deseo comunicarme con ustedes.';
  return `https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent(text)}`;
});

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
    <!-- Hero Contactos con 3 Action Pills -->
    <section class="relative overflow-hidden py-16 sm:py-24 bg-gradient-to-br from-[#0A2540] via-[#102d49] to-[#1a2f45] text-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row md:items-center justify-between gap-10">
        <div class="max-w-xl">
          <h1 class="text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight">
            Contáctanos
          </h1>
          <p class="mt-4 text-base sm:text-lg text-white/90 leading-relaxed">
            Estamos aquí para ayudarte. Envíanos tu consulta por correo o escríbenos directamente por WhatsApp.
          </p>
        </div>

        <div class="flex flex-col sm:flex-row md:flex-col gap-3 shrink-0">
          <a
            :href="CONTACT_INFO.socials.whatsapp"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center gap-3.5 px-5 py-3 rounded-2xl bg-emerald-800 hover:bg-emerald-900 text-white font-semibold transition-all shadow-md"
          >
            <MessageCircle class="h-6 w-6 text-white shrink-0" aria-hidden="true" />
            <div>
              <span class="block text-sm leading-tight">Escribir por WhatsApp</span>
              <small class="text-xs text-white/90 font-normal">972 021 374</small>
            </div>
          </a>

          <a
            :href="`tel:+${CONTACT_INFO.phoneClean}`"
            class="flex items-center gap-3.5 px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold transition-all"
          >
            <Phone class="h-5 w-5 text-gold shrink-0" aria-hidden="true" />
            <div>
              <span class="block text-sm leading-tight">Llamar ahora</span>
              <small class="text-xs text-white/80 font-normal">Lunes a sábado</small>
            </div>
          </a>

          <a
            href="#formulario"
            class="flex items-center gap-3.5 px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold transition-all"
          >
            <Mail class="h-5 w-5 text-gold shrink-0" aria-hidden="true" />
            <div>
              <span class="block text-sm leading-tight">Enviar un mensaje</span>
              <small class="text-xs text-white/80 font-normal">Completa el formulario</small>
            </div>
          </a>
        </div>
      </div>
    </section>

    <!-- Sección Principal: Tarjetas de Información + Formulario + Mapa -->
    <section class="bg-[#F7F9FC] py-16 sm:py-24 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="grid gap-10 lg:grid-cols-2">
          <!-- Información de contacto con botón Copiar -->
          <div class="rounded-3xl border border-stone-200 bg-white p-7 sm:p-9 shadow-sm">
            <h2 class="text-2xl sm:text-3xl font-bold text-[#0A2540]">
              Información de contacto
            </h2>
            <p class="mt-2 text-sm text-slate-600 mb-8">
              Puedes comunicarte con nosotros a través de los siguientes medios oficiales.
            </p>

            <div class="space-y-5">
              <div class="flex items-center justify-between gap-3 p-4 rounded-2xl border border-stone-200 bg-stone-50">
                <div class="flex items-center gap-4 min-w-0">
                  <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gold/20 text-[#0A2540] shrink-0">
                    <Phone class="h-5 w-5" aria-hidden="true" />
                  </div>
                  <div class="min-w-0">
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Teléfono y WhatsApp</h3>
                    <p class="text-base font-semibold text-[#0A2540]">{{ CONTACT_INFO.phone }}</p>
                  </div>
                </div>
                <button
                  type="button"
                  class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stone-300 bg-white text-xs font-semibold text-slate-700 hover:bg-stone-100 transition-colors cursor-pointer shrink-0"
                  @click="copyToClipboard(CONTACT_INFO.phone, 'tel')"
                >
                  <Check v-if="copiedField === 'tel'" class="h-3.5 w-3.5 text-emerald-700" aria-hidden="true" />
                  <Copy v-else class="h-3.5 w-3.5" aria-hidden="true" />
                  <span :class="copiedField === 'tel' ? 'text-emerald-700' : ''">{{ copiedField === 'tel' ? 'Copiado' : 'Copiar' }}</span>
                </button>
              </div>

              <div class="flex items-center justify-between gap-3 p-4 rounded-2xl border border-stone-200 bg-stone-50">
                <div class="flex items-center gap-4 min-w-0">
                  <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gold/20 text-[#0A2540] shrink-0">
                    <Mail class="h-5 w-5" aria-hidden="true" />
                  </div>
                  <div class="min-w-0">
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Correo electrónico</h3>
                    <p class="text-sm sm:text-base font-semibold text-[#0A2540] break-all">{{ CONTACT_INFO.email }}</p>
                  </div>
                </div>
                <button
                  type="button"
                  class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stone-300 bg-white text-xs font-semibold text-slate-700 hover:bg-stone-100 transition-colors cursor-pointer shrink-0"
                  @click="copyToClipboard(CONTACT_INFO.email, 'mail')"
                >
                  <Check v-if="copiedField === 'mail'" class="h-3.5 w-3.5 text-emerald-700" aria-hidden="true" />
                  <Copy v-else class="h-3.5 w-3.5" aria-hidden="true" />
                  <span :class="copiedField === 'mail' ? 'text-emerald-700' : ''">{{ copiedField === 'mail' ? 'Copiado' : 'Copiar' }}</span>
                </button>
              </div>

              <div class="flex items-center justify-between gap-3 p-4 rounded-2xl border border-stone-200 bg-stone-50">
                <div class="flex items-center gap-4 min-w-0">
                  <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gold/20 text-[#0A2540] shrink-0">
                    <MapPin class="h-5 w-5" aria-hidden="true" />
                  </div>
                  <div class="min-w-0">
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Ubicación</h3>
                    <p class="text-base font-semibold text-[#0A2540]">
                      Jr. José Gálvez #322 <span class="text-slate-600 font-normal block sm:inline">Cajamarca, Perú</span>
                    </p>
                  </div>
                </div>
                <a
                  :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(CONTACT_INFO.mapQuery)}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-stone-300 bg-white text-xs font-semibold text-slate-700 hover:bg-stone-100 transition-colors shrink-0"
                >
                  <span>Cómo llegar</span>
                  <ExternalLink class="h-3.5 w-3.5" aria-hidden="true" />
                </a>
              </div>

              <div class="flex items-center p-4 rounded-2xl border border-stone-200 bg-stone-50">
                <div class="flex items-center gap-4">
                  <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gold/20 text-[#0A2540] shrink-0">
                    <Clock class="h-5 w-5" aria-hidden="true" />
                  </div>
                  <div>
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Horario de atención</h3>
                    <p class="text-sm font-semibold text-[#0A2540]">
                      Lunes a Viernes: 8:00 AM - 1:00 PM | 3:00 PM - 7:00 PM
                      <span class="block text-slate-600 font-normal">Sábados: 8:30 AM - 1:00 PM</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Formulario de Mensaje -->
          <form
            id="formulario"
            class="rounded-3xl border border-stone-200 bg-white p-7 sm:p-9 shadow-sm scroll-mt-6 space-y-4"
            @submit.prevent="enviarMensaje"
          >
            <h2 class="text-2xl sm:text-3xl font-bold text-[#0A2540]">
              Envíanos un mensaje
            </h2>
            <p class="text-sm text-slate-600">
              Escríbenos y Secretaría te responderá por teléfono o correo. También puedes abrirlo listo en WhatsApp.
            </p>

            <div v-if="status === 'sent'" role="status" class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-emerald-800 text-sm">
              <CheckCircle2 class="h-5 w-5 shrink-0" aria-hidden="true" />
              <span>Mensaje enviado correctamente. Secretaría se pondrá en contacto contigo pronto.</span>
            </div>

            <div v-if="status === 'error'" role="alert" class="rounded-2xl bg-red-50 border border-red-200 p-4 text-red-700 text-sm">
              {{ errorMessage }}
            </div>

            <div>
              <label for="full_name" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nombre completo *</label>
              <input
                id="full_name"
                v-model="form.full_name"
                required
                placeholder="Ej. María Pérez"
                class="w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-[#0A2540] outline-none"
              >
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label for="phone" class="block text-xs font-bold text-slate-700 uppercase mb-1">Teléfono / WhatsApp *</label>
                <input
                  id="phone"
                  v-model="form.phone"
                  required
                  placeholder="Ej. +51 972 000 000"
                  class="w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-[#0A2540] outline-none"
                >
              </div>
              <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase mb-1">Correo electrónico *</label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  placeholder="nombre@correo.com"
                  class="w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-[#0A2540] outline-none"
                >
              </div>
            </div>

            <div>
              <label for="subject" class="block text-xs font-bold text-slate-700 uppercase mb-1">Asunto</label>
              <input
                id="subject"
                v-model="form.subject"
                placeholder="Ej. Información sobre cursos o capacitaciones"
                class="w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-[#0A2540] outline-none"
              >
            </div>

            <div>
              <label for="message" class="block text-xs font-bold text-slate-700 uppercase mb-1">Mensaje *</label>
              <textarea
                id="message"
                v-model="form.message"
                rows="4"
                required
                placeholder="Cuéntanos qué curso o asesoría necesitas"
                class="w-full rounded-xl border border-stone-300 bg-stone-50 px-4 py-2.5 text-sm text-slate-800 focus:bg-white focus:border-[#0A2540] outline-none resize-none"
              />
            </div>

            <div class="grid sm:grid-cols-2 gap-3 pt-2">
              <button
                type="submit"
                :disabled="status === 'sending'"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#0A2540] hover:bg-[#153A59] disabled:opacity-60 text-white font-semibold text-sm transition-colors shadow-sm cursor-pointer"
              >
                <Send class="h-4 w-4" aria-hidden="true" />
                <span>{{ status === 'sending' ? 'Enviando…' : 'Enviar consulta' }}</span>
              </button>

              <a
                :href="whatsappFormUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-800 hover:bg-emerald-900 text-white font-semibold text-sm transition-colors shadow-sm"
              >
                <MessageCircle class="h-4 w-4" aria-hidden="true" />
                <span>Enviar por WhatsApp</span>
              </a>
            </div>
          </form>
        </div>

        <!-- Mapa completo -->
        <div class="rounded-3xl border border-stone-200 bg-white p-4 sm:p-6 shadow-sm overflow-hidden">
          <div class="overflow-hidden rounded-2xl border border-stone-200 bg-stone-100">
            <iframe
              title="Mapa de la ubicación de Horus Group en Cajamarca"
              src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d989.6682159535815!2d-78.5107197!3d-7.1637652!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91b25b809bf9b7e3%3A0x70d5a58b14aa7eff!2sPlazuela%20Bolognesi!5e0!3m2!1ses-419!2spe!4v1772667902105!5m2!1ses-419!2spe"
              class="h-[340px] w-full border-0 block"
              loading="lazy"
              allowfullscreen
              referrerpolicy="no-referrer-when-downgrade"
            />
          </div>

          <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-2">
            <p class="text-sm text-slate-700">
              <strong class="text-[#0A2540]">Horus Group SRL: </strong>
              {{ CONTACT_INFO.address }}
            </p>
            <a
              :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(CONTACT_INFO.mapQuery)}`"
              target="_blank"
              rel="noopener noreferrer"
              class="text-xs font-bold text-gold-text hover:underline inline-flex items-center gap-1"
            >
              <span>Abrir en Google Maps</span>
              <ExternalLink class="h-3.5 w-3.5" aria-hidden="true" />
            </a>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
