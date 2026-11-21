import { expect, test } from '@playwright/test';
import { abrir } from './ayudas';

/**
 * Tablero «Hoy» (inicio del panel): lo que requiere atención hoy. Aquí se prueba la tarjeta de reclamos con plazo cercano
 * (Libro de Reclamaciones), que solo llega del servidor a quien tiene `reclamaciones.ver` y solo como cifras.
 */
test.describe('Tablero: requiere tu atención', () => {
  test('muestra los reclamos con plazo cercano con el desglose por urgencia', async ({ page }) => {
    await abrir(page, '/admin/academico');

    const seccion = page.getByRole('region', { name: 'Requiere tu atención' });
    await expect(seccion).toBeVisible();
    const tarjeta = seccion.getByRole('link', { name: /Reclamos con plazo cercano/ });
    await expect(tarjeta).toBeVisible();
    await expect(tarjeta).toContainText('3');
    await expect(tarjeta).toContainText('1 vencidos · 0 vencen hoy · 2 por vencer.');
    // Las demás tarjetas de acción siguen ahí.
    await expect(seccion.getByRole('link', { name: /Pagos por confirmar/ })).toBeVisible();
  });

  test('la tarjeta lleva directo a lo más urgente (los vencidos)', async ({ page }) => {
    await abrir(page, '/admin/academico');

    const tarjeta = page.getByRole('link', { name: /Reclamos con plazo cercano/ });
    await expect(tarjeta).toHaveAttribute('href', '/admin/academico/reclamaciones?plazo=vencidas');
    await tarjeta.click();
    await expect(page).toHaveURL(/\/admin\/academico\/reclamaciones\?plazo=vencidas/);
  });
});
