<script setup lang="ts">
import { Phone, Mail, MapPin, BookOpen } from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';
</script>

<template>
  <footer class="mt-20 border-t-4 border-gold bg-[#0A1929] text-stone-200">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
      <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
        <div>
          <div class="mb-5 flex items-center gap-3">
            <img
              src="/images/index/logos/logo-horus.png"
              alt="Horus Group SRL"
              class="h-12 w-12 flex-shrink-0 object-contain"
            >
            <div>
              <h3 class="text-lg font-bold text-white tracking-wide">Horus Group SRL</h3>
              <span class="text-xs font-semibold tracking-wider text-gold">RUC {{ CONTACT_INFO.ruc }}</span>
            </div>
          </div>
          <p class="text-sm leading-relaxed text-stone-300">
            Especialistas en formación profesional, capacitaciones certificadas y asesoramiento para empresas y profesionales en Cajamarca - Perú.
          </p>
        </div>

        <div>
          <h4 class="mb-4 text-base font-bold text-gold uppercase tracking-wider">
            Términos y Condiciones
          </h4>
          <ul class="space-y-2.5 text-sm text-stone-300">
            <li>
              <NuxtLink to="/legales/politicas-devolucion" class="hover:text-gold transition-colors">
                Políticas de devolución
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/legales/politicas-privacidad" class="hover:text-gold transition-colors">
                Políticas de privacidad
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/legales/politicas-cookies" class="hover:text-gold transition-colors">
                Políticas de cookies
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/legales/preguntas-frecuentes" class="hover:text-gold transition-colors">
                Preguntas frecuentes (FAQ)
              </NuxtLink>
            </li>
          </ul>
        </div>

        <div>
          <h4 class="mb-4 text-base font-bold text-gold uppercase tracking-wider">
            Contactos
          </h4>
          <ul class="space-y-3 text-sm text-stone-300">
            <li class="flex items-start gap-2.5">
              <Phone class="h-4 w-4 text-gold flex-shrink-0 mt-0.5" aria-hidden="true" />
              <a :href="`tel:${CONTACT_INFO.phoneClean}`" class="hover:text-gold transition-colors">
                {{ CONTACT_INFO.phone }}
              </a>
            </li>
            <li class="flex items-start gap-2.5">
              <Mail class="h-4 w-4 text-gold flex-shrink-0 mt-0.5" aria-hidden="true" />
              <a :href="`mailto:${CONTACT_INFO.email}`" class="hover:text-gold transition-colors break-all">
                {{ CONTACT_INFO.email }}
              </a>
            </li>
            <li class="flex items-start gap-2.5">
              <MapPin class="h-4 w-4 text-gold flex-shrink-0 mt-0.5" aria-hidden="true" />
              <span>{{ CONTACT_INFO.address }}</span>
            </li>
          </ul>
        </div>

        <div>
          <h4 class="mb-4 text-base font-bold text-gold uppercase tracking-wider">
            Libro de Reclamaciones
          </h4>
          <p class="mb-4 text-xs text-stone-300 leading-relaxed">
            Conforme al D.S. N.° 011-2011-PCM y normativa INDECOPI vigente.
          </p>
          <NuxtLink
            to="/legales/libro-reclamaciones"
            class="group inline-flex items-center gap-3 rounded-2xl border border-gold/40 bg-gold/5 px-4 py-2.5 text-sm font-semibold text-stone-200 hover:bg-gold/15 hover:border-gold transition-all"
          >
            <BookOpen class="h-4 w-4 text-gold group-hover:scale-110 transition-transform" aria-hidden="true" />
            <span>Libro de Reclamaciones</span>
          </NuxtLink>
        </div>
      </div>
    </div>

    <div class="border-t border-white/10 bg-[#071321] py-4 text-center text-xs text-stone-400">
      © 2026 Horus Group SRL. Todos los derechos reservados.
    </div>
  </footer>
</template>
