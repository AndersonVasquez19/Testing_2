import type { H3Event } from 'h3';

export default defineEventHandler(async (event: H3Event) => {
  const config = useRuntimeConfig();
  const token = getCookie(event, 'horus_token');

  if (!token) {
    throw createError({ statusCode: 401, statusMessage: 'No autenticado' });
  }

  const response = await fetch(`${config.apiBaseUrl.replace(/\/+$/, '')}/auth/me`, {
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
  });

  if (!response.ok) {
    deleteCookie(event, 'horus_token', { path: '/' });
    throw createError({ statusCode: response.status, statusMessage: 'Sesión expirada' });
  }

  return await response.json();
});
