import type { ColorInsignia } from '@/components/shared/ui/insignia';
import type { EstadoCobro } from '@/features/academico/tipos';

/** Texto y color de la insignia de cada estado de cobro de una obligación de pago. */
export const ESTADOS_COBRO: Record<EstadoCobro, { texto: string; color: ColorInsignia }> = {
  pendiente: { texto: 'Pendiente', color: 'ambar' },
  parcial: { texto: 'Parcial', color: 'azul' },
  pagada: { texto: 'Pagada', color: 'verde' },
  anulada: { texto: 'Anulada', color: 'rojo' },
};

/**
 * Suma importes en soles que llegan como texto («1250.50») sin errores de coma flotante: trabaja en céntimos.
 * Solo para mostrar totales en pantalla; el libro de pagos (Laravel) es la autoridad de los saldos.
 */
export function sumarSoles(valores: readonly string[]): string {
  const centimos = valores.reduce((total, valor) => total + Math.round(Number.parseFloat(valor) * 100), 0);
  return (centimos / 100).toFixed(2);
}
