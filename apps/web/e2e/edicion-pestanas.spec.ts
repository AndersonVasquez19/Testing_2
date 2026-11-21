import AxeBuilder from '@axe-core/playwright';
import { expect, test } from '@playwright/test';
import { abrir } from './ayudas';

/**
 * Ficha de una edición con pestañas (Radix Tabs): Resumen, Inscritos, Sesiones y asistencia, Evaluaciones y acta.
 * El Laravel falso solo simula el detalle de la edición; el resto de secciones tolera su 404.
 */
const RUTA = '/admin/academico/convocatorias/11111111-2222-4333-8444-555555555555';

test.describe('Edición: pestañas', () => {
  test('se abre en «Resumen» y solo ese panel es visible', async ({ page }) => {
    await abrir(page, RUTA);

    const lista = page.getByRole('tablist', { name: 'Secciones de la edición' });
    await expect(lista.getByRole('tab')).toHaveText([/Resumen/, /Inscritos\s*2/, /Sesiones y asistencia/, /Evaluaciones y acta/]);
    await expect(lista.getByRole('tab', { name: 'Resumen' })).toHaveAttribute('aria-selected', 'true');
    await expect(page.getByRole('heading', { name: 'Datos' })).toBeVisible();
    await expect(page.getByText('Luis Torres Díaz')).toBeHidden();
  });

  test('un clic cambia de pestaña y refleja la elegida en la URL sin recargar', async ({ page }) => {
    await abrir(page, RUTA);

    await page.getByRole('tab', { name: /Inscritos/ }).click();
    await expect(page.getByText('Luis Torres Díaz')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Datos' })).toBeHidden();
    await expect(page).toHaveURL(/[?&]pestana=inscritos/);

    // Volver a la primera pestaña limpia el parámetro: es el estado por defecto.
    await page.getByRole('tab', { name: 'Resumen' }).click();
    await expect(page).not.toHaveURL(/pestana=/);
  });

  test('las flechas del teclado mueven el foco y activan la pestaña contigua', async ({ page }) => {
    await abrir(page, RUTA);

    await page.getByRole('tab', { name: 'Resumen' }).focus();
    await page.keyboard.press('ArrowRight');
    await expect(page.getByRole('tab', { name: /Inscritos/ })).toBeFocused();
    await expect(page.getByRole('tab', { name: /Inscritos/ })).toHaveAttribute('aria-selected', 'true');

    await page.keyboard.press('End');
    await expect(page.getByRole('tab', { name: 'Evaluaciones y acta' })).toBeFocused();
  });

  test('el enlace con ?pestana= abre directamente esa pestaña; un valor inválido cae en «Resumen»', async ({ page }) => {
    await abrir(page, `${RUTA}?pestana=inscritos`);
    await expect(page.getByRole('tab', { name: /Inscritos/ })).toHaveAttribute('aria-selected', 'true');
    await expect(page.getByText('Ana Quispe Rojas')).toBeVisible();

    await abrir(page, `${RUTA}?pestana=no-existe`);
    await expect(page.getByRole('tab', { name: 'Resumen' })).toHaveAttribute('aria-selected', 'true');
  });

  test('sin violaciones graves de accesibilidad (pestaña «Inscritos» abierta)', async ({ page }) => {
    await abrir(page, `${RUTA}?pestana=inscritos`);
    const resultado = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
    const graves = resultado.violations.filter((v) => v.impact === 'serious' || v.impact === 'critical').map((v) => `${v.id}: ${v.help}`);
    expect(graves).toEqual([]);
  });
});
