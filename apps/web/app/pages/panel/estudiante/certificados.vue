<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Award, Download, ExternalLink } from 'lucide-vue-next';
import type { EnrollmentItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['student', 'admin'],
});
useHead({ title: 'Mis Certificados' });

const enrollments = ref<EnrollmentItem[]>([]);

onMounted(async () => {
  const res = await $fetch<{ data: EnrollmentItem[] }>('/api/bff/student/enrollments');
  enrollments.value = res.data.filter((e) => e.active_certificate);
});
</script>

<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold text-[#0A2540]">Mis Certificados Verificables por QR</h2>
    <div class="grid gap-4 md:grid-cols-2">
      <article v-for="enr in enrollments" :key="enr.id" class="rounded-2xl bg-white border border-slate-200 p-6 space-y-3">
        <div class="flex items-center gap-2 text-gold-text font-bold text-xs uppercase">
          <Award class="h-4 w-4" />
          <span>{{ enr.active_certificate?.certificate_code }}</span>
        </div>
        <h3 class="font-bold text-[#0A2540]">{{ enr.cohort?.program?.name }}</h3>
        <div class="flex flex-wrap gap-2 pt-2">
          <a
            :href="`/api/bff/student/certificates/${enr.active_certificate?.id}/download`"
            class="inline-flex items-center gap-1.5 rounded-xl bg-[#0A2540] px-4 py-2 text-xs font-bold text-white"
          >
            <Download class="h-4 w-4" />
            <span>Descargar PDF</span>
          </a>
          <NuxtLink
            :to="`/verificar-certificado/${enr.active_certificate?.verification_uuid}`"
            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700"
          >
            <ExternalLink class="h-4 w-4" />
            <span>Ver verificación QR</span>
          </NuxtLink>
        </div>
      </article>
    </div>
    <p v-if="enrollments.length === 0" class="text-sm text-slate-600">No tienes certificados emitidos todavía.</p>
  </div>
</template>
