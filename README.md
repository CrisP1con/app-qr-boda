# Álbum digital de boda

Aplicación web para una única boda. Los invitados acceden mediante un código QR, consultan la información del evento, suben fotografías sin registrarse y ven una galería pública actualizada en tiempo real.

El administrador configura el evento, controla la recepción de fotografías, elimina imágenes, genera el código QR, abre la pantalla de proyección y descarga los originales.

El proyecto no es multi-boda, multi-cliente ni SaaS.

## Funcionalidades

- Página pública de la boda en `/boda`.
- Galería pública de fotografías.
- Subida pública sin cuenta ni login de invitados.
- Hasta 20 fotografías por solicitud.
- Máximo 20 MB por fotografía.
- Formatos permitidos: JPG, JPEG, PNG y WEBP.
- Previsualización y eliminación de fotos antes de publicarlas.
- Identificación de las fotos propias mediante token del navegador.
- Eliminación de fotografías propias.
- Panel administrativo protegido por Laravel Jetstream.
- Configuración básica del evento y portada.
- Apertura y cierre de la recepción.
- Eliminación administrativa de cualquier fotografía.
- Procesamiento de miniaturas y previews.
- Actualización en tiempo real mediante Laravel Reverb.
- Pantalla de proyección.
- Código QR en PNG y SVG.
- Descarga de fotografías originales.
- Rate limiting y validación de archivos.

## Stack

- Laravel 13.
- PHP 8.3.
- SQLite.
- Vue 3 e Inertia.js.
- Tailwind CSS 4.
- Laravel Jetstream para el administrador.
- Laravel Queue para procesar imágenes.
- Laravel Reverb para tiempo real.
- Endroid QR Code para generar códigos QR.
- ZipStream para descargar originales sin depender de `ZipArchive`.

## Requisitos

- PHP 8.3 o superior.
- Composer.
- Node.js y npm.
- Extensión GD de PHP para procesar imágenes.
- SQLite habilitado.

## Instalación inicial

Desde la raíz del proyecto:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

En Windows PowerShell, si no existe `.env`:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Verificá que exista la base SQLite configurada en `.env`. Luego ejecutá:

```bash
php artisan migrate
php artisan storage:link
npm install
npm run build
```

## Administrador provisional

Configurá estas variables en `.env`:

```env
ADMIN_NAME="Administrador"
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD="una-clave-provisional-de-al-menos-12-caracteres"
```

Creá el usuario administrador:

```bash
php artisan db:seed --class=AdminUserSeeder --no-interaction
```

El seeder no sobrescribe la contraseña si el email ya existe. Después del primer acceso, la contraseña puede cambiarse desde el perfil de Jetstream en `/user/profile`.

## Desarrollo

La aplicación requiere varios procesos activos. Abrí terminales separadas:

### Servidor Laravel

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Abrí:

```text
http://127.0.0.1:8000/boda
```

### Frontend

```bash
npm run dev
```

### Cola de procesamiento

```bash
php artisan queue:work
```

Procesa las miniaturas y previews de las fotografías subidas.

### Tiempo real

```bash
php artisan reverb:start
```

Es necesario para que la galería y la proyección reciban fotografías nuevas en tiempo real.

Después de modificar variables de entorno o configuración:

```bash
php artisan optimize:clear
```

## Flujo de uso

1. Ingresar a `/login` con el usuario administrador.
2. Acceder a `/admin`.
3. Entrar en **Configurar evento**.
4. Completar nombres, fecha, portada y mensajes.
5. Guardar la configuración.
6. Verificar la página pública en `/boda`.
7. Probar una subida desde `/boda/subir`.
8. Revisar la galería en `/boda/fotos`.
9. Revisar las fotos desde `/admin/fotos`.
10. Generar el QR desde `/admin/qr`.

## Rutas principales

| Ruta | Acceso | Uso |
|---|---|---|
| `/boda` | Público | Página principal de la boda |
| `/boda/fotos` | Público | Galería |
| `/boda/subir` | Público | Subida de fotografías |
| `/boda/proyeccion` | Público | Pantalla de proyección |
| `/login` | Administrador | Inicio de sesión |
| `/admin` | Administrador | Dashboard |
| `/admin/configuracion` | Administrador | Configuración del evento |
| `/admin/fotos` | Administrador | Gestión de fotografías |
| `/admin/qr` | Administrador | Visualización y descarga del QR |
| `/admin/proyeccion` | Administrador | Proyección desde administración |
| `/admin/download` | Administrador | Descarga de originales |

## Pruebas y build

Ejecutar las pruebas:

```bash
php artisan test --compact
```

Generar los assets de producción:

```bash
npm run build
```

También se puede verificar el formato PHP con:

```bash
vendor/bin/pint --dirty --format agent
```

## Despliegue en producción

### 1. Preparar el servidor

El servidor debe tener PHP 8.3, Composer, Node.js durante el build, SQLite y la extensión GD habilitada.

El servidor web debe apuntar al directorio:

```text
/ruta/al/proyecto/public
```

No debe apuntar a la raíz del repositorio.

### 2. Instalar dependencias

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

### 3. Configurar `.env`

Usá `APP_ENV=production`, `APP_DEBUG=false` y la URL pública real:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.example

DB_CONNECTION=sqlite
QUEUE_CONNECTION=database
BROADCAST_CONNECTION=reverb
FILESYSTEM_DISK=local
```

Configurá también credenciales seguras para Reverb y el administrador. Nunca subas `.env` al repositorio.

### 4. Aplicar configuración y base de datos

```bash
php artisan migrate --force
php artisan storage:link
php artisan db:seed --class=AdminUserSeeder --no-interaction
php artisan optimize
```

El seeder requiere que `ADMIN_PASSWORD` esté definida y tenga al menos 12 caracteres.

### 5. Ejecutar procesos permanentes

En producción deben mantenerse activos:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=120
```

```bash
php artisan reverb:start
```

Usá Supervisor, systemd o el administrador de procesos de tu proveedor para reiniciarlos automáticamente si fallan. Después de cada despliegue, reiniciá los workers:

```bash
php artisan queue:restart
```

### 6. HTTPS y almacenamiento

- Configurá HTTPS para el dominio público.
- Verificá que `APP_URL` use `https://`.
- Asegurá permisos de escritura para `storage` y `bootstrap/cache`.
- Confirmá que `public/storage` apunte correctamente a `storage/app/public`.
- Probá subida, procesamiento, galería, QR y descarga antes del evento.

### 7. Reverb en producción

Reverb debe ejecutarse en un host y puerto accesibles por el frontend. Las variables `REVERB_*` deben contener los valores reales del entorno de producción y las variables `VITE_REVERB_*` deben regenerarse al ejecutar `npm run build`.

El proxy web debe permitir conexiones WebSocket hacia Reverb si se utiliza un dominio HTTPS.

## Seguridad operativa

- No versionar `.env`, credenciales ni fotos privadas.
- Usar una contraseña administrativa única y cambiar la provisional.
- Mantener `APP_DEBUG=false` en producción.
- Mantener activo el rate limiting de subidas.
- Verificar límites de tamaño de PHP y del proxy web.
- Configurar backups de la base SQLite y de `storage/app/public`.
- No exponer el puerto interno de Reverb públicamente si el proxy puede actuar como intermediario.

## Alcance fuera del proyecto

Este proyecto no incluye registro de invitados, login para invitados, moderación, aprobación de fotos, multi-tenancy, pagos, suscripciones, organizaciones ni funcionalidades SaaS.
