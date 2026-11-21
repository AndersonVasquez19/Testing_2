<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Copy, ExternalLink, CheckCircle2, MessageSquare } from 'lucide-vue-next';
import { copiarAlPortapapeles, enlaceWaMe, mensajeBienvenidaMatricula } from '~/utils/panel/whatsapp-plantillas';
import { fecha } from '~/utils/panel/formato';
import type { EnrollmentItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['secretary', 'admin'],
});
useHead({ title: 'Cola de Incorporación a WhatsApp' });

const items = ref<EnrollmentItem[]>([]);
const filterPending = ref<string>('0');
const notice = ref('');

const cargarCola = async () => {
  const query: Record<string, string> = {};
  if (filterPending.value !== 'all') {
    query.added_to_whatsapp = filterPending.value;
  }
  const res = await $fetch<{ data: EnrollmentItem[] }>('/api/bff/secretary/whatsapp-queue', { query });
  items.value = res.data;
};

const copiarTelefono = async (phone: string | null | undefined) => {
  if (!phone) return;
  await copiarAlPortapapeles(phone);
  notice.value = `Teléfono ${phone} copiado al portapapeles.`;
};

const linkWhatsappAlumno = (item: EnrollmentItem): string => {
  const mensaje = mensajeBienvenidaMatricula({
    estudiante: item.student?.name ?? 'Estudiante',
    curso: item.cohort?.program?.name ?? 'tu curso',
    edicion: item.cohort?.code,
    enlaceGrupo: item.cohort?.whatsapp_group_link ?? item.whatsapp_group_link,
  });
  return enlaceWaMe(item.student?.phone, mensaje) ?? '#';
};

const marcarAgregado = async (item: EnrollmentItem, added: boolean) => {
  await $fetch(`/api/bff/secretary/whatsapp-queue/${item.id}/mark-added`, {
    method: 'PATCH',
    body: { added },
  });
  notice.value = added
    ? `${item.student?.name} marcado como añadido al grupo de WhatsApp.`
    : `Estado revertido para ${item.student?.name}.`;
  await cargarCola();
};

onMounted(cargarCola);
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-[#0A2540]">Cola de Incorporación a WhatsApp</h2>
        <p class="text-sm text-slate-600">Alumnos con pago validado pendientes de agregar al grupo de su edición.</p>
      </div>
      <select v-model="filterPending" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" @change="cargarCola">
        <option value="0">Pendientes por añadir</option>
        <option value="1">Ya incorporados</option>
        <option value="all">Todos</option>
      </select>
    </div>

    <p v-if="notice" role="status" class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm text-emerald-800">
      {{ notice }}
    </p>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-xs">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-bold uppercase text-slate-600">
          <tr>
            <th class="px-4 py-3">Estudiante</th>
            <th class="px-4 py-3">Teléfono</th>
            <th class="px-4 py-3">Curso / Edición</th>
            <th class="px-4 py-3">Grupo WA</th>
            <th class="px-4 py-3">Auditoría</th>
            <th class="px-4 py-3 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="item in items" :key="item.id">
            <td class="px-4 py-3">
              <p class="font-semibold text-[#0A2540]">{{ item.student?.name }}</p>
              <p class="text-xs text-slate-500">DNI: {{ item.student?.dni ?? '—' }}</p>
            </td>
            <td class="px-4 py-3 font-mono text-xs">
              <div class="flex items-center gap-2">
                <span>{{ item.student?.phone ?? '—' }}</span>
                <button
                  v-if="item.student?.phone"
                  type="button"
                  title="Copiar teléfono"
                  class="rounded border p-1 hover:bg-slate-100 cursor-pointer"
                  @click="copiarTelefono(item.student?.phone)"
                >
                  <Copy class="h-3.5 w-3.5" />
                </button>
              </div>
            </td>
            <td class="px-4 py-3">
              <p class="font-medium text-slate-800">{{ item.cohort?.program?.name }}</p>
              <p class="text-xs text-slate-500">{{ item.cohort?.code }}</p>
            </td>
            <td class="px-4 py-3 text-xs">
              <a
                v-if="item.whatsapp_group_link"
                :href="item.whatsapp_group_link"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1 text-emerald-700 font-semibold hover:underline"
              >
                <span>Abrir grupo</span>
                <ExternalLink class="h-3 w-3" />
              </a>
              <span v-else class="text-slate-400">Sin enlace</span>
            </td>
            <td class="px-4 py-3 text-xs">
              <span v-if="item.added_to_whatsapp" class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 font-semibold text-emerald-800">
                <CheckCircle2 class="h-3.5 w-3.5" />
                Añadido ({{ fecha(item.whatsapp_added_at) }})
              </span>
              <span v-else class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-800">
                Pendiente
              </span>
            </td>
            <td class="px-4 py-3 text-right space-x-2">
              <a
                :href="linkWhatsappAlumno(item)"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1 rounded-lg border border-emerald-600 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-50"
              >
                <MessageSquare class="h-3.5 w-3.5" />
                <span>WA Web</span>
              </a>
              <button
                v-if="!item.added_to_whatsapp"
                type="button"
                class="inline-flex items-center gap-1 rounded-lg bg-[#0A2540] px-3 py-1.5 text-xs font-bold text-white hover:bg-[#153A59] cursor-pointer"
                @click="marcarAgregado(item, true)"
              >
                <CheckCircle2 class="h-3.5 w-3.5" />
                <span>Marcar añadido</span>
              </button>
            </td>
          </tr>
          <tr v-if="items.length === 0">
            <td colspan="6" class="px-4 py-8 text-center text-slate-500">No hay registros en esta vista.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
