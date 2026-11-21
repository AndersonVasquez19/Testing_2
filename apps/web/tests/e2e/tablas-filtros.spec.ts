import { expect, test } from '@playwright/test';

test.describe('Tablas, filtros y flujo de Secretaría contra Laravel 13', () => {
  test('Secretaría inicia sesión, filtra la Cola de WhatsApp y visualiza alumnos validados', async ({ page }) => {
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.locator('#email').fill('secretaria@horus.pe');
    await page.locator('#password').fill('Horus2026!');
    await page.getByRole('button', { name: 'Ingresar' }).click();

    await expect(page).toHaveURL(/\/panel\/secretaria/);
    await expect(page.getByRole('heading', { name: 'Bandeja Operativa de Secretaría' })).toBeVisible();

    await page.goto('/panel/secretaria/whatsapp');
    await expect(page.getByRole('heading', { name: 'Cola de Incorporación a WhatsApp' })).toBeVisible();
    await expect(page.getByText('María Torres Díaz')).toBeVisible();
  });
});
