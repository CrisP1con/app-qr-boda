# Changelog

## [Unreleased]

### Estado inicial

- El proyecto se encuentra en la etapa de planificación.
- La documentación de trabajo se basa exclusivamente en `docs/SDD.md`.
- Se documentaron decisiones de implementación para resolver los puntos ambiguos del SDD sin ampliar el alcance.
- Se completó la Fase 1: se configuraron Jetstream 5.5, Reverb 1.12, Inertia/Vue, Tailwind 4, Storage, Queue, broadcasting y Echo.
- Se completó la Fase 2: se crearon y aplicaron las migraciones y modelos `Album` y `Photo`, con sus relaciones, factories e índices.
- Se completó la Fase 3: se creó el dashboard administrativo y la configuración básica del evento protegidos por Jetstream.
- Se completó la Fase 4: se creó la página pública `/boda` con información del evento, portada, estado de recepción y navegación inicial.
- Se completó la Fase 5: se implementaron la galería pública `/boda/fotos` y la vista individual `/boda/fotos/{photo}` con thumbnails/previews y diseño responsive.
- Se completó la Fase 6: se implementaron el formulario `/boda/subir`, la publicación múltiple en `POST /boda/fotos` y la confirmación posterior con `thank_you_message`.
- Se implementó el token de subida requerido por la identificación del navegador y el almacenamiento inicial de originales, thumbnails y previews.
- Se completó y reforzó la Fase 7: el token se inicializa al entrar al formulario, se reutiliza durante la subida y los datos técnicos quedan ocultos en la serialización de `Photo`.
- Se completó la Fase 8: los invitados pueden eliminar únicamente sus propias fotografías desde la vista individual, con validación backend y borrado de original, thumbnail y preview.
- Se completó la Fase 9: se agregó la gestión administrativa `/admin/fotos` para visualizar y eliminar cualquier fotografía del álbum.
- Se completó la Fase 10: se agregó la apertura/cierre de recepción desde el panel y el bloqueo backend/frontend de nuevas subidas cuando está cerrada.
- Se completó la Fase 11: el procesamiento de originales, thumbnails y previews utiliza Queue con reintentos cuando corresponde y fallback síncrono para desarrollo.
- Se completó la Fase 12: se agregó la sección administrativa del código QR con destino basado en `APP_URL` y descargas PNG/SVG.
- Se completó la Fase 13 para la galería: `PhotoUploaded` y `PhotoDeleted` se transmiten por Reverb y la galería actualiza sus fotografías sin recargar.
- Se completó la Fase 14: se agregó la proyección pública/administrativa con slideshow de 5 segundos, pantalla completa y actualización en tiempo real.
- Se completó la Fase 15: se agregó la descarga administrativa en ZIP de los originales, excluyendo thumbnails y previews.
- Se completó la Fase 16: se agregaron rate limits por IP, validación explícita de extensiones/MIME/tamaño/cantidad y revisión de aislamiento y privacidad de archivos.
- Se completó la Fase 17: se agregaron pruebas funcionales de acceso, subida, validación, token, propiedad, recepción, eliminación, QR, Storage y descarga.
- Se completó la Fase 18: se revisaron los flujos responsive/mobile y la proyección para TV, incluyendo el estado de recepción en la navegación pública.
- Se completó la Fase 19: se realizó la revisión final contra los criterios del SDD y se confirmó el control de alcance para una única boda.
- Se agregó `AdminUserSeeder` para crear de forma explícita un administrador provisional mediante variables de entorno; Jetstream permite cambiar luego la contraseña desde el perfil.
- Se estableció MySQL como motor de base de datos para desarrollo y producción; SQLite dejó de ser la configuración documentada del proyecto.
