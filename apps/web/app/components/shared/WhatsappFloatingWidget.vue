<script setup lang="ts">
import { ref, useId, onMounted, onUnmounted } from 'vue';
import { MessageCircle, X } from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';

interface Motivo {
  id: string;
  titulo: string;
  detalle: string;
  mensaje: string;
}

const MOTIVOS: readonly Motivo[] = [
  {
    id: 'matricula',
    titulo: 'Matrícula e información de cursos',
    detalle: 'Fechas, modalidades y cómo inscribirte',
    mensaje: 'Hola, quiero información sobre cursos y matrícula.',
  },
  {
    id: 'pagos',
    titulo: 'Pagos, cuotas y certificados',
    detalle: 'Estado de tu pago o constancias',
    mensaje: 'Hola, tengo una consulta sobre pagos, cuotas o certificados.',
  },
  {
    id: 'soporte',
    titulo: 'Atención al cliente',
    detalle: 'Cualquier otra consulta o ayuda',
    mensaje: 'Hola, necesito ayuda de atención al cliente.',
  },
];

const route = useRoute();
const abierto = ref(false);
const panelId = useId();
const contenedor = ref<HTMLElement | null>(null);

function enlaceWhatsApp(mensaje: string, ruta: string): string {
  const texto = ruta && ruta !== '/' ? `${mensaje}\n\n(Escribo desde: ${ruta})` : mensaje;
  return `https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent(texto)}`;
}

const alTeclear = (evento: KeyboardEvent) => {
  if (evento.key === 'Escape' && abierto.value) {
    abierto.value = false;
  }
};

const alPulsarFuera = (evento: PointerEvent) => {
  if (abierto.value && contenedor.value && !contenedor.value.contains(evento.target as Node)) {
    abierto.value = false;
  }
};

onMounted(() => {
  document.addEventListener('keydown', alTeclear);
  document.addEventListener('pointerdown', alPulsarFuera);
});

onUnmounted(() => {
  document.removeEventListener('keydown', alTeclear);
  document.removeEventListener('pointerdown', alPulsarFuera);
});
</script>

<template>
  <div ref="contenedor" class="fixed bottom-4 right-4 z-50 flex flex-col items-end gap-3 sm:bottom-6 sm:right-6">
    <section
      v-if="abierto"
      :id="panelId"
      aria-label="Escríbenos por WhatsApp"
      class="w-[min(92vw,22rem)] rounded-2xl border border-slate-200 bg-white p-4 shadow-xl"
    >
      <header class="mb-3 flex items-start justify-between gap-3">
        <div>
          <p class="text-sm font-semibold text-slate-900">¿En qué podemos ayudarte?</p>
          <p class="text-xs text-slate-600">Elige un motivo y te atendemos por WhatsApp.</p>
        </div>
        <button
          type="button"
          aria-label="Cerrar"
          class="rounded-full p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700"
          @click="abierto = false"
        >
          <X class="size-4" aria-hidden="true" />
        </button>
      </header>

      <ul class="space-y-2">
        <li v-for="motivo in MOTIVOS" :key="motivo.id">
          <a
            :href="enlaceWhatsApp(motivo.mensaje, route.path)"
            target="_blank"
            rel="noopener noreferrer"
            class="block rounded-xl border border-slate-200 px-3 py-2.5 transition hover:border-emerald-600 hover:bg-emerald-600/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700"
          >
            <span class="block text-sm font-medium text-slate-900">{{ motivo.titulo }}</span>
            <span class="block text-xs text-slate-600">{{ motivo.detalle }}</span>
          </a>
        </li>
      </ul>

      <p class="mt-3 border-t border-slate-100 pt-3 text-xs text-slate-600">
        ¿Prefieres escribirnos formalmente?
        <NuxtLink to="/contactos" class="font-medium text-[#0A2540] underline underline-offset-2">
          Contacto
        </NuxtLink>
        ·
        <NuxtLink to="/legales/libro-reclamaciones" class="font-medium text-[#0A2540] underline underline-offset-2">
          Libro de Reclamaciones
        </NuxtLink>
      </p>
    </section>

    <button
      type="button"
      :aria-expanded="abierto"
      :aria-controls="abierto ? panelId : undefined"
      :aria-label="abierto ? 'Cerrar el menú de WhatsApp' : 'Escríbenos por WhatsApp'"
      class="flex size-14 items-center justify-center rounded-full bg-emerald-700 text-white shadow-lg transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 motion-safe:hover:scale-105 cursor-pointer"
      @click="abierto = !abierto"
    >
      <MessageCircle class="size-7" aria-hidden="true" />
    </button>
  </div>
</template>
