import { defineConfig, devices } from '@playwright/test';

const WEB = 'http://localhost:3100';
const API = 'http://127.0.0.1:8000';

export default defineConfig({
  testDir: './tests/e2e',
  testMatch: '**/*.spec.ts',
  outputDir: './test-results',
  timeout: 60_000,
  expect: { timeout: 10_000 },
  workers: 1,
  retries: 0,
  reporter: [['list']],
  use: {
    baseURL: WEB,
    locale: 'es-PE',
    timezoneId: 'America/Lima',
    trace: 'retain-on-failure',
  },
  projects: [
    { name: 'escritorio', use: { ...devices['Desktop Chrome'], viewport: { width: 1280, height: 800 } } },
    { name: 'movil', use: { ...devices['Desktop Chrome'], viewport: { width: 360, height: 740 }, isMobile: true, hasTouch: true } },
  ],
  webServer: [
    {
      command: 'php ../api/artisan serve --host=127.0.0.1 --port=8000',
      url: `${API}/up`,
      reuseExistingServer: true,
      timeout: 30_000,
    },
    {
      command: 'npx nuxt dev --port 3100',
      url: `${WEB}/login`,
      reuseExistingServer: true,
      timeout: 180_000,
      env: {
        HORUS_API_URL: `${API}/api/v1`,
        NUXT_PUBLIC_WHATSAPP_NUMBER: '51972021374',
      },
    },
  ],
});
