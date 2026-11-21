<script setup lang="ts">
import { computed } from 'vue';
import {
  Award,
  UserCheck,
  Handshake,
  TrendingUp,
  Users,
  GraduationCap,
  Briefcase,
  Scale,
  CheckCircle2,
  Clock,
  RefreshCw,
  HeartHandshake,
  Puzzle,
  Quote,
  MessageCircle,
} from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';
import type { ProgramItem } from '~/types/horus';

useHead({ title: 'Asesoramiento 1 a 1' });

const { data } = await useFetch<{ data: ProgramItem[] }>('/api/bff/public/programs', {
  query: { type: 'advisory' },
});
const advisories = computed(() => data.value?.data ?? []);

const pillars = [
  {
    icon: Clock,
    title: 'RESPUESTA ÁGIL',
    description: 'Ante cualquier consulta, nuestro equipo de expertos te brindará orientación clara en un plazo máximo de 48 horas hábiles.',
  },
  {
    icon: RefreshCw,
    title: 'FLEXIBILIDAD TOTAL',
    description: 'Nos adaptamos a tu ritmo y ubicación, ofreciendo respaldo continuo tanto en modalidad presencial como online.',
  },
  {
    icon: HeartHandshake,
    title: 'ACOMPAÑAMIENTO INTEGRAL',
    description: 'Estamos contigo en todo el proceso; desde el nacimiento de tu idea y su implementación hasta la medición final de resultados.',
  },
  {
    icon: Puzzle,
    title: 'SOLUCIONES A MEDIDA',
    description: 'Personalizamos cada sesión 1 a 1 para asegurar que se ajuste exactamente a tus objetivos específicos.',
  },
];
</script>

<template>
  <div class="pt-20">
    <!-- Intro Asesoramiento Header con lineShine -->
    <section class="relative overflow-hidden py-16 sm:py-24 bg-gradient-to-br from-[#0A2540] via-[#102d49] to-[#1a2f45] text-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
          <div class="inline-flex items-center gap-2 px-6 py-2 rounded-full border-2 border-gold bg-gold/20 mb-8">
            <Award class="h-4 w-4 text-gold" aria-hidden="true" />
            <span class="text-xs sm:text-sm font-semibold tracking-wider text-gold uppercase">
              ASESORÍA PROFESIONAL 1 A 1
            </span>
          </div>

          <h1 class="relative pb-6 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight">
            ASESORAMIENTO EDUCATIVO, EMPRESARIAL Y LEGAL
            <span class="absolute bottom-0 left-0 h-1 w-48 rounded-full animate-line-shine" />
          </h1>

          <p class="mt-6 text-base sm:text-lg text-white/90 leading-relaxed text-justify mb-10">
            Contamos con profesionales altamente calificados y con amplia experiencia en asesoramiento 1 a 1, comprometidos en brindarle orientación estratégica para el desarrollo de sus proyectos personales y profesionales, con el objetivo de alcanzar resultados exitosos y sostenibles.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex items-center gap-3.5 rounded-xl border border-white/15 bg-white/10 p-4 backdrop-blur-md">
              <UserCheck class="h-8 w-8 text-gold shrink-0" aria-hidden="true" />
              <div>
                <strong class="block text-base font-bold text-white">Expertos</strong>
                <span class="text-xs text-white/80">Amplia experiencia práctica</span>
              </div>
            </div>

            <div class="flex items-center gap-3.5 rounded-xl border border-white/15 bg-white/10 p-4 backdrop-blur-md">
              <Handshake class="h-8 w-8 text-gold shrink-0" aria-hidden="true" />
              <div>
                <strong class="block text-base font-bold text-white">Personalizado</strong>
                <span class="text-xs text-white/80">Sesiones 1 a 1 dedicadas</span>
              </div>
            </div>

            <div class="flex items-center gap-3.5 rounded-xl border border-white/15 bg-white/10 p-4 backdrop-blur-md">
              <TrendingUp class="h-8 w-8 text-gold shrink-0" aria-hidden="true" />
              <div>
                <strong class="block text-base font-bold text-white">Resultados</strong>
                <span class="text-xs text-white/80">Enfoque medible y eficaz</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Bloque Destacado + 3 Cartas Principales -->
    <section class="bg-white py-16 sm:py-24 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-16">
        <div class="grid gap-10 lg:grid-cols-2 items-center rounded-3xl border border-stone-200 bg-[#F7F9FC] p-8 sm:p-12 shadow-sm">
          <div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[#0A2540] text-gold mb-6 shadow-md">
              <Users class="h-7 w-7" aria-hidden="true" />
            </div>
            <h2 class="text-2xl font-bold text-[#0A2540] mb-4">
              Reserva tu horario en el Aula Virtual o por WhatsApp
            </h2>
            <p class="text-base leading-relaxed text-slate-700 mb-6 text-justify">
              Contamos con personal capacitado y con trayectoria en diversos rubros, preparado para brindarte respaldo y orientación en la planificación y ejecución de tu proyecto. Si ya tienes cuenta, puedes elegir tu cupo horario directamente desde tu portal.
            </p>
            <div class="flex flex-wrap gap-3">
              <NuxtLink
                to="/login"
                class="rounded-xl bg-[#0A2540] px-5 py-3 text-xs sm:text-sm font-bold text-white hover:bg-[#153A59] transition-colors"
              >
                Reservar desde mi cuenta
              </NuxtLink>
              <a
                :href="`https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent('Hola Horus Academic, deseo agendar un asesoramiento 1 a 1.')}`"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 rounded-xl bg-emerald-800 px-5 py-3 text-xs sm:text-sm font-bold text-white hover:bg-emerald-900 transition-colors"
              >
                <MessageCircle class="h-4 w-4" aria-hidden="true" />
                <span>Agendar por WhatsApp</span>
              </a>
            </div>
          </div>

          <div class="relative h-64 sm:h-80 w-full rounded-2xl overflow-hidden border border-stone-200 shadow-lg">
            <img
              src="/images/asesoramiento/asesoramiento2.jpg"
              alt="Asesoramiento en Horus Group"
              class="h-full w-full object-cover"
            >
          </div>
        </div>

        <!-- 3 Áreas de Asesoramiento -->
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
          <article class="card-hover-border rounded-2xl bg-white border border-stone-200 shadow-[0_8px_30px_rgba(0,0,0,0.08)] hover:-translate-y-2 hover:shadow-[0_15px_45px_rgba(0,0,0,0.13)] hover:border-gold flex flex-col justify-between overflow-hidden">
            <div>
              <div class="relative h-52 w-full bg-stone-100 overflow-hidden">
                <img
                  src="/images/asesoramiento/asesoramiento-educativo.jpg"
                  alt="Asesoramiento Educativo"
                  class="h-full w-full object-cover hover:scale-105 transition-transform duration-500"
                >
              </div>
              <div class="relative z-10 -mt-8 ml-6 flex h-14 w-14 items-center justify-center rounded-full border-4 border-white bg-gradient-to-br from-[#0A2540] to-gold shadow-md text-white">
                <GraduationCap class="h-6 w-6" aria-hidden="true" />
              </div>
              <div class="p-6 pt-3">
                <h3 class="text-xl font-bold text-[#0A2540] mb-3 leading-snug">ASESORAMIENTO EDUCATIVO</h3>
                <p class="text-sm text-slate-600 leading-relaxed mb-6">
                  Te ayudamos a estructurar, redactar y finalizar tu tesis o trabajo de grado con rigor académico y claridad metodológica.
                </p>
                <div class="space-y-2 border-t border-stone-100 pt-4 text-xs sm:text-sm text-slate-700 font-medium">
                  <div class="flex items-center gap-2">
                    <CheckCircle2 class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                    <span>Metodología rigurosa</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <CheckCircle2 class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                    <span>Acompañamiento continuo</span>
                  </div>
                </div>
              </div>
            </div>
          </article>

          <article class="card-hover-border rounded-2xl bg-white border border-stone-200 shadow-[0_8px_30px_rgba(0,0,0,0.08)] hover:-translate-y-2 hover:shadow-[0_15px_45px_rgba(0,0,0,0.13)] hover:border-gold flex flex-col justify-between overflow-hidden">
            <div>
              <div class="relative h-52 w-full bg-stone-100 overflow-hidden">
                <img
                  src="/images/asesoramiento/asesoramiento-empresarial.jpg"
                  alt="Asesoramiento Empresarial"
                  class="h-full w-full object-cover hover:scale-105 transition-transform duration-500"
                >
              </div>
              <div class="relative z-10 -mt-8 ml-6 flex h-14 w-14 items-center justify-center rounded-full border-4 border-white bg-gradient-to-br from-[#0A2540] to-gold shadow-md text-white">
                <Briefcase class="h-6 w-6" aria-hidden="true" />
              </div>
              <div class="p-6 pt-3">
                <h3 class="text-xl font-bold text-[#0A2540] mb-3 leading-snug">ASESORAMIENTO EMPRESARIAL</h3>
                <p class="text-sm text-slate-600 leading-relaxed mb-6">
                  Te brindamos las herramientas necesarias tanto en gestión financiera y proyectos como en liderazgo y organización para consolidar tu empresa.
                </p>
                <div class="space-y-2 border-t border-stone-100 pt-4 text-xs sm:text-sm text-slate-700 font-medium">
                  <div class="flex items-center gap-2">
                    <CheckCircle2 class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                    <span>Estrategias efectivas</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <CheckCircle2 class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                    <span>Resultados medibles</span>
                  </div>
                </div>
              </div>
            </div>
          </article>

          <article class="card-hover-border rounded-2xl bg-white border border-stone-200 shadow-[0_8px_30px_rgba(0,0,0,0.08)] hover:-translate-y-2 hover:shadow-[0_15px_45px_rgba(0,0,0,0.13)] hover:border-gold flex flex-col justify-between overflow-hidden">
            <div>
              <div class="relative h-52 w-full bg-stone-100 overflow-hidden">
                <img
                  src="/images/asesoramiento/asesoramiento-legal.jpg"
                  alt="Asesoramiento Legal"
                  class="h-full w-full object-cover hover:scale-105 transition-transform duration-500"
                >
              </div>
              <div class="relative z-10 -mt-8 ml-6 flex h-14 w-14 items-center justify-center rounded-full border-4 border-white bg-gradient-to-br from-[#0A2540] to-gold shadow-md text-white">
                <Scale class="h-6 w-6" aria-hidden="true" />
              </div>
              <div class="p-6 pt-3">
                <h3 class="text-xl font-bold text-[#0A2540] mb-3 leading-snug">ASESORAMIENTO LEGAL</h3>
                <p class="text-sm text-slate-600 leading-relaxed mb-6">
                  Orientación normativa, laboral, administrativa y protección al consumidor con especialistas comprometidos en darte seguridad jurídica.
                </p>
                <div class="space-y-2 border-t border-stone-100 pt-4 text-xs sm:text-sm text-slate-700 font-medium">
                  <div class="flex items-center gap-2">
                    <CheckCircle2 class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                    <span>Asesoría especializada</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <CheckCircle2 class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                    <span>Respaldo profesional</span>
                  </div>
                </div>
              </div>
            </div>
          </article>
        </div>

        <div v-if="advisories.length > 0" class="rounded-2xl border border-stone-200 bg-stone-50 p-6 sm:p-8">
          <h2 class="text-lg font-bold text-[#0A2540] mb-4">Programas de Asesoramiento Activos</h2>
          <div class="grid gap-4 sm:grid-cols-2">
            <div v-for="adv in advisories" :key="adv.id" class="rounded-xl bg-white p-5 border border-stone-200">
              <p class="font-bold text-[#0A2540]">{{ adv.name }}</p>
              <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ adv.description }}</p>
              <p class="text-xs font-bold text-gold-text mt-2 tabular-nums">S/ {{ adv.price }} · {{ adv.duration_hours }} horas</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Compromiso y Respaldo Profesional + Cita Final -->
    <section class="bg-gradient-to-b from-[#F7F9FC] to-white py-16 sm:py-24 border-t border-stone-200 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="relative inline-block pb-4 text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight text-[#0A2540]">
            Compromiso y Respaldo Profesional
            <span class="absolute bottom-0 left-1/2 -translate-x-1/2 h-1 w-44 rounded-full animate-line-shine" />
          </h2>
          <p class="mt-4 text-base text-slate-600 leading-relaxed">
            En Horus Group S.R.L. convertimos nuestra experiencia en tu mayor tranquilidad, con acompañamiento técnico y profesional en cada paso.
          </p>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-16">
          <div
            v-for="item in pillars"
            :key="item.title"
            class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-gold/50 transition-all"
          >
            <component :is="item.icon" class="h-8 w-8 text-gold mb-4" aria-hidden="true" />
            <h3 class="text-lg font-bold text-[#0A2540] mb-2">{{ item.title }}</h3>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
              {{ item.description }}
            </p>
          </div>
        </div>

        <div class="rounded-3xl border border-gold/30 bg-[#0A2540] p-8 sm:p-12 text-center text-white max-w-4xl mx-auto shadow-xl">
          <Quote class="h-10 w-10 text-gold mx-auto mb-4 opacity-80" aria-hidden="true" />
          <p class="text-lg sm:text-xl font-medium leading-relaxed italic text-white/95">
            «Elegirnos significa elegir tranquilidad, profesionalismo y un respaldo constante que transforma tus dudas en soluciones concretas.»
          </p>
        </div>
      </div>
    </section>
  </div>
</template>
