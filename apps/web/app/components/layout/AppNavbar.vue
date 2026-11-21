<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Menu, X, ChevronDown, PhoneCall, LogIn } from 'lucide-vue-next';
import { CONTACT_INFO } from '~/data/contactos';
import { panelPathForRole, useSessionStore } from '~/stores/session';

const route = useRoute();
const session = useSessionStore();
const mobileOpen = ref(false);
const openDropdown = ref<string | null>(null);
const scrolled = ref(false);
const navRef = ref<HTMLElement | null>(null);
let closeTimer: ReturnType<typeof setTimeout> | null = null;
let lastHoverOpenAt = 0;

const isActive = (path: string) => {
  if (path === '/') return route.path === '/';
  return route.path.startsWith(path);
};

const panelHref = computed(() => (session.user ? panelPathForRole(session.user.role) : '/login'));

const handleMouseEnter = (menu: string) => {
  if (closeTimer) {
    clearTimeout(closeTimer);
    closeTimer = null;
  }
  if (openDropdown.value !== menu) {
    openDropdown.value = menu;
    lastHoverOpenAt = Date.now();
  }
};

const handleToggleDropdown = (menu: string) => {
  if (openDropdown.value === menu && Date.now() - lastHoverOpenAt < 400) {
    return;
  }
  openDropdown.value = openDropdown.value === menu ? null : menu;
};

const handleMouseLeave = () => {
  closeTimer = setTimeout(() => {
    openDropdown.value = null;
  }, 200);
};

const handleScroll = () => {
  scrolled.value = window.scrollY > 20;
};

const handleClickOutside = (e: MouseEvent) => {
  if (navRef.value && !navRef.value.contains(e.target as Node)) {
    openDropdown.value = null;
  }
};

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape') {
    openDropdown.value = null;
    mobileOpen.value = false;
  }
};

watch(() => route.path, () => {
  mobileOpen.value = false;
  openDropdown.value = null;
});

onMounted(() => {
  session.fetchSession().catch(() => null);
  window.addEventListener('scroll', handleScroll, { passive: true });
  document.addEventListener('mousedown', handleClickOutside);
  document.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  if (closeTimer) clearTimeout(closeTimer);
  window.removeEventListener('scroll', handleScroll);
  document.removeEventListener('mousedown', handleClickOutside);
  document.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
  <header
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-300 px-4',
      scrolled ? 'bg-[#0A2540]/95 backdrop-blur-md shadow-xl py-2 sm:py-3' : 'bg-[#0A2540]/75 backdrop-blur-sm py-3 sm:py-4',
    ]"
  >
    <div class="mx-auto max-w-7xl">
      <nav
        ref="navRef"
        aria-label="Navegación principal"
        class="flex items-center justify-between rounded-full border border-white/10 bg-white/5 backdrop-blur-lg px-4 py-2 sm:px-6 shadow-2xl relative"
      >
        <NuxtLink to="/" class="flex items-center gap-3 group focus-visible:outline-2 focus-visible:outline-gold rounded-full pr-2">
          <img
            src="/images/index/logos/logo-horus.png"
            alt="Horus Group Logo"
            class="h-10 w-10 sm:h-11 sm:w-11 object-contain transition-transform group-hover:scale-105"
          >
          <div class="flex flex-col">
            <span class="text-base sm:text-lg font-bold tracking-wide text-white group-hover:text-gold transition-colors">
              Horus Academic
            </span>
            <span class="text-[10px] tracking-wider text-gold font-semibold uppercase hidden sm:block">
              Educación &amp; Certificación
            </span>
          </div>
        </NuxtLink>

        <ul class="hidden lg:flex items-center gap-1 xl:gap-2">
          <li>
            <NuxtLink
              to="/"
              :class="[
                'px-3 py-1.5 rounded-full text-sm font-medium transition-all focus-visible:outline-2 focus-visible:outline-gold',
                isActive('/') ? 'text-gold bg-gold/10 font-semibold' : 'text-stone-200 hover:text-gold hover:bg-white/5',
              ]"
            >
              Inicio
            </NuxtLink>
          </li>

          <li>
            <NuxtLink
              to="/quienes-somos"
              :class="[
                'px-3 py-1.5 rounded-full text-sm font-medium transition-all focus-visible:outline-2 focus-visible:outline-gold',
                isActive('/quienes-somos') ? 'text-gold bg-gold/10 font-semibold' : 'text-stone-200 hover:text-gold hover:bg-white/5',
              ]"
            >
              Quiénes Somos
            </NuxtLink>
          </li>

          <li
            class="relative"
            @mouseenter="handleMouseEnter('educacion')"
            @mouseleave="handleMouseLeave"
          >
            <button
              type="button"
              :aria-expanded="openDropdown === 'educacion'"
              :class="[
                'flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium transition-all cursor-pointer focus-visible:outline-2 focus-visible:outline-gold',
                isActive('/educacion') || openDropdown === 'educacion'
                  ? 'text-gold bg-gold/10 font-semibold'
                  : 'text-stone-200 hover:text-gold hover:bg-white/5',
              ]"
              @click="handleToggleDropdown('educacion')"
            >
              <span>Educación</span>
              <ChevronDown
                :class="['h-3.5 w-3.5 transition-transform duration-200', openDropdown === 'educacion' && 'rotate-180 text-gold']"
              />
            </button>

            <div
              v-if="openDropdown === 'educacion'"
              class="absolute top-full left-0 pt-2 z-50 w-60"
              @mouseenter="handleMouseEnter('educacion')"
              @mouseleave="handleMouseLeave"
            >
              <div class="rounded-2xl border border-gold/30 bg-[#0A2540] p-2 text-white shadow-2xl backdrop-blur-xl">
                <NuxtLink
                  to="/educacion/cursos"
                  class="block rounded-xl px-3.5 py-2 text-sm text-stone-200 hover:text-gold hover:bg-white/10 transition-colors"
                  @click="openDropdown = null"
                >
                  Cursos
                </NuxtLink>
                <NuxtLink
                  to="/educacion/capacitaciones"
                  class="block rounded-xl px-3.5 py-2 text-sm text-stone-200 hover:text-gold hover:bg-white/10 transition-colors"
                  @click="openDropdown = null"
                >
                  Capacitaciones
                </NuxtLink>
                <NuxtLink
                  to="/educacion/asesoramiento"
                  class="block rounded-xl px-3.5 py-2 text-sm text-stone-200 hover:text-gold hover:bg-white/10 transition-colors"
                  @click="openDropdown = null"
                >
                  Asesoramiento 1 a 1
                </NuxtLink>
              </div>
            </div>
          </li>

          <li>
            <NuxtLink
              to="/galeria"
              :class="[
                'px-3 py-1.5 rounded-full text-sm font-medium transition-all focus-visible:outline-2 focus-visible:outline-gold',
                isActive('/galeria') ? 'text-gold bg-gold/10 font-semibold' : 'text-stone-200 hover:text-gold hover:bg-white/5',
              ]"
            >
              Galería
            </NuxtLink>
          </li>

          <li>
            <NuxtLink
              to="/verificar-certificado"
              :class="[
                'px-3 py-1.5 rounded-full text-sm font-medium transition-all focus-visible:outline-2 focus-visible:outline-gold',
                isActive('/verificar-certificado') ? 'text-gold bg-gold/10 font-semibold' : 'text-stone-200 hover:text-gold hover:bg-white/5',
              ]"
            >
              Certificados
            </NuxtLink>
          </li>

          <li>
            <NuxtLink
              to="/contactos"
              :class="[
                'px-3 py-1.5 rounded-full text-sm font-medium transition-all focus-visible:outline-2 focus-visible:outline-gold',
                isActive('/contactos') ? 'text-gold bg-gold/10 font-semibold' : 'text-stone-200 hover:text-gold hover:bg-white/5',
              ]"
            >
              Contáctanos
            </NuxtLink>
          </li>
        </ul>

        <div class="hidden lg:flex items-center gap-2.5">
          <a
            :href="CONTACT_INFO.socials.whatsapp"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 rounded-full border border-gold/40 bg-gold/10 px-4 py-1.5 text-xs font-semibold text-gold hover:bg-gold hover:text-[#0A2540] transition-colors focus-visible:outline-2 focus-visible:outline-gold"
          >
            <PhoneCall class="h-3.5 w-3.5" aria-hidden="true" />
            <span>WhatsApp</span>
          </a>
          <NuxtLink
            :to="panelHref"
            class="inline-flex items-center gap-2 rounded-full bg-gold px-4 py-1.5 text-xs font-bold text-[#0A2540] hover:bg-gold-light transition-colors focus-visible:outline-2 focus-visible:outline-white"
          >
            <LogIn class="h-3.5 w-3.5" aria-hidden="true" />
            <span>{{ session.user ? 'Ir al panel' : 'Aula / Panel' }}</span>
          </NuxtLink>
        </div>

        <button
          type="button"
          class="flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white lg:hidden hover:bg-white/20 transition-colors cursor-pointer"
          :aria-expanded="mobileOpen"
          :aria-label="mobileOpen ? 'Cerrar menú de navegación' : 'Abrir menú de navegación'"
          @click="mobileOpen = !mobileOpen"
        >
          <X v-if="mobileOpen" class="h-5 w-5" />
          <Menu v-else class="h-5 w-5" />
        </button>
      </nav>

      <div
        v-if="mobileOpen"
        class="lg:hidden mt-2 rounded-3xl border border-white/15 bg-[#0A1929]/95 backdrop-blur-2xl p-5 text-sm text-white shadow-2xl space-y-1.5"
      >
        <NuxtLink to="/" :class="['block rounded-xl px-4 py-2 font-medium transition-colors', isActive('/') ? 'text-gold bg-gold/10 font-semibold' : 'text-stone-200 hover:bg-white/5']">Inicio</NuxtLink>
        <NuxtLink to="/quienes-somos" :class="['block rounded-xl px-4 py-2 font-medium transition-colors', isActive('/quienes-somos') ? 'text-gold bg-gold/10 font-semibold' : 'text-stone-200 hover:bg-white/5']">Quiénes Somos</NuxtLink>
        <div class="pt-2 pb-1 border-t border-white/10">
          <span class="px-4 text-[11px] font-semibold uppercase tracking-wider text-gold">Educación</span>
          <div class="mt-1 space-y-1 pl-2">
            <NuxtLink to="/educacion/asesoramiento" class="block rounded-lg px-3 py-1.5 text-xs text-stone-200 hover:text-gold hover:bg-white/5">Asesoramiento 1 a 1</NuxtLink>
            <NuxtLink to="/educacion/capacitaciones" class="block rounded-lg px-3 py-1.5 text-xs text-stone-200 hover:text-gold hover:bg-white/5">Capacitaciones</NuxtLink>
            <NuxtLink to="/educacion/cursos" class="block rounded-lg px-3 py-1.5 text-xs text-stone-200 hover:text-gold hover:bg-white/5">Cursos</NuxtLink>
          </div>
        </div>
        <div class="pt-2 border-t border-white/10 space-y-1">
          <NuxtLink to="/galeria" :class="['block rounded-xl px-4 py-2 font-medium transition-colors', isActive('/galeria') ? 'text-gold bg-gold/10 font-semibold' : 'text-stone-200 hover:bg-white/5']">Galería</NuxtLink>
          <NuxtLink to="/verificar-certificado" :class="['block rounded-xl px-4 py-2 font-medium transition-colors', isActive('/verificar-certificado') ? 'text-gold bg-gold/10 font-semibold' : 'text-stone-200 hover:bg-white/5']">Verificar Certificado</NuxtLink>
          <NuxtLink to="/contactos" :class="['block rounded-xl px-4 py-2 font-medium transition-colors', isActive('/contactos') ? 'text-gold bg-gold/10 font-semibold' : 'text-stone-200 hover:bg-white/5']">Contáctanos</NuxtLink>
        </div>
        <div class="pt-3 grid gap-2">
          <a
            :href="CONTACT_INFO.socials.whatsapp"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center justify-center gap-2 rounded-xl border border-gold/40 bg-gold/10 py-2.5 text-center text-sm font-semibold text-gold"
          >
            <PhoneCall class="h-4 w-4" aria-hidden="true" />
            <span>Escribir por WhatsApp</span>
          </a>
          <NuxtLink :to="panelHref" class="block rounded-xl bg-gradient-to-r from-gold to-gold-dark py-2.5 text-center text-sm font-bold text-[#0A1929]">
            {{ session.user ? 'Ir al panel' : 'Aula / Iniciar sesión' }}
          </NuxtLink>
        </div>
      </div>
    </div>
  </header>
</template>
