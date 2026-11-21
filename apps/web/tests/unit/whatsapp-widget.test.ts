import { describe, expect, it } from 'vitest';
import { mountSuspended } from '@nuxt/test-utils/runtime';
import WhatsappFloatingWidget from '../../app/components/shared/WhatsappFloatingWidget.vue';

describe('WhatsappFloatingWidget', () => {
  it('abre la tarjeta de motivos de WhatsApp sin afirmar disponibilidad en línea', async () => {
    const wrapper = await mountSuspended(WhatsappFloatingWidget);

    const toggle = wrapper.get('button[aria-label="Escríbenos por WhatsApp"]');
    expect(toggle.attributes('aria-expanded')).toBe('false');

    await toggle.trigger('click');

    const region = wrapper.get('section[aria-label="Escríbenos por WhatsApp"]');
    expect(region.text()).toContain('Matrícula e información de cursos');
    expect(region.text()).toContain('Pagos, cuotas y certificados');
    expect(region.text()).toContain('Atención al cliente');
    expect(region.text()).not.toMatch(/en línea|disponible ahora/i);

    const links = region.findAll('a[href^="https://wa.me/"]');
    expect(links).toHaveLength(3);
    expect(links[0]?.attributes('rel')).toContain('noreferrer');
  });
});
