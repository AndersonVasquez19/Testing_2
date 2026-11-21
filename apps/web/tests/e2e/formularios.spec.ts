import { expect, test } from '@playwright/test';

test.describe('Formularios públicos y de acceso contra Laravel 13', () => {
  test('el formulario de registro alterna mostrar/ocultar contraseña con aria-pressed', async ({ page }) => {
    await page.goto('/registro');
    await page.waitForLoadState('networkidle');
    const campo = page.locator('input[name="password"]');
    await expect(campo).toHaveAttribute('type', 'password');

    await page.getByRole('button', { name: 'Mostrar contraseña' }).click();
    await expect(campo).toHaveAttribute('type', 'text');
    await expect(page.getByRole('button', { name: 'Ocultar contraseña' })).toHaveAttribute('aria-pressed', 'true');
  });

  test('el formulario de contacto registra un lead real en PostgreSQL', async ({ page }) => {
    await page.goto('/contactos');
    await page.waitForLoadState('networkidle');
    await page.locator('#full_name').fill('Carlos E2E');
    await page.locator('#phone').fill('+51977889900');
    await page.locator('#email').fill('carlos.e2e@horus.pe');
    await page.locator('#subject').fill('Consulta E2E');
    await page.locator('#message').fill('Mensaje de prueba E2E contra Laravel 13.');
    await page.getByRole('button', { name: 'Enviar consulta' }).click();

    await expect(page.getByText(/Mensaje enviado correctamente/)).toBeVisible();
  });
});
