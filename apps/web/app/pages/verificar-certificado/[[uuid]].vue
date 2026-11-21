<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { ShieldCheck, ShieldAlert, Search } from 'lucide-vue-next';

useHead({ title: 'Verificar Certificado QR' });

const route = useRoute();
const router = useRouter();
const codeInput = ref(String(route.params.uuid ?? ''));

interface VerifyResult {
  valid: boolean;
  status: 'valid' | 'revoked' | 'not_found';
  certificate_code?: string;
  verification_uuid?: string;
  issued_at?: string;
  revoked_at?: string | null;
  revocation_reason?: string | null;
  student_name?: string;
  program_name?: string;
  cohort_code?: string;
  duration_hours?: number | null;
  message?: string;
}

const result = ref<VerifyResult | null>(null);
const loading = ref(false);

const buscar = async () => {
  const code = codeInput.value.trim();
  if (!code) return;
  loading.value = true;
  result.value = null;
  try {
    result.value = await $fetch<VerifyResult>(`/api/bff/public/certificates/verify/${encodeURIComponent(code)}`);
  } catch (err: unknown) {
    const e = err as { data?: VerifyResult };
    result.value = e?.data ?? { valid: false, status: 'not_found', message: 'Certificado no encontrado.' };
  } finally {
    loading.value = false;
  }
};

const submitSearch = async () => {
  if (!codeInput.value.trim()) return;
  await router.push(`/verificar-certificado/${encodeURIComponent(codeInput.value.trim())}`);
  await buscar();
};

onMounted(() => {
  if (codeInput.value.trim()) {
    buscar();
  }
});
</script>

<template>
  <div class="pt-28 pb-20 mx-auto max-w-3xl px-4 text-slate-900">
    <div class="rounded-3xl bg-white p-8 shadow-xl border border-slate-200 space-y-6">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-gold-text">Autenticidad Académica</span>
        <h1 class="mt-1 text-2xl sm:text-3xl font-bold text-[#0A2540]">Verificación de Certificados QR</h1>
        <p class="mt-1 text-sm text-slate-600">Ingresa el código de certificado (ej. CERT-2026-000001) o UUID de verificación.</p>
      </div>

      <form class="flex gap-2" @submit.prevent="submitSearch">
        <input
          v-model="codeInput"
          placeholder="Código o UUID del certificado"
          class="flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-mono"
        >
        <button
          type="submit"
          :disabled="loading"
          class="inline-flex items-center gap-2 rounded-xl bg-[#0A2540] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#153A59] cursor-pointer"
        >
          <Search class="h-4 w-4" />
          <span>{{ loading ? 'Buscando…' : 'Verificar' }}</span>
        </button>
      </form>

      <div v-if="result" class="rounded-2xl border p-6" :class="result.valid ? 'border-emerald-300 bg-emerald-50' : 'border-red-300 bg-red-50'">
        <div class="flex items-start gap-3">
          <ShieldCheck v-if="result.valid" class="h-7 w-7 text-emerald-700 shrink-0" />
          <ShieldAlert v-else class="h-7 w-7 text-red-700 shrink-0" />
          <div class="space-y-1 text-sm">
            <h2 class="text-lg font-bold" :class="result.valid ? 'text-emerald-900' : 'text-red-900'">
              {{ result.valid ? 'Certificado Válido y Auténtico' : (result.status === 'revoked' ? 'Certificado Revocado' : 'Certificado No Encontrado') }}
            </h2>
            <template v-if="result.certificate_code">
              <p><strong>Código:</strong> <span class="font-mono">{{ result.certificate_code }}</span></p>
              <p><strong>Otorgado a:</strong> {{ result.student_name }}</p>
              <p><strong>Programa:</strong> {{ result.program_name }}</p>
              <p><strong>Edición:</strong> {{ result.cohort_code }} · <strong>Duración:</strong> {{ result.duration_hours }} h</p>
              <p><strong>Emitido el:</strong> {{ result.issued_at }}</p>
              <p v-if="result.revoked_at" class="text-red-800 font-semibold">
                Revocado el {{ result.revoked_at }} — Motivo: {{ result.revocation_reason }}
              </p>
            </template>
            <p v-else>{{ result.message }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
