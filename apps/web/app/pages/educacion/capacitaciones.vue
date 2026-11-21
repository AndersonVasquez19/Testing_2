<script setup lang="ts">
import { computed } from 'vue';
import { Star, Users, Clock, Award, Check, MessageCircle } from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';
import type { ProgramItem } from '~/types/horus';

useHead({ title: 'Capacitaciones' });

const { data } = await useFetch<{ data: ProgramItem[] }>('/api/bff/public/programs', {
  query: { type: 'training' },
});
const trainings = computed(() => data.value?.data ?? []);

const bloques = [
  {
    titulo: 'CAPACITACIONES EMPRESARIALES',
    descripcion: 'Programas diseñados para colaboradores de empresas e instituciones.',
    imagen: '/images/capacitaciones/capacitaciones-empresariales.jpg',
    features: ['Programas personalizados', 'Modalidad híbrida u online', 'Certificación incluida'],
  },
  {
    titulo: 'FORMACIÓN PERSONALIZADA',
    descripcion: 'Capacitaciones individuales y grupales adaptadas a tus metas laborales.',
    imagen: '/images/capacitaciones/formacion-personalizada.jpg',
    features: ['Atención individualizada', 'Horarios flexibles', 'Seguimiento continuo'],
  },
  {
    titulo: 'CERTIFICACIONES PROFESIONALES',
    descripcion: 'Valida tus competencias con certificación física y digital verificable por QR.',
    imagen: '/images/capacitaciones/certificaciones-profesionales.jpg',
    features: ['Certificados oficiales', 'Verificación QR en línea', 'Validez curricular'],
  },
  {
    titulo: 'CONVENIOS INSTITUCIONALES',
    descripcion: 'Alianzas con Colegios Profesionales e institutos para respaldo académico.',
    imagen: '/images/capacitaciones/convenios-institucionales.jpg',
    features: ['Alianzas estratégicas', 'Beneficios corporativos', 'Docentes especialistas'],
  },
];
</script>

<template>
  <div class="pt-20">
    <section class="py-16 sm:py-24 bg-gradient-to-br from-[#0A2540] via-[#102d49] to-[#1a2f45] text-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
          <div class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full border-2 border-gold bg-gold/20 mb-8">
            <Star class="h-4 w-4 text-gold" />
            <span class="text-xs sm:text-sm font-semibold tracking-wider text-gold uppercase">FORMACIÓN PROFESIONAL</span>
          </div>
          <h1 class="text-3xl sm:text-5xl font-bold tracking-tight">CAPACITACIONES</h1>
          <p class="mt-6 text-base sm:text-lg text-white/90 leading-relaxed mb-10">
            Impulsamos el talento de tu equipo con programas estratégicos y modalidades flexibles.
          </p>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex items-center gap-4 p-4 rounded-xl border border-white/15 bg-white/10">
              <Users class="h-8 w-8 text-gold shrink-0" />
              <div>
                <strong class="block text-xl font-bold">+400</strong>
                <span class="text-xs text-white/80">Profesionales capacitados</span>
              </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-xl border border-white/15 bg-white/10">
              <Clock class="h-8 w-8 text-gold shrink-0" />
              <div>
                <strong class="block text-xl font-bold">Flexible</strong>
                <span class="text-xs text-white/80">Sesiones híbridas</span>
              </div>
            </div>
            <div class="flex items-center gap-4 p-4 rounded-xl border border-white/15 bg-white/10">
              <Award class="h-8 w-8 text-gold shrink-0" />
              <div>
                <strong class="block text-xl font-bold">100%</strong>
                <span class="text-xs text-white/80">Certificación QR</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-[#F7F9FC] py-16 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="grid gap-8 md:grid-cols-2">
          <article
            v-for="item in bloques"
            :key="item.titulo"
            class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm"
          >
            <img :src="item.imagen" :alt="item.titulo" class="h-52 w-full object-cover">
            <div class="p-6">
              <h2 class="text-lg font-bold text-[#0A2540]">{{ item.titulo }}</h2>
              <p class="mt-2 text-sm text-slate-600">{{ item.descripcion }}</p>
              <ul class="mt-4 space-y-1.5 text-xs text-slate-700">
                <li v-for="feat in item.features" :key="feat" class="flex items-center gap-2">
                  <Check class="h-4 w-4 text-emerald-700" />
                  <span>{{ feat }}</span>
                </li>
              </ul>
            </div>
          </article>
        </div>

        <div v-if="trainings.length > 0" class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8">
          <h2 class="text-xl font-bold text-[#0A2540] mb-4">Capacitaciones con Inscripción Abierta</h2>
          <div class="grid gap-4 md:grid-cols-2">
            <div v-for="t in trainings" :key="t.id" class="rounded-2xl border border-slate-200 p-5 flex items-center justify-between gap-4">
              <div>
                <h3 class="font-bold text-[#0A2540]">{{ t.name }}</h3>
                <p class="text-xs text-slate-600 mt-1">{{ t.duration_hours }} h · S/ {{ t.price }}</p>
              </div>
              <a
                :href="`https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent(`Hola, solicito información de la capacitación: ${t.name}`)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-800 shrink-0"
              >
                <MessageCircle class="h-4 w-4" />
                <span>Consultar</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
