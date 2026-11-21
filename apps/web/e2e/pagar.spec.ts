import AxeBuilder from '@axe-core/playwright';
import { expect, test } from '@playwright/test';

/**
 * Página pública de pago de una reserva (`/pagar/<enlace>`): instrucciones, importe, referencia y WhatsApp. El Laravel falso
 * devuelve un caso por token de 40 caracteres (a: activa con importe y Yape, b: sin importe ni Yape, c: con pago, d: vencida,
 * e: cerrada); cualquier otro token es 404, como en Laravel.
 */
const token = (letra: string) => `${letra}${'0'.repeat(39)}`;
const ruta = (letra: string) => `/pagar/${token(letra)}`;

test.describe('Página de pago', () => {
  test('reserva activa: muestra el importe, el Yape, la referencia y cuánto falta', async ({ page }) => {
    await page.goto(ruta('a'));

    await expect(page.getByRole('heading', { level: 1, name: /Hola, María: completa tu pago/ })).toBeVisible();
    await expect(page.getByText('Excel avanzado · Edición ED-2026-001')).toBeVisible();
    await expect(page.getByText(/Te quedan 2 días/)).toBeVisible();

    const importe = page.getByRole('region', { name: 'Importe' });
    await expect(importe).toContainText(/Total\s*S\/\s*300[.,]00/);
    await expect(importe).toContainText(/Ya pagado\s*S\/\s*100[.,]00/);
    await expect(importe).toContainText(/Debes pagar\s*S\/\s*200[.,]00/);

    const como = page.getByRole('region', { name: 'Cómo pagar' });
    await expect(como).toContainText('+51 900 111 222');
    await expect(como).toContainText('a nombre de Titular de Prueba');
    await expect(como).toContainText('HRS-7K2M9Q');
  });

  test('copia la referencia y el número de Yape y avisa con un mensaje accesible', async ({ page, context }) => {
    await context.grantPermissions(['clipboard-read', 'clipboard-write']);
    await page.goto(ruta('a'));

    await page.getByRole('button', { name: 'Copiar la referencia' }).click();
    await expect(page.getByRole('status').filter({ hasText: 'la referencia copiado' })).toHaveCount(1);
    expect(await page.evaluate(() => navigator.clipboard.readText())).toBe('HRS-7K2M9Q');

    await page.getByRole('button', { name: 'Copiar el número de Yape' }).click();
    expect(await page.evaluate(() => navigator.clipboard.readText())).toBe('+51 900 111 222');
  });

  test('el botón de WhatsApp lleva la referencia, no filtra el enlace secreto y no pasa el Referer', async ({ page }) => {
    await page.goto(ruta('a'));

    const wa = page.getByRole('link', { name: 'Enviar mi captura por WhatsApp' });
    const href = (await wa.getAttribute('href')) ?? '';
    expect(href).toMatch(/^https:\/\/wa\.me\/51972021374\?text=/);
    expect(decodeURIComponent(href)).toContain('HRS-7K2M9Q');
    expect(href).not.toContain(token('a'));
    expect(await wa.getAttribute('rel')).toMatch(/noreferrer/);
    expect(await wa.getAttribute('target')).toBe('_blank');
  });

  test('no se indexa y no manda el enlace en el encabezado Referer', async ({ page }) => {
    await page.goto(ruta('a'));

    await expect(page.locator('meta[name="robots"]')).toHaveAttribute('content', /noindex/);
    await expect(page.locator('meta[name="referrer"]')).toHaveAttribute('content', 'no-referrer');
  });

  test('sin importe asignado ni Yape configurado lo dice y manda a WhatsApp, sin inventar datos', async ({ page }) => {
    await page.goto(ruta('b'));

    await expect(page.getByText(/Todavía no tienes un importe asignado/)).toBeVisible();
    await expect(page.getByText(/Escríbenos por WhatsApp para indicarte los medios de pago/)).toBeVisible();
    await expect(page.getByRole('button', { name: 'Copiar el número de Yape' })).toHaveCount(0);
    await expect(page.getByText('Debes pagar')).toHaveCount(0);
  });

  test('con pago: avisa que se está verificando y conserva la referencia', async ({ page }) => {
    await page.goto(ruta('c'));

    await expect(page.getByRole('heading', { level: 1, name: /Recibimos tu pago, María/ })).toBeVisible();
    await expect(page.getByText(/Estamos verificándolo/)).toBeVisible();
    await expect(page.getByText('HRS-7K2M9Q')).toBeVisible();
    await expect(page.getByRole('button', { name: /Copiar/ })).toHaveCount(0);
  });

  test('vencida: explica que el cupo se liberó y ofrece volver a reservar por WhatsApp', async ({ page }) => {
    await page.goto(ruta('d'));

    await expect(page.getByRole('heading', { level: 1, name: 'Tu reserva venció' })).toBeVisible();
    await expect(page.getByText(/el cupo se liberó/)).toBeVisible();
    const wa = page.getByRole('link', { name: 'Escribirnos por WhatsApp' });
    expect(decodeURIComponent((await wa.getAttribute('href')) ?? '')).toContain('quiero volver a reservar');
    // Una reserva vencida no muestra cómo pagar.
    await expect(page.getByRole('region', { name: 'Cómo pagar' })).toHaveCount(0);
  });

  test('cerrada: dice que ya no está activa', async ({ page }) => {
    await page.goto(ruta('e'));

    await expect(page.getByRole('heading', { level: 1, name: 'Esta reserva ya no está activa' })).toBeVisible();
    await expect(page.getByRole('region', { name: 'Cómo pagar' })).toHaveCount(0);
  });

  test('un enlace desconocido muestra un aviso amable (no un 404) y no revela si existió', async ({ page }) => {
    const respuesta = await page.goto(`/pagar/${'z'.repeat(40)}`);

    expect(respuesta?.status()).toBe(200);
    await expect(page.getByRole('heading', { level: 1, name: 'No pudimos abrir este enlace de pago' })).toBeVisible();
    await expect(page.getByText(/no es válido o no está disponible/)).toBeVisible();
  });

  for (const [nombre, letra] of [['activa', 'a'], ['vencida', 'd']] as const) {
    test(`reserva ${nombre}: sin violaciones graves de accesibilidad`, async ({ page }) => {
      await page.goto(ruta(letra));
      await expect(page.getByRole('heading', { level: 1 })).toBeVisible();

      const resultado = await new AxeBuilder({ page }).include('main').withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']).analyze();
      const graves = resultado.violations
        .filter((v) => v.impact === 'serious' || v.impact === 'critical')
        .map((v) => `${v.id}: ${v.help} → ${v.nodes.slice(0, 4).map((n) => `${n.target.join(' ')} ⟶ ${n.any.map((c) => c.message).join(' | ')}`).join(' ;; ')}`);
      expect(graves).toEqual([]);
    });
  }
});
