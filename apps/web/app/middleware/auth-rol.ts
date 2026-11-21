import { panelPathForRole, useSessionStore } from '~/stores/session';
import type { UserRole } from '~/types/horus';

export default defineNuxtRouteMiddleware(async (to) => {
  const session = useSessionStore();
  const user = await session.fetchSession();

  if (!user) {
    return navigateTo({
      path: '/login',
      query: { redirect: to.fullPath },
    });
  }

  const allowedRoles = to.meta.roles as UserRole[] | undefined;
  if (allowedRoles && allowedRoles.length > 0 && !allowedRoles.includes(user.role)) {
    return navigateTo(panelPathForRole(user.role));
  }
});
