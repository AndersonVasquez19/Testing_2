<script setup lang="ts">
import { ref } from 'vue';
import { LogIn, Eye, EyeOff } from 'lucide-vue-next';
import { panelPathForRole, useSessionStore } from '~/stores/session';

definePageMeta({ layout: false });
useHead({ title: 'Inicia sesión' });

const router = useRouter();
const route = useRoute();
const session = useSessionStore();

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const errorMsg = ref('');
const loading = ref(false);

const submitLogin = async () => {
  loading.value = true;
  errorMsg.value = '';
  try {
    const user = await session.login(email.value, password.value);
    const redirect = typeof route.query.redirect === 'string' ? route.query.redirect : null;
    await router.push(redirect || panelPathForRole(user.role));
  } catch (err: unknown) {
    const e = err as { data?: { message?: string } };
    errorMsg.value = e?.data?.message || 'Credenciales incorrectas.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen bg-[#EEF2F7] flex items-center justify-center px-4 py-12 text-slate-900">
    <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-lg space-y-6">
      <div class="text-center">
        <NuxtLink to="/" class="inline-flex items-center gap-2 mb-3">
          <img src="/images/index/logos/logo-horus.png" alt="Horus Academic" class="h-10 w-10 object-contain">
          <span class="text-lg font-bold text-[#0A2540]">Horus Academic</span>
        </NuxtLink>
        <h1 class="text-2xl font-bold text-[#0A2540]">Inicia sesión</h1>
        <p class="mt-1 text-sm text-slate-600">Accede a tu panel operativo o Aula Virtual.</p>
      </div>

      <div v-if="errorMsg" role="alert" class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        {{ errorMsg }}
      </div>

      <form class="space-y-4" @submit.prevent="submitLogin">
        <div>
          <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Correo electrónico</label>
          <input id="email" v-model="email" type="email" required class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm">
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
          <div class="relative">
            <input
              id="password"
              v-model="password"
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

        <button
          type="submit"
          :disabled="loading"
          class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#0A2540] px-4 py-3 text-sm font-bold text-white hover:bg-[#153A59] disabled:opacity-50 cursor-pointer"
        >
          <LogIn class="h-4 w-4" />
          <span>{{ loading ? 'Ingresando…' : 'Ingresar' }}</span>
        </button>
      </form>

      <p class="text-center text-xs text-slate-600">
        ¿Eres estudiante nuevo?
        <NuxtLink to="/registro" class="font-semibold text-[#1B4469] underline">Regístrate aquí</NuxtLink>
      </p>
    </div>
  </div>
</template>
