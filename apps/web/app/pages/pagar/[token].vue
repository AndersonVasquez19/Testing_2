<script setup lang="ts">
import { ref, computed } from 'vue';
import { MessageCircle, Copy, Check } from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';
import { moneda } from '~/utils/panel/formato';

definePageMeta({ layout: false });

useHead({
  title: 'Pago de tu reserva',
  meta: [
    { name: 'robots', content: 'noindex, nofollow' },
    { name: 'referrer', content: 'no-referrer' },
  ],
});

interface ReservaResponse {
  data: {
    estado: 'activa' | 'con_pago' | 'vencida' | 'cerrada';
    vigente: boolean;
    referencia: string;
    vence_en: string;
    estudiante: string;
    curso: string | null;
    edicion: string | null;
    monto: { moneda: 'PEN'; total: string; pagado: string; por_confirmar: string; saldo: string } | null;
    metodos: { yape: { number: string; holder: string } | null };
  };
}

const route = useRoute();
const token = computed(() => String(route.params.token ?? ''));

const { data, error, refresh } = await useFetch<ReservaResponse>(() => `/api/bff/public/reservations/${encodeURIComponent(token.value)}`);

const d = computed(() => (error.value ? null : data.value?.data ?? null));
const copiedMsg = ref('');

const copiar = async (texto: string, etiqueta: string) => {
  if (typeof navigator !== 'undefined' && navigator.clipboard) {
    await navigator.clipboard.writeText(texto);
  }
  copiedMsg.value = `${etiqueta} copiado`;
  setTimeout(() => {
    copiedMsg.value = '';
  }, 3000);
};

const tiempoRestante = (iso: string): string => {
  const horas = Math.max(0, Math.floor((new Date(iso).getTime() - Date.now()) / 3_600_000));
  if (horas >= 48) return `${Math.floor(horas / 24)} días`;
  if (horas >= 1) return `${horas} ${horas === 1 ? 'hora' : 'horas'}`;
  return 'menos de una hora';
};

const waUrl = (texto: string) => `https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent(texto)}`;

const paymentMethod = ref<'yape' | 'plin' | 'transfer'>('yape');
const operationCode = ref('');
const amount = ref('');
const submitting = ref(false);
const submitMsg = ref('');

const enviarComprobante = async () => {
  if (!d.value) return;
  submitting.value = true;
  submitMsg.value = '';
  try {
    await $fetch(`/api/bff/public/reservations/${encodeURIComponent(token.value)}/voucher`, {
      method: 'POST',
      body: {
        payment_method: paymentMethod.value,
        operation_code: operationCode.value,
        amount: amount.value || d.value.monto?.saldo || '0',
      },
    });
    submitMsg.value = 'Comprobante registrado correctamente.';
    await refresh();
  } catch {
    submitMsg.value = 'No pudimos registrar el comprobante. Verifica los datos.';
  } finally {
    submitting.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-[#EEF2F7] text-slate-900 py-10 px-4">
    <main class="mx-auto max-w-lg">
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p class="text-xs font-semibold uppercase tracking-wider text-sky-700">Horus Group · Pago de tu reserva</p>

        <template v-if="!d">
          <h1 class="mt-1 text-xl font-bold text-slate-900">No pudimos abrir este enlace de pago</h1>
          <p class="mt-2 text-sm text-slate-600">
            El enlace no es válido o no está disponible en este momento. Revisa que esté completo o inténtalo de nuevo en unos minutos.
          </p>
          <a
            :href="waUrl('Hola, no puedo abrir mi enlace de pago.')"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-800"
          >
            <MessageCircle class="size-4" aria-hidden="true" />
            <span>Escribirnos por WhatsApp</span>
          </a>
        </template>

        <template v-else-if="d.estado === 'con_pago'">
          <h1 class="mt-1 text-xl font-bold text-slate-900">Recibimos tu pago, {{ d.estudiante }}</h1>
          <p class="mt-2 text-sm text-slate-600">
            Estamos verificándolo. Tu cupo en <strong>{{ d.curso ?? 'tu curso' }}</strong> sigue reservado y te avisaremos cuando quede confirmado.
          </p>
          <p class="mt-4 text-xs text-slate-500">
            Tu referencia: <span class="font-mono font-semibold text-slate-700">{{ d.referencia }}</span>
          </p>
          <a
            :href="waUrl(`Hola, soy ${d.estudiante}. Referencia ${d.referencia} (${d.curso ?? 'tu curso'}). Consulta sobre mi pago.`)"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-800"
          >
            <MessageCircle class="size-4" aria-hidden="true" />
            <span>Consultar por WhatsApp</span>
          </a>
        </template>

        <template v-else-if="!d.vigente">
          <h1 class="mt-1 text-xl font-bold text-slate-900">
            {{ d.estado === 'vencida' ? 'Tu reserva venció' : 'Esta reserva ya no está activa' }}
          </h1>
          <p class="mt-2 text-sm text-slate-600">
            {{ d.estado === 'vencida'
              ? `El plazo para pagar tu cupo en ${d.curso ?? 'tu curso'} terminó y el cupo se liberó. Escríbenos y, si aún hay lugar, volvemos a reservarlo.`
              : 'Ya no hace falta que pagues con este enlace. Si tienes dudas, escríbenos.' }}
          </p>
          <a
            :href="waUrl(`Hola, soy ${d.estudiante}. Referencia ${d.referencia}. ${d.estado === 'vencida' ? 'Mi reserva venció y quiero volver a reservar.' : 'Tengo una consulta sobre mi reserva.'}`)"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-800"
          >
            <MessageCircle class="size-4" aria-hidden="true" />
            <span>Escribirnos por WhatsApp</span>
          </a>
        </template>

        <template v-else>
          <h1 class="mt-1 text-xl font-bold text-slate-900">Hola, {{ d.estudiante }}: completa tu pago</h1>
          <p class="mt-1 text-sm text-slate-600">
            {{ d.curso ?? 'tu curso' }}{{ d.edicion ? ` · Edición ${d.edicion}` : '' }}
          </p>

          <p class="mt-4 rounded-xl bg-sky-50 px-4 py-3 text-sm text-sky-900">
            Tu cupo está reservado. Te quedan {{ tiempoRestante(d.vence_en) }}. Pasado ese plazo, el cupo se libera.
          </p>

          <section aria-labelledby="titulo-importe" class="mt-6">
            <h2 id="titulo-importe" class="text-sm font-semibold text-slate-900">Importe</h2>
            <dl v-if="d.monto" class="mt-2 space-y-1 text-sm">
              <div class="flex justify-between"><dt>Total</dt><dd>{{ moneda(d.monto.total) }}</dd></div>
              <div class="flex justify-between"><dt>Ya pagado</dt><dd>{{ moneda(d.monto.pagado) }}</dd></div>
              <div class="flex justify-between font-bold text-slate-900 border-t pt-1">
                <dt>Debes pagar</dt><dd>{{ moneda(d.monto.saldo) }}</dd>
              </div>
            </dl>
            <p v-else class="mt-2 text-sm text-slate-600">Todavía no tienes un importe asignado.</p>
          </section>

          <section aria-labelledby="titulo-como-pagar" class="mt-6 border-t pt-5">
            <h2 id="titulo-como-pagar" class="text-sm font-semibold text-slate-900">Cómo pagar</h2>
            <div v-if="d.metodos.yape" class="mt-3 space-y-3 text-sm">
              <div class="flex items-center justify-between rounded-xl border p-3">
                <div>
                  <p class="text-xs text-slate-500">Yape a nombre de {{ d.metodos.yape.holder }}</p>
                  <p class="font-mono font-bold">{{ d.metodos.yape.number }}</p>
                </div>
                <button
                  type="button"
                  aria-label="Copiar el número de Yape"
                  class="rounded-lg border px-3 py-1.5 text-xs font-semibold hover:bg-slate-50 inline-flex items-center gap-1"
                  @click="copiar(d.metodos.yape.number, 'el número de Yape')"
                >
                  <Check v-if="copiedMsg.includes('Yape')" class="size-3.5 text-emerald-700" />
                  <Copy v-else class="size-3.5" />
                  <span>Copiar</span>
                </button>
              </div>

              <div class="flex items-center justify-between rounded-xl border p-3">
                <div>
                  <p class="text-xs text-slate-500">Referencia de reserva</p>
                  <p class="font-mono font-bold">{{ d.referencia }}</p>
                </div>
                <button
                  type="button"
                  aria-label="Copiar la referencia"
                  class="rounded-lg border px-3 py-1.5 text-xs font-semibold hover:bg-slate-50 inline-flex items-center gap-1"
                  @click="copiar(d.referencia, 'la referencia')"
                >
                  <Check v-if="copiedMsg.includes('referencia')" class="size-3.5 text-emerald-700" />
                  <Copy v-else class="size-3.5" />
                  <span>Copiar</span>
                </button>
              </div>
            </div>
            <p v-else class="mt-2 text-sm text-slate-600">Escríbenos por WhatsApp para indicarte los medios de pago.</p>

            <p v-if="copiedMsg" role="status" class="mt-2 text-xs font-semibold text-emerald-700">{{ copiedMsg }}</p>
          </section>

          <form class="mt-6 border-t pt-5 space-y-3" @submit.prevent="enviarComprobante">
            <h3 class="text-sm font-semibold text-slate-900">Registrar operación realizada</h3>
            <div class="grid grid-cols-2 gap-3">
              <select v-model="paymentMethod" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <option value="yape">Yape</option>
                <option value="plin">Plin</option>
                <option value="transfer">Transferencia</option>
              </select>
              <input v-model="operationCode" placeholder="Código de operación" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
            </div>
            <button
              type="submit"
              :disabled="submitting"
              class="w-full rounded-xl bg-[#0A2540] px-4 py-2.5 text-xs font-bold text-white hover:bg-[#153A59] cursor-pointer"
            >
              {{ submitting ? 'Registrando…' : 'Informar pago realizado' }}
            </button>
            <p v-if="submitMsg" class="text-xs text-emerald-700 font-medium">{{ submitMsg }}</p>
          </form>

          <a
            :href="waUrl(`Hola, soy ${d.estudiante}. Referencia ${d.referencia} (${d.curso ?? 'tu curso'}). Adjunto mi captura de pago.`)"
            target="_blank"
            rel="noopener noreferrer"
            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-800"
          >
            <MessageCircle class="size-4" aria-hidden="true" />
            <span>Enviar mi captura por WhatsApp</span>
          </a>
        </template>
      </div>
    </main>
  </div>
</template>
