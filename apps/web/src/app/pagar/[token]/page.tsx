import type { Metadata } from 'next';
import { MessageCircle } from 'lucide-react';
import type { ReactNode } from 'react';
import { CONTACT_INFO } from '@/data/contactos';
import { horusGet } from '@/lib/horus-api';
import { moneda } from '@/lib/panel/formato';
import { BotonCopiar } from './boton-copiar';

interface PagoReserva {
  data: {
    estado: 'activa' | 'con_pago' | 'vencida' | 'cerrada';
    vigente: boolean;
    referencia: string;
    vence_en: string;
    estudiante: string;
    curso: string | null;
    edicion: string | null;
    monto: { moneda: 'PEN'; total: string; pagado: string; por_confirmar: string; saldo: string } | null;
    metodos: { yape: { numero: string; titular: string } | null };
  };
}

// El enlace es un secreto: no se indexa y no viaja en el encabezado Referer hacia los enlaces externos (WhatsApp).
export const metadata: Metadata = {
  title: 'Pago de tu reserva · Horus Group',
  robots: { index: false, follow: false },
  referrer: 'no-referrer',
};

// Lee Laravel por petición (params + horusGet): sin esto `cacheComponents` intenta prerenderizarla y el build falla.
export const instant = false;

const ZONA = 'America/Lima';

function fechaLarga(iso: string): string {
  return new Intl.DateTimeFormat('es-PE', { timeZone: ZONA, weekday: 'long', day: 'numeric', month: 'long', hour: '2-digit', minute: '2-digit' }).format(new Date(iso));
}

/** «2 días», «19 horas»: lo que falta para que venza la reserva. */
function tiempoRestante(iso: string): string {
  const horas = Math.max(0, Math.floor((new Date(iso).getTime() - Date.now()) / 3_600_000));
  if (horas >= 48) return `${Math.floor(horas / 24)} días`;
  if (horas >= 1) return `${horas} ${horas === 1 ? 'hora' : 'horas'}`;
  return 'menos de una hora';
}

function enlaceWhatsApp(texto: string): string {
  return `https://wa.me/${CONTACT_INFO.phoneClean}?text=${encodeURIComponent(texto)}`;
}

function Tarjeta({ children }: { children: ReactNode }) {
  return (
    <main className="mx-auto max-w-lg px-4 py-10 sm:py-12">
      <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <p className="text-xs font-semibold uppercase tracking-wider text-sky-700">Horus Group · Pago de tu reserva</p>
        {children}
      </div>
    </main>
  );
}

function BotonWhatsApp({ texto, children }: { texto: string; children: ReactNode }) {
  return (
    <a
      href={enlaceWhatsApp(texto)}
      target="_blank"
      rel="noopener noreferrer"
      className="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-semibold text-white outline-offset-2 transition hover:bg-emerald-800 focus-visible:outline-2 focus-visible:outline-emerald-700"
    >
      <MessageCircle className="size-4" aria-hidden="true" />
      {children}
    </a>
  );
}

export default async function PagoReservaPage({ params }: { params: Promise<{ token: string }> }) {
  const { token } = await params;
  const res = await horusGet<PagoReserva>(`/pagos/${encodeURIComponent(token)}`);

  // Un enlace inválido y una caída de la API se muestran igual: no se revela si el enlace existió ni se asusta con un «404».
  if (!res.ok) {
    return (
      <Tarjeta>
        <h1 className="mt-1 text-xl font-bold text-slate-900">No pudimos abrir este enlace de pago</h1>
        <p className="mt-2 text-sm text-slate-600">
          El enlace no es válido o no está disponible en este momento. Revisa que esté completo o inténtalo de nuevo en unos minutos.
        </p>
        <BotonWhatsApp texto="Hola, no puedo abrir mi enlace de pago.">Escribirnos por WhatsApp</BotonWhatsApp>
      </Tarjeta>
    );
  }

  const d = res.data.data;
  const curso = d.curso ?? 'tu curso';
  const asuntoWa = `Hola, soy ${d.estudiante}. Referencia ${d.referencia} (${curso}).`;

  if (d.estado === 'con_pago') {
    return (
      <Tarjeta>
        <h1 className="mt-1 text-xl font-bold text-slate-900">Recibimos tu pago, {d.estudiante}</h1>
        <p className="mt-2 text-sm text-slate-600">
          Estamos verificándolo. Tu cupo en <strong>{curso}</strong> sigue reservado y te avisaremos cuando quede confirmado.
        </p>
        <p className="mt-4 text-xs text-slate-500">
          Tu referencia: <span className="font-mono font-semibold text-slate-700">{d.referencia}</span>
        </p>
        <BotonWhatsApp texto={`${asuntoWa} Consulta sobre mi pago.`}>Consultar por WhatsApp</BotonWhatsApp>
      </Tarjeta>
    );
  }

  if (!d.vigente) {
    const vencida = d.estado === 'vencida';
    return (
      <Tarjeta>
        <h1 className="mt-1 text-xl font-bold text-slate-900">{vencida ? 'Tu reserva venció' : 'Esta reserva ya no está activa'}</h1>
        <p className="mt-2 text-sm text-slate-600">
          {vencida
            ? `El plazo para pagar tu cupo en ${curso} terminó y el cupo se liberó. Escríbenos y, si aún hay lugar, volvemos a reservarlo.`
            : 'Ya no hace falta que pagues con este enlace. Si tienes dudas, escríbenos.'}
        </p>
        <BotonWhatsApp texto={`${asuntoWa} ${vencida ? 'Mi reserva venció y quiero volver a reservar.' : 'Tengo una consulta sobre mi reserva.'}`}>
          Escribirnos por WhatsApp
        </BotonWhatsApp>
      </Tarjeta>
    );
  }

  const yape = d.metodos.yape;
  const saldo = d.monto?.saldo;

  return (
    <Tarjeta>
      <h1 className="mt-1 text-xl font-bold text-slate-900">Hola, {d.estudiante}: completa tu pago</h1>
      <p className="mt-1 text-sm text-slate-600">
        {curso}
        {d.edicion ? ` · Edición ${d.edicion}` : ''}
      </p>

      <p className="mt-4 rounded-xl bg-sky-50 px-4 py-3 text-sm text-sky-900">
        Tu cupo está reservado hasta el <strong>{fechaLarga(d.vence_en)}</strong> (hora de Lima). Te quedan {tiempoRestante(d.vence_en)}. Pasado ese plazo, el
        cupo se libera.
      </p>

      <section aria-labelledby="titulo-importe" className="mt-6">
        <h2 id="titulo-importe" className="text-sm font-semibold text-slate-900">
          Importe
        </h2>
        {d.monto ? (
          <dl className="mt-2 space-y-1 text-sm">
            <div className="flex justify-between">
              <dt className="text-slate-600">Total</dt>
              <dd className="tabular-nums text-slate-900">{moneda(d.monto.total)}</dd>
            </div>
            {Number.parseFloat(d.monto.pagado) > 0 ? (
              <div className="flex justify-between">
                <dt className="text-slate-600">Ya pagado</dt>
                <dd className="tabular-nums text-slate-900">{moneda(d.monto.pagado)}</dd>
              </div>
            ) : null}
            {Number.parseFloat(d.monto.por_confirmar) > 0 ? (
              <div className="flex justify-between">
                <dt className="text-slate-600">En verificación</dt>
                <dd className="tabular-nums text-slate-900">{moneda(d.monto.por_confirmar)}</dd>
              </div>
            ) : null}
            <div className="flex justify-between border-t border-slate-200 pt-2 text-base font-bold">
              <dt className="text-slate-900">Debes pagar</dt>
              <dd className="tabular-nums text-slate-900">{moneda(d.monto.saldo)}</dd>
            </div>
          </dl>
        ) : (
          <p className="mt-2 text-sm text-slate-600">Todavía no tienes un importe asignado: te lo confirmaremos por WhatsApp antes de pagar.</p>
        )}
      </section>

      <section aria-labelledby="titulo-como" className="mt-6">
        <h2 id="titulo-como" className="text-sm font-semibold text-slate-900">
          Cómo pagar
        </h2>
        <ol className="mt-3 space-y-4 text-sm text-slate-700">
          <li className="flex gap-3">
            <span className="flex size-6 shrink-0 items-center justify-center rounded-full bg-sky-700 text-xs font-bold text-white" aria-hidden="true">
              1
            </span>
            <div className="min-w-0 flex-1">
              {yape ? (
                <>
                  <p>
                    Paga {saldo ? <strong>{moneda(saldo)}</strong> : 'el importe indicado'} por <strong>Yape</strong> al número:
                  </p>
                  <div className="mt-2 flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                    <div className="min-w-0">
                      <p className="font-mono text-base font-semibold text-slate-900">{yape.numero}</p>
                      {yape.titular ? <p className="truncate text-xs text-slate-500">a nombre de {yape.titular}</p> : null}
                    </div>
                    <BotonCopiar texto={yape.numero} etiqueta="el número de Yape" />
                  </div>
                </>
              ) : (
                <p>Escríbenos por WhatsApp para indicarte los medios de pago disponibles.</p>
              )}
            </div>
          </li>
          <li className="flex gap-3">
            <span className="flex size-6 shrink-0 items-center justify-center rounded-full bg-sky-700 text-xs font-bold text-white" aria-hidden="true">
              2
            </span>
            <div className="min-w-0 flex-1">
              <p>
                En el mensaje del pago escribe esta <strong>referencia</strong>, para reconocer tu pago:
              </p>
              <div className="mt-2 flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                <p className="font-mono text-lg font-bold tracking-wider text-slate-900">{d.referencia}</p>
                <BotonCopiar texto={d.referencia} etiqueta="la referencia" />
              </div>
            </div>
          </li>
          <li className="flex gap-3">
            <span className="flex size-6 shrink-0 items-center justify-center rounded-full bg-sky-700 text-xs font-bold text-white" aria-hidden="true">
              3
            </span>
            <p className="min-w-0 flex-1">
              Envíanos la <strong>captura del pago</strong> por WhatsApp. Adjúntala en el chat: el botón de abajo solo abre la conversación con tu
              referencia.
            </p>
          </li>
        </ol>
        <BotonWhatsApp texto={`${asuntoWa} Ya realicé el pago; te envío la captura.`}>Enviar mi captura por WhatsApp</BotonWhatsApp>
      </section>

      <p className="mt-6 text-xs text-slate-500">
        Este enlace es personal: no lo compartas. Horus Group nunca te pedirá claves ni códigos de Yape.
      </p>
    </Tarjeta>
  );
}
