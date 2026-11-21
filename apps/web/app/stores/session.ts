import { defineStore } from 'pinia';
import type { AuthUser, UserRole } from '~/types/horus';

export function panelPathForRole(role: UserRole): string {
  switch (role) {
    case 'admin':
      return '/panel/admin';
    case 'secretary':
      return '/panel/secretaria';
    case 'coordinator':
      return '/panel/coordinador';
    case 'student':
      return '/panel/estudiante';
  }
}

export const useSessionStore = defineStore('session', {
  state: () => ({
    user: null as AuthUser | null,
    loaded: false,
    loading: false,
  }),

  getters: {
    isAuthenticated: (state) => state.user !== null,
    homePath: (state) => (state.user ? panelPathForRole(state.user.role) : '/login'),
  },

  actions: {
    async fetchSession(force = false): Promise<AuthUser | null> {
      if (this.loaded && !force) return this.user;
      this.loading = true;
      try {
        const res = await $fetch<{ user: AuthUser }>('/api/auth/me');
        this.user = res.user;
      } catch {
        this.user = null;
      } finally {
        this.loaded = true;
        this.loading = false;
      }
      return this.user;
    },

    async login(email: string, password: string): Promise<AuthUser> {
      const res = await $fetch<{ user: AuthUser }>('/api/auth/login', {
        method: 'POST',
        body: { email, password },
      });
      this.user = res.user;
      this.loaded = true;
      return res.user;
    },

    async register(payload: {
      name: string;
      email: string;
      dni?: string;
      phone?: string;
      password: string;
      password_confirmation: string;
    }): Promise<AuthUser> {
      const res = await $fetch<{ user: AuthUser }>('/api/auth/register', {
        method: 'POST',
        body: payload,
      });
      this.user = res.user;
      this.loaded = true;
      return res.user;
    },

    async logout(): Promise<void> {
      try {
        await $fetch('/api/auth/logout', { method: 'POST' });
      } finally {
        this.user = null;
        this.loaded = true;
      }
    },
  },
});
