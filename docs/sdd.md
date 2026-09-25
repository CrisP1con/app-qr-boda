# SDD — Álbum Digital de Boda

## 1. Objetivo

Desarrollar un álbum digital de fotos para una única boda.

El sistema permitirá que los invitados accedan mediante un código QR, visualicen información de la boda, suban fotografías desde sus dispositivos y vean una galería pública que se actualiza en tiempo real.

No se requiere registro ni inicio de sesión para los invitados.

Las fotografías subidas por los invitados se publicarán inmediatamente, sin ningún sistema de aprobación o moderación.

El administrador tendrá acceso a un panel protegido mediante la autenticación existente de Laravel Jetstream.

El sistema también tendrá una pantalla de proyección para TV/proyector que mostrará las fotografías automáticamente y se actualizará en tiempo real.

El sistema está diseñado exclusivamente para esta boda y no debe convertirse en un sistema SaaS, multi-evento o multi-cliente.

---

# 2. Stack tecnológico

## Backend

* Laravel 13
* PHP según la versión requerida por el proyecto existente
* SQLite
* Laravel Storage
* Laravel Queue
* Laravel Reverb
* Laravel Events
* Laravel Rate Limiting

## Frontend

* Vue 3
* Inertia.js
* Tailwind CSS
* Flowbite opcional para componentes del panel administrativo

## Autenticación

* Laravel Jetstream
* La autenticación existente de Jetstream será utilizada para el administrador.
* No se implementará un sistema de login personalizado.
* Los invitados no tendrán cuentas.

## Tiempo real

* Laravel Reverb
* Broadcasting mediante eventos Laravel

---

# 3. Alcance

El sistema tendrá:

* Página pública de la boda.
* Galería pública.
* Subida pública de fotografías.
* Eliminación de fotografías propias por parte del invitado.
* Eliminación de cualquier fotografía por parte del administrador.
* Cierre y apertura de recepción de fotografías.
* Proyección pública.
* Actualización en tiempo real.
* Código QR.
* Descarga de fotografías originales desde administración.
* Configuración básica del evento.
* Dashboard administrativo.
* Estimación del espacio utilizado.
* Protección contra abuso.
* Validación de archivos.
* Procesamiento de imágenes.
* Sistema de pruebas automatizadas.

No tendrá:

* Registro de invitados.
* Login de invitados.
* Aprobación de fotografías.
* Moderación manual.
* Sistema multi-boda.
* Sistema multi-cliente.
* Suscripciones.
* Pagos.
* Facturación.
* Organizaciones.
* Roles complejos.
* SaaS.

---

# 4. Flujo general

El flujo principal será:

1. El invitado escanea el QR.
2. Accede a `/boda`.
3. Visualiza la portada y los datos del evento.
4. Puede entrar a la galería.
5. Puede subir fotografías.
6. Las fotografías son publicadas inmediatamente.
7. Las fotografías aparecen en la galería.
8. La galería se actualiza automáticamente mediante Reverb.
9. La pantalla de proyección recibe las nuevas fotografías automáticamente.
10. El invitado puede eliminar las fotografías que él mismo subió.
11. El administrador puede eliminar cualquier fotografía.
12. El administrador puede cerrar o volver a abrir la recepción.
13. Aunque la recepción esté cerrada, la galería y la proyección continúan funcionando.

---

# 5. Página pública

La página pública será:

`/boda`

Debe mostrar:

* Nombres de los novios.
* Fecha del evento.
* Imagen de portada.
* Mensaje principal.
* Botón para subir fotografías.
* Botón para acceder a la galería.
* Estado de recepción de fotografías.

La interfaz debe ser:

* Elegante.
* Romántica.
* Moderna.
* Limpia.
* Mobile-first.
* Fácil de utilizar desde teléfonos.

---

# 6. Código QR

El administrador tendrá una sección para visualizar y descargar el código QR.

El QR apuntará a:

`${APP_URL}/boda`

El dominio no se almacenará en la base de datos.

La URL deberá construirse utilizando la configuración `APP_URL` de Laravel.

El sistema debe permitir descargar el QR en:

* PNG.
* SVG.

El QR debe apuntar siempre a la página pública `/boda`.

---

# 7. Subida de fotografías

Los invitados podrán subir fotografías sin autenticarse.

La subida estará disponible desde:

`/boda/subir`

Endpoint:

`POST /boda/fotos`

Características:

* Hasta 20 fotografías por solicitud.
* Máximo 20 MB por fotografía.
* Las fotografías se publican inmediatamente.
* No existe estado pendiente.
* No existe aprobación.
* No existe rechazo manual.

Formatos iniciales permitidos:

* JPG.
* JPEG.
* PNG.
* WEBP.

HEIC/HEIF queda fuera de la primera versión y podrá incorporarse posteriormente.

---

# 8. Validación

El backend debe validar realmente el contenido del archivo y no confiar únicamente en la extensión.

Validar:

* MIME real.
* Extensión.
* Tamaño.
* Cantidad de archivos.
* Integridad básica del archivo.
* Tipo de imagen.

Límites:

* Máximo 20 archivos por request.
* Máximo 20 MB por archivo.

Un archivo inválido debe ser rechazado sin afectar a las fotografías válidas cuando sea técnicamente posible.

Los mensajes de error deben ser claros para el usuario.

---

# 9. Estado de recepción

El álbum tendrá un estado:

`upload_enabled`

Valores:

* `true`: recepción abierta.
* `false`: recepción cerrada.

Cuando esté abierta:

* Se pueden subir fotografías.
* Se pueden eliminar fotografías propias.
* La galería funciona.
* La proyección funciona.

Cuando esté cerrada:

* No se pueden subir nuevas fotografías.
* Se pueden eliminar fotografías propias.
* El administrador puede eliminar fotografías.
* La galería continúa funcionando.
* La proyección continúa funcionando.
* Las fotografías existentes continúan disponibles.
* El administrador puede descargar las fotografías.

El bloqueo de recepción debe existir tanto en frontend como en backend.

El frontend no debe ser la única protección.

---

# 10. Cierre de recepción

El administrador podrá abrir o cerrar la recepción desde el panel.

Al cerrar:

* El botón de subida deja de estar disponible.
* El endpoint de subida debe rechazar nuevas solicitudes.
* La galería continúa funcionando.
* La proyección continúa funcionando.
* La eliminación de fotografías propias continúa permitida.
* El administrador continúa teniendo control total.

Al volver a abrir:

* Las subidas vuelven a estar disponibles inmediatamente.

---

# 11. Identificación del invitado

Los invitados no tendrán cuentas.

Para identificar qué fotografías pertenecen a cada navegador se utilizará un `upload_token`.

El token será:

* Aleatorio.
* No predecible.
* Generado por el backend.
* Guardado en una cookie segura.
* Con duración de 30 días.

La cookie debe utilizar:

* `HttpOnly`.
* `SameSite=Lax`.
* `Secure` en producción.

El token no debe basarse únicamente en la dirección IP.

La IP solamente se utilizará para mecanismos de rate limiting y protección contra abuso.

---

# 12. Eliminación de fotografías por el invitado

Un invitado podrá eliminar únicamente fotografías asociadas a su `upload_token`.

Endpoint:

`DELETE /boda/fotos/{photo}`

El backend debe comprobar la propiedad antes de eliminar.

No debe ser posible eliminar una fotografía de otro invitado modificando manualmente el ID de la fotografía.

La validación de propiedad debe realizarse siempre en backend.

La eliminación debe:

1. Eliminar el registro de base de datos.
2. Eliminar el original.
3. Eliminar el thumbnail.
4. Eliminar el preview.
5. Emitir el evento `PhotoDeleted`.

La eliminación de fotografías propias continuará disponible aunque la recepción esté cerrada.

---

# 13. Eliminación de fotografías por el administrador

El administrador podrá eliminar cualquier fotografía desde el panel administrativo.

La acción estará protegida mediante autenticación Jetstream.

El administrador no estará limitado por el `upload_token`.

La eliminación debe borrar:

* Registro.
* Original.
* Thumbnail.
* Preview.

También debe emitirse:

`PhotoDeleted`

para actualizar automáticamente la galería y la proyección.

---

# 14. Galería

Ruta:

`/boda/fotos`

La galería será pública.

Debe mostrar las fotografías disponibles del álbum.

Orden inicial:

* Fotografías más recientes primero.

Debe funcionar correctamente en:

* Teléfono.
* Tablet.
* PC.

La galería se actualizará automáticamente mediante Laravel Reverb.

No será necesario recargar la página para visualizar una fotografía nueva.

---

# 15. Vista individual

Cada fotografía podrá visualizarse individualmente.

La vista debe permitir:

* Visualizar la fotografía.
* Volver a la galería.
* Eliminarla si pertenece al visitante actual.

La autorización para eliminar debe realizarse nuevamente en backend.

---

# 16. Proyección

Ruta pública:

`/boda/proyeccion`

También existirá una ruta administrativa:

`/admin/proyeccion`

La ruta pública no requiere login.

La ruta administrativa requiere autenticación Jetstream.

La proyección estará optimizada para:

* TV.
* Proyector.
* Pantalla 16:9.
* Pantalla completa.

La interfaz debe ser minimalista y evitar elementos innecesarios.

Debe poder funcionar durante períodos prolongados.

---

# 17. Tiempo real

Se utilizará:

* Laravel Reverb.
* Laravel Broadcasting.
* Laravel Events.

Eventos principales:

`PhotoUploaded`

`PhotoDeleted`

Cuando se suba una fotografía:

* Se guarda.
* Se procesa.
* Se emite `PhotoUploaded`.
* Galería y proyección reciben el evento.

Cuando se elimine:

* Se elimina.
* Se emite `PhotoDeleted`.
* Galería y proyección actualizan su contenido.

---

# 18. Slideshow

La configuración inicial será fija.

No se almacenará configuración del slideshow en la base de datos.

Configuración:

* 5 segundos por fotografía.
* Orden inicial: fotografías más recientes primero.
* Transición suave.
* Pantalla completa.
* Sin controles innecesarios para el público.

La configuración podrá modificarse posteriormente mediante código si fuese necesario.

---

# 19. Nuevas fotografías durante la proyección

Si una fotografía nueva llega mientras la proyección está funcionando:

1. El evento `PhotoUploaded` será recibido.
2. La fotografía se incorporará automáticamente.
3. No será necesario recargar la página.
4. La proyección continuará funcionando.

Las fotografías eliminadas deben desaparecer de la proyección mediante `PhotoDeleted`.

---

# 20. Storage

Las fotografías serán almacenadas mediante Laravel Storage.

Estructura conceptual:

```text
storage/app/public/boda/
├── originals/
├── thumbnails/
└── previews/
```

Cada fotografía tendrá nombres basados en UUID.

No se utilizará directamente el nombre original del archivo como nombre físico.

La base de datos almacenará las rutas correspondientes.

---

# 21. Procesamiento de imágenes

Cada fotografía tendrá:

* Original.
* Thumbnail.
* Preview.

El original se conservará para descarga.

Los thumbnails se utilizarán principalmente para la galería.

Los previews se utilizarán para visualización y proyección cuando corresponda.

El procesamiento deberá utilizar Laravel Queue cuando el sistema de colas esté disponible.

Durante desarrollo se permitirá procesamiento síncrono como fallback.

El sistema no debe bloquear innecesariamente la respuesta HTTP durante operaciones pesadas cuando Queue esté correctamente configurada.

---

# 22. Base de datos

## Tabla `albums`

Campos:

```text
id
name
slug
event_date
cover_image
message
primary_color
thank_you_message
upload_enabled
created_at
updated_at
```

`slug` se conservará para organización interna, aunque no se utilizará para generar las URLs públicas.

El sistema tendrá un único álbum.

## Tabla `photos`

Campos:

```text
id
album_id
upload_token
original_path
thumbnail_path
preview_path
original_filename
mime_type
size
created_at
updated_at
```

No existirán campos:

```text
status
approved
rejected
pending
```

No habrá sistema de moderación.

---

# 23. Administración

El panel administrativo utilizará la autenticación existente de Laravel Jetstream.

No se implementará un login personalizado.

El administrador podrá:

* Acceder al dashboard.
* Visualizar fotografías.
* Eliminar fotografías.
* Abrir recepción.
* Cerrar recepción.
* Ver el estado de recepción.
* Ver QR.
* Descargar QR.
* Acceder a la proyección.
* Descargar fotografías originales.
* Ver espacio utilizado.
* Configurar los datos básicos del evento.

Las rutas administrativas deben estar protegidas mediante middleware de autenticación.

---

# 24. Dashboard

El dashboard debe mostrar como mínimo:

* Nombre del evento.
* Fecha.
* Estado de recepción.
* Cantidad de fotografías.
* Espacio utilizado.
* Accesos rápidos a:

  * Galería.
  * Subida.
  * Proyección.
  * QR.
  * Descarga.
  * Configuración.

El espacio utilizado será una estimación basada en los archivos almacenados correspondientes al álbum.

Debe contemplar los archivos reales almacenados:

* Originales.
* Thumbnails.
* Previews.

---

# 25. Configuración del evento

El administrador podrá configurar:

* Nombre del evento / nombres de los novios.
* Fecha.
* Imagen de portada.
* Mensaje principal.
* Color principal.
* Mensaje posterior a la subida.
* Recepción abierta/cerrada.

La configuración se almacenará en el registro `albums`.

No se almacenará el dominio del sitio.

El dominio siempre se obtendrá de:

`APP_URL`

---

# 26. Descarga

El administrador podrá descargar las fotografías originales.

La descarga será mediante un archivo ZIP.

El ZIP deberá contener únicamente los originales.

La descarga estará protegida mediante autenticación administrativa.

Si la cantidad de fotografías es elevada, se podrá utilizar Queue para generar el ZIP.

Los thumbnails y previews no deben incluirse en la descarga final.

---

# 27. Seguridad

El sistema deberá:

* Validar archivos en backend.
* Validar MIME real.
* Limitar tamaño.
* Limitar cantidad.
* Utilizar nombres UUID.
* No confiar en nombres originales.
* Verificar propiedad antes de eliminar.
* Proteger rutas administrativas.
* Aplicar rate limiting.
* No permitir ejecución de archivos subidos.
* Evitar exposición innecesaria de rutas internas.
* Utilizar cookies seguras para el `upload_token`.
* Validar que la fotografía pertenezca al álbum correspondiente.

Nunca se debe confiar únicamente en validaciones realizadas en JavaScript.

---

# 28. Protección contra abuso

Se utilizarán límites de rate limiting.

Configuración inicial:

### Por IP

Máximo:

* 10 solicitudes de subida por minuto por IP.
* 500 fotografías por hora por IP.

### Por solicitud

Máximo:

* 20 fotografías por request.

### Por archivo

Máximo:

* 20 MB.

Estos límites deben aplicarse en backend.

CAPTCHA no será obligatorio inicialmente.

Podrá incorporarse posteriormente si se detecta abuso real.

La dirección IP no será utilizada como mecanismo de propiedad de fotografías.

---

# 29. Privacidad

No se solicitarán datos personales de los invitados.

No será necesario crear cuentas.

El sistema únicamente almacenará la información técnica necesaria para:

* Gestionar las fotografías.
* Identificar al propietario mediante `upload_token`.
* Aplicar límites antiabuso.
* Administrar el álbum.

No se debe mostrar públicamente información técnica innecesaria.

---

# 30. Estados del sistema

## Recepción abierta

```text
Subir fotografías       SI
Eliminar propias       SI
Galería                 SI
Proyección              SI
Eliminar como admin     SI
Descargar como admin    SI
```

## Recepción cerrada

```text
Subir fotografías       NO
Eliminar propias       SI
Galería                 SI
Proyección              SI
Eliminar como admin     SI
Descargar como admin    SI
```

---

# 31. Rutas públicas

```text
GET    /boda
GET    /boda/fotos
GET    /boda/proyeccion
GET    /boda/subir
POST   /boda/fotos
DELETE /boda/fotos/{photo}
```

No se utilizará:

```text
/boda/{slug}
/event/{slug}
/album/{slug}
```

Las URLs públicas serán fijas porque el sistema corresponde a una única boda.

---

# 32. Rutas administrativas

```text
GET    /admin
GET    /admin/fotos
DELETE /admin/fotos/{photo}
POST   /admin/album/toggle-upload
GET    /admin/qr
GET    /admin/proyeccion
GET    /admin/download
```

Las rutas administrativas deben estar protegidas mediante autenticación Jetstream.

Podrán agregarse rutas adicionales para la configuración del álbum si son necesarias.

---

# 33. Experiencia del invitado

El flujo debe ser extremadamente simple.

Desde el teléfono:

```text
QR
 ↓
Página de boda
 ↓
Subir fotos
 ↓
Seleccionar fotografías
 ↓
Subir
 ↓
Confirmación
 ↓
Galería
```

No solicitar:

* Usuario.
* Contraseña.
* Email.
* Registro.

El invitado debe poder completar la operación con la menor cantidad de pasos posible.

---

# 34. Mensaje después de subir

Después de una subida exitosa se mostrará el mensaje configurado en:

`thank_you_message`

El mensaje puede indicar que las fotografías fueron agregadas correctamente.

Debe existir un enlace para:

* Volver a la galería.
* Subir más fotografías mientras la recepción esté abierta.

---

# 35. Diseño

## Público

Estilo:

* Romántico.
* Elegante.
* Moderno.
* Minimalista.
* Fotográfico.

La fotografía debe ser el elemento principal.

Evitar interfaces recargadas.

## Administración

Estilo:

* Funcional.
* Claro.
* Técnico.
* Orientado a gestión.

Flowbite puede utilizarse para componentes administrativos cuando facilite la implementación.

No es obligatorio utilizar Flowbite en toda la interfaz pública.

---

# 36. Responsive

El sistema debe funcionar correctamente en:

* Smartphones.
* Tablets.
* Notebooks.
* PCs.
* TV/proyector.

La prioridad para la parte pública será mobile-first.

La proyección debe priorizar pantallas grandes y formato 16:9.

---

# 37. Fuera de alcance

No implementar:

* Multi-boda.
* Multi-tenant.
* SaaS.
* Suscripciones.
* Pagos.
* Facturación.
* Registro de invitados.
* Login de invitados.
* Aprobación de fotografías.
* Moderación.
* Sistema de comentarios.
* Likes.
* Reacciones.
* Chat.
* Integración con redes sociales.
* Sistema de invitaciones complejo.
* Gestión de múltiples eventos.

Estas funcionalidades podrán evaluarse en otro proyecto, pero no forman parte de esta versión.

---

# 38. Criterios de aceptación

El sistema será considerado funcional cuando:

1. `/boda` sea accesible públicamente.
2. El visitante pueda visualizar la información del evento.
3. El visitante pueda acceder a la galería.
4. El visitante pueda subir fotografías sin registrarse.
5. Las fotografías válidas se publiquen inmediatamente.
6. No exista aprobación manual.
7. Los archivos inválidos sean rechazados.
8. Se respete el límite de 20 MB.
9. Se respete el límite de 20 fotografías por request.
10. Se aplique rate limiting.
11. Se genere automáticamente un `upload_token`.
12. El invitado pueda eliminar únicamente sus fotografías.
13. Un invitado no pueda eliminar fotografías de otro invitado.
14. El administrador pueda eliminar cualquier fotografía.
15. El administrador pueda abrir y cerrar la recepción.
16. El backend bloquee las subidas cuando la recepción esté cerrada.
17. El invitado pueda eliminar sus fotografías aunque la recepción esté cerrada.
18. La galería continúe funcionando con la recepción cerrada.
19. La proyección continúe funcionando con la recepción cerrada.
20. La galería se actualice mediante Reverb.
21. La proyección se actualice mediante Reverb.
22. La eliminación se refleje en tiempo real.
23. El QR apunte correctamente a `${APP_URL}/boda`.
24. El administrador pueda descargar el QR.
25. El administrador pueda descargar los originales en ZIP.
26. El dashboard muestre cantidad de fotografías.
27. El dashboard muestre espacio utilizado.
28. La autenticación administrativa utilice Jetstream.
29. Las rutas administrativas estén protegidas.
30. Los originales, thumbnails y previews sean gestionados correctamente.
31. El slideshow funcione con 5 segundos por fotografía.
32. El sistema sea usable desde teléfonos.
33. El sistema sea usable desde una pantalla de proyección.

---

# 39. Pruebas

Se deberán crear pruebas automatizadas para las funcionalidades críticas.

Como mínimo:

## Acceso

* Acceso público a `/boda`.
* Acceso público a galería.
* Acceso público a proyección.
* Acceso autenticado al administrador.
* Bloqueo de rutas administrativas sin autenticación.

## Subidas

* Subida válida.
* Formato inválido.
* Archivo superior a 20 MB.
* Más de 20 archivos en una solicitud.
* Recepción cerrada.
* Generación del `upload_token`.

## Propiedad

* Eliminación de fotografía propia.
* Intento de eliminación de fotografía ajena.
* Eliminación administrativa.

## Recepción

* Apertura.
* Cierre.
* Bloqueo backend de nuevas subidas.
* Eliminación propia con recepción cerrada.
* Galería con recepción cerrada.
* Proyección con recepción cerrada.

## Tiempo real

* `PhotoUploaded`.
* `PhotoDeleted`.

## QR

* Utilización correcta de `APP_URL`.

## Descarga

* Descarga de originales.
* Exclusión de thumbnails/previews del ZIP.

## Storage

* Creación de original.
* Creación de thumbnail.
* Creación de preview.
* Eliminación correcta de archivos.

## Seguridad

* Rate limiting.
* Validación MIME.
* Validación de propiedad.

---

# 40. Prioridad de desarrollo

## Fase 1 — Verificación del proyecto existente

* Verificar Laravel 13.
* Verificar Jetstream.
* Verificar Inertia.
* Verificar Vue.
* Verificar Tailwind.
* Verificar SQLite.
* Verificar configuración de Storage.
* Verificar Reverb.
* Verificar Queue.

No crear un proyecto Laravel nuevo si el repositorio existente ya contiene la aplicación.

## Fase 2 — Base de datos

* Crear migración `albums`.
* Crear migración `photos`.
* Crear modelos.
* Crear relaciones.

## Fase 3 — Administración

* Utilizar autenticación Jetstream existente.
* Crear dashboard.
* Configuración del evento.
* Control de recepción.
* Gestión de fotografías.
* QR.
* Descarga.

## Fase 4 — Parte pública

* Página `/boda`.
* Galería.
* Upload.
* Mensaje posterior.
* Eliminación propia.

## Fase 5 — Storage e imágenes

* Originales.
* Thumbnails.
* Previews.
* Procesamiento.
* Queue/fallback síncrono.

## Fase 6 — Tiempo real

* Configurar Reverb.
* `PhotoUploaded`.
* `PhotoDeleted`.
* Actualización de galería.
* Actualización de proyección.

## Fase 7 — Proyección

* Vista 16:9.
* Fullscreen.
* Slideshow.
* Nuevas fotografías en tiempo real.
* Eliminaciones en tiempo real.

## Fase 8 — Seguridad

* Rate limiting.
* Validaciones.
* Ownership.
* Protección administrativa.

## Fase 9 — QR y descarga

* Generación QR.
* PNG.
* SVG.
* ZIP de originales.

## Fase 10 — Pruebas

* Ejecutar tests.
* Corregir errores.
* Verificar flujos completos.
* Probar desde dispositivos móviles.

---

# 41. Principios de desarrollo

El proyecto debe mantenerse simple.

No implementar arquitectura innecesariamente compleja.

Priorizar:

* Código claro.
* Código mantenible.
* Seguridad.
* Experiencia de usuario.
* Performance.
* Validaciones backend.
* Componentes reutilizables cuando realmente aporten valor.

No agregar funcionalidades fuera del alcance.

No implementar un sistema multi-tenant.

No implementar un sistema de aprobación.

No crear autenticación personalizada.

Utilizar las funcionalidades existentes de Laravel y Jetstream siempre que sea posible.

El proyecto ya existe, por lo que las decisiones deben adaptarse al repositorio actual en lugar de recrearlo desde cero.

---

# 42. Flujo final

```text
INVITADO
   │
   ▼
ESCANEA QR
   │
   ▼
/boda
   │
   ├──────────────► GALERÍA
   │
   └──────────────► SUBIR FOTOS
                       │
                       ▼
                 VALIDACIÓN
                       │
                       ▼
                  STORAGE
                       │
                       ▼
                 PROCESAMIENTO
                       │
                       ▼
                PhotoUploaded
                       │
              ┌────────┴────────┐
              ▼                 ▼
           GALERÍA          PROYECCIÓN
              │                 │
              └───────┬─────────┘
                      ▼
                VISUALIZACIÓN
```

Administración:

```text
ADMIN
 │
 ▼
LOGIN JETSTREAM
 │
 ▼
/admin
 │
 ├── Dashboard
 ├── Fotografías
 ├── Configuración
 ├── Abrir/Cerrar recepción
 ├── QR
 ├── Proyección
 └── Descargar ZIP
```

---

# 43. Resultado esperado

El resultado final debe ser un álbum digital de boda funcional, simple y robusto.

El invitado debe poder:

```text
Escanear QR
→ entrar
→ subir fotos
→ verlas publicadas inmediatamente
→ continuar viendo nuevas fotos en tiempo real
→ eliminar sus propias fotos
```

El administrador debe poder:

```text
Iniciar sesión
→ administrar el álbum
→ ver fotografías
→ eliminar fotografías
→ abrir/cerrar recepción
→ ver proyección
→ generar/descargar QR
→ descargar originales
→ consultar espacio utilizado
```

La solución debe funcionar sin necesidad de intervención manual sobre cada fotografía.

No debe existir aprobación previa.

La publicación de fotografías debe ser inmediata.

La recepción puede cerrarse sin afectar la visualización de las fotografías existentes.

La galería y la proyección deben actualizarse en tiempo real mediante Laravel Reverb.

El sistema debe permanecer enfocado exclusivamente en la experiencia del álbum digital de esta boda.
