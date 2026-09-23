# SDD — ÁLBUM DIGITAL DE FOTOS PARA BODA

## 1. Objetivo del proyecto

Desarrollar una aplicación web para una única boda que permita a los invitados compartir fotografías desde sus teléfonos celulares mediante un código QR.

El sistema debe permitir:

1. Acceder al álbum mediante QR.
2. Subir fotografías sin registrarse.
3. Publicar las fotografías inmediatamente después de subirlas.
4. Visualizar las fotografías del evento.
5. Permitir al invitado eliminar las fotografías que haya subido.
6. Permitir al administrador eliminar cualquier fotografía.
7. Mostrar fotografías en una pantalla de proyección.
8. Actualizar la galería/proyección en tiempo real cuando llegan nuevas fotografías.
9. Permitir cerrar y volver a habilitar la recepción de fotografías.
10. Mantener la galería disponible aunque la recepción de fotografías esté cerrada.

El sistema será utilizado exclusivamente para una boda.

---

# 2. Stack tecnológico

## Backend

* Laravel 11/12
* PHP 8.2+
* SQLite inicialmente
* Laravel Storage
* Laravel Queue cuando sea necesario
* Laravel Reverb para tiempo real

## Frontend

* Vue 3
* Inertia.js
* Tailwind CSS
* Flowbite opcional

## QR

Utilizar una librería compatible con Laravel/PHP para generar el código QR.

---

# 3. Alcance

El sistema tendrá un único álbum correspondiente a una única boda.

NO implementar:

* multiempresa
* múltiples clientes
* SaaS
* suscripciones
* pagos
* facturación
* planes
* tenants
* aplicación móvil nativa
* cuentas para invitados

La aplicación debe mantenerse simple y enfocada exclusivamente en el evento.

---

# 4. Flujo general

```text
Invitado
   ↓
Escanea QR
   ↓
Página de la boda
   ↓
┌───────────────┬──────────────┐
│ Subir fotos   │ Ver álbum    │
└───────────────┴──────────────┘
        ↓
Selecciona fotos
        ↓
Sube
        ↓
Publicación inmediata
        ↓
Galería / Proyección
```

No se debe solicitar al invitado:

* registro
* email
* contraseña
* cuenta
* nombre obligatorio

---

# 5. Página pública

URL conceptual:

```text
/boda
```

Debe mostrar:

* nombre de los novios
* fecha de la boda
* imagen de portada opcional
* mensaje
* botón "Subir fotos"
* botón "Ver álbum"

Ejemplo:

```text
CRISTIAN & XXXXX

26 DE SEPTIEMBRE DE 2026

Compartí las fotos de este día especial

[ 📷 SUBIR FOTOS ]

[ 🖼️ VER ÁLBUM ]
```

Diseño:

* elegante
* romántico
* moderno
* responsive
* mobile-first
* sencillo de utilizar

---

# 6. Código QR

El administrador debe poder visualizar y descargar el QR.

El QR debe apuntar a:

```text
https://dominio.com/boda
```

Debe poder descargarse preferentemente como:

* PNG
* SVG

El QR podrá imprimirse en:

* mesas
* carteles
* tarjetas
* invitaciones

---

# 7. Subida de fotografías

Los invitados deben poder seleccionar fotografías desde sus teléfonos.

Debe permitirse:

* seleccionar una fotografía
* seleccionar múltiples fotografías
* acceder a la galería del dispositivo
* utilizar la cámara cuando el navegador lo permita

No se debe requerir autenticación.

## Publicación inmediata

Una vez que la fotografía pasa las validaciones:

```text
Seleccionar
    ↓
Subir
    ↓
Validar
    ↓
Guardar
    ↓
PUBLICAR INMEDIATAMENTE
```

No debe existir un estado:

```text
pending
approved
rejected
```

No habrá sistema de aprobación previa.

---

# 8. Validación de fotografías

El backend debe validar obligatoriamente:

* MIME
* extensión
* tamaño máximo
* contenido real del archivo
* cantidad máxima de archivos por petición

Formatos iniciales:

```text
jpg
jpeg
png
webp
```

HEIC/HEIF puede incorporarse posteriormente si se considera necesario.

No confiar exclusivamente en la extensión proporcionada por el navegador.

---

# 9. Estado de recepción

El álbum tendrá una configuración:

```text
upload_enabled
```

Valores:

```text
true
false
```

## Recepción abierta

Cuando:

```text
upload_enabled = true
```

La página pública muestra:

```text
[ 📷 SUBIR FOTOS ]
```

Los endpoints de subida están habilitados.

## Recepción cerrada

Cuando:

```text
upload_enabled = false
```

La página pública NO muestra el botón de subida.

Además, el backend debe rechazar cualquier intento directo de subida.

No debe depender únicamente de ocultar el botón.

La galería continuará funcionando.

---

# 10. Cierre de recepción

Desde el panel administrativo:

```text
[ 🔴 CERRAR RECEPCIÓN ]
```

Al cerrar:

```text
upload_enabled = false
```

El sistema debe mostrar:

```text
Recepción de fotos cerrada
```

Pero deben continuar disponibles:

* galería
* fotografías existentes
* proyección
* descarga
* visualización

Debe existir:

```text
[ 🟢 VOLVER A HABILITAR ]
```

para permitir nuevamente las cargas.

---

# 11. Identificación de las fotos del invitado

Como los invitados no tendrán cuentas, el sistema debe utilizar un identificador temporal para determinar qué fotografías pertenecen a cada sesión de invitado.

Ejemplo:

```text
Invitado
   ↓
Entra al álbum
   ↓
Laravel genera upload_token
   ↓
Sube fotografías
   ↓
Las fotografías quedan asociadas a ese token
```

El token puede almacenarse en una cookie/sesión segura del navegador.

No se debe utilizar la dirección IP como identificador principal.

---

# 12. Eliminación por parte del invitado

El invitado debe poder eliminar las fotografías que haya subido desde su propia sesión.

Ejemplo:

```text
¡Fotos subidas!

┌────────┐ ┌────────┐ ┌────────┐
│ FOTO 1 │ │ FOTO 2 │ │ FOTO 3 │
│        │ │        │ │        │
│  🗑️    │ │  🗑️    │ │  🗑️    │
└────────┘ └────────┘ └────────┘

[ SUBIR MÁS FOTOS ]
```

Cuando el invitado elimina una foto:

```text
DELETE /boda/fotos/{photo}
```

El backend debe comprobar que esa fotografía pertenece al `upload_token` de la sesión actual.

Un invitado NO debe poder eliminar fotografías pertenecientes a otra persona simplemente modificando el ID de la URL.

---

# 13. Eliminación administrativa

El administrador debe poder eliminar cualquier fotografía desde el panel.

Ejemplo:

```text
ADMIN

Galería

[ FOTO ] [ FOTO ] [ FOTO ]
    🗑️       🗑️       🗑️
```

La eliminación administrativa no depende del `upload_token`.

---

# 14. Galería

URL conceptual:

```text
/boda/fotos
```

Debe mostrar todas las fotografías publicadas.

Como no existe moderación:

```text
Foto subida
    ↓
Publicada
    ↓
Galería
```

La galería debe incluir:

* grid/masonry
* thumbnails
* lazy loading
* visualización en tamaño completo
* navegación entre fotografías

Las fotografías originales no deben utilizarse directamente para cargar toda la galería cuando existan thumbnails.

---

# 15. Visualización individual

Al seleccionar una fotografía:

```text
┌───────────────────────────────┐
│                               │
│            FOTO               │
│                               │
│                               │
│    ‹                    ›     │
│                               │
└───────────────────────────────┘
```

Debe permitir:

* ampliar
* siguiente
* anterior
* cerrar

En dispositivos móviles debe funcionar mediante gestos cuando sea posible.

---

# 16. Proyección

Crear una página especial:

```text
/boda/proyeccion
```

Esta página se utilizará en una computadora conectada a:

* TV
* proyector
* pantalla

Debe estar optimizada para:

* 16:9
* pantalla completa
* resolución Full HD
* funcionamiento continuo

Las fotografías nuevas deben incorporarse automáticamente.

---

# 17. Actualización en tiempo real

Utilizar:

* Laravel Reverb
* Broadcasting
* WebSockets

Flujo:

```text
Invitado sube foto
        ↓
Laravel guarda foto
        ↓
Evento PhotoUploaded
        ↓
Reverb
        ↓
Galería / Proyección
        ↓
Nueva foto aparece automáticamente
```

No debe ser necesario actualizar manualmente la página.

---

# 18. Slideshow

La página de proyección debe poder funcionar como presentación automática.

Ejemplo:

```text
Foto 1
 ↓
Foto 2
 ↓
Foto 3
 ↓
Foto 4
```

Configurable:

* tiempo entre fotografías
* transición
* orden

Opcionalmente:

```text
Más recientes primero
```

o:

```text
Aleatorio
```

---

# 19. Comportamiento de nuevas fotografías durante la proyección

Si la proyección está funcionando y llega una fotografía nueva:

```text
Proyección
     ↓
Nueva foto
     ↓
WebSocket
     ↓
La fotografía se incorpora automáticamente
```

No debe ser necesario recargar la página.

---

# 20. Almacenamiento

No almacenar imágenes directamente en la base de datos.

Utilizar Laravel Storage.

Estructura conceptual:

```text
storage/
└── app/
    └── public/
        └── boda/
            ├── originals/
            ├── thumbnails/
            └── previews/
```

Ejemplo:

```text
originals/
    UUID.jpg

thumbnails/
    UUID.webp

previews/
    UUID.webp
```

Los nombres deben generarse automáticamente.

No utilizar directamente el nombre original proporcionado por el usuario como nombre físico del archivo.

---

# 21. Procesamiento de imágenes

Al recibir una fotografía:

```text
Archivo original
       ↓
Validación
       ↓
Guardar original
       ↓
Generar thumbnail
       ↓
Generar preview
       ↓
Guardar información
       ↓
Publicar
```

Los thumbnails deben utilizarse para la galería.

Los previews deben utilizarse para visualización cuando corresponda.

El original se conserva para descarga.

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
upload_enabled
created_at
updated_at
```

Aunque solamente exista una boda, mantener `albums` permite una estructura ordenada.

---

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

No incluir:

```text
status
approved
rejected
```

porque no existe moderación.

---

# 23. Administración

Zona protegida:

```text
/admin
```

Debe requerir autenticación.

El administrador debe poder:

* ver cantidad de fotografías
* ver galería
* eliminar cualquier fotografía
* abrir/cerrar recepción
* visualizar QR
* descargar QR
* acceder a proyección
* descargar fotografías
* configurar información de la boda

---

# 24. Dashboard

Ejemplo:

```text
ÁLBUM DE BODA

Recepción:
🟢 ABIERTA

Fotos:
247

Espacio utilizado:
3.8 GB


[ VER GALERÍA ]

[ CERRAR RECEPCIÓN ]

[ PROYECCIÓN ]

[ MOSTRAR QR ]

[ DESCARGAR FOTOS ]
```

Cuando se cierre:

```text
Recepción:
🔴 CERRADA

[ VOLVER A HABILITAR ]
```

---

# 25. Configuración del evento

El administrador podrá modificar:

```text
Nombre de los novios
Fecha
Mensaje principal
Imagen de portada
Color principal
Mensaje de agradecimiento
```

Ejemplo:

```text
Cristian & XXXXX

26 de septiembre de 2026

Gracias por ser parte de este día tan especial.
Compartí tus mejores momentos con nosotros.
```

---

# 26. Descarga

Debe existir una opción:

```text
[ DESCARGAR TODAS LAS FOTOS ]
```

Debe descargar las fotografías originales.

Si son muchas fotografías, utilizar un Job/Queue para crear el ZIP sin bloquear la petición web.

Ejemplo:

```text
boda-cristian-xxxx-fotos.zip
```

---

# 27. Seguridad

Aunque el álbum sea público, se deben aplicar medidas básicas.

## Archivos

Validar:

* MIME real
* extensión
* tamaño
* contenido
* cantidad de archivos

Generar nombres aleatorios/UUID.

Nunca confiar en:

```text
filename
```

enviado por el navegador.

---

# 28. Protección contra abuso

Implementar:

* rate limiting
* límite de tamaño
* límite de cantidad de archivos por petición
* validación MIME
* nombres aleatorios
* bloqueo de extensiones peligrosas

Opcionalmente:

* CAPTCHA
* límite de almacenamiento
* límite de uploads por sesión

El sistema debe ser sencillo, pero no debe permitir que una persona pueda enviar miles de archivos sin restricciones.

---

# 29. Privacidad

Las fotografías serán accesibles públicamente durante el funcionamiento del álbum.

El sistema no debe publicar:

* nombres
* emails
* direcciones IP
* información personal

salvo la información mínima necesaria para el funcionamiento técnico.

El `upload_token` debe considerarse privado y no debe mostrarse públicamente.

---

# 30. Estados del sistema

## Recepción abierta

```text
🟢 ABIERTA

Subir fotos:       ✅
Ver álbum:         ✅
Eliminar propias:  ✅
Proyección:        ✅
Administrador:     ✅
```

## Recepción cerrada

```text
🔴 CERRADA

Subir fotos:       ❌
Eliminar propias:  ❌
Ver álbum:         ✅
Proyección:        ✅
Administrador:     ✅
Descarga:          ✅
```

---

# 31. Rutas públicas conceptuales

```text
GET  /boda
GET  /boda/fotos
GET  /boda/proyeccion

GET  /boda/subir
POST /boda/fotos

DELETE /boda/fotos/{photo}
```

El `DELETE` debe verificar que la fotografía pertenece al `upload_token` de la sesión.

---

# 32. Rutas administrativas conceptuales

```text
GET  /admin
GET  /admin/fotos

DELETE /admin/fotos/{photo}

POST /admin/album/toggle-upload

GET  /admin/qr
GET  /admin/proyeccion
GET  /admin/download
```

Las rutas reales pueden modificarse según la arquitectura final.

---

# 33. Experiencia del invitado

La prioridad absoluta es que sea sencillo.

Flujo ideal:

```text
1. Escanea QR

2. Ve la página de la boda

3. Toca "Subir fotos"

4. Selecciona fotografías

5. Toca "Subir"

6. Las fotografías aparecen inmediatamente

7. Puede eliminar alguna si se equivocó

8. Puede seguir subiendo más
```

No debe haber:

* login
* registro
* aprobación
* formularios largos
* confirmaciones innecesarias

---

# 34. Mensaje después de subir

Ejemplo:

```text
¡Fotos subidas! ❤️

Tus fotos ya forman parte del álbum.

[ VER ÁLBUM ]

[ SUBIR MÁS FOTOS ]
```

También debe mostrarse la cantidad de fotografías subidas en esa sesión.

Ejemplo:

```text
Has subido 5 fotos.
```

---

# 35. Diseño

## Invitados

Diseño:

* elegante
* romántico
* limpio
* moderno
* mobile-first

Debe sentirse como una página de la boda y no como una aplicación administrativa.

## Administración

Puede utilizar una interfaz más técnica y funcional.

---

# 36. Responsive

Debe funcionar correctamente en:

* Android
* iPhone
* tablet
* notebook
* TV

La interfaz de invitados debe priorizar celulares.

La proyección debe priorizar pantallas 16:9.

---

# 37. Funcionalidades fuera de alcance

No implementar:

* aprobación de fotografías
* moderación previa
* estados pending/approved/rejected
* multiempresa
* múltiples clientes
* SaaS
* pagos
* suscripciones
* facturación
* aplicación móvil nativa
* cuentas de invitados
* login de invitados
* comentarios
* chat
* red social
* sistema de reservas

---

# 38. Criterios de aceptación

## Caso 1 — Acceso

Un invitado escanea el QR y accede al álbum sin registrarse.

## Caso 2 — Subida

Un invitado selecciona varias fotografías y las sube.

Las fotografías quedan publicadas inmediatamente.

## Caso 3 — Visualización

Las fotografías aparecen en la galería.

## Caso 4 — Eliminación propia

El invitado puede eliminar una fotografía que haya subido desde su sesión.

## Caso 5 — Seguridad

Un invitado no puede eliminar una fotografía perteneciente a otra sesión modificando manualmente el ID de la URL.

## Caso 6 — Administración

El administrador puede eliminar cualquier fotografía.

## Caso 7 — Cierre

El administrador puede cerrar la recepción.

## Caso 8 — Bloqueo real

Con la recepción cerrada, ningún invitado puede subir nuevas fotografías aunque conozca directamente el endpoint.

## Caso 9 — Galería después del cierre

Las fotografías existentes continúan visibles.

## Caso 10 — Proyección

La proyección continúa funcionando después del cierre.

## Caso 11 — Tiempo real

Una fotografía nueva aparece automáticamente en la galería/proyección sin recargar la página.

## Caso 12 — Reactivación

El administrador puede volver a habilitar la recepción.

## Caso 13 — Descarga

El administrador puede descargar las fotografías originales.

---

# 39. Prioridad de desarrollo

## Fase 1 — Base

* Crear proyecto Laravel
* Configurar SQLite
* Crear Album
* Crear Photo
* Migraciones
* Seed inicial
* Autenticación administrativa

## Fase 2 — Página pública

* Página de boda
* Diseño responsive
* Galería
* Página de subida

## Fase 3 — Subida

* Selección múltiple
* Validación
* Storage
* generación de UUID
* publicación inmediata
* upload_token

## Fase 4 — Eliminación

* Eliminar fotos propias
* Validación mediante upload_token
* Eliminación administrativa

## Fase 5 — Cierre

* Abrir recepción
* Cerrar recepción
* Bloqueo real del endpoint

## Fase 6 — Procesamiento

* thumbnails
* previews
* optimización

## Fase 7 — QR

* generación
* visualización
* descarga PNG/SVG

## Fase 8 — Tiempo real

* Laravel Reverb
* Events
* Broadcasting
* actualización automática

## Fase 9 — Proyección

* 16:9
* fullscreen
* slideshow
* nuevas fotografías en tiempo real

## Fase 10 — Descarga y pruebas

* ZIP
* seguridad
* rate limiting
* pruebas de carga
* responsive
* optimización

---

# 40. Principio de desarrollo

No sobreingenierizar.

Es una aplicación para una única boda.

Prioridad:

1. facilidad de uso
2. funcionamiento
3. seguridad
4. velocidad
5. diseño

El invitado debe poder utilizar el sistema sin instrucciones técnicas.

El administrador debe poder controlar el álbum con la mínima cantidad de acciones.

---

# 41. Flujo final

```text
                         QR
                          │
                          ▼
                  ┌───────────────┐
                  │ Página de boda│
                  └───────┬───────┘
                          │
                 ┌────────┴────────┐
                 ▼                 ▼
            SUBIR FOTOS        VER ÁLBUM
                 │                 │
                 ▼                 │
          Seleccionar fotos        │
                 │                 │
                 ▼                 │
              Laravel              │
                 │                 │
                 ▼                 │
         Publicación inmediata     │
                 │                 │
                 ├────────────┐    │
                 │            │    │
                 ▼            ▼    ▼
            Mis fotos      Galería
                 │            │
                 │            ▼
                 │       Proyección
                 │            │
                 ▼            │
             🗑️ Eliminar      │
             propias          │
                              │
                              ▼
                         Tiempo real


ADMINISTRADOR
      │
      ├── Abrir/Cerrar recepción
      ├── Eliminar cualquier foto
      ├── Ver QR
      ├── Ver galería
      ├── Proyección
      └── Descargar fotos
```

# 42. Resultado esperado

El resultado final debe ser una aplicación Laravel independiente que permita que los invitados:

**escaneen → suban → publiquen → vean → eliminen sus propias fotos**, sin registrarse.

El administrador debe poder:

**abrir/cerrar la recepción → eliminar cualquier foto → proyectar → descargar el álbum.**

La recepción de fotografías y la visualización del álbum deben ser funcionalidades independientes: **cerrar la recepción nunca debe cerrar ni eliminar la galería existente.**
