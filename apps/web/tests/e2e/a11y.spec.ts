import AxeBuilder from '@axe-core/playwright';
import { expect, test } from '@playwright/test';

test.describe('Accesibilidad WCAG 2.1 AA', () => {
  const publicRoutes = [
    ['Inicio', '/'],
    ['Cursos', '/educacion/cursos'],
    ['Contáctanos', '/contactos'],
    ['Libro de Reclamaciones', '/legales/libro-reclamaciones'],
    ['Inicia sesión', '/login'],
  ] as const;

  for (const [name, path] of publicRoutes) {
    test(`${name}: sin violaciones graves`, async ({ page }) => {
      await page.goto(path);
      await page.waitForLoadState('networkidle');
      const result = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
      const serious = result.violations.filter((v) => v.impact === 'serious' || v.impact === 'critical');
      expect(serious).toEqual([]);
    });
  }

  test('Panel Admin: sin violaciones graves', async ({ page }) => {
    await page.goto('/login');
    await page.waitForLoadState('networkidle');
    await page.locator('#email').fill('admin@horus.pe');
    await page.locator('#password').fill('Horus2026!');
    await page.getByRole('button', { name: 'Ingresar' }).click();
    await expect(page).toHaveURL(/\/panel\/admin/);
    await page.waitForLoadState('networkidle');

    const result = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
    const serious = result.violations.filter((v) => v.impact === 'serious' || v.impact === 'critical');
    expect(serious).toEqual([]);
  });
});
