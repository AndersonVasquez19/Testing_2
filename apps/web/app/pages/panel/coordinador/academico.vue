<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Award, CheckCircle2 } from 'lucide-vue-next';
import type { CohortItem, EnrollmentItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['coordinator', 'admin'],
});
useHead({ title: 'Aprobación y Certificados' });

const route = useRoute();
const cohorts = ref<CohortItem[]>([]);
const selectedCohortId = ref<number>(0);
const enrollments = ref<EnrollmentItem[]>([]);
const notice = ref('');

const loadEnrollments = async () => {
  if (!selectedCohortId.value) return;
  const res = await $fetch<{ data: EnrollmentItem[] }>(`/api/bff/coordinator/cohorts/${selectedCohortId.value}/enrollments`);
  enrollments.value = res.data;
};

onMounted(async () => {
  const cRes = await $fetch<{ data: CohortItem[] }>('/api/bff/coordinator/cohorts');
  cohorts.value = cRes.data;
  selectedCohortId.value = Number(route.query.cohort || 0) || cohorts.value[0]?.id || 0;
  await loadEnrollments();
});

const aprobar = async (e: EnrollmentItem) => {
  const grade = window.prompt('Nota final (0 - 20):', e.final_grade || '17');
  if (grade === null) return;
  await $fetch(`/api/bff/coordinator/enrollments/${e.id}/academic`, {
    method: 'PATCH',
    body: {
      final_grade: Number(grade),
      attendance_percentage: Number(e.attendance_percentage || 100),
      academic_approved: true,
    },
  });
  notice.value = `Aprobación académica guardada para ${e.student?.name}.`;
  await loadEnrollments();
};

const emitirCertificado = async (e: EnrollmentItem) => {
  await $fetch(`/api/bff/coordinator/enrollments/${e.id}/certificate`, { method: 'POST' });
  notice.value = `Certificado QR emitido para ${e.student?.name}.`;
  await loadEnrollments();
};
</script>

<template>
  <div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-bold text-[#0A2540]">Cierre Académico y Emisión de Certificados QR</h2>
        <p class="text-sm text-slate-600">Aprueba notas/asistencia y emite certificados PDF con código QR.</p>
      </div>
      <select v-model="selectedCohortId" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" @change="loadEnrollments">
        <option v-for="c in cohorts" :key="c.id" :value="c.id">{{ c.code }} · {{ c.name }}</option>
      </select>
    </div>

    <p v-if="notice" role="status" class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm text-emerald-800">
      {{ notice }}
    </p>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-bold uppercase text-slate-600">
          <tr>
            <th class="px-4 py-3">Estudiante</th>
            <th class="px-4 py-3">Asistencia</th>
            <th class="px-4 py-3">Nota Final</th>
            <th class="px-4 py-3">Aprobación</th>
            <th class="px-4 py-3">Certificado</th>
            <th class="px-4 py-3 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="e in enrollments" :key="e.id">
            <td class="px-4 py-3 font-semibold">{{ e.student?.name }}</td>
            <td class="px-4 py-3">{{ e.attendance_percentage ?? '—' }}%</td>
            <td class="px-4 py-3 font-bold">{{ e.final_grade ?? '—' }}</td>
            <td class="px-4 py-3 text-xs">
              <span v-if="e.academic_approved" class="text-emerald-700 font-bold inline-flex items-center gap-1"><CheckCircle2 class="h-3.5 w-3.5" /> Aprobado</span>
              <span v-else class="text-slate-500">Pendiente</span>
            </td>
            <td class="px-4 py-3 font-mono text-xs">{{ e.active_certificate?.certificate_code ?? '—' }}</td>
            <td class="px-4 py-3 text-right space-x-2">
              <button type="button" class="rounded-lg border px-3 py-1.5 text-xs font-semibold hover:bg-slate-50 cursor-pointer" @click="aprobar(e)">
                Calificar / Aprobar
              </button>
              <button
                v-if="e.academic_approved && !e.active_certificate"
                type="button"
                class="inline-flex items-center gap-1 rounded-lg bg-[#0A2540] px-3 py-1.5 text-xs font-bold text-white cursor-pointer"
                @click="emitirCertificado(e)"
              >
                <Award class="h-3.5 w-3.5" />
                <span>Emitir QR</span>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
