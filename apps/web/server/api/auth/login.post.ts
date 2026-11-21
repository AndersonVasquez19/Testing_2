import type { H3Event } from 'h3';

export default defineEventHandler(async (event: H3Event) => {
  const config = useRuntimeConfig();
  const body = await readBody(event);

  const response = await fetch(`${config.apiBaseUrl.replace(/\/+$/, '')}/auth/login`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    },
    body: JSON.stringify(body),
  });

  const data = await response.json().catch(() => ({ message: 'Error de autenticación.' }));

  if (!response.ok) {
    setResponseStatus(event, response.status);
    return data;
  }

  setCookie(event, 'horus_token', data.token, {
    httpOnly: true,
    sameSite: 'lax',
    path: '/',
    secure: process.env.NODE_ENV === 'production',
    maxAge: 60 * 60 * 24 * 7,
  });

  return { user: data.user };
});
