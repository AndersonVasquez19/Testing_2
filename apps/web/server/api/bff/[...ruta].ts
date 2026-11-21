import type { H3Event } from 'h3';
import { buildUpstreamUrl } from '../../utils/proxy';

export default defineEventHandler(async (event: H3Event) => {
  const config = useRuntimeConfig();
  const routeParam = getRouterParam(event, 'ruta') || '';
  const query = getQuery(event);
  const url = buildUpstreamUrl(config.apiBaseUrl, routeParam, query);

  const token = getCookie(event, 'horus_token');
  const incomingHeaders = getRequestHeaders(event);
  const headers: Record<string, string> = {
    Accept: 'application/json',
  };

  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  const contentType = incomingHeaders['content-type'];
  if (contentType) {
    headers['Content-Type'] = contentType;
  }

  const method = event.method.toUpperCase();
  let body: BodyInit | undefined;
  if (!['GET', 'HEAD'].includes(method)) {
    const raw = await readRawBody(event, false);
    if (raw) {
      body = raw as unknown as BodyInit;
    }
  }

  const response = await fetch(url, {
    method,
    headers,
    body,
  });

  setResponseStatus(event, response.status);

  const resContentType = response.headers.get('content-type');
  if (resContentType) {
    setHeader(event, 'content-type', resContentType);
  }
  const disposition = response.headers.get('content-disposition');
  if (disposition) {
    setHeader(event, 'content-disposition', disposition);
  }

  return Buffer.from(await response.arrayBuffer());
});
