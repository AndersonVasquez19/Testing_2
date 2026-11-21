<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { ProgramItem } from '~/types/horus';

definePageMeta({
  layout: 'panel',
  middleware: 'auth-rol',
  roles: ['admin'],
});
useHead({ title: 'Catálogo de Programas' });

const programs = ref<ProgramItem[]>([]);
const form = ref({
  code: '',
  name: '',
  type: 'course' as 'course' | 'training' | 'advisory',
  description: '',
  price: '250.00',
  duration_hours: 40,
});

const loadPrograms = async () => {
  const res = await $fetch<{ data: ProgramItem[] }>('/api/bff/admin/programs');
  programs.value = res.data;
};

const createProgram = async () => {
  await $fetch('/api/bff/admin/programs', { method: 'POST', body: form.value });
  form.value = { code: '', name: '', type: 'course', description: '', price: '250.00', duration_hours: 40 };
  await loadPrograms();
};

onMounted(loadPrograms);
</script>

<template>
  <div class="space-y-6">
    <h2 class="text-2xl font-bold text-[#0A2540]">Catálogo y Precios de Programas</h2>

    <form class="rounded-2xl bg-white border border-slate-200 p-6 grid gap-3 sm:grid-cols-3" @submit.prevent="createProgram">
      <input v-model="form.code" placeholder="Código (CUR-NEW-01)" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <input v-model="form.name" placeholder="Nombre del programa" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm sm:col-span-2">
      <select v-model="form.type" class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
        <option value="course">Curso</option>
        <option value="training">Capacitación</option>
        <option value="advisory">Asesoramiento 1 a 1</option>
      </select>
      <input v-model="form.price" placeholder="Precio (PEN)" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <input v-model.number="form.duration_hours" type="number" min="1" required class="rounded-xl border border-slate-300 px-3 py-2 text-sm">
      <textarea v-model="form.description" placeholder="Descripción" required class="sm:col-span-3 rounded-xl border border-slate-300 px-3 py-2 text-sm" />
      <button type="submit" class="rounded-xl bg-[#0A2540] px-5 py-2.5 text-xs font-bold text-white w-fit cursor-pointer">
        Guardar Programa
      </button>
    </form>

    <div class="grid gap-4 md:grid-cols-2">
      <article v-for="p in programs" :key="p.id" class="rounded-2xl bg-white border border-slate-200 p-5">
        <span class="text-xs font-mono font-bold text-gold-text">{{ p.code }} · {{ p.type }}</span>
        <h3 class="font-bold text-[#0A2540] mt-1">{{ p.name }}</h3>
        <p class="text-xs text-slate-600 mt-1">S/ {{ p.price }} · {{ p.duration_hours }} horas</p>
      </article>
    </div>
  </div>
</template>
