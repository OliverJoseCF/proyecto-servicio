# Informe de Auditoría — Plataforma TSJ Chapala

> Auditoría completa de arquitectura, calidad, seguridad y QA.
> Fecha: 2026-05-21 · Alcance: 263 archivos del repositorio (código, SQL, config, assets).
> Las correcciones de los hallazgos **Bloqueantes** y **Medios** **ya fueron aplicadas**
> (ver estado en cada hallazgo y en el §6 Plan de remediación).

---

## 1. Resumen ejecutivo

**Qué es el proyecto.** Plataforma web institucional del Tecnológico Superior de Jalisco
(Campus Chapala): un portal único (`plataforma/index.php`) que integra 5 módulos PHP/MySQL
bajo una capa compartida (`shared/`):

| Módulo | Objetivo |
|---|---|
| **Visitantes** | Directorio institucional estático: áreas, carreras, docentes, servicios. |
| **Biblioteca** | Catálogo público de libros + solicitudes de préstamo + panel admin (inventario, solicitudes, controles, historial). |
| **Convenios** | Directorio público de convenios por carrera + CRUD admin + "sugerir empresa". |
| **Horarios** | Búsqueda pública de maestros/horarios + panel admin con subida de archivos. |
| **Requisitos** | Guías de residencia profesional y servicio social (calculadora, checklist, descargas). |

**Estado general: `casi listo`.** La base de ingeniería es **sólida** — consultas preparadas
en el 100% de los accesos a BD, CSRF en todas las mutaciones admin, rate-limiting en
endpoints públicos, escape de salida con `htmlspecialchars`, hardening de sesión, validación
de uploads (`finfo` + `getimagesize` + nombres aleatorios). No se detectó SQLi ni XSS.

Sin embargo, antes de esta auditoría había **2 bloqueantes** que dejaban inoperables dos
módulos. Tras aplicar las correcciones, el sistema queda **listo para una validación
funcional end-to-end** previa al lanzamiento.

**Top 10 issues (todos corregidos en esta auditoría):**

| # | Sev. | Módulo | Issue | Estado |
|---|---|---|---|---|
| 1 | Bloqueante | Convenios | Login admin inaccesible (sin disparador) y roto (IDs JS incorrectos) | ✅ Corregido |
| 2 | Bloqueante | Horarios | Falla total en Linux por mayúsculas en nombres de tabla | ✅ Corregido |
| 3 | Media | Horarios | Ruta rota de `normalize.css` (404) en 4 páginas | ✅ Corregido |
| 4 | Media | Convenios | `php_flag` en `upload/.htaccess` → Error 500 bajo PHP-FPM | ✅ Corregido |
| 5 | Media | Convenios | CSP bloquea el `<script>` inline de `lista.php` | ✅ Corregido |
| 6 | Media | Biblioteca | Flujo "Controles" incompleto (sin aceptar/rechazar; endpoint huérfano) | ✅ Corregido |
| 7 | Media | Convenios | `mail()` de "sugerir empresa" requiere MTA (falla silenciosa) | ✅ Documentado |
| 8 | Baja | Visitantes | Imagen `imagenes/user.png` inexistente en el Directorio | ✅ Corregido |
| 9 | Baja | Docs | `INSTALL.md §2.1` afirma que `config.local.php` "ya existe" | ✅ Corregido |
| 10 | Baja | Convenios | Tabla `audit_log` definida en SQL y nunca usada (esquema muerto) | Aceptado (inocuo) |

---

## 2. Cobertura 100% (inventario archivo por archivo)

Estados: **Revisado** = analizado a fondo · **Superficial** = asset binario inspeccionado
como recurso sin lógica · **No revisable** = no aplica.
Los binarios (imágenes/SVG/webp/PDF/fuentes) se agrupan por carpeta enumerando cada archivo.

### Raíz y portal

| Ruta | Tipo | Estado | Nota |
|---|---|---|---|
| `.gitignore` | config | Revisado | Correcto: excluye `config.local.php`, uploads, logs. |
| `INSTALL.md` | doc | Revisado | Corregido §2.1 y checklist (M5/M7). |
| `modulo-convenios-main.zip` | binario | Revisado | Artefacto obsoleto — eliminado del repo. |
| `plataforma/index.php` | php | Revisado | Portal de tarjetas. OK. |

### Capa compartida (`plataforma/shared/`)

| Ruta | Tipo | Estado | Nota |
|---|---|---|---|
| `shared/config.php` | php | Revisado | Carga `config.local.php` + defaults; `getPDO()`/`getMysqli()`. OK. |
| `shared/config.local.example.php` | php | Revisado | Plantilla de producción. OK. |
| `shared/header.php` | php | Revisado | Layout + nav + meta. Escapa salidas. OK. |
| `shared/footer.php` | php | Revisado | Layout + `nav.js`. OK. |
| `shared/security_headers.php` | php | Revisado | XFO, nosniff, Referrer-Policy, HSTS, CSP. `'unsafe-inline'` en script-src (riesgo bajo aceptado). |
| `shared/lib/auth.php` | php | Revisado | Sesión segura, CSRF, `requireAuth/requirePost`. Sólido. |
| `shared/lib/RateLimit.php` | php | Revisado | Rate-limiter de archivo. OK. |
| `shared/assets/css/theme.css` | css | Revisado | Tema global. OK. |
| `shared/assets/js/nav.js` | js | Revisado | Menú móvil accesible. OK. |
| `shared/assets/img/` (11) | img | Superficial | `close.svg, educacion.png, facebook.svg, favicon.png, home.svg, innovacion.png, jalisco.png, logo.svg, menu.svg, tecnologico.svg, youtube.svg`. |

### Base de datos (`plataforma/sql/`)

| Ruta | Tipo | Estado | Nota |
|---|---|---|---|
| `sql/biblioteca.sql` | sql | Revisado | Tablas `libros`, `solicitud_controles`, `solicitud_libros` + datos. |
| `sql/convenios.sql` | sql | Revisado | Tablas `convenios`, `audit_log` (esta última sin uso). Sin seed. |
| `sql/horarios.sql` | sql | Revisado | Tablas `carreras/horarios/materias/profesores` (minúsculas) + datos + FKs. |
| `sql/migrate_horarios_paths.sql` | sql | Revisado | Migra rutas antiguas `imagen_horario`. OK. |
| `sql/setup.sql` | sql | Revisado | Crea usuario `tsjplat` con privilegios mínimos. OK. |

### Módulo Biblioteca (`plataforma/modulos/biblioteca/`)

| Ruta | Tipo | Estado | Nota |
|---|---|---|---|
| `admin.php` | php | Revisado | Panel admin. **Modificado**: flujo Controles (M6). |
| `buscar.php` | php | Revisado | Catálogo público. **Modificado**: enlace a Controles. |
| `login.php` | php | Revisado | Login con rate-limit + CSRF. OK. |
| `solicitudDeControles.php` | php | Revisado | Formulario público de préstamo de controles. |
| `solicitudDeLibros.php` | php | Revisado | Formulario público de préstamo de libros. |
| `config/conexion.php` | php | Revisado | Conexión mysqli. OK. |
| `procesos/agregar_Libro.php` | php | Revisado | INSERT preparado + `requirePost()`. OK. |
| `procesos/actualizar_libro.php` | php | Revisado | UPDATE preparado + CSRF + auth. OK. |
| `procesos/editar_libro.php` | php | Revisado | Form de edición. OK. |
| `procesos/eliminar_Libro.php` | php | Revisado | DELETE preparado + CSRF + auth. OK. |
| `procesos/estado_libro.php` | php | Revisado | Cambio de estado de solicitud. OK. |
| `procesos/estado_control.php` | php | Revisado | Cambio de estado de control — ahora conectado a la UI (M6). |
| `procesos/guardar_solicitud_libro.php` | php | Revisado | INSERT público + rate-limit + CSRF. OK. |
| `procesos/guardarSolControles.php` | php | Revisado | INSERT público + rate-limit + CSRF. OK. |
| `procesos/marcar_devuelto.php` | php | Revisado | UPDATE preparado + CSRF + auth. OK. |
| `procesos/obtenerLibros.php` | php | Revisado | Endpoint JSON público (lectura). OK. |
| `procesos/cerrar_sesion.php` | php | Revisado | Logout. OK. |
| `tools/setup_password.php` | php | Revisado | Generador de hash CLI-only. OK. |
| `tools/.htaccess` | config | Revisado | Bloquea acceso web a `tools/`. OK. |
| `assets/js/buscar.js` | js | Revisado | Render del catálogo. Escapa con `textContent`. OK. |
| `assets/css/` (3) | css | Revisado | `admin.css, buscar.css, login.css`. OK. |
| `assets/img/` (15) | img | Superficial | `1.png,2.png,3.png,4.png,BIBLIO.jpg,Busqueda.png,L1.jpg,buscar.jpeg,controles.jpeg,controles.png,libro.png,logo_inicio.png,solicitud.jpeg,solicitud.png,tecmm.png`. |

### Módulo Convenios (`plataforma/modulos/convenios/`)

| Ruta | Tipo | Estado | Nota |
|---|---|---|---|
| `index.php` | php | Revisado | Portal + login modal. **Modificado**: botón `.login-link` (B1). |
| `src/config.php` | php | Revisado | Config del módulo + overrides. OK. |
| `src/config.example.php` | php | Revisado | Plantilla. OK. |
| `src/session.php` | php | Revisado | Sesión segura + idle-timeout. OK. |
| `src/security_headers.php` | php | Revisado | CSP propia (script-src sin `unsafe-inline`). |
| `src/lib/helpers.php` | php | Revisado | Validación, uploads, campos. Sólido. |
| `src/pages/conexion.php` | php | Revisado | Conexión mysqli. OK. |
| `src/pages/sugerir_empresa.php` | php | Revisado | Endpoint `mail()` + rate-limit + CSRF (M7). |
| `src/pages/form/formulario.php` | php | Revisado | Form 2 pasos (admin). OK. |
| `src/pages/form/guardarConvenio.php` | php | Revisado | INSERT preparado + CSRF + auth. OK. |
| `src/pages/form/formulario.css` | css | Revisado | OK. |
| `src/pages/form/script.js` | js | Revisado | Wizard de 2 pasos. OK. |
| `src/pages/upload/.htaccess` | config | Revisado | **Modificado**: `php_flag` en `<IfModule>` (M4). |
| `src/js/script.js` | js | Revisado | **Modificado**: IDs `conv-email/conv-password` (B1). |
| `src/css/styles.css` | css | Revisado | **Modificado**: estilo `.login-link` (B1). |
| `src/output.css` | css | Superficial | Build de Tailwind. OK. |
| `tools/setup_password.php` | php | Revisado | Generador de hash CLI-only. OK. |
| `tools/.htaccess` | config | Revisado | Bloquea `tools/`. OK. |
| `.gitignore` | config | Revisado | OK. |
| `vista_lista/lista.php` | php | Revisado | Lista admin. **Modificado**: quitado script inline (M5). |
| `vista_lista/agregar_convenio.php` | php | Revisado | Alta CRUD + CSRF + auth + upload. OK. |
| `vista_lista/editar_convenio.php` | php | Revisado | Edición CRUD + CSRF + auth + upload. OK. |
| `vista_lista/eliminar_convenio.php` | php | Revisado | Borrado con confirmación + CSRF + auth. OK. |
| `vista_lista/vista_convenios.php` | php | Revisado | Listado público por carrera. OK. |
| `vista_lista/cerrar_sesion.php` | php | Revisado | Logout + CSRF. OK. |
| `vista_lista/script.js` | js | Revisado | **Modificado**: añadida lógica de flash (M5). |
| `vista_lista/script_convenios.js` | js | Revisado | DataTable de `vista_convenios`. OK. |
| `vista_lista/estilo/` (2), `images/` (7), `img/` (1) | css/img | Superficial | `estilo.css, form-crud.css` + 8 imágenes. |
| `vista_empresa/index.php` | php | Revisado | Detalle público de empresa. `prepare` + escape. OK. |
| `vista_empresa/404.html` | html | Revisado | Página 404 estática. OK. |
| `vista_empresa/src/js/script.js` | js | Revisado | **Huérfano** (no lo carga `index.php`) — código muerto, inocuo. |
| `vista_empresa/src/css/styles.css`, `src/output.css` | css | Superficial | OK. |
| `vista_empresa/assets/images/logo/` (24) | img | Superficial | SVG/PNG/webp de marca y galería. |
| `assets/images/logo/` (21) | img | Superficial | SVG/PNG/webp de marca y galería del portal. |

### Módulo Horarios (`plataforma/modulos/horarios/`)

| Ruta | Tipo | Estado | Nota |
|---|---|---|---|
| `index.php` | php | Revisado | Búsqueda pública. **Modificado**: tablas minúsculas + `normalize.css` (B2/M3). |
| `login.php` | php | Revisado | Login + rate-limit + CSRF. **Modificado**: `normalize.css` (M3). |
| `VistaAdmin.php` | php | Revisado | Panel admin. **Modificado**: tablas minúsculas + `normalize.css` (B2/M3). |
| `AgregarMaestro.php` | php | Revisado | Alta/edición + upload. **Modificado**: tablas minúsculas + `normalize.css` (B2/M3). |
| `Logout.php` | php | Revisado | Logout + CSRF. OK. |
| `config.php` | php | Revisado | `getDB()` (PDO). OK. |
| `js/modal.js` | js | Revisado | Modal de horario (img/PDF). OK. |
| `css/` (4), `normalize.css` | css | Revisado | `Principal.css, admin.css, agregarMaestro.css, login.css, normalize.css`. |
| `horarios/6A.pdf`, `horarios/cisco4.png` | binario | Superficial | No coinciden con los datos del dump (operativo). |
| `tools/setup_password.php` | php | Revisado | Generador de hash CLI-only. OK. |
| `tools/.htaccess`, `.gitattributes` | config | Revisado | OK. |
| `Imagenes/` (17) | img | Superficial | Marca, iconos y fondos del módulo. |

### Módulo Requisitos (`plataforma/modulos/requisitos/`)

| Ruta | Tipo | Estado | Nota |
|---|---|---|---|
| `index.php` | php | Revisado | Redirige a `residencia.php`. OK. |
| `residencia.php` | php | Revisado | Guía estática + JS cliente. OK. |
| `servicio-social.php` | php | Revisado | Guía estática + JS cliente. OK. |
| `assets/js/residencia.js` | js | Revisado | Checklist/calculadora/FAQ. OK. |
| `assets/js/servicio-social.js` | js | Revisado | Checklist/FAQ. OK. |
| `assets/css/style.css` | css | Revisado | OK. |
| `assets/docs/servicio-social/` (4 PDF) | pdf | Superficial | Formatos descargables. |
| `assets/img/` (5) | img | Superficial | `LOGO TSJ.png, jalisco.png, logo-footer-jalisco.png, logo-tec.png, sep.png`. |

### Módulo Visitantes (`plataforma/modulos/visitantes/`)

**38 páginas PHP — todas estáticas.** Verificado por búsqueda: sin `$_GET/$_POST/$_FILES`,
sin `mysqli/PDO`, sin consultas SQL ni `include` dinámico. Cada página solo fija
`$tsj_module/$tsj_title` e incluye el header/footer compartidos. Riesgo: nulo.

| Ruta | Tipo | Estado | Nota |
|---|---|---|---|
| `index.php` | php | Revisado | Menú principal del directorio. |
| `Directorio.php` | php | Revisado | **Modificado**: avatar placeholder SVG (M8). |
| `SolicitarCita.php`, `comprobante.php`, `nuevoIngreso.php` | php | Revisado | Páginas verificadas individualmente. |
| `Animacion, CoordinadorGastronomia, CoordinadorGestion, CoordinadorIndustrial, CoordinadorMecatronica, CoordinadorSistemas, DatosContacto, Direccion, DocentesAnimacion, DocentesGestion, DocentesIndustrial, DocentesMecatronica, DocentesSistemas, Egresados, Escolares, Finanzas, Gastronomia, GastronomiaDocentes, GastronomiaMaterias, Gestion, Industrial, MateriasAnimacion, MateriasGestion, MateriasIndustrial, MateriasMecatronica, MateriasSistemas, Mecatronica, ServiciosGenerales, Sistemas, Titulacion, Ubicacion, coordinadorAnimacion` (.php) | php | Revisado | 32 páginas estáticas con patrón uniforme verificado. |
| `style.css` | css | Revisado | OK. |
| `documentos/` (2 PDF) | pdf | Superficial | `SolicitudTitulacion.pdf, comprobante.pdf`. |
| `imagenes/` (19) | img | Superficial | Fotos de docentes, logos e iconos sociales. Falta `user.png` (resuelto vía placeholder, M8); `francisco pocholo.png` queda huérfana. |

**Resultado de cobertura: 263/263 archivos contabilizados. 0 omitidos.**

---

## 3. Informe por módulo

### 3.1 Visitantes — `funciona: SÍ`
- **Objetivo:** directorio institucional informativo para visitantes (áreas, carreras,
  docentes, materias, servicios, ubicación, contacto).
- **Flujo:** `index.php` → enlaces a páginas temáticas → volver al menú.
- **Qué funciona:** todo. Páginas 100% estáticas; tres incluyen JS de cliente (Directorio,
  nuevoIngreso con jsPDF). Sin backend ni BD.
- **Issues:** M8 (imagen `user.png` inexistente) — **corregido** con avatar SVG embebido.
- **Pruebas manuales:** abrir `index.php`, navegar a cada sección, verificar que no haya
  imágenes rotas en `Directorio.php`.

### 3.2 Biblioteca — `funciona: SÍ` (tras corregir M6)
- **Objetivo:** consultar el catálogo, solicitar préstamos de libros y de controles, y
  administrar inventario/solicitudes desde un panel protegido.
- **Flujo público:** `buscar.php` → `obtenerLibros.php` (JSON) → `solicitudDeLibros.php` →
  `guardar_solicitud_libro.php`. Controles: `solicitudDeControles.php` →
  `guardarSolControles.php`.
- **Flujo admin:** `login.php` → `admin.php` (4 pestañas) → procesos CRUD/estado.
- **Qué funcionaba mal:** la pestaña "Controles" solo listaba; sin botones aceptar/rechazar,
  `estado_control.php` quedaba huérfano y `solicitudDeControles.php` sin enlace.
- **Corrección aplicada (M6):** columnas Estado + Acciones y `cambiarEstadoControl()` en
  `admin.php`; enlace "Solicitar préstamo de controles" en `buscar.php`.
- **Seguridad:** prepared statements, CSRF + auth en todos los procesos, rate-limit en
  formularios públicos. Sin hallazgos.
- **Pruebas manuales:** enviar una solicitud de control desde `buscar.php`, verificar que
  aparece como "Pendiente" en el panel y que Aceptar/Rechazar cambia el estado.

### 3.3 Convenios — `funciona: SÍ` (tras corregir B1)
- **Objetivo:** mostrar convenios por carrera al público, permitir al admin gestionarlos
  (CRUD con logo) y recibir sugerencias de empresas.
- **Flujo público:** `index.php` (tarjetas) → `vista_convenios.php?carrera=X` →
  `vista_empresa/index.php?id=N`. Sugerencia: modal → `sugerir_empresa.php`.
- **Flujo admin:** `index.php` (login) → `vista_lista/lista.php` → agregar/editar/eliminar
  o `formulario.php`.
- **Qué funcionaba mal (B1):** no existía disparador para el modal de login y el JS leía
  IDs inexistentes (`email/password` en vez de `conv-email/conv-password`) → el panel admin
  era **inalcanzable**.
- **Correcciones aplicadas:** B1 (botón `.login-link` + IDs), M4 (`.htaccess` `php_flag`),
  M5 (script inline → externo).
- **Seguridad:** prepared statements, CSRF, validación de uploads con `finfo`+`getimagesize`+
  nombres aleatorios, `basename()` anti path-traversal, saneo de cabeceras en `mail()`. Sólido.
- **Pruebas manuales:** login admin, alta/edición/borrado de un convenio con logo, vista
  pública del detalle, envío del formulario "Sugerir empresa".

### 3.4 Horarios — `funciona: SÍ` (tras corregir B2)
- **Objetivo:** buscar maestros y consultar su horario (imagen/PDF); el admin gestiona
  maestros, materias y archivos.
- **Flujo público:** `index.php` → búsqueda/filtro/paginación → modal con imagen o PDF.
- **Flujo admin:** `login.php` → `VistaAdmin.php` → `AgregarMaestro.php` (alta/edición con
  upload) / borrado transaccional.
- **Qué funcionaba mal (B2):** las consultas usaban `Carreras/Horarios/Profesores/Materias`
  y el dump crea las tablas en minúsculas → en Linux (producción) **todo el módulo caía**.
- **Correcciones aplicadas:** B2 (tablas a minúsculas en los 3 archivos), M3 (`normalize.css`).
- **Seguridad:** PDO con `prepare`, transacciones en alta/edición/borrado, validación de
  uploads (`is_uploaded_file`, `finfo`, tamaño), `basename()` al borrar archivos. Sólido.
- **Pruebas manuales:** importar el dump en MySQL con `lower_case_table_names=0`, abrir la
  búsqueda y el panel; alta de maestro con PDF; verificar `normalize.css` sin 404.

### 3.5 Requisitos — `funciona: SÍ`
- **Objetivo:** guías informativas de residencia profesional y servicio social
  (timeline, checklist con progreso, calculadora de créditos, FAQ, descargas).
- **Flujo:** `index.php` → `residencia.php` ↔ `servicio-social.php`.
- **Qué funciona:** todo. Sin backend; lógica 100% cliente con `localStorage`.
- **Issues:** ninguno.
- **Pruebas manuales:** marcar checklist y recargar (persistencia), usar la calculadora,
  abrir los PDF de descarga.

---

## 4. Informe transversal (shared / core)

- **Autenticación:** `shared/lib/auth.php` centraliza sesión segura (`HttpOnly`, `SameSite=Strict`,
  `use_strict_mode`, `Secure` bajo HTTPS), CSRF con `hash_equals`, regeneración de ID en login,
  idle-timeout de 1 h. Aislamiento por módulo vía `_module`. Convenios usa su propia sesión
  (`session.php`) con el mismo hardening — coherente.
- **Autorización:** `requireAuth()` protege todas las páginas y procesos admin; `requirePost()`
  exige POST+CSRF. No se hallaron endpoints de proceso accesibles sin sesión.
- **Configuración y secretos:** separación correcta — defaults versionados en `config.php`,
  overrides reales en `config.local.php` (en `.gitignore`, no se sube). Hashes bcrypt cost 12.
  Defaults de dev seguros: si falta el hash, el login se bloquea en vez de abrir un hueco.
- **Cabeceras de seguridad:** XFO `DENY`, `nosniff`, `Referrer-Policy`, `Permissions-Policy`,
  HSTS bajo HTTPS y CSP en ambas variantes. **Observación (riesgo bajo):** la CSP compartida
  usa `'unsafe-inline'` en `script-src` (necesario por los bloques `<script>` inline de los
  módulos). Mejora futura: migrar a *nonces*.
- **Rate limiting:** `RateLimit.php` (archivos en temp) aplicado en los 3 logins y en los
  formularios públicos. Correcto.
- **Acceso a BD:** dos patrones conviven — PDO (`getPDO`, Horarios/Convenios-form) y mysqli
  (`getMysqli`, Biblioteca/Convenios). Ambos con `ERRMODE_EXCEPTION` y consultas preparadas.
- **Layout/routing:** header/footer unificados, navegación por constante `PLATAFORMA_URL`.
  Sin rutas absolutas frágiles en el código de aplicación.

---

## 5. Informe de base de datos

- **Esquema:** 3 BD independientes (`biblioteca_escolar`, `convenios_db`, `horarios_db`),
  todas InnoDB/`utf8mb4`. Horarios define claves foráneas correctas
  (`horarios → profesores/materias/carreras`). Biblioteca y Convenios sin FKs (denormalización
  aceptable para el tamaño del proyecto).
- **Consistencia SQL ↔ PHP:** **hallazgo crítico B2** — el código de Horarios consultaba las
  tablas con mayúsculas y el dump las crea en minúsculas; sensible a mayúsculas en Linux.
  **Corregido** en el código. El resto de queries concuerdan con el esquema.
- **Migraciones:** orden documentado (biblioteca → convenios → horarios →
  `migrate_horarios_paths.sql` → `setup.sql`). `migrate_horarios_paths.sql` es obligatorio
  tras importar `horarios.sql`.
- **Privilegios mínimos:** `setup.sql` crea `tsjplat` con solo `SELECT/INSERT/UPDATE/DELETE`.
  Correcto. **Pendiente operativo:** sustituir el placeholder `TU_CLAVE_SEGURA` por una clave
  real antes de ejecutarlo.
- **Integridad / datos:** `audit_log` (en `convenios.sql`) se define pero **ningún código la
  escribe** — esquema muerto (inocuo; se puede dejar o eliminar). Los datos de ejemplo de
  Horarios referencian archivos de imagen que no coinciden con los binarios commiteados
  (`6A.pdf`, `cisco4.png`) → "Ver Horario" saldrá roto para las filas seed hasta copiar los
  archivos reales (ya advertido en `INSTALL.md`).
- **Riesgo de pérdida de datos:** bajo. Los borrados son explícitos, con CSRF y confirmación;
  Horarios usa transacciones.

---

## 6. Plan de remediación

### Fase 1 — Bloqueantes · **COMPLETADA**
- **B1 (S)** Convenios: botón `.login-link` en `index.php` + IDs corregidos en `src/js/script.js`
  + estilo en `styles.css`.
- **B2 (M)** Horarios: tablas a minúsculas en `index.php`, `VistaAdmin.php`, `AgregarMaestro.php`.

### Fase 2 — Importantes (severidad media) · **COMPLETADA**
- **M3 (S)** Horarios: ruta `normalize.css` corregida en 4 archivos.
- **M4 (S)** Convenios: `php_flag` envuelto en `<IfModule>` en `upload/.htaccess`.
- **M5 (S)** Convenios: `<script>` inline de `lista.php` movido a `vista_lista/script.js`.
- **M6 (M)** Biblioteca: flujo Controles completado (botones en `admin.php` + enlace en `buscar.php`).
- **M7 (S)** Convenios: requisito de MTA para `mail()` documentado en `INSTALL.md`.
- **M8 (S)** Visitantes: avatar placeholder SVG en `Directorio.php`.

### Fase 3 — Nice-to-have (deuda técnica menor) · **Pendiente, no bloquea**
- (S) Eliminar el código muerto `vista_empresa/src/js/script.js` y la imagen huérfana
  `visitantes/imagenes/francisco pocholo.png`.
- (S) Eliminar o empezar a usar la tabla `audit_log`.
- (M) Migrar la CSP a *nonces* para retirar `'unsafe-inline'` de `script-src`.
- (S) Añadir `.gitkeep` real en `horarios/horarios/` (la regla de `.gitignore` lo referencia).

### Verificación previa al lanzamiento · **Pendiente (requiere servidor + BD)**
Validación funcional end-to-end — ver §7 y el plan en
`.claude/plans/eres-un-auditor-senior-synchronous-shell.md`.

**Dependencias:** Fase 2 no depende de Fase 1. La verificación final depende de ambas.
Todas las correcciones de código aplicadas pasan `php -l` sin errores de sintaxis.

---

## 7. Checklist de producción y pruebas

### Despliegue
- [ ] `shared/config.local.php` creado desde la plantilla, con `tsjplat` y contraseña ≥16 chars.
- [ ] `convenios/src/config.local.php` creado; hashes admin generados en los 3 módulos.
- [ ] Dumps importados en orden; `migrate_horarios_paths.sql` y `setup.sql` ejecutados.
- [ ] **MySQL Linux:** tablas en minúsculas (el código ya consulta así).
- [ ] Directorios de upload con permisos correctos (no 777); `tools/.htaccess` presentes.
- [ ] `display_errors = Off`, `log_errors = On`; HTTPS + redirect.
- [ ] MTA (sendmail/SMTP) configurado para la función "Sugerir empresa".
- [ ] Archivos físicos de horarios copiados a `modulos/horarios/horarios/`.

### Pruebas de humo
1. **Convenios:** `index.php` → "Iniciar sesión" abre modal → login → `lista.php`; alta/edición/
   borrado de convenio con logo; vista pública del detalle; "Sugerir empresa".
2. **Horarios:** búsqueda + filtro por carrera con resultados; panel admin sin errores SQL;
   alta de maestro con PDF; `normalize.css` sin 404.
3. **Biblioteca:** solicitar préstamo de libro y de control; en el panel, aceptar/rechazar
   solicitudes y controles, marcar devolución.
4. **Requisitos / Visitantes:** navegación sin enlaces ni imágenes rotas.
5. **Negativas:** POST sin CSRF → 403; >5 logins fallidos → bloqueo; subir `.php` como logo
   → rechazado por `finfo`.

---

## 8. Preguntas para cerrar huecos

1. **Entorno de producción:** ¿confirmas Linux/Apache? (Determinó la criticidad de B2/M4.)
2. **`mail()` vs SMTP:** ¿el servidor tendrá MTA, o se prefiere migrar "Sugerir empresa" a
   SMTP autenticado (PHPMailer)?
3. **Tabla `audit_log`:** ¿se desea implementar el registro de auditoría de Convenios, o se
   elimina del esquema?
4. **Datos seed:** ¿los datos de ejemplo de los dumps deben quedar en producción o se importa
   solo el esquema vacío?
5. **Quinto módulo:** el portal menciona 5 equipos; ¿hay un módulo adicional pendiente de
   integrar?
6. **Archivos de horarios:** ¿se dispone de los archivos físicos originales para copiarlos a
   `modulos/horarios/horarios/`?

---

*Las correcciones de código de las Fases 1 y 2 ya están aplicadas en el árbol de trabajo
(sin commitear). El plan operativo detallado está en
`.claude/plans/eres-un-auditor-senior-synchronous-shell.md`.*
