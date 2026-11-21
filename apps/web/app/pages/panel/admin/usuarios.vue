<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { AuthUser, UserRole } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['admin'],
});
useHead({ title: 'Usuarios y Roles' });

const users = ref<AuthUser[]>([]);
const notice = ref('');
const form = ref({
  name: '',
  email: '',
  dni: '',
  phone: '',
  role: 'secretary' as UserRole,
  password: '',
});

const loadUsers = async () => {
  const res = await $fetch<{ data: AuthUser[] }>('/api/bff/admin/users');
  users.value = res.data;
};

const createUser = async () => {
  await $fetch('/api/bff/admin/users', { method: 'POST', body: form.value });
  notice.value = 'Usuario creado correctamente.';
  form.value = { name: '', email: '', dni: '', phone: '', role: 'secretary', password: '' };
  await loadUsers();
};

const toggleActive = async (u: AuthUser) => {
  await $fetch(`/api/bff/admin/users/${u.id}`, {
    method: 'PATCH',
    body: { is_active: !u.is_active },
  });
  await loadUsers();
};

onMounted(loadUsers);
</script>

<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold text-[#0A2540]">Gestión de Usuarios y Roles (RBAC)</h2>
    <p v-if="notice" role="status" class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2 text-sm text-emerald-800">{{ notice }}</p>

    <form class="rounded-2xl bg-white border border-slate-200 p-6 grid gap-3 sm:grid-cols-3" @submit.prevent="createUser">
      <input v-model="form.name" placeholder="Nombre completo" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <input v-model="form.email" type="email" placeholder="Correo" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <select v-model="form.role" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <option value="admin">Admin</option>
        <option value="secretary">Secretaría</option>
        <option value="coordinator">Coordinador</option>
        <option value="student">Estudiante</option>
      </select>
      <input v-model="form.dni" placeholder="DNI (8 dígitos)" maxlength="8" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <input v-model="form.phone" placeholder="+519..." class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <input v-model="form.password" type="password" placeholder="Contraseña inicial (mín 8)" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <button type="submit" class="sm:col-span-3 rounded-xl bg-[#0A2540] px-5 py-2.5 text-xs font-bold text-white w-fit cursor-pointer">
        Crear Usuario
      </button>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-bold uppercase text-slate-600">
          <tr>
            <th class="px-4 py-3">Nombre</th>
            <th class="px-4 py-3">Correo</th>
            <th class="px-4 py-3">Rol</th>
            <th class="px-4 py-3">Teléfono</th>
            <th class="px-4 py-3">Estado</th>
            <th class="px-4 py-3 text-right">Acción</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="u in users" :key="u.id">
            <td class="px-4 py-3 font-semibold">{{ u.name }}</td>
            <td class="px-4 py-3 text-xs">{{ u.email }}</td>
            <td class="px-4 py-3 text-xs uppercase font-bold text-[#1B4469]">{{ u.role }}</td>
            <td class="px-4 py-3 text-xs">{{ u.phone ?? '—' }}</td>
            <td class="px-4 py-3 text-xs">{{ u.is_active ? 'Activo' : 'Inactivo' }}</td>
            <td class="px-4 py-3 text-right">
              <button type="button" class="rounded-lg border px-3 py-1 text-xs font-semibold cursor-pointer" @click="toggleActive(u)">
                {{ u.is_active ? 'Desactivar' : 'Activar' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
