<script setup lang="ts">
import { ref, computed } from 'vue';
import {
  LayoutDashboard,
  Users,
  BookOpen,
  CreditCard,
  MessageSquare,
  Calendar,
  Award,
  FileWarning,
  ShieldCheck,
  LogOut,
  Menu,
  X,
  GraduationCap,
} from 'lucide-vue-next';
import { useSessionStore } from '~/stores/session';

const route = useRoute();
const router = useRouter();
const session = useSessionStore();
const sidebarOpen = ref(false);

interface NavLink {
  label: string;
  to: string;
  icon: unknown;
}

const navItems = computed<NavLink[]>(() => {
  const role = session.user?.role;
  if (role === 'admin') {
    return [
      { label: 'Resumen Ejecutivo', to: '/panel/admin', icon: LayoutDashboard },
      { label: 'Usuarios y Roles', to: '/panel/admin/usuarios', icon: Users },
      { label: 'Catálogo de Programas', to: '/panel/admin/programas', icon: BookOpen },
      { label: 'Libro de Reclamaciones', to: '/panel/admin/reclamaciones', icon: FileWarning },
      { label: 'Certificados y Auditoría', to: '/panel/admin/auditoria', icon: ShieldCheck },
      { label: 'Secretaría · Pagos y WA', to: '/panel/secretaria', icon: CreditCard },
      { label: 'Coordinación · Sesiones', to: '/panel/coordinador', icon: Calendar },
    ];
  }
  if (role === 'secretary') {
    return [
      { label: 'Bandeja Secretaría', to: '/panel/secretaria', icon: LayoutDashboard },
      { label: 'Validación de Pagos', to: '/panel/secretaria/pagos', icon: CreditCard },
      { label: 'Cola de WhatsApp', to: '/panel/secretaria/whatsapp', icon: MessageSquare },
      { label: 'Leads y Matrículas', to: '/panel/secretaria/matriculas', icon: Users },
    ];
  }
  if (role === 'coordinator') {
    return [
      { label: 'Coordinación Académica', to: '/panel/coordinador', icon: LayoutDashboard },
      { label: 'Convocatorias y Sesiones', to: '/panel/coordinador/sesiones', icon: Calendar },
      { label: 'Asistencia y Certificados', to: '/panel/coordinador/academico', icon: Award },
      { label: 'Asesorías 1 a 1', to: '/panel/coordinador/asesorias', icon: BookOpen },
    ];
  }
  return [
    { label: 'Mi Aula Virtual', to: '/panel/estudiante', icon: GraduationCap },
    { label: 'Asesorías 1 a 1', to: '/panel/estudiante/asesorias', icon: Calendar },
    { label: 'Mis Certificados', to: '/panel/estudiante/certificados', icon: Award },
  ];
});

const handleLogout = async () => {
  await session.logout();
  await router.push('/login');
};
</script>

<template>
  <div class="min-h-screen bg-[#EEF2F7] text-slate-900 flex">
    <aside
      :class="[
        'fixed inset-y-0 left-0 z-40 w-68 bg-[#0A2540] text-white flex flex-col transition-transform duration-200 lg:static lg:translate-x-0',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
      ]"
    >
      <div class="flex items-center justify-between px-5 py-5 border-b border-white/10">
        <NuxtLink to="/" class="flex items-center gap-3">
          <img src="/images/index/logos/logo-horus.png" alt="Horus" class="h-9 w-9 object-contain">
          <div>
            <span class="block text-sm font-bold text-white">Horus Academic</span>
            <span class="block text-[11px] uppercase tracking-wider text-gold">
              {{ session.user?.role ?? 'Panel' }}
            </span>
          </div>
        </NuxtLink>
        <button
          type="button"
          class="lg:hidden rounded-lg p-1.5 text-white/80 hover:bg-white/10"
          aria-label="Cerrar menú lateral"
          @click="sidebarOpen = false"
        >
          <X class="h-5 w-5" />
        </button>
      </div>

      <nav aria-label="Menú del panel" class="flex-1 p-4 space-y-1.5 overflow-y-auto">
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          :class="[
            'flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition-colors',
            route.path === item.to
              ? 'bg-gold text-[#0A2540] font-semibold'
              : 'text-stone-200 hover:bg-white/10 hover:text-white',
          ]"
          @click="sidebarOpen = false"
        >
          <component :is="item.icon" class="h-4 w-4 shrink-0" aria-hidden="true" />
          <span>{{ item.label }}</span>
        </NuxtLink>
      </nav>

      <div class="p-4 border-t border-white/10 space-y-3">
        <div class="rounded-xl bg-white/5 px-3.5 py-2.5 text-xs">
          <p class="font-semibold text-white truncate">{{ session.user?.name }}</p>
          <p class="text-stone-300 truncate">{{ session.user?.email }}</p>
        </div>
        <button
          type="button"
          class="w-full flex items-center justify-center gap-2 rounded-xl border border-white/15 px-3 py-2 text-xs font-semibold text-stone-200 hover:bg-white/10 transition cursor-pointer"
          @click="handleLogout"
        >
          <LogOut class="h-4 w-4" aria-hidden="true" />
          <span>Cerrar sesión</span>
        </button>
      </div>
    </aside>

    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden"
      @click="sidebarOpen = false"
    />

    <div class="flex-1 flex flex-col min-w-0">
      <header class="bg-white border-b border-slate-200 px-4 sm:px-8 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <button
            type="button"
            class="lg:hidden rounded-xl border border-slate-200 p-2 text-slate-700 hover:bg-slate-50"
            aria-label="Abrir menú del panel"
            @click="sidebarOpen = true"
          >
            <Menu class="h-5 w-5" />
          </button>
          <h1 class="text-base sm:text-lg font-bold text-[#0A2540]">
            Panel Operativo Horus Academic
          </h1>
        </div>
        <NuxtLink to="/" class="text-xs font-semibold text-[#1B4469] hover:underline">
          Ver sitio público
        </NuxtLink>
      </header>

      <main id="contenido-panel" class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
        <slot />
      </main>
    </div>
  </div>
</template>
