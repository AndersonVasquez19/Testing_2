import Link from 'next/link';
import { ArrowLeft, Plus } from 'lucide-react';
import { Suspense } from 'react';
import { AccionesEstudiante } from '@/components/academico/acciones-estudiante';
import { HistorialAcademico } from '@/components/academico/historial-academico';
import { Alerta } from '@/components/shared/ui/alerta';
import { BotonesContacto, BotonesGrupoWhatsApp } from '@/components/shared/ui/acciones-contacto';
import { EncabezadoPagina } from '@/components/shared/ui/encabezado-pagina';
import { EstadoError, EstadoProhibido, EstadoSinResultados } from '@/components/shared/ui/estados';
import { Insignia, colorEstado } from '@/components/shared/ui/insignia';
import { PanelPestana, Pestanas, type DefinicionPestana } from '@/components/shared/ui/pestanas';
import { EsqueletoFicha, ListaDatos, Seccion, Tabla } from '@/components/shared/ui/tabla';
import { ESTADOS_COBRO, sumarSoles } from '@/features/academico/cobro';
import type { EstudianteDetalle, ObligacionResumen } from '@/features/academico/tipos';
import { consultarPanel } from '@/lib/panel/api-servidor';
import { mensajeEstudiante } from '@/lib/panel/contacto';
import { etiqueta, fecha, moneda } from '@/lib/panel/formato';

export const metadata = { title: 'Ficha del estudiante · Gestión académica' };

// Ver la nota de cursos/[id]: la Navbar pública del layout raíz bloquea el prerender de rutas dinámicas.
export const instant = false;

const LISTADO = '/admin/academico/estudiantes';

interface PropsFicha {
  params: Promise<{ id: string }>;
  searchParams: Promise<{ pestana?: string }>;
}

export default function EstudianteDetallePage({ params, searchParams }: PropsFicha) {
  return (
    <Suspense fallback={<EsqueletoFicha etiqueta="Cargando el estudiante" />}>
      <Ficha params={params} searchParams={searchParams} />
    </Suspense>
  );
}

async function Ficha({ params, searchParams }: PropsFicha) {
  const { id } = await params;
  const { pestana } = await searchParams;
  const volver = (
    <Link href={LISTADO} className="inline-flex items-center gap-1 text-sm font-semibold text-academico-700 hover:underline">
      <ArrowLeft className="h-4 w-4" aria-hidden="true" /> Volver a estudiantes
    </Link>
  );

  if (!/^\d{1,10}$/.test(id)) return <EstadoSinResultados titulo="El estudiante no existe" accion={volver} />;

  const resultado = await consultarPanel<{ data: EstudianteDetalle }>(`academico/estudiantes/${id}`, `${LISTADO}/${id}`);
  if (resultado.estado === 'prohibido') return <EstadoProhibido volver="/admin/academico" />;
  if (resultado.estado === 'no_encontrado') return <EstadoSinResultados titulo="El estudiante no existe" accion={volver} />;
  if (resultado.estado !== 'ok') return <EstadoError texto="Inténtalo de nuevo en unos minutos." accion={volver} />;

  const estudiante = resultado.datos.data;
  const nombre = `${estudiante.nombres} ${estudiante.apellidos}`;
  const pestanas: DefinicionPestana[] = [
    { id: 'resumen', etiqueta: 'Resumen' },
    ...(estudiante.matriculas ? [{ id: 'matriculas', etiqueta: 'Matrículas', contador: estudiante.matriculas.length }] : []),
    { id: 'cuenta', etiqueta: 'Cuenta corriente' },
  ];

  return (
    <>
      <EncabezadoPagina
        titulo={nombre}
        descripcion={`DNI ${estudiante.dni}`}
        migas={[
          { etiqueta: 'Inicio', href: '/admin/academico' },
          { etiqueta: 'Estudiantes', href: LISTADO },
          { etiqueta: estudiante.dni },
        ]}
        acciones={<AccionesEstudiante estudiante={estudiante} />}
      />

      {estudiante.eliminado ? (
        <Alerta tipo="aviso" className="mb-6">Este estudiante está desactivado: no aparece en los listados ni puede matricularse.</Alerta>
      ) : null}

      <Pestanas pestanas={pestanas} inicial={pestana} etiqueta="Secciones de la ficha del estudiante" parametro="pestana">
      <PanelPestana id="resumen">
      <div className="grid gap-6 xl:grid-cols-3">
        <Seccion
          id="titulo-datos"
          titulo="Datos personales"
          acciones={
            <div className="flex flex-wrap gap-1">
              <Insignia color={colorEstado(estudiante.estado)}>{etiqueta('estadoEstudiante', estudiante.estado)}</Insignia>
              {estudiante.eliminado ? <Insignia color="rojo">Desactivado</Insignia> : null}
            </div>
          }
        >
          <ListaDatos
            filas={[
              ['DNI', estudiante.dni],
              ['Correo', estudiante.email ?? '—'],
              ['Teléfono', estudiante.telefono ?? '—'],
              ['Nacimiento', fecha(estudiante.fecha_nacimiento)],
              ['Dirección', estudiante.direccion ?? '—'],
              ['Registrado', fecha(estudiante.creado_en)],
              ['Última actualización', fecha(estudiante.actualizado_en)],
            ]}
          />
          <div className="mt-4 border-t border-panel-border pt-3">
            <BotonesContacto
              telefono={estudiante.telefono}
              email={estudiante.email}
              mensajeWa={mensajeEstudiante({ nombre })}
              asuntoCorreo="Horus Group · Contacto académico"
            />
          </div>
        </Seccion>

        <Seccion id="titulo-historial" titulo="Historial" descripcion="Operaciones registradas en la auditoría." className="xl:col-span-2">
          <HistorialAcademico entidad="estudiante" eventos={estudiante.historial} />
        </Seccion>
      </div>
      </PanelPestana>

        {estudiante.matriculas ? (
          <PanelPestana id="matriculas">
          <Seccion
            id="titulo-matriculas"
            titulo="Matrículas"
            descripcion="Historial de matrículas del estudiante, incluidas las anuladas."
            sinRelleno
            acciones={
              !estudiante.eliminado && estudiante.estado === 'activo' ? (
                <Link
                  href={`/admin/academico/matriculas/nueva?estudiante_id=${estudiante.id}`}
                  className="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-semibold text-academico-800 hover:bg-academico-50"
                >
                  <Plus className="h-4 w-4" aria-hidden="true" /> Matricular
                </Link>
              ) : null
            }
          >
            {estudiante.matriculas.length === 0 ? (
              <p className="px-5 py-12 text-center text-sm text-panel-muted">Este estudiante todavía no tiene matrículas.</p>
            ) : (
              <Tabla plana titulo={`Matrículas de ${nombre}`} columnas={['Curso', 'Fecha', 'Estado']}>
                    {estudiante.matriculas.map((matricula) => (
                      <tr key={matricula.id}>
                        <td className="px-4 py-3">
                          <Link href={`/admin/academico/matriculas/${matricula.id}`} className="font-medium text-panel-text hover:text-academico-800 hover:underline">
                            {matricula.curso?.nombre ?? 'Curso no disponible'}
                          </Link>
                          {matricula.curso ? <p className="text-xs text-panel-muted">{matricula.curso.codigo}</p> : null}
                        </td>
                        <td className="whitespace-nowrap px-4 py-3 text-panel-muted">{fecha(matricula.fecha)}</td>
                        <td className="px-4 py-3">
                          <div className="space-y-1.5">
                            <Insignia color={colorEstado(matricula.estado)}>{etiqueta('estadoMatricula', matricula.estado)}</Insignia>
                            {matricula.curso?.enlace_grupo_whatsapp ? (
                              <BotonesGrupoWhatsApp enlace={matricula.curso.enlace_grupo_whatsapp} compacto />
                            ) : null}
                          </div>
                        </td>
                      </tr>
                    ))}

</Tabla>
            )}
          </Seccion>
          </PanelPestana>
        ) : null}

      <PanelPestana id="cuenta">
        <Suspense fallback={<div className="h-40 animate-pulse rounded-xl border border-panel-border bg-panel-surface" aria-busy="true" aria-label="Cargando la cuenta corriente" />}>
          <CuentaCorriente estudianteId={estudiante.id} dni={estudiante.dni} nombre={nombre} />
        </Suspense>
      </PanelPestana>
      </Pestanas>
    </>
  );
}

/**
 * Cuenta corriente: las obligaciones de pago de las matrículas del estudiante, con lo pagado y lo que falta. Solo con
 * `pagos.ver` (Laravel responde 403 al resto). La API filtra por texto (DNI, nombre o código de edición), así que el
 * resultado se vuelve a filtrar por el identificador del estudiante: nunca se muestran obligaciones de otra persona.
 */
async function CuentaCorriente({ estudianteId, dni, nombre }: { estudianteId: number; dni: string; nombre: string }) {
  const resultado = await consultarPanel<{ data: ObligacionResumen[] }>(
    `academico/pagos/obligaciones?busqueda=${encodeURIComponent(dni)}&per_page=50`,
    `${LISTADO}/${estudianteId}?pestana=cuenta`,
  );
  if (resultado.estado === 'prohibido') {
    return <p className="text-sm text-panel-muted">Tu cuenta no tiene permiso para ver los pagos de este estudiante.</p>;
  }
  if (resultado.estado !== 'ok') return <EstadoError texto="No se pudo cargar la cuenta corriente. Inténtalo de nuevo en unos minutos." />;

  const obligaciones = resultado.datos.data.filter((o) => o.estudiante.id === estudianteId);
  const vigentes = obligaciones.filter((o) => o.estado !== 'anulada');
  const adeudado = sumarSoles(vigentes.map((o) => o.saldo));

  return (
    <Seccion
      id="titulo-cuenta"
      titulo="Cuenta corriente"
      descripcion="Lo pagado y lo que falta por cada matrícula (soles). Las obligaciones anuladas no suman."
      sinRelleno
      acciones={
        vigentes.length > 0 ? (
          <span className="text-sm text-panel-muted">
            Saldo pendiente: <strong className={Number.parseFloat(adeudado) > 0 ? 'text-estado-error' : 'text-panel-text'}>{moneda(adeudado)}</strong>
          </span>
        ) : null
      }
    >
      {obligaciones.length === 0 ? (
        <p className="px-5 py-12 text-center text-sm text-panel-muted">Este estudiante todavía no tiene obligaciones de pago.</p>
      ) : (
        <Tabla
          plana
          titulo={`Cuenta corriente de ${nombre}`}
          columnas={['Concepto', 'Estado', { texto: 'Total', derecha: true }, { texto: 'Pagado', derecha: true }, { texto: 'Saldo', derecha: true }]}
        >
          {obligaciones.map((o) => {
            const estado = ESTADOS_COBRO[o.estado];
            return (
              <tr key={o.identificador}>
                <td className="px-4 py-3">
                  <Link href={`/admin/academico/pagos/${o.identificador}`} className="font-medium text-panel-text hover:text-academico-800 hover:underline">
                    {o.concepto}
                  </Link>
                  {o.edicion ? <p className="text-xs text-panel-muted">Edición {o.edicion}</p> : null}
                  {o.proxima_cuota ? (
                    <p className={`text-xs ${o.proxima_cuota.vencida ? 'font-semibold text-estado-error' : 'text-panel-muted'}`}>
                      {o.proxima_cuota.vencida ? 'Cuota vencida' : 'Próxima cuota'} {fecha(o.proxima_cuota.vence)} · {moneda(o.proxima_cuota.saldo)}
                    </p>
                  ) : null}
                </td>
                <td className="px-4 py-3">
                  <Insignia color={estado.color}>{estado.texto}</Insignia>
                </td>
                <td className="whitespace-nowrap px-4 py-3 text-right tabular-nums">{moneda(o.monto_total)}</td>
                <td className="whitespace-nowrap px-4 py-3 text-right tabular-nums">{moneda(o.pagado)}</td>
                <td className="whitespace-nowrap px-4 py-3 text-right font-semibold tabular-nums">{moneda(o.saldo)}</td>
              </tr>
            );
          })}
        </Tabla>
      )}
    </Seccion>
  );
}
