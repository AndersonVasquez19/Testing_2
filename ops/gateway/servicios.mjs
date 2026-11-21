// Definición y supervisión de los dos servicios que levanta el gateway: Laravel (API) y Next.js (interfaz y BFF).
// Sin dependencias: solo módulos de Node.

import { spawn, spawnSync } from 'node:child_process';
import { randomBytes } from 'node:crypto';
import fs from 'node:fs';
import http from 'node:http';
import net from 'node:net';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

export const RAIZ = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..', '..');
export const RUNTIME = path.join(RAIZ, 'ops', 'gateway', '.runtime');
const ES_WINDOWS = process.platform === 'win32';

const ENTERO = /^\d{2,5}$/;

function puerto(valor, nombre) {
  if (!ENTERO.test(String(valor)) || Number(valor) < 1024 || Number(valor) > 65535) throw new Error(`${nombre}: puerto no válido (${valor}); usa un número entre 1024 y 65535.`);
  return Number(valor);
}

/** @param {string[]} argv argumentos sin `node` ni el script. @param {Record<string,string|undefined>} env */
export function resolverConfig(argv, env = process.env) {
  const opciones = { modo: 'dev', build: false, puerto: env.GATEWAY_PORT ?? '8080' };
  for (const arg of argv) {
    if (arg === '--build') opciones.build = true;
    else if (arg.startsWith('--modo=')) opciones.modo = arg.slice(7);
    else if (arg.startsWith('--puerto=')) opciones.puerto = arg.slice(9);
    else throw new Error(`Opción desconocida: ${arg}`);
  }
  if (!['dev', 'prod'].includes(opciones.modo)) throw new Error('--modo debe ser dev o prod.');

  const cfg = {
    modo: opciones.modo,
    build: opciones.build,
    puertos: {
      gateway: puerto(opciones.puerto, 'gateway'),
      laravel: puerto(env.HORUS_LARAVEL_PORT ?? '8000', 'laravel'),
      next: puerto(env.HORUS_NEXT_PORT ?? '3000', 'next'),
    },
  };
  const usados = [cfg.puertos.gateway, cfg.puertos.laravel, cfg.puertos.next];
  if (new Set(usados).size !== usados.length) throw new Error('Los puertos del gateway, Laravel y Next deben ser distintos.');

  // Secreto compartido: el de entorno si existe; si no, uno aleatorio por ejecución (nunca se imprime ni se guarda).
  const secreto = (valor) => (typeof valor === 'string' && valor.length >= 32 ? valor : randomBytes(36).toString('base64url'));
  cfg.tokens = { servicioWeb: secreto(env.HORUS_WEB_SERVICE_TOKEN) };
  cfg.origen = `http://127.0.0.1:${cfg.puertos.gateway}`;
  cfg.origenes = [cfg.origen, `http://localhost:${cfg.puertos.gateway}`];
  return cfg;
}

const rutaNext = () => path.join(RAIZ, 'apps', 'web', 'node_modules', 'next', 'dist', 'bin', 'next');
// `artisan serve` ejecuta `php -S` con `public/` como directorio actual y este enrutador (que busca `index.php` en él).
const rutaServidorPhp = () => path.join(RAIZ, 'apps', 'api', 'vendor', 'laravel', 'framework', 'src', 'Illuminate', 'Foundation', 'resources', 'server.php');

/** Los servicios con su comando, carpeta, variables de entorno y comprobación de salud. Función pura (probada). */
export function definirServicios(cfg) {
  const { puertos, tokens } = cfg;
  return [
    {
      nombre: 'laravel',
      cwd: path.join(RAIZ, 'apps', 'api', 'public'),
      comando: 'php',
      args: ['-S', `127.0.0.1:${puertos.laravel}`, rutaServidorPhp()],
      env: {
        HORUS_WEB_URL: cfg.origen,
        HORUS_WEB_SERVICE_TOKEN: tokens.servicioWeb,
        // `php -S` es de un solo hilo en Windows; en Linux/macOS se admiten varios trabajadores.
        ...(ES_WINDOWS ? {} : { PHP_CLI_SERVER_WORKERS: '4' }),
      },
      // Un 200 no basta: PHP responde 200 aunque imprima un error fatal o un aviso. La página de salud de Laravel dice «Application up».
      salud: { puerto: puertos.laravel, ruta: '/up', maxSegundos: 30, contiene: 'Application up' },
    },
    {
      nombre: 'next',
      cwd: path.join(RAIZ, 'apps', 'web'),
      comando: process.execPath,
      args: [rutaNext(), cfg.modo === 'dev' ? 'dev' : 'start', '-p', String(puertos.next), '-H', '127.0.0.1'],
      env: {
        HORUS_API_URL: `http://127.0.0.1:${puertos.laravel}`,
        HORUS_WEB_SERVICE_TOKEN: tokens.servicioWeb,
        HORUS_WEB_ORIGIN: cfg.origenes.join(','),
      },
      salud: { puerto: puertos.next, ruta: '/', maxSegundos: 120 },
    },
  ];
}

/** Lo que falta para poder arrancar; vacío si todo está. */
export function verificarRequisitos() {
  const faltas = [];
  const php = spawnSync('php', ['-v'], { encoding: 'utf8' });
  if (php.error || php.status !== 0) faltas.push('PHP no está en el PATH (necesario para Laravel).');
  if (!fs.existsSync(path.join(RAIZ, 'apps', 'api', 'vendor', 'autoload.php'))) faltas.push('Falta `composer install` en apps/api.');
  if (!fs.existsSync(rutaNext())) faltas.push('Falta `npm ci` en apps/web.');
  return faltas;
}

export function puertoLibre(numero) {
  return new Promise((resolver) => {
    const probe = net.createServer();
    probe.once('error', () => resolver(false));
    probe.once('listening', () => probe.close(() => resolver(true)));
    probe.listen(numero, '127.0.0.1');
  });
}

/**
 * Destino de base de datos que usará Laravel, sin credenciales (solo para avisar a la persona). Una variable del entorno del
 * gateway manda sobre `apps/api/.env`, igual que para Laravel.
 */
export function destinoBaseDeDatos(env = process.env) {
  let texto = '';
  try {
    texto = fs.readFileSync(path.join(RAIZ, 'apps', 'api', '.env'), 'utf8');
  } catch {
    // sin .env: solo cuentan las variables del entorno
  }
  const valor = (clave) => (env[clave] ?? texto.match(new RegExp(`^${clave}=(.*)$`, 'm'))?.[1] ?? '').trim().replace(/^["']|["']$/g, '');
  const tipo = valor('DB_CONNECTION') || 'sqlite';
  if (tipo === 'sqlite') return `sqlite (${path.basename(valor('DB_DATABASE') || 'database.sqlite')})`;
  return `${tipo}://${valor('DB_HOST')}:${valor('DB_PORT')}/${valor('DB_DATABASE')}`;
}

export function sondear({ puerto: p, ruta, contiene }) {
  return new Promise((resolver) => {
    const req = http.get({ host: '127.0.0.1', port: p, path: ruta, timeout: 2000 }, (res) => {
      const trozos = [];
      res.on('data', (t) => trozos.push(t));
      res.on('end', () => {
        const bueno = (res.statusCode ?? 500) < 500;
        resolver(bueno && (contiene === undefined || Buffer.concat(trozos).toString().includes(contiene)));
      });
    });
    req.on('error', () => resolver(false));
    req.on('timeout', () => {
      req.destroy();
      resolver(false);
    });
  });
}

export function matarArbol(hijo) {
  if (!hijo?.pid || hijo.exitCode !== null) return;
  if (ES_WINDOWS) spawnSync('taskkill', ['/pid', String(hijo.pid), '/T', '/F'], { stdio: 'ignore' });
  else {
    try {
      process.kill(-hijo.pid, 'SIGTERM');
    } catch {
      hijo.kill('SIGTERM');
    }
  }
}

/** Arranca un servicio con su salida en `.runtime/logs/<nombre>.log`. */
export function arrancar(servicio) {
  fs.mkdirSync(path.join(RUNTIME, 'logs'), { recursive: true });
  const archivoLog = path.join(RUNTIME, 'logs', `${servicio.nombre}.log`);
  const fd = fs.openSync(archivoLog, 'w');
  const hijo = spawn(servicio.comando, servicio.args, {
    cwd: servicio.cwd,
    env: { ...process.env, ...servicio.env },
    stdio: ['ignore', fd, fd],
    windowsHide: true,
    detached: !ES_WINDOWS,
  });
  fs.closeSync(fd);
  return { hijo, archivoLog };
}

export function colaDeLog(archivo, lineas = 15) {
  try {
    return fs.readFileSync(archivo, 'utf8').split(/\r?\n/).filter(Boolean).slice(-lineas).join('\n');
  } catch {
    return '(sin registro)';
  }
}
