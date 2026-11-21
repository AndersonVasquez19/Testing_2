import type { H3Event } from 'h3';

export default defineEventHandler(async (event: H3Event) => {
  const config = useRuntimeConfig();
  const token = getCookie(event, 'horus_token');

  if (token) {
    await fetch(`${config.apiBaseUrl.replace(/\/+$/, '')}/auth/logout`, {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
      },
    }).catch(() => null);
  }

  deleteCookie(event, 'horus_token', { path: '/' });
  return { ok: true };
});
