'use client';

import { useState } from 'react';
import { Check, Copy } from 'lucide-react';

/**
 * Copia un texto al portapapeles (referencia de pago, número de Yape). Avisa con un mensaje accesible; si el navegador no deja
 * copiar (permiso o contexto no seguro) lo dice, en lugar de fingir que copió.
 */
export function BotonCopiar({ texto, etiqueta }: { texto: string; etiqueta: string }) {
  const [estado, setEstado] = useState<'inicial' | 'copiado' | 'error'>('inicial');

  async function copiar() {
    try {
      await navigator.clipboard.writeText(texto);
      setEstado('copiado');
    } catch {
      setEstado('error');
    }
    window.setTimeout(() => setEstado('inicial'), 2500);
  }

  return (
    <>
      <button
        type="button"
        onClick={copiar}
        aria-label={`Copiar ${etiqueta}`}
        className="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 outline-offset-2 transition hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-sky-700"
      >
        {estado === 'copiado' ? <Check className="size-3.5 text-emerald-600" aria-hidden="true" /> : <Copy className="size-3.5" aria-hidden="true" />}
        {estado === 'copiado' ? 'Copiado' : 'Copiar'}
      </button>
      <span role="status" className="sr-only">
        {estado === 'copiado' ? `${etiqueta} copiado` : estado === 'error' ? `No se pudo copiar ${etiqueta}; cópialo a mano` : ''}
      </span>
    </>
  );
}
