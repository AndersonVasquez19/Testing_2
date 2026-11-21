import AxeBuilder from '@axe-core/playwright';
import { expect, test, type Page } from '@playwright/test';

/**
 * Botón flotante de WhatsApp del sitio público (sustituye al asistente virtual). Abre una tarjeta con tres motivos que arman un
 * enlace `wa.me` con la ruta de la página, y enlaza al contacto formal y al Libro de Reclamaciones. No afirma disponibilidad.
 */
const RUTA = '/legales/politicas-privacidad';

/** Con `next dev` el HTML llega antes que el JavaScript: un clic dado antes de hidratar se pierde sin error. */
async function abrirHidratada(page: Page) {
  await page.goto(RUTA);
  const boton = page.getByRole('button', { name: 'Escríbenos por WhatsApp' });
  await expect(boton).toBeVisible();
  await page.waitForFunction(() => {
    const nodo = document.querySelector('button[aria-label="Escríbenos por WhatsApp"]');
    return nodo !== null && Object.keys(nodo).some((clave) => clave.startsWith('__reactFiber'));
  });
  return boton;
}

test.describe('Botón flotante de WhatsApp', () => {
  test('abre la tarjeta con los tres motivos y los enlaces formales', async ({ page }) => {
    const boton = await abrirHidratada(page);
    await expect(boton).toHaveAttribute('aria-expanded', 'false');

    await boton.click();
    const tarjeta = page.getByRole('region', { name: 'Escríbenos por WhatsApp' });
    await expect(tarjeta).toBeVisible();
    await expect(page.getByRole('button', { name: 'Cerrar el menú de WhatsApp' })).toHaveAttribute('aria-expanded', 'true');

    for (const motivo of ['Matrícula e información de cursos', 'Pagos, cuotas y certificados', 'Atención al cliente']) {
      await expect(tarjeta.getByRole('link', { name: new RegExp(motivo) })).toBeVisible();
    }
    await expect(tarjeta.getByRole('link', { name: 'Contacto' })).toHaveAttribute('href', '/contactos');
    await expect(tarjeta.getByRole('link', { name: 'Libro de Reclamaciones' })).toHaveAttribute('href', '/legales/libro-reclamaciones');
    // No afirma que haya alguien «en línea»: el horario se publica con valores distintos.
    await expect(tarjeta).not.toContainText(/en l[ií]nea|disponible ahora/i);
  });

  test('cada motivo abre WhatsApp con el mensaje y la ruta de origen, en otra pestaña y sin Referer', async ({ page }) => {
    const boton = await abrirHidratada(page);
    await boton.click();

    const enlace = page.getByRole('link', { name: /Pagos, cuotas y certificados/ });
    const href = (await enlace.getAttribute('href')) ?? '';
    expect(href).toMatch(/^https:\/\/wa\.me\/51972021374\?text=/);
    const texto = decodeURIComponent(href.split('?text=')[1] ?? '');
    expect(texto).toContain('consulta sobre pagos, cuotas o certificados');
    expect(texto).toContain(`Escribo desde: ${RUTA}`);
    expect(await enlace.getAttribute('target')).toBe('_blank');
    const rel = (await enlace.getAttribute('rel')) ?? '';
    expect(rel).toContain('noopener');
    expect(rel).toContain('noreferrer');
  });

  test('Escape cierra la tarjeta y el clic fuera también', async ({ page }) => {
    const boton = await abrirHidratada(page);
    const tarjeta = page.getByRole('region', { name: 'Escríbenos por WhatsApp' });

    await boton.click();
    await expect(tarjeta).toBeVisible();
    await page.keyboard.press('Escape');
    await expect(tarjeta).toBeHidden();

    await page.getByRole('button', { name: 'Escríbenos por WhatsApp' }).click();
    await expect(tarjeta).toBeVisible();
    await page.mouse.click(5, 300);
    await expect(tarjeta).toBeHidden();
  });

  test('con la tarjeta abierta: sin violaciones graves de accesibilidad (contraste incluido)', async ({ page }) => {
    const boton = await abrirHidratada(page);
    await boton.click();
    await expect(page.getByRole('region', { name: 'Escríbenos por WhatsApp' })).toBeVisible();

    const resultado = await new AxeBuilder({ page }).include('.fixed').withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
    const graves = resultado.violations
      .filter((v) => v.impact === 'serious' || v.impact === 'critical')
      .map((v) => `${v.id}: ${v.help} → ${v.nodes.slice(0, 3).map((n) => n.target.join(' ')).join(' ;; ')}`);
    expect(graves).toEqual([]);
  });
});
