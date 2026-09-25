# Plan de implementación — Álbum digital de boda

Fuente de verdad: [`docs/SDD.md`](SDD.md).

Este documento organiza la implementación del SDD para una única boda. No contempla multi-boda, multi-tenancy, SaaS, suscripciones, pagos, facturación, organizaciones, registro de invitados, moderación ni funcionalidades fuera de alcance.

Todos los ítems comienzan con el estado `⬜ Pendiente`.

## Fase 1 — Configuración base del proyecto

### 1.1 Verificación del proyecto existente

- **Qué:** Verificar que el repositorio existente utiliza Laravel 13 y comprobar la disponibilidad/configuración de PHP, Jetstream, Inertia.js, Vue 3, Tailwind CSS, MySQL, Laravel Storage, Laravel Queue y Laravel Reverb. Adaptar la implementación al proyecto existente; no crear otro proyecto Laravel.
- **Archivos/componentes probables:** `composer.json`, `package.json`, `.env`, `.env.example`, `config/`, `bootstrap/`, estructura actual de `resources/`.
- **Dependencias:** Ninguna.
- **Estado:** ✅ Completada — Laravel 13, Jetstream 5.5, Inertia 2, Vue 3, Tailwind 4, MySQL, Storage, Queue y Reverb verificados/configurados.

### 1.2 Preparación de Storage, Queue y tiempo real

- **Qué:** Dejar disponibles las configuraciones existentes necesarias para Storage, Queue y Reverb, sin implementar todavía la lógica del álbum.
- **Archivos/componentes probables:** `config/filesystems.php`, `config/queue.php`, `config/broadcasting.php`, `config/reverb.php`, `.env`, `bootstrap/`.
- **Dependencias:** 1.1.
- **Estado:** ✅ Completada — Arranque de Inertia, Tailwind/Vite, middleware raíz, Storage público, Queue database, broadcasting y Echo/Reverb preparados.

## Fase 2 — Base de datos y modelos

### 2.1 Álbum único

- **Qué:** Crear la tabla `albums` con los campos definidos por el SDD y modelar el único álbum de la boda: `name`, `slug`, `event_date`, `cover_image`, `message`, `primary_color`, `thank_you_message`, `upload_enabled` y timestamps.
- **Archivos/componentes probables:** migración de `albums`, `app/Models/Album.php`, relaciones y configuración del álbum único.
- **Dependencias:** Fase 1.
- **Estado:** ✅ Completada — Migración, modelo, casts, relación con fotografías e índice único para `slug` creados y aplicados.

### 2.2 Fotografías

- **Qué:** Crear la tabla `photos` y el modelo con `album_id`, `upload_token`, rutas de original/thumbnail/preview, nombre original, MIME, tamaño y timestamps. No agregar estados de aprobación o moderación.
- **Archivos/componentes probables:** migración de `photos`, `app/Models/Photo.php`, relación `Album`–`Photo`.
- **Dependencias:** 2.1.
- **Estado:** ✅ Completada — Migración, modelo, relación con álbum, casts, claves foráneas e índices creados y aplicados. Sin estados de moderación.

## Fase 3 — Autenticación del administrador

### 3.1 Acceso administrativo con Jetstream

- **Qué:** Utilizar la autenticación existente de Laravel Jetstream para proteger `/admin` y todas las rutas administrativas. No crear login personalizado ni cuentas para invitados.
- **Archivos/componentes probables:** middleware de autenticación existente, `routes/web.php`, configuración de Jetstream y páginas administrativas.
- **Dependencias:** Fase 1.
- **Estado:** ✅ Completada — Jetstream protege el acceso administrativo y no existe registro público habilitado.

### 3.2 Dashboard administrativo

- **Qué:** Implementar el dashboard con nombre del evento, fecha, estado de recepción, cantidad de fotografías, estimación del espacio utilizado y accesos a galería, subida, proyección, QR, descarga y configuración.
- **Archivos/componentes probables:** rutas admin, controlador/página de dashboard, componentes Vue/Inertia, modelo `Album` y consulta de `Photo`/Storage.
- **Dependencias:** 2.1, 2.2, 3.1.
- **Estado:** ✅ Completada — `/admin` muestra evento, fecha, recepción, cantidad de fotografías, espacio utilizado y acceso a configuración.

### 3.3 Configuración de la información de la boda

- **Qué:** Permitir al administrador guardar nombres de los novios, fecha, imagen de portada, mensaje principal, color principal, mensaje posterior a la subida y estado de recepción.
- **Archivos/componentes probables:** `app/Http/Controllers/Admin/AlbumController.php`, `app/Http/Requests/UpdateAlbumRequest.php`, `resources/js/Pages/Admin/Configuration.vue`, `routes/web.php`, `app/Models/Album.php`.
- **Dependencias:** 2.1, 3.1.
- **Estado:** ✅ Completada — La configuración crea o actualiza el único álbum y almacena la portada mediante Laravel Storage.

## Fase 4 — Página pública de la boda

### 4.1 Página `/boda`

- **Qué:** Mostrar nombres de los novios, fecha, portada, mensaje principal, estado de recepción y botones para subir fotografías y acceder a la galería. Mantener el flujo público sin login, registro, usuario, email ni contraseña.
- **Archivos/componentes probables:** ruta pública, controlador/página Inertia, componentes Vue y estilos Tailwind.
- **Dependencias:** 2.1.
- **Estado:** ✅ Completada — `/boda` muestra la información configurada, portada, estado de recepción y accesos a subida/galería, con diseño responsive mobile-first.

## Fase 5 — Galería pública

### 5.1 Listado `/boda/fotos`

- **Qué:** Mostrar públicamente las fotografías del álbum, ordenadas de más recientes a más antiguas, con funcionamiento responsive para teléfono, tablet y PC.
- **Archivos/componentes probables:** ruta, controlador/consulta de `Photo`, página y componentes Vue de galería.
- **Dependencias:** 2.2, 4.1 y procesamiento de imágenes de la Fase 11 para usar derivados.
- **Estado:** ✅ Completada — `/boda/fotos` lista las fotografías del álbum de más recientes a más antiguas usando previews/thumbnails y una grilla responsive.

### 5.2 Vista individual

- **Qué:** Permitir visualizar una fotografía, volver a la galería y eliminarla si pertenece al visitante actual. La comprobación de propiedad debe repetirse en backend.
- **Archivos/componentes probables:** ruta/controlador de vista individual, componente Vue de visualización y acción de eliminación.
- **Dependencias:** 5.1, Fases 7 y 8.
- **Estado:** ✅ Completada — `/boda/fotos/{photo}` permite visualizar una fotografía, volver a la galería y eliminarla cuando pertenece al visitante actual; la autorización se repite en backend.

## Fase 6 — Sistema de subida de fotos

### 6.1 Formulario público de subida

- **Qué:** Implementar `GET /boda/subir` para seleccionar fotografías desde el dispositivo, sin autenticación de invitados, con un flujo simple y mobile-first.
- **Archivos/componentes probables:** ruta, página Vue/Inertia de subida y componentes de selección/progreso.
- **Dependencias:** 4.1.
- **Estado:** ✅ Completada — `/boda/subir` permite seleccionar múltiples fotografías desde el dispositivo, con interfaz mobile-first y bloqueo visual cuando la recepción está cerrada.

### 6.2 Endpoint de publicación

- **Qué:** Implementar `POST /boda/fotos`, con hasta 20 fotografías por solicitud, máximo 20 MB por archivo, formatos JPG/JPEG/PNG/WEBP, validación real del archivo y publicación inmediata sin estados pendientes ni aprobación.
- **Archivos/componentes probables:** ruta, controlador, Form Request/validador, modelo `Photo` y Storage.
- **Dependencias:** 2.2, 6.1, Fase 16 para las protecciones definitivas.
- **Estado:** ✅ Completada — `POST /boda/fotos` valida cantidad, tamaño, MIME/tipo de imagen y formatos permitidos; almacena originales, thumbnails y previews y publica las fotografías inmediatamente usando procesamiento síncrono o Queue según la configuración.

### 6.3 Confirmación posterior a la subida

- **Qué:** Mostrar `thank_you_message` después de una subida exitosa y enlaces para volver a la galería o subir más fotografías mientras la recepción esté abierta.
- **Archivos/componentes probables:** página/componente de confirmación, datos de `Album` y página de subida.
- **Dependencias:** 2.1, 6.2.
- **Estado:** ✅ Completada — `/boda/gracias` muestra `thank_you_message` y permite volver a la galería o subir más fotografías si la recepción continúa abierta.

## Fase 7 — Identificación de fotos por invitado

### 7.1 Token de subida

- **Qué:** Generar en backend un `upload_token` aleatorio y no predecible, guardarlo en una cookie segura con duración de 30 días y asociarlo a las fotografías del navegador. Usar `HttpOnly`, `SameSite=Lax` y `Secure` en producción. No usar la IP como propiedad.
- **Archivos/componentes probables:** middleware/servicio de token, controlador de subida, cookie de respuesta, modelo `Photo` y configuración de sesión/cookies.
- **Dependencias:** 2.2, 6.2.
- **Estado:** ✅ Completada — El endpoint genera y persiste un token aleatorio por navegador en una cookie HttpOnly, SameSite=Lax, de 30 días y Secure en producción.

## Fase 8 — Eliminación de fotos propias

### 8.1 Eliminación pública protegida

- **Qué:** Implementar `DELETE /boda/fotos/{photo}` comprobando en backend que la fotografía pertenece al `upload_token` actual. Eliminar registro, original, thumbnail y preview, y emitir `PhotoDeleted`. Mantener disponible durante recepción abierta y cerrada según el SDD.
- **Archivos/componentes probables:** ruta, controlador/acción de borrado, validación de propiedad, modelo `Photo`, Storage y evento `PhotoDeleted`.
- **Dependencias:** 2.2, 7.1, 11.1.
- **Estado:** ✅ Completada — `DELETE /boda/fotos/{photo}` comprueba álbum y `upload_token` en backend, elimina registro y archivos asociados, y emite `PhotoDeleted`. Continúa disponible aunque la recepción esté cerrada.

## Fase 9 — Eliminación de fotos por administrador

### 9.1 Gestión administrativa de fotografías

- **Qué:** Implementar `GET /admin/fotos` y `DELETE /admin/fotos/{photo}` para que el administrador autenticado pueda ver y eliminar cualquier fotografía, borrando todos sus archivos derivados y emitiendo `PhotoDeleted`.
- **Archivos/componentes probables:** rutas admin, controlador/página admin de fotografías, Storage, modelo `Photo` y evento.
- **Dependencias:** 2.2, 3.1, 11.1.
- **Estado:** ✅ Completada — `/admin/fotos` permite al administrador autenticado visualizar y eliminar cualquier fotografía del álbum mediante `DELETE /admin/fotos/{photo}`, borrando sus tres archivos y emitiendo `PhotoDeleted`.

## Fase 10 — Abrir/cerrar recepción

### 10.1 Control administrativo de `upload_enabled`

- **Qué:** Implementar `POST /admin/album/toggle-upload` y la visualización del estado para abrir o cerrar la recepción desde el panel.
- **Archivos/componentes probables:** ruta, controlador admin de álbum, modelo `Album`, dashboard y configuración pública.
- **Dependencias:** 2.1, 3.2.
- **Estado:** ✅ Completada — El panel autenticado permite alternar `upload_enabled` mediante `POST /admin/album/toggle-upload` y muestra el estado actual.

### 10.2 Aplicación del estado en frontend y backend

- **Qué:** Cuando esté cerrada, ocultar/deshabilitar la subida y rechazar el `POST /boda/fotos` en backend. Mantener disponibles galería, proyección, fotografías existentes, eliminación propia, administración y descarga, tal como define el SDD.
- **Archivos/componentes probables:** controlador/validador de subida, página `/boda`, middleware o regla de dominio si corresponde.
- **Dependencias:** 6.2, 8.1, 10.1.
- **Estado:** ✅ Completada — La portada y el formulario público ocultan o deshabilitan la subida cuando la recepción está cerrada, y el endpoint `POST /boda/fotos` vuelve a comprobar el estado en backend. Galería y eliminaciones permanecen disponibles.

## Fase 11 — Procesamiento de imágenes

### 11.1 Original, thumbnail y preview

- **Qué:** Guardar cada fotografía mediante Laravel Storage usando nombres basados en UUID en `storage/app/public/boda/originals`, `thumbnails` y `previews`. Conservar el original para descarga y generar thumbnail/preview para visualización.
- **Archivos/componentes probables:** servicio/job de imágenes, `config/filesystems.php`, Storage, modelo `Photo` y rutas almacenadas en base de datos.
- **Dependencias:** 2.2, 6.2.
- **Estado:** ✅ Completada — Se conserva el original y se generan thumbnail y preview en Storage con nombres UUID; la galería utiliza los derivados para visualización.

### 11.2 Queue y fallback síncrono

- **Qué:** Usar Laravel Queue para operaciones pesadas cuando esté disponible y permitir procesamiento síncrono como fallback de desarrollo, sin bloquear innecesariamente la respuesta HTTP cuando Queue esté configurada.
- **Archivos/componentes probables:** Job de procesamiento, configuración de Queue y flujo de subida.
- **Dependencias:** 1.2, 11.1.
- **Estado:** ✅ Completada — `ProcessPhotoDerivatives` usa Queue con reintentos y backoff cuando la conexión no es `sync`, y el flujo procesa de forma síncrona cuando corresponde. Los fallos agotados limpian los archivos y el registro.

## Fase 12 — Código QR

### 12.1 Visualización y descarga del QR

- **Qué:** Implementar la sección admin y `GET /admin/qr` para visualizar y descargar el QR en PNG y SVG. El destino debe construirse siempre como `${APP_URL}/boda`; el dominio no se almacena en `albums`.
- **Archivos/componentes probables:** ruta, controlador/página admin de QR, configuración `APP_URL` y componente/generador QR compatible con el stack existente.
- **Dependencias:** 3.1, 4.1.
- **Estado:** ✅ Completada — `/admin/qr` muestra el QR con destino `${APP_URL}/boda` y permite descargarlo en PNG y SVG mediante rutas administrativas protegidas.

## Fase 13 — Actualización en tiempo real con Laravel Reverb

### 13.1 Broadcasting y eventos

- **Qué:** Configurar Laravel Reverb, broadcasting y Laravel Events para publicar `PhotoUploaded` al subir una fotografía y `PhotoDeleted` al eliminarla.
- **Archivos/componentes probables:** `app/Events/PhotoUploaded.php`, `app/Events/PhotoDeleted.php`, `routes/channels.php`, configuración de broadcasting/Reverb y cliente Vue/Echo.
- **Dependencias:** 1.2, Fases 6, 8 y 9.
- **Estado:** ✅ Completada — `PhotoUploaded` y `PhotoDeleted` se emiten después del commit sobre el canal público `wedding`, con payloads limitados a la información necesaria para actualizar las vistas.

### 13.2 Actualización de galería y proyección

- **Qué:** Suscribir la galería y la proyección a los eventos para incorporar fotografías nuevas y retirar fotografías eliminadas sin recargar la página.
- **Archivos/componentes probables:** páginas/componentes Vue de galería y proyección, canales públicos y serialización de fotografías.
- **Dependencias:** 13.1, Fases 5 y 14.
- **Estado:** ✅ Completada — La galería y la proyección pública/administrativa se suscriben a Reverb e incorporan fotografías nuevas o retiran eliminadas sin recargar.

## Fase 14 — Página de proyección

### 14.1 Rutas y presentación

- **Qué:** Implementar `GET /boda/proyeccion` como vista pública y `GET /admin/proyeccion` como acceso administrativo autenticado. Optimizar para TV/proyector, 16:9, pantalla completa, interfaz minimalista y funcionamiento prolongado.
- **Archivos/componentes probables:** rutas, controlador/páginas Vue de proyección, estilos responsive y autenticación admin.
- **Dependencias:** 3.1, 5.1, 11.1.
- **Estado:** ✅ Completada — Se implementaron `/boda/proyeccion` público y `/admin/proyeccion` protegido, con vista 16:9, fondo negro, interfaz mínima y acceso a pantalla completa.

### 14.2 Slideshow fijo

- **Qué:** Implementar slideshow con 5 segundos por fotografía, orden más recientes primero, transición suave, pantalla completa y sin controles innecesarios.
- **Archivos/componentes probables:** componente Vue de slideshow y página de proyección.
- **Dependencias:** 14.1.
- **Estado:** ✅ Completada — La proyección avanza cada 5 segundos, mantiene el orden más recientes primero y aplica transición suave.

### 14.3 Integración en tiempo real

- **Qué:** Incorporar automáticamente nuevas fotografías y retirar eliminadas mientras la proyección continúa funcionando, incluso con recepción cerrada.
- **Archivos/componentes probables:** página/componente de proyección y suscripciones Reverb.
- **Dependencias:** 10.2, 13.2, 14.2.
- **Estado:** ✅ Completada — La proyección se suscribe a `PhotoUploaded` y `PhotoDeleted`, incorpora nuevas fotos y retira eliminadas sin recargar, incluso con recepción cerrada.

## Fase 15 — Descarga de fotos

### 15.1 ZIP administrativo de originales

- **Qué:** Implementar `GET /admin/download` para generar un ZIP protegido por autenticación que contenga únicamente los originales. Excluir thumbnails y previews; usar Queue si la cantidad lo requiere.
- **Archivos/componentes probables:** ruta, controlador, Job de generación ZIP, Storage y dashboard.
- **Dependencias:** 2.2, 3.1, 11.1 y 1.2.
- **Estado:** ✅ Completada — `GET /admin/download` genera una descarga ZIP protegida con únicamente los originales existentes, excluyendo thumbnails y previews. La respuesta se transmite sin guardar un ZIP permanente en el servidor.

## Fase 16 — Seguridad y protección contra abuso

### 16.1 Validación y aislamiento de archivos

- **Qué:** Validar MIME real, extensión, tamaño, cantidad, integridad básica y tipo de imagen en backend. Usar UUID, no confiar en nombres originales, evitar ejecución de archivos y exposición innecesaria de rutas internas.
- **Archivos/componentes probables:** Form Request/validador, controlador de subida, configuración de Storage, respuestas públicas y reglas del servidor.
- **Dependencias:** 6.2, 11.1.
- **Estado:** ✅ Completada — Se validan contenido de imagen, MIME real, extensión, tamaño y cantidad; los archivos se guardan con UUID y las respuestas públicas no exponen rutas, tokens ni metadatos técnicos.

### 16.2 Rate limiting

- **Qué:** Aplicar en backend máximo 10 solicitudes de subida por minuto por IP, 500 fotografías por hora por IP, 20 fotografías por solicitud y 20 MB por archivo. La IP se utilizará para abuso, no para propiedad.
- **Archivos/componentes probables:** configuración de rate limiters, rutas/controlador de subida y pruebas.
- **Dependencias:** 6.2, 7.1.
- **Estado:** ✅ Completada — `POST /boda/fotos` aplica por IP un máximo de 10 solicitudes por minuto y 500 por hora, además de los límites de 20 archivos por request y 20 MB por archivo.

### 16.3 Autorización y privacidad

- **Qué:** Proteger todas las rutas admin con Jetstream, verificar que la fotografía pertenece al álbum y ocultar públicamente IP, token y demás información técnica innecesaria.
- **Archivos/componentes probables:** middleware auth, políticas/validaciones de `Photo`, controladores, recursos/respuestas Inertia y vistas.
- **Dependencias:** 3.1, 7.1, 8.1, 9.1.
- **Estado:** ✅ Completada — Las rutas administrativas permanecen protegidas por Jetstream, las eliminaciones verifican álbum y propiedad en backend, y los datos técnicos de `Photo` están ocultos en serializaciones públicas.

## Fase 17 — Pruebas

### 17.1 Acceso y administración

- **Qué:** Probar acceso público a `/boda`, galería y proyección; acceso autenticado al administrador; bloqueo de rutas admin sin autenticación.
- **Archivos/componentes probables:** `tests/Feature/` y configuración de testing.
- **Dependencias:** Fases 3, 4, 5 y 14.
- **Estado:** ✅ Completada — Las pruebas cubren acceso público a boda/galería/proyección, acceso administrativo y bloqueo de rutas admin sin autenticación.

### 17.2 Subidas, validación y token

- **Qué:** Probar subida válida, formatos inválidos, archivo mayor a 20 MB, más de 20 archivos, recepción cerrada y generación del `upload_token`.
- **Archivos/componentes probables:** `tests/Feature/`, Storage fake y datos de prueba.
- **Dependencias:** Fases 6, 7, 10 y 16.
- **Estado:** ✅ Completada — Las pruebas cubren subida válida, formato inválido, archivo mayor a 20 MB, más de 20 archivos, recepción cerrada y generación del token.

### 17.3 Propiedad, recepción y eliminación

- **Qué:** Probar eliminación propia, rechazo de eliminación ajena, eliminación admin, apertura/cierre, bloqueo backend de nuevas subidas y eliminación propia con recepción cerrada.
- **Archivos/componentes probables:** `tests/Feature/` y eventos fake.
- **Dependencias:** Fases 8, 9, 10 y 16.
- **Estado:** ✅ Completada — Las pruebas cubren propiedad propia/ajena, eliminación administrativa, apertura/cierre, bloqueo backend y eliminación con recepción cerrada.

### 17.4 Tiempo real, QR, Storage y descarga

- **Qué:** Probar `PhotoUploaded`, `PhotoDeleted`, uso correcto de `APP_URL`, originales/thumbnail/preview, eliminación de archivos, ZIP de originales y exclusión de derivados.
- **Archivos/componentes probables:** `tests/Feature/`, `tests/Unit/` si corresponde, Storage fake y eventos.
- **Dependencias:** Fases 11, 12, 13 y 15.
- **Estado:** ✅ Completada — Las pruebas cubren eventos de subida/eliminación, QR PNG/SVG, Storage de derivados, borrado de archivos y ZIP de originales sin derivados.

## Fase 18 — Optimización responsive y móvil

### 18.1 Público mobile-first

- **Qué:** Verificar el flujo QR → `/boda` → subida → confirmación → galería en smartphones y tablets, además de notebooks y PCs. Revisar legibilidad, selección de archivos y navegación.
- **Archivos/componentes probables:** páginas Vue públicas y estilos Tailwind.
- **Dependencias:** Fases 4, 5, 6 y 17.
- **Estado:** ✅ Completada — Se revisaron y ajustaron portada, galería, subida, confirmación y navegación para smartphones, tablets, notebooks y PCs; la galería respeta el cierre de recepción también en su navegación.

### 18.2 TV y proyector

- **Qué:** Verificar formato 16:9, pantalla completa, slideshow, legibilidad y actualización en tiempo real en la vista de proyección.
- **Archivos/componentes probables:** página de proyección, componentes Vue y estilos responsive.
- **Dependencias:** Fases 13, 14 y 17.
- **Estado:** ✅ Completada — La proyección utiliza viewport completo, formato visual 16:9 mediante `object-contain`, controles mínimos y slideshow continuo con transición.

## Fase 19 — Revisión final

### 19.1 Revisión contra el SDD

- **Qué:** Recorrer los criterios de aceptación y flujos del SDD: acceso público, publicación inmediata, límites, propiedad, administración, recepción, QR, ZIP, Storage, Reverb, slideshow y responsive.
- **Archivos/componentes probables:** código implementado, tests y documentación de trabajo.
- **Dependencias:** Fases 1–18.
- **Estado:** ✅ Completada — Se revisaron los 33 criterios de aceptación, las rutas públicas/admin, los flujos de invitado/administrador, Storage, Queue, Reverb, QR, ZIP y responsive.

### 19.2 Control de alcance

- **Qué:** Confirmar que la solución sigue siendo para una sola boda y que no incorpora multi-tenancy, SaaS, pagos, suscripciones, registro/login de invitados, aprobación, moderación, comentarios, likes, reacciones, chat o redes sociales.
- **Archivos/componentes probables:** revisión global del proyecto y `docs/PLAN.md`.
- **Dependencias:** 19.1.
- **Estado:** ✅ Completada — La implementación permanece limitada a una única boda y no agrega multi-tenancy, SaaS, pagos, suscripciones, organizaciones, cuentas de invitados, moderación ni funciones sociales.

## Pendientes / Decisiones resueltas

Se adoptan las siguientes decisiones prácticas para implementar el SDD sin ampliar su alcance:

1. **Nombre del evento:** `albums.name` representará los nombres de los novios tal como deben mostrarse públicamente. Se validará como texto requerido, sin crear campos adicionales. `message` será el mensaje principal y `thank_you_message` el mensaje posterior a una subida.
2. **Imágenes:** se utiliza la extensión GD disponible en PHP 8.3 para generar thumbnails y previews, sin agregar una librería de procesamiento adicional. La validación se realiza en backend mediante MIME real, integridad, tipo de imagen y extensión permitida; nunca depende solo del nombre del archivo.
3. **Publicación y Queue:** no habrá estado pendiente. La fotografía quedará registrada como publicada cuando exista el original y estén disponibles thumbnail y preview. Se usará Queue cuando esté configurada; mientras los derivados no estén listos, no se emitirá `PhotoUploaded`. Si Queue no está disponible, se procesará de forma síncrona como fallback.
4. **Proyección administrativa:** `/admin/proyeccion` será una ruta autenticada que mostrará la misma proyección que `/boda/proyeccion`. No se duplicará la lógica del slideshow ni se creará una proyección administrativa diferente.
5. **Vista individual:** se utilizará `GET /boda/fotos/{photo}`. Esta ruta complementa el listado definido por el SDD y repetirá la autorización de eliminación en backend.
6. **Código QR:** se utilizará una librería PHP compatible con generación SVG y PNG, preferentemente `endroid/qr-code`. El destino se construirá exclusivamente con `APP_URL` y `/boda`.
7. **Espacio utilizado:** el dashboard calculará una estimación sumando el tamaño real de todos los archivos del álbum en Storage: originales, thumbnails y previews. El campo `photos.size` representará el tamaño del original y no sustituirá el cálculo completo.
8. **Administrador inicial:** Jetstream conserva su autenticación oficial. El primer usuario administrador se provisiona mediante `AdminUserSeeder` usando `ADMIN_NAME`, `ADMIN_EMAIL` y `ADMIN_PASSWORD`; el seeder no sobrescribe un email existente. No habrá registro público de invitados ni login personalizado.

9. **Procesos de ejecución:** cuando `QUEUE_CONNECTION` no sea `sync`, debe mantenerse un worker `php artisan queue:work` para procesar imágenes y broadcasts; Reverb debe ejecutarse con `php artisan reverb:start` para habilitar las actualizaciones en tiempo real. Esto es operación de infraestructura, no una funcionalidad adicional del álbum.

Estas decisiones no agregan funcionalidades visibles fuera del SDD. Si durante la implementación aparece una incompatibilidad con las versiones instaladas, se deberá detener la tarea afectada y revisar la decisión antes de introducir una alternativa.

## Fuera de alcance

No planificar: multi-boda, multi-cliente, multi-tenancy, SaaS, suscripciones, pagos, facturación, organizaciones, roles complejos, registro/login de invitados, aprobación, moderación, comentarios, likes, reacciones, chat, integración con redes sociales, invitaciones complejas y HEIC/HEIF en la primera versión.
