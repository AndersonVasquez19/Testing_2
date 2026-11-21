<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Video, MapPin, MessageSquare, Download, ExternalLink } from 'lucide-vue-next';
import type { EnrollmentItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['student', 'admin'],
});
useHead({ title: 'Mi Aula Virtual' });

const enrollments = ref<EnrollmentItem[]>([]);

onMounted(async () => {
  const res = await $fetch<{ data: EnrollmentItem[] }>('/api/bff/student/enrollments');
  enrollments.value = res.data;
});
</script>

<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-[#0A2540]">Mi Aula Virtual</h2>
      <p class="text-sm text-slate-600">Horario híbrido de sesiones, enlaces Meet/Zoom, grupo de WhatsApp y certificados.</p>
    </div>

    <div v-for="enr in enrollments" :key="enr.id" class="rounded-3xl bg-white border border-slate-200 p-6 space-y-5 shadow-xs">
      <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-gold-text">{{ enr.cohort?.code }}</span>
          <h3 class="text-xl font-bold text-[#0A2540]">{{ enr.cohort?.program?.name }}</h3>
          <p class="text-xs text-slate-600 mt-1">
            Estado: <strong class="uppercase">{{ enr.enrollment_status }}</strong>
            <span v-if="enr.attendance_percentage"> · Asistencia: {{ enr.attendance_percentage }}%</span>
            <span v-if="enr.final_grade"> · Nota: {{ enr.final_grade }}</span>
          </p>
        </div>

        <div class="flex flex-wrap gap-2">
          <a
            v-if="enr.whatsapp_group_link"
            :href="enr.whatsapp_group_link"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-800"
          >
            <MessageSquare class="h-4 w-4" />
            <span>Unirme al grupo de WhatsApp</span>
          </a>
          <a
            v-if="enr.active_certificate"
            :href="`/api/bff/student/certificates/${enr.active_certificate.id}/download`"
            class="inline-flex items-center gap-2 rounded-xl bg-[#0A2540] px-4 py-2 text-xs font-bold text-white"
          >
            <Download class="h-4 w-4" />
            <span>Descargar Certificado PDF</span>
          </a>
        </div>
      </div>

      <div v-if="enr.cohort?.sessions && enr.cohort.sessions.length > 0" class="space-y-2.5">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Cronograma de Sesiones</h4>
        <div v-for="s in enr.cohort.sessions" :key="s.id" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 flex flex-wrap items-center justify-between gap-3">
          <div>
            <p class="font-bold text-sm text-[#0A2540]">Sesión #{{ s.session_number }}: {{ s.title }}</p>
            <p class="text-xs text-slate-600">{{ s.session_date }} · {{ s.start_time }} - {{ s.end_time }}</p>
          </div>
          <div class="flex flex-wrap items-center gap-2 text-xs">
            <a
              v-if="s.session_type === 'virtual' && s.meeting_url"
              :href="s.meeting_url"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-1 rounded-lg bg-sky-700 px-3 py-1.5 font-bold text-white"
            >
              <Video class="h-3.5 w-3.5" />
              <span>Entrar a clase virtual</span>
            </a>
            <span v-else class="inline-flex items-center gap-1 rounded-lg bg-amber-100 px-3 py-1.5 font-semibold text-amber-900">
              <MapPin class="h-3.5 w-3.5" />
              <span>Presencial: {{ s.classroom || s.location_name }}</span>
            </span>
            <a
              v-if="s.recording_url"
              :href="s.recording_url"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 font-semibold text-slate-700"
            >
              <ExternalLink class="h-3.5 w-3.5" />
              <span>Ver grabación</span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div v-if="enrollments.length === 0" class="rounded-2xl bg-white border border-slate-200 p-8 text-center text-sm text-slate-600">
      Aún no tienes matrículas registradas. Explora nuestros cursos e inscríbete.
    </div>
  </div>
</template>
