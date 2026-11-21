export const TOKEN_COOKIE = 'horus_token';

export function buildUpstreamUrl(baseUrl: string, path: string, query?: Record<string, unknown>): string {
  const cleanBase = baseUrl.replace(/\/+$/, '');
  const cleanPath = path.replace(/^\/+/, '');
  const url = new URL(`${cleanBase}/${cleanPath}`);
  if (query) {
    for (const [k, v] of Object.entries(query)) {
      if (v === undefined || v === null || v === '') continue;
      if (Array.isArray(v)) {
        for (const item of v) url.searchParams.append(k, String(item));
      } else {
        url.searchParams.append(k, String(v));
      }
    }
  }
  return url.toString();
}
