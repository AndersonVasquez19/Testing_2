import { expect, test } from '@playwright/test';

test.describe('Menús de navegación pública y del panel', () => {
  test('el menú de Educación en escritorio despliega sus 3 rutas reales', async ({ page }, info) => {
    test.skip(info.project.name === 'movil', 'Menú de escritorio');
    await page.goto('/');
    await page.waitForLoadState('networkidle');

    const nav = page.getByRole('navigation', { name: 'Navegación principal' });
    const btn = nav.getByRole('button', { name: 'Educación' });
    await btn.click();
    await expect(btn).toHaveAttribute('aria-expanded', 'true');
    await expect(nav.getByRole('link', { name: 'Cursos', exact: true })).toHaveAttribute('href', '/educacion/cursos');
    await expect(nav.getByRole('link', { name: 'Capacitaciones', exact: true })).toHaveAttribute('href', '/educacion/capacitaciones');
    await expect(nav.getByRole('link', { name: 'Asesoramiento 1 a 1', exact: true })).toHaveAttribute('href', '/educacion/asesoramiento');
  });

  test('en móvil el menú hamburguesa abre la navegación completa', async ({ page }, info) => {
    test.skip(info.project.name !== 'movil', 'Solo móvil');
    await page.goto('/');
    await page.waitForLoadState('networkidle');
    await page.getByRole('button', { name: 'Abrir menú de navegación' }).click();
    await expect(page.getByRole('link', { name: 'Galería' }).first()).toBeVisible();
  });
});
