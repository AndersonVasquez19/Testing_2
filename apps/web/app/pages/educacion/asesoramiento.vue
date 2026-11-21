<script setup lang="ts">
import { computed } from 'vue';
import { Award, UserCheck, Handshake, TrendingUp, MessageCircle } from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';
import type { ProgramItem } from '~/types/horus';

useHead({ title: 'Asesoramiento 1 a 1' });

const { data } = await useFetch<{ data: ProgramItem[] }>('/api/bff/public/programs', {
  query: { type: 'advisory' },
});
const advisories = computed(() => data.value?.data ?? []);
</script>

<template>
  <div class="pt-20">
    <section class="py-16 sm:py-24 bg-gradient-to-br from-[#0A2540] via-[#102d49] to-[#1a2f45] text-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
          <div class="inline-flex items-center gap-2 px-6 py-2 rounded-full border-2 border-gold bg-gold/20 mb-8">
            <Award class="h-4 w-4 text-gold" />
            <span class="text-xs sm:text-sm font-semibold tracking-wider text-gold uppercase">ASESORÍA 1 A 1</span>
          </div>
          <h1 class="text-3xl sm:text-5xl font-bold tracking-tight leading-tight">
            ASESORAMIENTO EDUCATIVO, EMPRESARIAL Y LEGAL
          </h1>
          <p class="mt-6 text-base sm:text-lg text-white/90 leading-relaxed mb-10">
            Agenda sesiones personalizadas 1 a 1 de forma virtual o presencial con nuestros especialistas.
          </p>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex items-center gap-3.5 rounded-xl border border-white/15 bg-white/10 p-4">
              <UserCheck class="h-8 w-8 text-gold shrink-0" />
              <div>
                <strong class="block text-base font-bold">Expertos</strong>
                <span class="text-xs text-white/80">Amplia experiencia</span>
              </div>
            </div>
            <div class="flex items-center gap-3.5 rounded-xl border border-white/15 bg-white/10 p-4">
              <Handshake class="h-8 w-8 text-gold shrink-0" />
              <div>
                <strong class="block text-base font-bold">Personalizado</strong>
                <span class="text-xs text-white/80">Sesiones 1 a 1</span>
              </div>
            </div>
            <div class="flex items-center gap-3.5 rounded-xl border border-white/15 bg-white/10 p-4">
              <TrendingUp class="h-8 w-8 text-gold shrink-0" />
              <div>
                <strong class="block text-base font-bold">Resultados</strong>
                <span class="text-xs text-white/80">Enfoque práctico</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-white py-16 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-12">
        <div class="grid gap-10 lg:grid-cols-2 items-center rounded-3xl border border-stone-200 bg-[#F7F9FC] p-8">
          <div>
            <h2 class="text-2xl font-bold text-[#0A2540] mb-4">Reserva tu horario en el Aula Virtual o por WhatsApp</h2>
            <p class="text-sm text-slate-700 leading-relaxed mb-6">
              Los estudiantes registrados pueden elegir directamente un horario disponible en su portal. También puedes coordinar tu cita 1 a 1 por WhatsApp.
            </p>
            <div class="flex flex-wrap gap-3">
              <NuxtLink to="/login" class="rounded-xl bg-[#0A2540] px-5 py-3 text-xs font-bold text-white hover:bg-[#153A59]">
                Reservar desde mi cuenta
              </NuxtLink>
              <a
                :href="`https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent('Hola Horus Academic, deseo agendar un asesoramiento 1 a 1.')}`"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-xs font-bold text-white hover:bg-emerald-800"
              >
                <MessageCircle class="h-4 w-4" />
                <span>Agendar por WhatsApp</span>
              </a>
            </div>
          </div>
          <img src="/images/asesoramiento/asesoramiento2.jpg" alt="Asesoramiento Horus" class="rounded-2xl h-64 w-full object-cover">
        </div>

        <div class="grid gap-6 md:grid-cols-3">
          <article class="rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
            <img src="/images/asesoramiento/asesoramiento-educativo.jpg" alt="Asesoramiento Educativo" class="h-48 w-full object-cover">
            <div class="p-6">
              <h3 class="font-bold text-[#0A2540]">Asesoramiento Educativo</h3>
              <p class="mt-2 text-sm text-slate-600">Orientación en tesis, proyectos académicos y metodología de investigación.</p>
            </div>
          </article>
          <article class="rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
            <img src="/images/asesoramiento/asesoramiento-empresarial.jpg" alt="Asesoramiento Empresarial" class="h-48 w-full object-cover">
            <div class="p-6">
              <h3 class="font-bold text-[#0A2540]">Asesoramiento Empresarial</h3>
              <p class="mt-2 text-sm text-slate-600">Gestión estratégica, finanzas, formalización y optimización operativa.</p>
            </div>
          </article>
          <article class="rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
            <img src="/images/asesoramiento/asesoramiento-legal.jpg" alt="Asesoramiento Legal" class="h-48 w-full object-cover">
            <div class="p-6">
              <h3 class="font-bold text-[#0A2540]">Asesoramiento Legal</h3>
              <p class="mt-2 text-sm text-slate-600">Consultoría normativa, laboral, administrativa y protección al consumidor.</p>
            </div>
          </article>
        </div>

        <div v-if="advisories.length > 0" class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
          <h3 class="font-bold text-[#0A2540] mb-3">Servicios de Asesoramiento Activos</h3>
          <div class="grid gap-4 sm:grid-cols-2">
            <div v-for="adv in advisories" :key="adv.id" class="rounded-xl bg-white p-4 border border-slate-200">
              <p class="font-bold text-[#0A2540]">{{ adv.name }}</p>
              <p class="text-xs text-slate-600 mt-1">{{ adv.description }}</p>
              <p class="text-xs font-bold text-gold-text mt-2">S/ {{ adv.price }} · {{ adv.duration_hours }} horas</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
