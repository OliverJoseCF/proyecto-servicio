-- ================================================================
--  PRUEBA_datos_panel_admin.sql   ·   ARCHIVO TEMPORAL DE PRUEBA
--  Plataforma TSJ Campus Chapala — verificación del panel admin
--
--  PROPÓSITO:
--    Insertar datos de ejemplo en CADA módulo del panel para verificar
--    que todas las pantallas LEEN bien y que los controles (editar,
--    activar/desactivar, eliminar, aprobar/rechazar, filtros, semáforos,
--    reportes) funcionan. Después tú haces las acciones manualmente.
--
--  CÓMO USARLO:
--    1. phpMyAdmin → base `kiosko_tsj` → pestaña Importar (o SQL)
--    2. Ejecuta este archivo COMPLETO.
--    3. Recorre el panel con la lista que te di al final del chat.
--    4. Cuando termines, ejecuta el bloque "LIMPIEZA" del final
--       (descoméntalo) para borrar TODO lo de prueba.
--
--  Todo lo identificable lleva el prefijo  «PRUEBA –»  para que lo
--  reconozcas y lo borres fácil. Fecha de referencia: 2026-06-24.
--
--  Cuenta admin de prueba:  prueba.admin@chapala.tecmm.edu.mx
--  Contraseña:              Admin2026!
-- ================================================================

USE `kiosko_tsj`;

-- IMPORTANTE: fija el juego de caracteres a utf8mb4 para que el guión largo «–»
-- de los nombres "PRUEBA –" coincida correctamente en los DELETE ... LIKE.
-- Sin esto, según el cliente, la pre-limpieza/limpieza podría no borrar nada.
SET NAMES utf8mb4;

SET @OLD_FK = @@FOREIGN_KEY_CHECKS;

-- ================================================================
-- 0. PRE-LIMPIEZA — borra datos de PRUEBA de corridas anteriores.
-- ================================================================
-- Hace el script RE-EJECUTABLE: puedes importarlo varias veces sin que falle
-- por duplicados (p. ej. el código de libro 'BIB-PRUEBA-01' que es único).
-- Es idéntica a la LIMPIEZA del final, pero activa al inicio.
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM `horarios`                WHERE `id_profesor` IN (SELECT id FROM `docentes` WHERE `nombre` LIKE 'PRUEBA –%');
DELETE FROM `prestamos`               WHERE `estudiante_nombre` LIKE 'PRUEBA –%';
DELETE FROM `solicitudes_biblioteca`  WHERE `estudiante_nombre` LIKE 'PRUEBA –%';
DELETE FROM `libros`                  WHERE `codigo` = 'BIB-PRUEBA-01';
DELETE FROM `convenio_carreras`       WHERE `convenio_id` IN (SELECT id FROM `convenios` WHERE `nombre` LIKE 'PRUEBA –%');
DELETE FROM `convenios`               WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `sugerencias_empresa`     WHERE `nombre_empresa` LIKE 'PRUEBA –%';
DELETE FROM `docente_carrera`         WHERE `docente_id` IN (SELECT id FROM `docentes` WHERE `nombre` LIKE 'PRUEBA –%');
DELETE FROM `directorio`              WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `docentes`                WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `coordinadores`           WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `materias`                WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `atributos_egreso`        WHERE `texto`  LIKE 'PRUEBA –%';
DELETE FROM `secretarias`             WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `carrusel_fotos`          WHERE `titulo` LIKE 'PRUEBA –%';
DELETE FROM `avisos`                  WHERE `titulo` LIKE 'PRUEBA –%';
DELETE FROM `faq`                     WHERE `pregunta` LIKE 'PRUEBA –%';
DELETE FROM `requisitos_items`        WHERE `texto`  LIKE 'PRUEBA –%';
DELETE FROM `timeline_fases`          WHERE `titulo` LIKE 'PRUEBA –%';
DELETE FROM `documentos_descargables` WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `admins`                  WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `admin_log`               WHERE `detalle` LIKE 'PRUEBA –%';
SET FOREIGN_KEY_CHECKS = 1;   -- de aquí en adelante respetamos las FK (datos válidos)


-- ================================================================
-- 1. PÁGINA DE INICIO  (carrusel · avisos con vigencia · FAQ general)
-- ================================================================

-- 1.1 Carrusel — para probar activar/desactivar, orden y editar
INSERT INTO `carrusel_fotos` (`url`, `titulo`, `subtitulo`, `activo`, `orden`) VALUES
  ('https://placehold.co/1200x400/059669/ffffff?text=PRUEBA+Carrusel', 'PRUEBA – Banner de ejemplo', 'Esta foto es de prueba, puedes eliminarla', 1, 10),
  ('https://placehold.co/1200x400/dc2626/ffffff?text=PRUEBA+Inactivo', 'PRUEBA – Banner inactivo', 'Debe verse atenuado / oculto en el portal', 0, 11);

-- 1.2 Avisos — prueban el control de VIGENCIA (publicar_desde / publicar_hasta)
INSERT INTO `avisos` (`titulo`, `descripcion`, `fecha`, `publicar_desde`, `publicar_hasta`, `activo`, `orden`) VALUES
  ('PRUEBA – Aviso VIGENTE',     'Sin fechas de vigencia: debe aparecer siempre en el portal.',          '2026-06-24', NULL,         NULL,         1, 20),
  ('PRUEBA – Aviso CADUCADO',    'publicar_hasta ya pasó (2026-05-01): NO debe verse en el portal pero sí en el admin.', '2026-04-01', '2026-04-01', '2026-05-01', 1, 21),
  ('PRUEBA – Aviso PROGRAMADO',  'publicar_desde futuro (2026-09-01): aún NO debe verse en el portal, sí en el admin.',  '2026-06-24', '2026-09-01', NULL,         1, 22),
  ('PRUEBA – Aviso DESACTIVADO', 'activo = 0: oculto manualmente. Prueba reactivarlo.',                  '2026-06-24', NULL,         NULL,         0, 23);

-- 1.3 FAQ general (la que se administra desde Inicio)
INSERT INTO `faq` (`tipo`, `pregunta`, `respuesta`, `orden`, `activo`) VALUES
  ('general', 'PRUEBA – ¿Esta pregunta de ejemplo aparece en la portada?', 'Sí: es un FAQ general de prueba. Edítalo o elimínalo para verificar.', 50, 1);


-- ================================================================
-- 2. DIRECTORIO Y CARRERAS  (visitantes)
-- ================================================================

-- 2.1 Directorio — incluye el caso S/N (sin teléfono) que se corrigió.
--     Usa fotos REALES existentes en /modulos/visitantes/imagenes/ (solo el nombre de archivo).
INSERT INTO `directorio` (`nombre`, `puesto`, `correo`, `telefono`, `extension`, `ubicacion_fisica`, `foto`, `activo`, `orden`) VALUES
  ('PRUEBA – Juan Pérez (con teléfono)', 'Soporte Técnico',   'prueba.juan@chapala.tecmm.edu.mx',  '376-766-1234', '102', 'Edificio A, planta baja', 'miguel.png', 1, 30),
  ('PRUEBA – María López (sin número)',  'Recepción',         'prueba.maria@chapala.tecmm.edu.mx', 'S/N',          NULL,  'Recepción principal',     'carmen.png', 1, 31),
  ('PRUEBA – Persona Inactiva',          'Ex-colaborador',    NULL,                                'S/N',          NULL,  NULL,                      NULL,         0, 32);

-- 2.2 Docentes de prueba (con foto REAL) + asignación de carrera (M:N en docente_carrera).
--     Todos los horarios de prueba colgarán de estos docentes para que la limpieza
--     nunca toque horarios de docentes reales.
INSERT INTO `docentes` (`nombre`, `correo`, `foto`, `activo`, `orden`) VALUES
  ('PRUEBA – Docente Ejemplo',  'prueba.docente1@chapala.tecmm.edu.mx', 'gamas.png', 1, 98),
  ('PRUEBA – Docente Horarios', 'prueba.docente2@chapala.tecmm.edu.mx', 'jorge.png', 1, 99);
-- "Docente Ejemplo" → ISC (1) + Industrial (2)
INSERT INTO `docente_carrera` (`docente_id`, `carrera_id`)
  SELECT id, 1 FROM `docentes` WHERE `nombre` = 'PRUEBA – Docente Ejemplo';
INSERT INTO `docente_carrera` (`docente_id`, `carrera_id`)
  SELECT id, 2 FROM `docentes` WHERE `nombre` = 'PRUEBA – Docente Ejemplo';
-- "Docente Horarios" → IADEV (4) + Mecatrónica (3)
INSERT INTO `docente_carrera` (`docente_id`, `carrera_id`)
  SELECT id, 4 FROM `docentes` WHERE `nombre` = 'PRUEBA – Docente Horarios';
INSERT INTO `docente_carrera` (`docente_id`, `carrera_id`)
  SELECT id, 3 FROM `docentes` WHERE `nombre` = 'PRUEBA – Docente Horarios';

-- 2.3 Coordinador de prueba
INSERT INTO `coordinadores` (`carrera_id`, `nombre`, `correo`, `activo`) VALUES
  (1, 'PRUEBA – Coordinador Ejemplo', 'prueba.coord@chapala.tecmm.edu.mx', 1);

-- 2.4 Materia de prueba (ISC)
INSERT INTO `materias` (`carrera_id`, `nombre`, `orden`, `activo`) VALUES
  (1, 'PRUEBA – Materia de Ejemplo', 99, 1);

-- 2.5 Atributos de egreso (esta tabla viene VACÍA en el seed)
INSERT INTO `atributos_egreso` (`carrera_id`, `texto`, `orden`, `activo`) VALUES
  (1, 'PRUEBA – Capacidad de diseñar soluciones de software (atributo de ejemplo).', 1, 1),
  (1, 'PRUEBA – Trabajo en equipo multidisciplinario (atributo de ejemplo).',        2, 1);

-- 2.6 Secretaría de prueba
INSERT INTO `secretarias` (`nombre`, `rol`, `correo`, `telefono`, `activo`, `orden`) VALUES
  ('PRUEBA – Secretaria Ejemplo', 'Apoyo administrativo', 'prueba.secre@chapala.tecmm.edu.mx', '331-000-0000', 1, 99);

-- 2.7 OFERTA ACADÉMICA — rellena objetivo/perfil/objetivos de las 6 carreras REALES
--     (en el seed venían NULL y la vista pública mostraba "Próximamente").
--     Se administra en: Directorio y carreras → Carreras → Editar.
--     ⚠ Esto MODIFICA carreras reales; la LIMPIEZA del final los regresa a NULL.
--     La retícula apunta a un PDF de ejemplo existente sólo para probar el botón.
UPDATE `carreras` SET
  `objetivo_general`        = 'Formar profesionales capaces de diseñar, desarrollar e implementar sistemas de software y soluciones tecnológicas que respondan a las necesidades de organizaciones públicas y privadas, con ética y visión innovadora.',
  `perfil_profesional`      = 'El egresado desarrolla software a la medida, administra bases de datos y redes, gestiona proyectos de TI y aplica metodologías de ingeniería de software para crear soluciones eficientes y seguras.',
  `objetivos_educacionales` = 'A pocos años de egresar se desempeña en desarrollo de software, administración de infraestructura tecnológica o emprende proyectos propios, con aprendizaje continuo y liderazgo técnico.',
  `reticula_url`            = '/plataforma/modulos/horarios/horarios/6A.pdf'
  WHERE `clave` = 'ISC';
UPDATE `carreras` SET
  `objetivo_general`        = 'Formar ingenieros capaces de diseñar, optimizar y administrar sistemas productivos y de servicios, mejorando la calidad, la productividad y el uso eficiente de los recursos.',
  `perfil_profesional`      = 'Optimiza procesos productivos, implementa sistemas de calidad, gestiona cadenas de suministro y aplica herramientas estadísticas para la mejora continua.',
  `objetivos_educacionales` = 'El egresado lidera proyectos de mejora de procesos, gestiona operaciones y calidad en la industria, o emprende, con responsabilidad social y visión global.',
  `reticula_url`            = '/plataforma/modulos/horarios/horarios/horario_3_00c0e9077765.pdf'
  WHERE `clave` = 'II';
UPDATE `carreras` SET
  `objetivo_general`        = 'Formar ingenieros que integren la mecánica, la electrónica y el control automático para diseñar y mantener sistemas mecatrónicos y de automatización industrial.',
  `perfil_profesional`      = 'Diseña y programa sistemas automatizados, robots y controladores; integra sensores y actuadores; y da mantenimiento a equipos industriales.',
  `objetivos_educacionales` = 'El egresado se desempeña en automatización, robótica y mantenimiento industrial, o desarrolla soluciones mecatrónicas propias, adaptándose a nuevas tecnologías.',
  `reticula_url`            = '/plataforma/modulos/horarios/horarios/horario_3_b6e46d90eb73.pdf'
  WHERE `clave` = 'IM';
UPDATE `carreras` SET
  `objetivo_general`        = 'Formar profesionales creativos capaces de producir animación 2D/3D y efectos visuales para cine, televisión, videojuegos y medios digitales, con dominio técnico y artístico.',
  `perfil_profesional`      = 'Crea personajes, escenarios y efectos visuales; domina el pipeline de producción audiovisual; y aplica narrativa y composición digital.',
  `objetivos_educacionales` = 'El egresado trabaja en estudios de animación, VFX o videojuegos, o produce contenido propio, manteniéndose actualizado en herramientas y tendencias.',
  `reticula_url`            = NULL
  WHERE `clave` = 'IADEV';
UPDATE `carreras` SET
  `objetivo_general`        = 'Formar profesionales capaces de dirigir y gestionar organizaciones con enfoque estratégico, innovador y socialmente responsable, optimizando sus recursos y procesos.',
  `perfil_profesional`      = 'Administra recursos humanos, financieros y materiales; formula estrategias; impulsa la innovación; y gestiona proyectos y emprendimientos.',
  `objetivos_educacionales` = 'El egresado ocupa puestos directivos o de gestión, consultoría, o emprende, con liderazgo, ética y visión global.',
  `reticula_url`            = NULL
  WHERE `clave` = 'IGE';
UPDATE `carreras` SET
  `objetivo_general`        = 'Formar profesionales de la gastronomía capaces de crear, producir y administrar servicios de alimentos y bebidas con calidad, higiene, creatividad e identidad cultural.',
  `perfil_profesional`      = 'Domina técnicas culinarias nacionales e internacionales, gestiona cocinas y restaurantes, controla costos y aplica normas de higiene y seguridad alimentaria.',
  `objetivos_educacionales` = 'El egresado dirige cocinas, emprende negocios gastronómicos o se especializa en áreas culinarias, con innovación y responsabilidad.',
  `reticula_url`            = NULL
  WHERE `clave` = 'LG';


-- ================================================================
-- 3. MAESTROS Y HORARIOS  (tabla `horarios` viene VACÍA en el seed)
-- ================================================================
-- Todos cuelgan de los DOCENTES DE PRUEBA (no de docentes reales), y apuntan a
-- archivos PDF/imagen REALES que ya existen en /modulos/horarios/horarios/.
-- Así puedes abrirlos desde el portal y la limpieza no afecta horarios reales.

-- Docente Ejemplo: ISC/3er con PDF real, e Industrial/7mo con otro PDF real
INSERT INTO `horarios` (`id_profesor`, `id_carrera`, `semestre`, `imagen_horario`, `activo`)
  SELECT id, 1, '3er', '/plataforma/modulos/horarios/horarios/6A.pdf', 1
  FROM `docentes` WHERE `nombre` = 'PRUEBA – Docente Ejemplo';
INSERT INTO `horarios` (`id_profesor`, `id_carrera`, `semestre`, `imagen_horario`, `activo`)
  SELECT id, 2, '7mo', '/plataforma/modulos/horarios/horarios/horario_3_00c0e9077765.pdf', 1
  FROM `docentes` WHERE `nombre` = 'PRUEBA – Docente Ejemplo';
-- Docente Horarios: IADEV/5to con PDF real, y uno SIN carrera y SIN archivo ("Sin archivo")
INSERT INTO `horarios` (`id_profesor`, `id_carrera`, `semestre`, `imagen_horario`, `activo`)
  SELECT id, 4, '5to', '/plataforma/modulos/horarios/horarios/horario_3_b6e46d90eb73.pdf', 1
  FROM `docentes` WHERE `nombre` = 'PRUEBA – Docente Horarios';
INSERT INTO `horarios` (`id_profesor`, `id_carrera`, `semestre`, `imagen_horario`, `activo`)
  SELECT id, NULL, '1er', NULL, 1
  FROM `docentes` WHERE `nombre` = 'PRUEBA – Docente Horarios';


-- ================================================================
-- 4. BIBLIOTECA  (libro de prueba · préstamos · solicitudes)
-- ================================================================

-- 4.1 Un libro de prueba (código único)
INSERT INTO `libros` (`codigo`, `nombre`, `autor`, `editorial`, `categoria`, `ejemplares`, `activo`) VALUES
  ('BIB-PRUEBA-01', 'PRUEBA – Libro de Ejemplo', 'Autor de Prueba', 'Editorial Demo', 'Pruebas', 5, 1);

-- 4.2 Préstamos — alimentan reportes (top libros, por mes, por carrera, ATRASADOS)
--     fecha_prestamo dentro de los últimos 12 meses. 3 quedan ATRASADOS (devuelto=0, vencidos).
INSERT INTO `prestamos`
  (`libro_id`, `estudiante_nombre`, `estudiante_control`, `carrera`, `tipo`, `fecha_prestamo`, `fecha_devolucion`, `devuelto`, `fecha_devuelto`) VALUES
  (1, 'PRUEBA – Ana García',    '21100101', 'Sistemas Computacionales', 'prestamo',     '2026-06-01', '2026-06-15', 0, NULL),                 -- ATRASADO
  (1, 'PRUEBA – Luis Ramírez',  '21100102', 'Sistemas Computacionales', 'prestamo',     '2026-05-10', '2026-05-24', 1, '2026-05-20 10:00:00'),
  (1, 'PRUEBA – Sofía Méndez',  '21100103', 'Industrial',               'prestamo',     '2026-04-05', '2026-04-19', 1, '2026-04-18 12:00:00'),
  (3, 'PRUEBA – Diego Torres',  '21100104', 'Sistemas Computacionales', 'prestamo',     '2026-06-10', '2026-06-24', 0, NULL),                 -- vigente (vence hoy)
  (3, 'PRUEBA – Karla Ruiz',    '21100105', 'Mecatrónica',              'prestamo',     '2026-03-15', '2026-03-29', 1, '2026-03-28 09:00:00'),
  (5, 'PRUEBA – Pedro Salas',   '21100106', 'Sistemas Computacionales', 'prestamo',     '2026-05-20', '2026-06-03', 0, NULL),                 -- ATRASADO
  (5, 'PRUEBA – Elena Vargas',  '21100107', 'Gestión Empresarial',      'prestamo',     '2026-02-10', '2026-02-24', 1, '2026-02-22 11:00:00'),
  (6, 'PRUEBA – Mario Castro',  '21100108', 'Industrial',               'prestamo',     '2026-01-15', '2026-01-29', 1, '2026-01-27 16:00:00'),
  (6, 'PRUEBA – Lucía Ortiz',   '21100109', 'Mecatrónica',              'prestamo',     '2025-11-10', '2025-11-24', 1, '2025-11-23 10:30:00'),
  (7, 'PRUEBA – Hugo Flores',   '21100110', 'Gestión Empresarial',      'prestamo',     '2025-09-05', '2025-09-19', 1, '2025-09-18 13:00:00'),
  (9, 'PRUEBA – Valeria Nuño',  '21100111', 'Animación Digital',        'prestamo',     '2026-06-12', '2026-06-20', 0, NULL),                 -- ATRASADO
  (2, 'PRUEBA – Iván Robles',   '21100112', 'Sistemas Computacionales', 'consulta_sala','2025-07-20', '2025-08-03', 1, '2025-08-01 09:00:00');

-- 4.3 Solicitudes de biblioteca — para probar aprobar/rechazar y tasas
INSERT INTO `solicitudes_biblioteca`
  (`libro_id`, `estudiante_nombre`, `estudiante_control`, `carrera`, `tipo`, `estado`) VALUES
  (2, 'PRUEBA – Solicitante Pendiente 1', '21100201', 'Sistemas Computacionales', 'prestamo',      'pendiente'),
  (4, 'PRUEBA – Solicitante Pendiente 2', '21100202', 'Industrial',               'prestamo',      'pendiente'),
  (8, 'PRUEBA – Solicitante Pendiente 3', '21100203', 'Gestión Empresarial',      'consulta_sala', 'pendiente'),
  (1, 'PRUEBA – Solicitante Aprobado',    '21100204', 'Sistemas Computacionales', 'prestamo',      'aprobada'),
  (5, 'PRUEBA – Solicitante Rechazado',   '21100205', 'Mecatrónica',              'prestamo',      'rechazada');


-- ================================================================
-- 5. CONVENIOS  (semáforo de vencimiento · carreras · sugerencias)
-- ================================================================
-- Estados del semáforo respecto a HOY = 2026-06-24:
--   VIGENTE  (verde)    → vence lejos
--   POR VENCER (amarillo)→ vence en <= 30 días
--   VENCIDO  (rojo)     → vencimiento ya pasó
--   SIN FECHA (gris)    → vencimiento NULL

-- Logos = rutas REALES a /modulos/convenios/assets/images/logo/imagenes/.
-- El "SIN FECHA / GLOBAL" se deja sin logo a propósito (prueba la tarjeta sin imagen).
INSERT INTO `convenios`
  (`nombre`, `tipo_convenio`, `sector`, `nombre_contacto`, `correo_contacto`, `telefono_contacto`, `logo`, `vencimiento`, `activo`) VALUES
  ('PRUEBA – Convenio VIGENTE',    'residencia',      'privado', 'Contacto Vigente',  'vigente@prueba.com',  '376-100-0001', '/plataforma/modulos/convenios/assets/images/logo/imagenes/DSC04199_1.webp', '2027-03-01', 1),
  ('PRUEBA – Convenio POR VENCER', 'practicas',       'privado', 'Contacto PorVencer','porvencer@prueba.com','376-100-0002', '/plataforma/modulos/convenios/assets/images/logo/imagenes/9M6A4513.webp',  '2026-07-10', 1),
  ('PRUEBA – Convenio VENCIDO',    'servicio_social', 'publico', 'Contacto Vencido',  'vencido@prueba.com',  '376-100-0003', '/plataforma/modulos/convenios/assets/images/logo/imagenes/chapala01-2.webp', '2026-05-15', 1),
  ('PRUEBA – Convenio SIN FECHA / GLOBAL', 'otro',    'ac',      'Contacto Global',   'global@prueba.com',   '376-100-0004', NULL,                                                                       NULL,         1),
  ('PRUEBA – Convenio DESACTIVADO','residencia',      'otro',    'Contacto Inactivo', 'inactivo@prueba.com', '376-100-0005', '/plataforma/modulos/convenios/assets/images/logo/imagenes/DSC08323_1.webp', '2027-01-01', 0);

-- Asociar carreras (por subconsulta para no depender de los IDs auto):
INSERT INTO `convenio_carreras` (`convenio_id`, `carrera_id`)
  SELECT id, 1 FROM `convenios` WHERE `nombre` = 'PRUEBA – Convenio VIGENTE';      -- ISC
INSERT INTO `convenio_carreras` (`convenio_id`, `carrera_id`)
  SELECT id, 4 FROM `convenios` WHERE `nombre` = 'PRUEBA – Convenio VIGENTE';      -- + IADEV
INSERT INTO `convenio_carreras` (`convenio_id`, `carrera_id`)
  SELECT id, 2 FROM `convenios` WHERE `nombre` = 'PRUEBA – Convenio POR VENCER';   -- Industrial
INSERT INTO `convenio_carreras` (`convenio_id`, `carrera_id`)
  SELECT id, 3 FROM `convenios` WHERE `nombre` = 'PRUEBA – Convenio VENCIDO';      -- Mecatrónica
-- "SIN FECHA / GLOBAL" se queda SIN carreras a propósito: debe aparecer en TODOS los filtros.

-- Sugerencias de empresa — para probar aceptar/rechazar (tabla VACÍA en el seed)
INSERT INTO `sugerencias_empresa`
  (`nombre_empresa`, `correo_empresa`, `nombre_contacto`, `estado`, `ip_origen`) VALUES
  ('PRUEBA – Empresa Sugerida 1', 'sugerida1@prueba.com', 'Contacto Uno', 'pendiente', '127.0.0.1'),
  ('PRUEBA – Empresa Sugerida 2', 'sugerida2@prueba.com', 'Contacto Dos', 'pendiente', '127.0.0.1'),
  ('PRUEBA – Empresa Aceptada',   'aceptada@prueba.com',  'Contacto Tres','aceptada',  '127.0.0.1'),
  ('PRUEBA – Empresa Rechazada',  'rechazada@prueba.com', 'Contacto Cuatro','rechazada','127.0.0.1');


-- ================================================================
-- 6. REQUISITOS (Residencia / Servicio Social)
-- ================================================================
INSERT INTO `requisitos_items` (`tipo`, `texto`, `orden`, `activo`) VALUES
  ('residencia',     'PRUEBA – Requisito de ejemplo (residencia).', 99, 1),
  ('servicio_social','PRUEBA – Requisito de ejemplo (servicio social).', 99, 1);

INSERT INTO `timeline_fases` (`tipo`, `titulo`, `descripcion`, `tiempo_referencia`, `orden`, `activo`) VALUES
  ('residencia', 'PRUEBA – Fase de ejemplo', 'Descripción de fase de prueba.', 'Semana X', 99, 1);

INSERT INTO `documentos_descargables` (`tipo`, `nombre`, `url`, `tipo_archivo`, `orden`, `activo`) VALUES
  ('residencia', 'PRUEBA – Documento de ejemplo', 'https://example.com/prueba.pdf', 'PDF', 99, 1);


-- ================================================================
-- 7. CONFIGURACIÓN  (rellenar redes vacías · admin de prueba)
-- ================================================================

-- 7.1 Redes sociales que estaban vacías: para verificar que la pestaña Redes
--     las carga y las puedes editar/borrar.
UPDATE `configuracion` SET `valor` = 'https://instagram.com/prueba_tsj' WHERE `clave` = 'instagram_url';
UPDATE `configuracion` SET `valor` = 'https://twitter.com/prueba_tsj'   WHERE `clave` = 'twitter_url';
UPDATE `configuracion` SET `valor` = 'https://linkedin.com/company/prueba-tsj' WHERE `clave` = 'linkedin_url';

-- 7.2 Cuenta de administrador de prueba (tabla `admins` VACÍA en el seed)
--     Email: prueba.admin@chapala.tecmm.edu.mx   ·   Password: Admin2026!
INSERT INTO `admins` (`nombre`, `email`, `password_hash`, `activo`, `ultimo_acceso`) VALUES
  ('PRUEBA – Admin Ejemplo', 'prueba.admin@chapala.tecmm.edu.mx',
   '$2y$12$iNYpaEAL/nVt5el2fagMVe/ZmE77aXKeWQXAQaiSQ91/IJugosB7G', 1, NULL),
  ('PRUEBA – Admin Inactivo', 'prueba.inactivo@chapala.tecmm.edu.mx',
   '$2y$12$iNYpaEAL/nVt5el2fagMVe/ZmE77aXKeWQXAQaiSQ91/IJugosB7G', 0, NULL);


-- ================================================================
-- 8. BITÁCORA  (admin_log VACÍA en el seed)
-- ================================================================
-- Alimenta la pestaña Bitácora de Reportes (filtro por módulo + búsqueda) y
-- el dato "último respaldo descargado" de la pantalla Respaldos.
INSERT INTO `admin_log` (`modulo`, `accion`, `detalle`, `admin_id`, `admin_nombre`, `created_at`) VALUES
  ('login',         'login',              'PRUEBA – Inicio de sesión correcto',              NULL, 'Cuenta maestra',          '2026-06-24 08:00:00'),
  ('inicio',        'aviso_agregar',      'PRUEBA – Aviso agregado de ejemplo',              NULL, 'PRUEBA – Admin Ejemplo',  '2026-06-24 08:05:00'),
  ('visitantes',    'directorio_editar',  'PRUEBA – Edición de directorio de ejemplo',       NULL, 'PRUEBA – Admin Ejemplo',  '2026-06-23 17:20:00'),
  ('biblioteca',    'prestamo_registrar', 'PRUEBA – Préstamo registrado de ejemplo',         NULL, 'PRUEBA – Admin Ejemplo',  '2026-06-23 12:10:00'),
  ('convenios',     'convenio_agregar',   'PRUEBA – Convenio agregado de ejemplo',           NULL, 'Cuenta maestra',          '2026-06-22 15:45:00'),
  ('horarios',      'horario_guardar',    'PRUEBA – Horario subido de ejemplo',              NULL, 'PRUEBA – Admin Ejemplo',  '2026-06-22 11:30:00'),
  ('configuracion', 'guardar_redes',      'PRUEBA – Redes sociales actualizadas',            NULL, 'Cuenta maestra',          '2026-06-21 09:15:00'),
  ('admins',        'admin_agregar',      'PRUEBA – Administrador agregado de ejemplo',      NULL, 'Cuenta maestra',          '2026-06-20 18:00:00'),
  ('respaldos',     'exportar_respaldo',  'PRUEBA – Respaldo completo descargado (ejemplo)', NULL, 'Cuenta maestra',          '2026-06-24 07:50:00');


SET FOREIGN_KEY_CHECKS = @OLD_FK;

-- ================================================================
-- ¡Listo! Datos de prueba cargados. Recorre el panel y verifica.
-- ================================================================


-- ████████████████████████████████████████████████████████████████
-- ██  LIMPIEZA — BORRA TODO LO DE PRUEBA                          ██
-- ██  Descomenta este bloque y ejecútalo cuando termines.        ██
-- ████████████████████████████████████████████████████████████████
/*
USE `kiosko_tsj`;
SET NAMES utf8mb4;   -- necesario para que el guión «–» coincida en los LIKE
SET @OLD_FK = @@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS = 0;

-- Horarios de prueba: todos cuelgan de docentes de prueba (seguro; no toca reales).
DELETE FROM `horarios`
  WHERE `id_profesor` IN (SELECT id FROM `docentes` WHERE `nombre` LIKE 'PRUEBA –%');

DELETE FROM `prestamos`               WHERE `estudiante_nombre` LIKE 'PRUEBA –%';
DELETE FROM `solicitudes_biblioteca`  WHERE `estudiante_nombre` LIKE 'PRUEBA –%';
DELETE FROM `libros`                  WHERE `codigo` = 'BIB-PRUEBA-01';

-- Convenios — borrar primero la tabla de relación (con FK_CHECKS=0 el CASCADE NO
-- se dispara, así que hay que hacerlo a mano antes de borrar el convenio padre).
DELETE FROM `convenio_carreras`
  WHERE `convenio_id` IN (SELECT id FROM `convenios` WHERE `nombre` LIKE 'PRUEBA –%');
DELETE FROM `convenios`               WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `sugerencias_empresa`     WHERE `nombre_empresa` LIKE 'PRUEBA –%';

-- Visitantes — igual: borrar docente_carrera del docente de prueba antes que el docente.
DELETE FROM `docente_carrera`
  WHERE `docente_id` IN (SELECT id FROM `docentes` WHERE `nombre` LIKE 'PRUEBA –%');
DELETE FROM `directorio`              WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `docentes`                WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `coordinadores`           WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `materias`                WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `atributos_egreso`        WHERE `texto`  LIKE 'PRUEBA –%';
DELETE FROM `secretarias`             WHERE `nombre` LIKE 'PRUEBA –%';

-- Oferta académica: regresar las 6 carreras reales a su estado original (NULL).
UPDATE `carreras`
  SET `objetivo_general` = NULL, `perfil_profesional` = NULL,
      `objetivos_educacionales` = NULL, `reticula_url` = NULL
  WHERE `clave` IN ('ISC','II','IM','IADEV','IGE','LG');

-- Inicio
DELETE FROM `carrusel_fotos`          WHERE `titulo` LIKE 'PRUEBA –%';
DELETE FROM `avisos`                  WHERE `titulo` LIKE 'PRUEBA –%';
DELETE FROM `faq`                     WHERE `pregunta` LIKE 'PRUEBA –%';

-- Requisitos
DELETE FROM `requisitos_items`        WHERE `texto`  LIKE 'PRUEBA –%';
DELETE FROM `timeline_fases`          WHERE `titulo` LIKE 'PRUEBA –%';
DELETE FROM `documentos_descargables` WHERE `nombre` LIKE 'PRUEBA –%';

-- Configuración / admins / bitácora
DELETE FROM `admins`                  WHERE `nombre` LIKE 'PRUEBA –%';
DELETE FROM `admin_log`               WHERE `detalle` LIKE 'PRUEBA –%';
UPDATE `configuracion` SET `valor` = '' WHERE `clave` IN ('instagram_url','twitter_url','linkedin_url');

SET FOREIGN_KEY_CHECKS = @OLD_FK;
-- Limpieza terminada.
*/
