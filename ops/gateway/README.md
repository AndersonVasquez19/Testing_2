# Gateway de Horus System

Un solo comando levanta **Laravel** (API) y **Next.js** (interfaz y panel) y los publica detrás de **un único origen**
(`http://127.0.0.1:8080` por defecto). Es una herramienta de **desarrollo y demostración local**, sin dependencias: solo Node
(los módulos que ya trae).

```powershell
npm run gateway                      # desde la raíz del repositorio: modo dev
npm run gateway -- --modo=prod       # next start (compila antes si falta el build)
npm run gateway:test                 # pruebas del gateway
```

`Ctrl+C` apaga los dos servicios. Si uno se cae, el gateway lo avisa, muestra las últimas líneas de su registro y apaga el otro.

## Qué hace

1. **Comprueba requisitos** (PHP en el PATH, `composer install` y `npm ci` en `apps/web`) y que los puertos estén libres.
2. **Arranca los servicios** conectados entre sí. Genera, por ejecución, un secreto aleatorio (token de servicio del BFF) y lo pasa por
   variables de entorno; nunca lo imprime, lo guarda ni lo pone en la línea de comandos. Si defines `HORUS_WEB_SERVICE_TOKEN`
   (32 caracteres o más), usa ese.
3. **Espera la salud de cada uno** (`/up` de Laravel con el texto «Application up» y `/` de Next).
4. **Abre el proxy** y reparte las rutas:

| Ruta | Destino |
| --- | --- |
| `/up`, `/api/auth/*`, `/storage/*`, `/assets/images/*` | Laravel |
| todo lo demás (`/`, `/login`, `/admin/*`, `/api/bff/*`, …) | Next.js |
| `/api/panel/*`, `/api/public/*` | **404**: solo las llama el BFF de Next, de servidor a servidor |

El proxy conserva el encabezado `Host` (el BFF de Next deriva de él su origen propio para la protección CSRF), reenvía las cookies y las
redirecciones tal cual, encadena el WebSocket de la recarga en caliente de `next dev` y responde 502 si un servicio no contesta.
El reparto se decide sobre la ruta decodificada y normalizada (`%2e%2e`, `//`, `%00`), de modo que esas formas no saltan el bloqueo.

## Opciones y variables

| Opción | Efecto |
| --- | --- |
| `--modo=dev\|prod` | `next dev` (por defecto) o `next start` |
| `--build` | fuerza `next build` antes (con `--modo=prod`) |
| `--puerto=N` | puerto del gateway (8080, o `GATEWAY_PORT`) |

Puertos internos: `HORUS_LARAVEL_PORT` (8000) y `HORUS_NEXT_PORT` (3000).

## Cosas que conviene saber

- **La base de datos es la de `apps/api/.env`.** El gateway no la cambia y, al arrancar, imprime el destino (sin credenciales). Para una
  demo sin tocar datos reales, exporta antes `DB_CONNECTION=sqlite` y `DB_DATABASE=<archivo>` (más `SESSION_DRIVER=file` y
  `CACHE_STORE=file`): las variables del entorno mandan sobre el `.env`.
- **Apagado.** Con `Ctrl+C` se cierran los dos. Si matas el proceso del gateway a la fuerza (`Stop-Process -Force`), Windows no le deja
  limpiar y quedan servicios huérfanos; usa `taskkill /pid <PID> /T /F` para matar el árbol entero.
- **Registros** en `ops/gateway/.runtime/logs/` (`laravel.log`, `next.log`, `gateway.log`), que se sobrescriben en cada ejecución y no
  se versionan.
- **No es una pasarela de producción.** Sin TLS, sin límites de velocidad ni autenticación propios: los controles viven en Laravel y
  en el BFF de Next. Para producción sigue valiendo el mapa de rutas de `docs/guia-despliegue.md` en el servidor web real.
