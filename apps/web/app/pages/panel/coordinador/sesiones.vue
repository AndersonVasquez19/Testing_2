<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Video, MapPin, RefreshCw } from 'lucide-vue-next';
import type { CohortItem, CohortSessionItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['coordinator', 'admin'],
});
useHead({ title: 'Sesiones Híbridas' });

const route = useRoute();
const cohorts = ref<CohortItem[]>([]);
const selectedCohortId = ref<number>(0);
const sessions = ref<CohortSessionItem[]>([]);
const notice = ref('');

const newSession = ref({
  session_number: 1,
  title: '',
  session_date: '',
  start_time: '19:00',
  end_time: '21:00',
  session_type: 'virtual' as 'virtual' | 'presential',
  meeting_url: 'https://meet.google.com/horus-clase',
  classroom: '',
  location_name: 'Sede Central Horus Group',
});

const loadSessions = async () => {
  if (!selectedCohortId.value) return;
  const res = await $fetch<{ data: CohortSessionItem[] }>(`/api/bff/coordinator/cohorts/${selectedCohortId.value}/sessions`);
  sessions.value = res.data;
  newSession.value.session_number = sessions.value.length + 1;
};

onMounted(async () => {
  const cRes = await $fetch<{ data: CohortItem[] }>('/api/bff/coordinator/cohorts');
  cohorts.value = cRes.data;
  const qCohort = Number(route.query.cohort || 0);
  selectedCohortId.value = qCohort || cohorts.value[0]?.id || 0;
  await loadSessions();
});

const createSession = async () => {
  await $fetch(`/api/bff/coordinator/cohorts/${selectedCohortId.value}/sessions`, {
    method: 'POST',
    body: newSession.value,
  });
  notice.value = 'Sesión programada correctamente.';
  newSession.value.title = '';
  await loadSessions();
};

const toggleModality = async (s: CohortSessionItem) => {
  const nextType = s.session_type === 'virtual' ? 'presential' : 'virtual';
  await $fetch(`/api/bff/coordinator/sessions/${s.id}`, {
    method: 'PATCH',
    body: {
      session_type: nextType,
      meeting_url: nextType === 'virtual' ? (s.meeting_url || 'https://meet.google.com/horus-clase') : null,
      classroom: nextType === 'presential' ? (s.classroom || 'Aula 201') : null,
      location_name: nextType === 'presential' ? (s.location_name || 'Sede Central Cajamarca') : null,
    },
  });
  notice.value = `Sesión #${s.session_number} cambiada a ${nextType === 'virtual' ? 'Virtual' : 'Presencial'}.`;
  await loadSessions();
};

const guardarGrabacion = async (s: CohortSessionItem) => {
  const url = window.prompt('URL de grabación (Drive / YouTube oculto):', s.recording_url || '');
  if (url === null) return;
  await $fetch(`/api/bff/coordinator/sessions/${s.id}`, {
    method: 'PATCH',
    body: { recording_url: url || null, status: url ? 'completed' : s.status },
  });
  notice.value = `Grabación actualizada en sesión #${s.session_number}.`;
  await loadSessions();
};
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-[#0A2540]">Calendario y Modalidad por Sesión</h2>
        <p class="text-sm text-slate-600">Define día por día si la clase es Virtual (Meet/Zoom) o Presencial (Sede/Aula).</p>
      </div>
      <select v-model="selectedCohortId" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" @change="loadSessions">
        <option v-for="c in cohorts" :key="c.id" :value="c.id">{{ c.code }} · {{ c.name }}</option>
      </select>
    </div>

    <p v-if="notice" role="status" class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm text-emerald-800">
      {{ notice }}
    </p>

    <form class="rounded-2xl bg-white border border-slate-200 p-6 space-y-4" @submit.prevent="createSession">
      <h3 class="font-bold text-[#0A2540]">Programar Sesión</h3>
      <div class="grid gap-3 sm:grid-cols-3">
        <input v-model.number="newSession.session_number" type="number" min="1" required placeholder="N°" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <input v-model="newSession.title" placeholder="Título de la sesión" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm sm:col-span-2">
        <input v-model="newSession.session_date" type="date" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <input v-model="newSession.start_time" type="time" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <input v-model="newSession.end_time" type="time" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <select v-model="newSession.session_type" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
          <option value="virtual">Virtual</option>
          <option value="presential">Presencial</option>
        </select>
        <input v-if="newSession.session_type === 'virtual'" v-model="newSession.meeting_url" placeholder="URL Zoom/Meet" class="rounded-xl border border-slate-300 px-3 py-2 text-sm sm:col-span-2">
        <input v-else v-model="newSession.classroom" placeholder="Aula (ej. Lab 201)" class="rounded-xl border border-slate-300 px-3 py-2 text-sm sm:col-span-2">
      </div>
      <button type="submit" class="rounded-xl bg-[#0A2540] px-5 py-2.5 text-xs font-bold text-white cursor-pointer">
        Agregar Sesión
      </button>
    </form>

    <div class="space-y-3">
      <article v-for="s in sessions" :key="s.id" class="rounded-2xl bg-white border border-slate-200 p-5 flex flex-wrap items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs font-bold uppercase">
            <span class="text-[#0A2540]">Sesión #{{ s.session_number }}</span>
            <span :class="s.session_type === 'virtual' ? 'bg-sky-100 text-sky-800' : 'bg-amber-100 text-amber-800'" class="rounded-full px-2.5 py-0.5 inline-flex items-center gap-1">
              <Video v-if="s.session_type === 'virtual'" class="h-3 w-3" />
              <MapPin v-else class="h-3 w-3" />
              {{ s.session_type === 'virtual' ? 'Virtual' : 'Presencial' }}
            </span>
          </div>
          <h4 class="font-bold text-[#0A2540] mt-1">{{ s.title }}</h4>
          <p class="text-xs text-slate-600">{{ s.session_date }} · {{ s.start_time }} - {{ s.end_time }}</p>
          <p class="text-xs text-slate-500 mt-1">
            {{ s.session_type === 'virtual' ? s.meeting_url : `${s.location_name ?? ''} · ${s.classroom ?? ''}` }}
          </p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold hover:bg-slate-50 cursor-pointer"
            @click="toggleModality(s)"
          >
            <RefreshCw class="h-3.5 w-3.5" />
            <span>Cambiar a {{ s.session_type === 'virtual' ? 'Presencial' : 'Virtual' }}</span>
          </button>
          <button
            type="button"
            class="rounded-xl bg-[#0A2540] px-3 py-2 text-xs font-bold text-white cursor-pointer"
            @click="guardarGrabacion(s)"
          >
            Grabación / Video
          </button>
        </div>
      </article>
    </div>
  </div>
</template>
