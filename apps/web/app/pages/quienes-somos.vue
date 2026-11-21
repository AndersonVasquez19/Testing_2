<script setup lang="ts">
import { ref } from 'vue';
import { Target, Eye, Star } from 'lucide-vue-next';

useHead({ title: 'Quiénes Somos' });

const activeCard = ref<string | null>(null);
const valores = ['Puntualidad', 'Confianza', 'Calidad', 'Vocación', 'Superación', 'Trabajo en equipo', 'Empatía'];
const equipo = [
  {
    id: 'marco',
    nombre: 'MARCO ALVAREZ',
    cargo: 'GERENTE GENERAL',
    foto: '/images/somos/marco2.jpeg',
    descripcion: 'Profesional de Economía de la Universidad Nacional de Cajamarca. Docente en institutos de educación superior en proyectos de investigación y competencias profesionales.',
  },
  {
    id: 'monica',
    nombre: 'MÓNICA MONZÓN',
    cargo: 'COORDINADORA INSTITUCIONAL',
    foto: '/images/somos/monica.jpeg',
    descripcion: 'Profesional de Enfermería por la Universidad Nacional de Cajamarca. Maestra en Ciencias con mención en Salud Ocupacional y Ambiental.',
  },
  {
    id: 'karen',
    nombre: 'KAREN TÁVARA',
    cargo: 'ASESORA LEGAL',
    foto: '/images/somos/Persona4.jpeg',
    descripcion: 'Profesional en Derecho por la Universidad Privada Antonio Guillermo Urrelo. Sólida experiencia en el ejercicio legal y en la docencia.',
  },
  {
    id: 'milagros',
    nombre: 'MILAGROS VILLEGAS',
    cargo: 'SECRETARIA',
    foto: '/images/somos/milagros.jpeg',
    descripcion: 'Profesional de enfermería técnica, con experiencia en atención al cliente, matrículas y coordinación académica.',
  },
];
</script>

<template>
  <div class="pt-20">
    <section class="relative overflow-hidden py-16 sm:py-24 bg-gradient-to-br from-[#060f1a] via-[#0A2540] to-[#0d2d4a] text-white">
      <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="max-w-2xl">
          <span class="inline-block rounded-full bg-gold/15 border border-gold/40 px-4 py-1.5 text-xs font-semibold tracking-[3px] text-gold uppercase mb-4">
            HORUS GROUP SRL
          </span>
          <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-white mb-4">
            ¿Quiénes Somos?
          </h1>
          <p class="text-lg text-stone-300 font-light max-w-lg leading-relaxed">
            Descubre nuestra misión, visión, valores y al equipo humano detrás de Horus Academic.
          </p>
        </div>
        <img src="/images/index/stiker/stiker.png" alt="Horus Group" class="h-28 w-auto drop-shadow-[0_4px_12px_rgba(201,169,97,0.4)]">
      </div>
    </section>

    <section class="py-16 sm:py-20 bg-[#f8f9fa] text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid gap-8 md:grid-cols-3">
        <article class="rounded-3xl bg-white border border-stone-200 p-8 shadow-sm">
          <Target class="h-9 w-9 text-gold mb-4" aria-hidden="true" />
          <h2 class="text-xl font-bold text-[#0A2540] mb-3">Misión</h2>
          <p class="text-sm text-slate-600 leading-relaxed">
            Brindar capacitación especializada, asesoramiento integral y certificaciones con respaldo institucional para potenciar la empleabilidad y competitividad profesional.
          </p>
        </article>
        <article class="rounded-3xl bg-white border border-stone-200 p-8 shadow-sm">
          <Eye class="h-9 w-9 text-gold mb-4" aria-hidden="true" />
          <h2 class="text-xl font-bold text-[#0A2540] mb-3">Visión</h2>
          <p class="text-sm text-slate-600 leading-relaxed">
            Consolidarnos como la institución referente en formación continua híbrida y presencial en el norte del país, reconocida por su calidad académica y calidez humana.
          </p>
        </article>
        <article class="rounded-3xl bg-white border border-stone-200 p-8 shadow-sm">
          <Star class="h-9 w-9 text-gold mb-4" aria-hidden="true" />
          <h2 class="text-xl font-bold text-[#0A2540] mb-3">Valores</h2>
          <ul class="flex flex-wrap gap-2">
            <li v-for="v in valores" :key="v" class="rounded-full bg-[#0A2540]/5 px-3 py-1 text-xs font-semibold text-[#0A2540]">
              {{ v }}
            </li>
          </ul>
        </article>
      </div>
    </section>

    <section class="py-16 bg-white text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center text-[#0A2540] mb-12">Nuestro Equipo</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <article
            v-for="persona in equipo"
            :key="persona.id"
            class="rounded-2xl border border-stone-200 bg-white overflow-hidden shadow-sm hover:border-gold transition-all"
          >
            <img :src="persona.foto" :alt="persona.nombre" class="h-56 w-full object-cover object-top">
            <div class="p-5">
              <span class="text-[11px] font-bold uppercase tracking-wider text-gold-text">{{ persona.cargo }}</span>
              <h3 class="text-base font-bold text-[#0A2540] mt-1">{{ persona.nombre }}</h3>
              <p class="mt-2 text-xs text-slate-600 leading-relaxed">{{ persona.descripcion }}</p>
              <button
                type="button"
                class="mt-3 text-xs font-semibold text-[#1B4469] hover:underline cursor-pointer"
                @click="activeCard = activeCard === persona.id ? null : persona.id"
              >
                {{ activeCard === persona.id ? 'Ocultar contacto' : 'Contacto directo' }}
              </button>
              <p v-if="activeCard === persona.id" class="mt-2 text-xs text-emerald-700 font-medium">
                Atención mediante Mesa de Partes y WhatsApp institucional.
              </p>
            </div>
          </article>
        </div>
      </div>
    </section>
  </div>
</template>
