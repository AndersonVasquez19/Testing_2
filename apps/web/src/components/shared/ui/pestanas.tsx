'use client';

import { useCallback, useState, type ReactNode } from 'react';
import { Tabs } from 'radix-ui';
import { cn } from '@/lib/utils';

export interface DefinicionPestana {
  id: string;
  etiqueta: string;
  /** Cifra opcional junto a la etiqueta (p. ej. cuántos inscritos hay). */
  contador?: number;
}

interface PestanasProps {
  /** Describe las pestañas; el contenido de cada una se pasa como `<PanelPestana id="…">` en `children`. */
  pestanas: readonly DefinicionPestana[];
  /** Pestaña con la que se abre; si no existe, se usa la primera. */
  inicial?: string;
  /** Nombre accesible de la lista de pestañas. */
  etiqueta: string;
  /** Si se indica, la pestaña activa se refleja en `?<parametro>=` (sin recargar), para poder enlazarla. */
  parametro?: string;
  children: ReactNode;
}

/**
 * Pestañas del panel (Radix Tabs: flechas, Inicio/Fin y ARIA incluidos). Los paneles inactivos siguen montados pero ocultos,
 * de modo que un formulario a medias no pierde su estado al cambiar de pestaña. El contenido puede ser de servidor: llega
 * como `children`, así que las secciones que cargan sus propios datos funcionan igual que fuera de las pestañas.
 */
export function Pestanas({ pestanas, inicial, etiqueta, parametro, children }: PestanasProps) {
  const primera = pestanas[0]?.id ?? '';
  const [valor, setValor] = useState(pestanas.some((p) => p.id === inicial) ? (inicial as string) : primera);

  const cambiar = useCallback(
    (nuevo: string) => {
      setValor(nuevo);
      if (!parametro || typeof window === 'undefined') return;
      const url = new URL(window.location.href);
      if (nuevo === primera) url.searchParams.delete(parametro);
      else url.searchParams.set(parametro, nuevo);
      window.history.replaceState(window.history.state, '', url);
    },
    [parametro, primera],
  );

  return (
    <Tabs.Root value={valor} onValueChange={cambiar}>
      <Tabs.List aria-label={etiqueta} className="mb-6 flex gap-1 overflow-x-auto border-b border-panel-border print:hidden">
        {pestanas.map((p) => (
          <Tabs.Trigger
            key={p.id}
            value={p.id}
            className={cn(
              '-mb-px inline-flex shrink-0 items-center gap-2 whitespace-nowrap border-b-2 border-transparent px-4 py-2.5 text-sm font-semibold',
              'text-panel-muted outline-offset-2 transition hover:text-panel-text focus-visible:outline-2 focus-visible:outline-academico-700',
              'data-[state=active]:border-academico-700 data-[state=active]:text-academico-800',
            )}
          >
            {p.etiqueta}
            {p.contador !== undefined ? (
              <span className="rounded-full bg-academico-50 px-2 py-0.5 text-xs font-semibold text-academico-800" aria-label={`${p.contador}`}>
                {p.contador}
              </span>
            ) : null}
          </Tabs.Trigger>
        ))}
      </Tabs.List>
      {children}
    </Tabs.Root>
  );
}

/** Contenido de una pestaña; va dentro de `<Pestanas>`. Oculto (no desmontado) cuando no está activa. */
export function PanelPestana({ id, children }: { id: string; children: ReactNode }) {
  return (
    <Tabs.Content value={id} forceMount className="outline-none data-[state=inactive]:hidden print:block!">
      {children}
    </Tabs.Content>
  );
}
