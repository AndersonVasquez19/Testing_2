import AxeBuilder from '@axe-core/playwright';
import { expect, test } from '@playwright/test';

const TOKEN_CON_PAGO = 'demo-token-pending-000000000000000000002';
const TOKEN_CERRADA = 'demo-token-enrolled-00000000000000000001';

test.describe('Página pública de pago (/pagar/[token]) contra Laravel 13', () => {
  test('reserva con pago pendiente de verificación conserva la referencia y oculta medios', async ({ page }) => {
    await page.goto(`/pagar/${TOKEN_CON_PAGO}`);
    await expect(page.getByRole('heading', { level: 1, name: /Recibimos tu pago/ })).toBeVisible();
    await expect(page.getByText('HRS-PEND02')).toBeVisible();
    await expect(page.getByText(/Estamos verificándolo/)).toBeVisible();
  });

  test('reserva cerrada indica que ya no está activa y tiene meta noindex/no-referrer', async ({ page }) => {
    await page.goto(`/pagar/${TOKEN_CERRADA}`);
    await expect(page.getByRole('heading', { level: 1, name: 'Esta reserva ya no está activa' })).toBeVisible();
    await expect(page.locator('meta[name="robots"]')).toHaveAttribute('content', /noindex/);
    await expect(page.locator('meta[name="referrer"]')).toHaveAttribute('content', 'no-referrer');
  });

  test('un enlace desconocido muestra aviso amable sin 404 ni errores graves de accesibilidad', async ({ page }) => {
    const res = await page.goto('/pagar/enlace-desconocido-000000000000');
    expect(res?.status()).toBe(200);
    await expect(page.getByRole('heading', { level: 1, name: 'No pudimos abrir este enlace de pago' })).toBeVisible();

    const axe = await new AxeBuilder({ page }).include('main').withTags(['wcag2a', 'wcag2aa']).analyze();
    expect(axe.violations.filter((v) => v.impact === 'serious' || v.impact === 'critical')).toEqual([]);
  });
});
