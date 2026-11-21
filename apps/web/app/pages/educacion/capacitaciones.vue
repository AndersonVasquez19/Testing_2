<script setup lang="ts">
import { computed } from 'vue';
import { Star, Users, Clock, Award, Layers, Handshake, Check, MessageCircle } from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';
import type { ProgramItem } from '~/types/horus';

useHead({ title: 'Capacitaciones' });

const { data } = await useFetch<{ data: ProgramItem[] }>('/api/bff/public/programs', {
  query: { type: 'training' },
});
const trainings = computed(() => data.value?.data ?? []);

const programas = [
  {
    titulo: 'CAPACITACIONES EMPRESARIALES',
    descripcion:
      'Programas profesionales diseñados para los colaboradores de tu empresa o institución, manteniéndolos a la vanguardia de las competencias que exige el mercado actual.',
    imagen: '/images/capacitaciones/capacitaciones-empresariales.jpg',
    icon: Users,
    features: ['Programas personalizados', 'Modalidad híbrida / online', 'Certificación incluida'],
  },
  {
    titulo: 'FORMACIÓN PERSONALIZADA',
    descripcion:
      'Capacitaciones personales y grupales adaptadas a tus necesidades educativas, profesionales y laborales, con certificación que garantiza la efectividad del aprendizaje.',
    imagen: '/images/capacitaciones/formacion-personalizada.jpg',
    icon: Layers,
    features: ['Atención individualizada', 'Horarios flexibles', 'Seguimiento continuo'],
  },
  {
    titulo: 'CERTIFICACIONES PROFESIONALES',
    descripcion:
      'Actualiza tus conocimientos teóricos y experiencias laborales mediante evaluación y certificación física y digital verificable por código QR.',
    imagen: '/images/capacitaciones/certificaciones-profesionales.jpg',
    icon: Award,
    features: ['Certificados oficiales', 'Verificación QR en línea', 'Validez curricular'],
  },
  {
    titulo: 'CONVENIOS INSTITUCIONALES',
    descripcion:
      'Establecemos alianzas estratégicas con Colegios Profesionales e instituciones para avanzar con un trabajo mancomunado generando beneficios compartidos.',
    imagen: '/images/capacitaciones/convenios-institucionales.jpg',
    icon: Handshake,
    features: ['Alianzas estratégicas', 'Beneficios exclusivos', 'Respaldo institucional'],
  },
];
</script>

<template>
  <div class="pt-20">
    <!-- Hero Capacitaciones con lineShine y 3 stats glass -->
    <section class="relative overflow-hidden py-16 sm:py-24 bg-gradient-to-br from-[#0A2540] via-[#102d49] to-[#1a2f45] text-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
          <div class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full border-2 border-gold bg-gold/20 mb-8">
            <Star class="h-4 w-4 text-gold fill-gold" aria-hidden="true" />
            <span class="text-xs sm:text-sm font-semibold tracking-wider text-gold uppercase">
              FORMACIÓN PROFESIONAL
            </span>
          </div>

          <h1 class="relative pb-6 text-3xl sm:text-5xl font-bold tracking-tight text-white leading-tight">
            CAPACITACIONES
            <span class="absolute bottom-0 left-0 h-1 w-44 rounded-full animate-line-shine" />
          </h1>

          <p class="mt-6 text-base sm:text-lg text-white/90 leading-relaxed text-justify mb-10">
            Impulsamos el talento de tu equipo para enfrentar los desafíos de un mercado en constante evolución. Diseñamos programas de formación estratégica que aseguran que tus colaboradores estén siempre a la vanguardia.
          </p>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex items-center gap-4 p-4 rounded-xl border border-white/15 bg-white/10 backdrop-blur-md">
              <Users class="h-8 w-8 text-gold shrink-0" aria-hidden="true" />
              <div>
                <strong class="block text-xl font-bold text-white tabular-nums">+400</strong>
                <span class="text-xs text-white/80">Profesionales capacitados</span>
              </div>
            </div>

            <div class="flex items-center gap-4 p-4 rounded-xl border border-white/15 bg-white/10 backdrop-blur-md">
              <Clock class="h-8 w-8 text-gold shrink-0" aria-hidden="true" />
              <div>
                <strong class="block text-xl font-bold text-white">Flexible</strong>
                <span class="text-xs text-white/80">Horarios y sesiones híbridas</span>
              </div>
            </div>

            <div class="flex items-center gap-4 p-4 rounded-xl border border-white/15 bg-white/10 backdrop-blur-md">
              <Award class="h-8 w-8 text-gold shrink-0" aria-hidden="true" />
              <div>
                <strong class="block text-xl font-bold text-white tabular-nums">100%</strong>
                <span class="text-xs text-white/80">Certificación QR garantizada</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Nuestros Programas de Capacitación -->
    <section class="bg-gradient-to-b from-white to-[#f8f9fa] py-16 sm:py-24 text-slate-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-16">
        <div class="text-center mb-12">
          <h2 class="relative inline-block pb-4 text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight text-[#0A2540]">
            NUESTROS PROGRAMAS DE CAPACITACIÓN
            <span class="absolute bottom-0 left-1/2 -translate-x-1/2 h-1 w-48 rounded-full animate-line-shine" />
          </h2>
          <p class="mt-4 text-base text-slate-600">
            Soluciones formativas adaptadas a tus metas profesionales y organizacionales.
          </p>
        </div>

        <div class="grid gap-8 sm:grid-cols-2">
          <article
            v-for="prog in programas"
            :key="prog.titulo"
            class="card-hover-border rounded-2xl bg-white border border-stone-200 overflow-hidden shadow-[0_8px_30px_rgba(0,0,0,0.08)] hover:-translate-y-2 hover:shadow-[0_15px_45px_rgba(0,0,0,0.13)] hover:border-gold transition-all duration-300 flex flex-col justify-between"
          >
            <div>
              <div class="relative h-56 sm:h-64 w-full bg-stone-100 overflow-hidden">
                <img
                  :src="prog.imagen"
                  :alt="prog.titulo"
                  class="h-full w-full object-cover hover:scale-105 transition-transform duration-500"
                >
              </div>

              <div class="relative z-10 -mt-8 ml-8 flex h-16 w-16 items-center justify-center rounded-full border-4 border-white bg-gradient-to-br from-[#0A2540] to-gold shadow-md text-white">
                <component :is="prog.icon" class="h-7 w-7" aria-hidden="true" />
              </div>

              <div class="p-8 pt-4">
                <h3 class="text-xl sm:text-2xl font-bold text-[#0A2540] mb-3 leading-snug">
                  {{ prog.titulo }}
                </h3>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed mb-6">
                  {{ prog.descripcion }}
                </p>
                <div class="space-y-2.5 border-t border-stone-100 pt-5">
                  <div v-for="feat in prog.features" :key="feat" class="flex items-center gap-2.5 text-xs sm:text-sm text-slate-700 font-medium">
                    <Check class="h-4 w-4 text-gold-text shrink-0" aria-hidden="true" />
                    <span>{{ feat }}</span>
                  </div>
                </div>
              </div>
            </div>
          </article>
        </div>

        <div v-if="trainings.length > 0" class="rounded-3xl bg-white border border-stone-200 p-6 sm:p-8 shadow-sm">
          <h2 class="text-xl font-bold text-[#0A2540] mb-4">Capacitaciones con Inscripción Abierta</h2>
          <div class="grid gap-4 md:grid-cols-2">
            <div v-for="t in trainings" :key="t.id" class="rounded-2xl border border-stone-200 bg-stone-50/70 p-5 flex items-center justify-between gap-4">
              <div>
                <h3 class="font-bold text-[#0A2540]">{{ t.name }}</h3>
                <p class="text-xs text-slate-600 mt-1 tabular-nums">{{ t.duration_hours }} h · S/ {{ t.price }}</p>
              </div>
              <a
                :href="`https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent(`Hola, solicito información de la capacitación: ${t.name}`)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-800 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-900 shrink-0 transition-colors"
              >
                <MessageCircle class="h-4 w-4" aria-hidden="true" />
                <span>Consultar</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
