<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { AuthUser, CohortItem, ContactLeadItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['secretary', 'admin'],
});
useHead({ title: 'Leads y Matrículas' });

const leads = ref<ContactLeadItem[]>([]);
const students = ref<AuthUser[]>([]);
const cohorts = ref<CohortItem[]>([]);

const newStudent = ref({ name: '', dni: '', phone: '', email: '' });
const newEnrollment = ref({ user_id: '', cohort_id: '' });
const notice = ref('');

const loadAll = async () => {
  const [lRes, sRes, cRes] = await Promise.all([
    $fetch<{ data: ContactLeadItem[] }>('/api/bff/secretary/leads'),
    $fetch<{ data: AuthUser[] }>('/api/bff/secretary/students'),
    $fetch<{ data: CohortItem[] }>('/api/bff/secretary/cohorts'),
  ]);
  leads.value = lRes.data;
  students.value = sRes.data;
  cohorts.value = cRes.data;
};

const crearEstudiante = async () => {
  const res = await $fetch<{ data: AuthUser }>('/api/bff/secretary/students', {
    method: 'POST',
    body: newStudent.value,
  });
  notice.value = `Estudiante ${res.data.name} registrado.`;
  newStudent.value = { name: '', dni: '', phone: '', email: '' };
  await loadAll();
};

const crearMatricula = async () => {
  await $fetch('/api/bff/secretary/enrollments', {
    method: 'POST',
    body: {
      user_id: Number(newEnrollment.value.user_id),
      cohort_id: Number(newEnrollment.value.cohort_id),
    },
  });
  notice.value = 'Matrícula registrada con reserva de cupo.';
  newEnrollment.value = { user_id: '', cohort_id: '' };
  await loadAll();
};

const marcarLeadContactado = async (lead: ContactLeadItem) => {
  await $fetch(`/api/bff/secretary/leads/${lead.id}`, {
    method: 'PATCH',
    body: { status: 'contacted' },
  });
  await loadAll();
};

onMounted(loadAll);
</script>

<template>
  <div class="space-y-8">
    <h2 class="text-2xl font-bold text-[#0A2540]">Gestión de Leads, Alumnos y Matrículas</h2>

    <p v-if="notice" role="status" class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm text-emerald-800">
      {{ notice }}
    </p>

    <div class="grid gap-6 lg:grid-cols-2">
      <form class="rounded-2xl bg-white border border-slate-200 p-6 space-y-4" @submit.prevent="crearEstudiante">
        <h3 class="font-bold text-[#0A2540]">Registrar Estudiante (DNI peruano)</h3>
        <div class="grid gap-3 sm:grid-cols-2">
          <input v-model="newStudent.name" placeholder="Nombres y apellidos" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm sm:col-span-2">
          <input v-model="newStudent.dni" placeholder="DNI (8 dígitos)" maxlength="8" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
          <input v-model="newStudent.phone" placeholder="+519..." required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
          <input v-model="newStudent.email" type="email" placeholder="Correo electrónico" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm sm:col-span-2">
        </div>
        <button type="submit" class="rounded-xl bg-[#0A2540] px-4 py-2.5 text-xs font-bold text-white cursor-pointer">
          Guardar Estudiante
        </button>
      </form>

      <form class="rounded-2xl bg-white border border-slate-200 p-6 space-y-4" @submit.prevent="crearMatricula">
        <h3 class="font-bold text-[#0A2540]">Matricular en Convocatoria Abierta</h3>
        <select v-model="newEnrollment.user_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
          <option value="">Selecciona estudiante…</option>
          <option v-for="s in students" :key="s.id" :value="s.id">{{ s.name }} ({{ s.dni }})</option>
        </select>
        <select v-model="newEnrollment.cohort_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
          <option value="">Selecciona convocatoria…</option>
          <option v-for="c in cohorts" :key="c.id" :value="c.id">{{ c.name }} ({{ c.code }})</option>
        </select>
        <button type="submit" class="rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white cursor-pointer">
          Generar Reserva de Matrícula
        </button>
      </form>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-200 font-bold text-[#0A2540]">Bandeja de Leads / Contactos Web</div>
      <div class="divide-y divide-slate-100">
        <div v-for="lead in leads" :key="lead.id" class="p-4 flex flex-wrap items-center justify-between gap-4 text-sm">
          <div>
            <p class="font-semibold text-[#0A2540]">{{ lead.full_name }} · <span class="text-xs text-slate-500">{{ lead.phone }} · {{ lead.email }}</span></p>
            <p class="text-xs text-slate-600 mt-0.5">{{ lead.message }}</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold uppercase">{{ lead.status }}</span>
            <button
              v-if="lead.status === 'new'"
              type="button"
              class="rounded-lg bg-[#0A2540] px-3 py-1.5 text-xs font-bold text-white cursor-pointer"
              @click="marcarLeadContactado(lead)"
            >
              Marcar contactado
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
