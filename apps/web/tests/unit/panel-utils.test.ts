import { describe, expect, it } from 'vitest';
import { fecha, moneda, sumarDias } from '../../app/utils/panel/formato';
import { enlaceWaMe, mensajeBienvenidaMatricula, normalizarTelefonoWa } from '../../app/utils/panel/whatsapp-plantillas';
import { esEnlaceGrupoWhatsApp, normalizarTelefonoPeru, urlMailto } from '../../app/utils/panel/contacto';
import { esquemaEstudiante } from '../../app/utils/panel/esquemas';

describe('Utilidades puras de Horus Academic', () => {
  it('formatea fechas, moneda PEN y suma días en Lima', () => {
    expect(moneda('280.00')).toContain('280.00');
    expect(fecha('2026-11-21')).toContain('2026');
    expect(sumarDias('2026-11-21', 2)).toBe('2026-11-23');
  });

  it('normaliza teléfonos peruanos y construye enlaces wa.me', () => {
    expect(normalizarTelefonoWa('987654321')).toBe('51987654321');
    expect(normalizarTelefonoPeru('+51 972 021 374')).toBe('51972021374');
    const url = enlaceWaMe('987654321', 'Hola Horus');
    expect(url).toBe('https://wa.me/51987654321?text=Hola%20Horus');
  });

  it('valida enlaces de grupo WhatsApp y construye plantilla de bienvenida', () => {
    expect(esEnlaceGrupoWhatsApp('https://chat.whatsapp.com/HorusCctvGrupo2026')).toBe(true);
    expect(esEnlaceGrupoWhatsApp('https://evil.com/grupo')).toBe(false);
    const msg = mensajeBienvenidaMatricula({
      estudiante: 'María',
      curso: 'CCTV IP',
      enlaceGrupo: 'https://chat.whatsapp.com/HorusCctvGrupo2026',
    });
    expect(msg).toContain('María');
    expect(msg).toContain('CCTV IP');
    expect(msg).toContain('https://chat.whatsapp.com/HorusCctvGrupo2026');
  });

  it('valida mailto seguro y esquema Zod de estudiante', () => {
    expect(urlMailto('ana@horus.pe?bcc=x')).toBeNull();
    expect(urlMailto('ana@horus.pe', 'Consulta')).toBe('mailto:ana@horus.pe?subject=Consulta');
    expect(esquemaEstudiante.safeParse({
      dni: '12345678',
      nombres: 'Ana',
      apellidos: 'Quispe',
      email: 'ana@horus.pe',
      telefono: '+51987654321',
      fecha_nacimiento: '',
      direccion: '',
      estado: 'activo',
    }).success).toBe(true);
  });
});
