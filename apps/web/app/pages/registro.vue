<script setup lang="ts">
import { ref } from 'vue';
import { Eye, EyeOff, UserPlus } from 'lucide-vue-next';
import { panelPathForRole, useSessionStore } from '~/stores/session';

definePageMeta({ layout: false });
useHead({ title: 'Registro de Estudiante' });

const router = useRouter();
const session = useSessionStore();

const form = ref({
  name: '',
  dni: '',
  phone: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const showPassword = ref(false);
const errorMsg = ref('');
const loading = ref(false);

const submitRegister = async () => {
  loading.value = true;
  errorMsg.value = '';
  try {
    const user = await session.register(form.value);
    await router.push(panelPathForRole(user.role));
  } catch (err: unknown) {
    const e = err as { data?: { message?: string } };
    errorMsg.value = e?.data?.message || 'Revisa los datos ingresados.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-[#EEF2F7] flex items-center justify-center px-4 py-12 text-slate-900">
    <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-lg space-y-6">
      <div class="text-center">
        <h1 class="text-2xl font-bold text-[#0A2540]">Crear cuenta de estudiante</h1>
        <p class="mt-1 text-sm text-slate-600">Inscríbete y accede a tus cursos, enlaces y certificados.</p>
      </div>

      <div v-if="errorMsg" role="alert" class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        {{ errorMsg }}
      </div>

      <form class="space-y-4" @submit.prevent="submitRegister">
        <div>
          <label for="reg_name" class="block text-sm font-medium text-slate-700 mb-1">Nombre completo</label>
          <input id="reg_name" v-model="form.name" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label for="reg_dni" class="block text-sm font-medium text-slate-700 mb-1">DNI (8 dígitos)</label>
            <input id="reg_dni" v-model="form.dni" maxlength="8" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
          </div>
          <div>
            <label for="reg_phone" class="block text-sm font-medium text-slate-700 mb-1">WhatsApp</label>
            <input id="reg_phone" v-model="form.phone" placeholder="+519..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
          </div>
        </div>
        <div>
          <label for="reg_email" class="block text-sm font-medium text-slate-700 mb-1">Correo electrónico</label>
          <input id="reg_email" v-model="form.email" type="email" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
        </div>
        <div>
          <label for="reg_password" class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
          <div class="relative">
            <input
              id="reg_password"
              v-model="form.password"
              name="password"
              :type="showPassword ? 'text' : 'password'"
              required
              class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 pr-10 text-sm"
            >
            <button
              type="button"
              :aria-label="showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'"
              :aria-pressed="showPassword"
              class="absolute inset-y-0 right-0 px-3 text-slate-500 hover:text-slate-800"
              @click="showPassword = !showPassword"
            >
              <EyeOff v-if="showPassword" class="h-4 w-4" />
              <Eye v-else class="h-4 w-4" />
            </button>
          </div>
        </div>
        <div>
          <label for="reg_confirm" class="block text-sm font-medium text-slate-700 mb-1">Confirmar contraseña</label>
          <input
            id="reg_confirm"
            v-model="form.password_confirmation"
            :type="showPassword ? 'text' : 'password'"
            required
            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm"
          >
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#0A2540] px-4 py-3 text-sm font-bold text-white hover:bg-[#153A59] disabled:opacity-50 cursor-pointer"
        >
          <UserPlus class="h-4 w-4" />
          <span>{{ loading ? 'Creando cuenta…' : 'Registrarme' }}</span>
        </button>
      </form>
    </div>
  </div>
</template>
