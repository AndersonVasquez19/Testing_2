import AxeBuilder from '@axe-core/playwright';
import { expect, test } from '@playwright/test';
import { abrir } from './ayudas';

/**
 * Expediente del estudiante con pestañas: Resumen, Matrículas y Cuenta corriente.
 * El Laravel falso devuelve SIEMPRE la obligación de otra persona junto a las del estudiante 1, para comprobar que la
 * página filtra por el identificador del estudiante y nunca muestra una obligación ajena.
 */
const RUTA = '/admin/academico/estudiantes/1';

test.describe('Estudiante: expediente con pestañas', () => {
  test('se abre en «Resumen» con las tres pestañas y el contador de matrículas', async ({ page }) => {
    await abrir(page, RUTA);

    const lista = page.getByRole('tablist', { name: 'Secciones de la ficha del estudiante' });
    await expect(lista.getByRole('tab')).toHaveText([/Resumen/, /Matrículas\s*1/, /Cuenta corriente/]);
    await expect(lista.getByRole('tab', { name: 'Resumen' })).toHaveAttribute('aria-selected', 'true');
    await expect(page.getByRole('heading', { name: 'Datos personales' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Historial' })).toBeVisible();
    // Los paneles inactivos siguen en el DOM pero ocultos: para el usuario (y los lectores de pantalla) solo hay uno.
    await expect(page.getByRole('tabpanel')).toHaveCount(1);
    await expect(page.getByRole('link', { name: 'Excel avanzado' })).toHaveCount(0);
  });

  test('«Matrículas» muestra la matrícula del estudiante', async ({ page }) => {
    await abrir(page, RUTA);
    await page.getByRole('tab', { name: /Matrículas/ }).click();

    await expect(page.getByRole('link', { name: 'Excel avanzado' })).toBeVisible();
    await expect(page).toHaveURL(/[?&]pestana=matriculas/);
  });

  test('«Cuenta corriente» suma solo lo vigente y nunca muestra obligaciones de otra persona', async ({ page }) => {
    await abrir(page, `${RUTA}?pestana=cuenta`);

    await expect(page.getByRole('tab', { name: 'Cuenta corriente' })).toHaveAttribute('aria-selected', 'true');
    await expect(page.getByRole('link', { name: 'Excel avanzado' })).toBeVisible();
    await expect(page.getByRole('link', { name: 'Taller anulado' })).toBeVisible();
    // La obligación de otra persona llega del servidor pero no se pinta.
    await expect(page.getByText('Curso ajeno')).toHaveCount(0);
    await expect(page.getByText(/999[.,]00/)).toHaveCount(0);

    // Saldo pendiente: solo la obligación vigente (200,00). La anulada (50,00) no suma.
    const saldo = page.getByText(/Saldo pendiente:/);
    await expect(saldo).toContainText(/200[.,]00/);
    await expect(saldo).not.toContainText(/250[.,]00/);

    // Cuota vencida avisada junto al concepto, y la anulada marcada como tal.
    await expect(page.getByText(/Cuota vencida/)).toBeVisible();
    await expect(page.getByRole('row', { name: /Taller anulado/ })).toContainText('Anulada');
  });

  test('sin violaciones graves de accesibilidad (pestaña «Cuenta corriente» abierta)', async ({ page }) => {
    await abrir(page, `${RUTA}?pestana=cuenta`);
    const resultado = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
    const graves = resultado.violations.filter((v) => v.impact === 'serious' || v.impact === 'critical').map((v) => `${v.id}: ${v.help}`);
    expect(graves).toEqual([]);
  });
});
