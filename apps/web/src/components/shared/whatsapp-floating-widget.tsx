'use client';

import { useEffect, useId, useRef, useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { MessageCircle, X } from 'lucide-react';
import { CONTACT_INFO } from '@/data/contactos';

/**
 * Botón flotante de WhatsApp para el sitio público. Sustituye al asistente virtual: la atención es humana.
 * No afirma disponibilidad ("en línea") porque el horario de atención se publica en varios sitios con valores distintos;
 * la tarjeta solo ofrece motivos de consulta y enlaza al formulario formal y al Libro de Reclamaciones.
 * El mensaje que se abre en WhatsApp lleva la ruta de la página actual para dar contexto a quien responde.
 */

interface Motivo {
  id: string;
  titulo: string;
  detalle: string;
  mensaje: string;
}

const MOTIVOS: readonly Motivo[] = [
  {
    id: 'matricula',
    titulo: 'Matrícula e información de cursos',
    detalle: 'Fechas, modalidades y cómo inscribirte',
    mensaje: 'Hola, quiero información sobre cursos y matrícula.',
  },
  {
    id: 'pagos',
    titulo: 'Pagos, cuotas y certificados',
    detalle: 'Estado de tu pago o constancias',
    mensaje: 'Hola, tengo una consulta sobre pagos, cuotas o certificados.',
  },
  {
    id: 'soporte',
    titulo: 'Atención al cliente',
    detalle: 'Cualquier otra consulta o ayuda',
    mensaje: 'Hola, necesito ayuda de atención al cliente.',
  },
];

function enlaceWhatsApp(mensaje: string, ruta: string): string {
  const texto = ruta && ruta !== '/' ? `${mensaje}\n\n(Escribo desde: ${ruta})` : mensaje;
  return `https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent(texto)}`;
}

export function WhatsAppFloatingWidget() {
  const [abierto, setAbierto] = useState(false);
  const ruta = usePathname() ?? '';
  const panelId = useId();
  const contenedor = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!abierto) return;
    const alTeclear = (evento: KeyboardEvent) => {
      if (evento.key === 'Escape') setAbierto(false);
    };
    const alPulsarFuera = (evento: PointerEvent) => {
      if (contenedor.current && !contenedor.current.contains(evento.target as Node)) setAbierto(false);
    };
    document.addEventListener('keydown', alTeclear);
    document.addEventListener('pointerdown', alPulsarFuera);
    return () => {
      document.removeEventListener('keydown', alTeclear);
      document.removeEventListener('pointerdown', alPulsarFuera);
    };
  }, [abierto]);

  return (
    <div ref={contenedor} className="fixed bottom-4 right-4 z-50 flex flex-col items-end gap-3 sm:bottom-6 sm:right-6">
      {abierto ? (
        <section
          id={panelId}
          aria-label="Escríbenos por WhatsApp"
          className="w-[min(92vw,22rem)] rounded-2xl border border-slate-200 bg-white p-4 shadow-xl"
        >
          <header className="mb-3 flex items-start justify-between gap-3">
            <div>
              <p className="text-sm font-semibold text-slate-900">¿En qué podemos ayudarte?</p>
              <p className="text-xs text-slate-600">Elige un motivo y te atendemos por WhatsApp.</p>
            </div>
            <button
              type="button"
              onClick={() => setAbierto(false)}
              aria-label="Cerrar"
              className="rounded-full p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700"
            >
              <X className="size-4" aria-hidden="true" />
            </button>
          </header>

          <ul className="space-y-2">
            {MOTIVOS.map((motivo) => (
              <li key={motivo.id}>
                <a
                  href={enlaceWhatsApp(motivo.mensaje, ruta)}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="block rounded-xl border border-slate-200 px-3 py-2.5 transition hover:border-emerald-600 hover:bg-emerald-600/5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700"
                >
                  <span className="block text-sm font-medium text-slate-900">{motivo.titulo}</span>
                  <span className="block text-xs text-slate-600">{motivo.detalle}</span>
                </a>
              </li>
            ))}
          </ul>

          <p className="mt-3 border-t border-slate-100 pt-3 text-xs text-slate-600">
            ¿Prefieres escribirnos formalmente?{' '}
            <Link href="/contactos" className="font-medium text-[#0A2540] underline underline-offset-2">
              Contacto
            </Link>{' '}
            ·{' '}
            <Link href="/legales/libro-reclamaciones" className="font-medium text-[#0A2540] underline underline-offset-2">
              Libro de Reclamaciones
            </Link>
          </p>
        </section>
      ) : null}

      <button
        type="button"
        onClick={() => setAbierto((valor) => !valor)}
        aria-expanded={abierto}
        aria-controls={abierto ? panelId : undefined}
        aria-label={abierto ? 'Cerrar el menú de WhatsApp' : 'Escríbenos por WhatsApp'}
        className="flex size-14 items-center justify-center rounded-full bg-emerald-700 text-white shadow-lg transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 motion-safe:hover:scale-105"
      >
        <MessageCircle className="size-7" aria-hidden="true" />
      </button>
    </div>
  );
}
