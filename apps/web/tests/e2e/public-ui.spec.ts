import AxeBuilder from '@axe-core/playwright';
import { expect, test } from '@playwright/test';

test.describe('Secciones públicas completas y accesibilidad', () => {
  test('Inicio: hero stats y carrusel de convenios con modal funcionan', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('networkidle');

    await expect(page.getByText('+400')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Convenios estratégicos' })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Pausar rotación automática' })).toBeVisible();

    await page.getByRole('button', { name: /Ver convenio/i }).first().click();
    const dialog = page.getByRole('dialog');
    await expect(dialog).toBeVisible();
    await expect(dialog.getByText('Beneficios para participantes')).toBeVisible();
    await page.getByRole('button', { name: 'Entendido' }).click();
    await expect(dialog).toBeHidden();
  });

  test('Quiénes Somos: muestra Misión/Visión/Valores, equipo y acordeón', async ({ page }) => {
    await page.goto('/quienes-somos');
    await page.waitForLoadState('networkidle');

    await expect(page.getByRole('heading', { name: 'Misión, Visión y Valores' })).toBeVisible();
    await expect(page.getByText('MARCO ALVAREZ')).toBeVisible();

    const marcoCard = page.locator('article').filter({ hasText: 'MARCO ALVAREZ' });
    const toggleBtn = marcoCard.getByRole('button', { name: /Conoce más|Ocultar información/i });
    await expect(toggleBtn).toHaveAttribute('aria-expanded', 'false');
    await toggleBtn.click();
    await expect(toggleBtn).toHaveAttribute('aria-expanded', 'true');
    await expect(marcoCard.getByText(/Profesional de Economía/i)).toBeVisible();

    const a11y = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
    expect(a11y.violations.filter((v) => v.impact === 'serious' || v.impact === 'critical')).toEqual([]);
  });

  test('Educación: Cursos (modalidades y detalle), Capacitaciones y Asesoramiento sin fallos A11y', async ({ page }) => {
    await page.goto('/educacion/cursos');
    await page.waitForLoadState('networkidle');
    await expect(page.getByRole('heading', { name: 'MODALIDADES DE CURSOS' })).toBeVisible();

    await page.goto('/educacion/capacitaciones');
    await page.waitForLoadState('networkidle');
    await expect(page.getByRole('heading', { name: 'CAPACITACIONES', exact: true })).toBeVisible();
    const a11yCap = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
    expect(a11yCap.violations.filter((v) => v.impact === 'serious' || v.impact === 'critical')).toEqual([]);

    await page.goto('/educacion/asesoramiento');
    await page.waitForLoadState('networkidle');
    await expect(page.getByRole('heading', { name: 'Compromiso y Respaldo Profesional' })).toBeVisible();
    const a11yAdv = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
    expect(a11yAdv.violations.filter((v) => v.impact === 'serious' || v.impact === 'critical')).toEqual([]);
  });

  test('Galería: filtros con conteo y lightbox accesible', async ({ page }) => {
    await page.goto('/galeria');
    await page.waitForLoadState('networkidle');

    const expandBtn = page.getByRole('button', { name: /Ampliar foto:/i }).first();
    await expandBtn.click();
    const dialog = page.getByRole('dialog');
    await expect(dialog).toBeVisible();
    await page.getByRole('button', { name: 'Cerrar galería ampliada' }).click();
    await expect(dialog).toBeHidden();

    const a11yGal = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
    expect(a11yGal.violations.filter((v) => v.impact === 'serious' || v.impact === 'critical')).toEqual([]);
  });
});
