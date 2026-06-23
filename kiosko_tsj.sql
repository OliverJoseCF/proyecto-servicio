-- ================================================================
--  kiosko_tsj.sql
--  Base de datos unificada — Plataforma TSJ Campus Chapala
--
--  Instrucciones:
--    1. Abre MySQL Workbench (o phpMyAdmin)
--    2. Abre este archivo y ejecuta TODO el script (Ctrl+Shift+Enter)
--    3. Listo — la base de datos queda creada con todos los datos
--       iniciales listos para usar.
--
--  Incluye:
--    • Creación de la BD desde cero (DROP + CREATE)
--    • Todas las tablas con su estructura completa
--    • Datos iniciales de todos los módulos
--
--  Tablas (25):
--    configuracion
--    carrusel_fotos, avisos
--    carreras
--    directorio, docentes, docente_carrera, coordinadores, materias,
--    atributos_egreso, secretarias, nuevo_ingreso_config
--    libros, prestamos, solicitudes_biblioteca, solicitud_controles
--    convenios, sugerencias_empresa
--    profesores, horarios
--    requisitos_items, timeline_fases,
--    documentos_descargables, faq
--    admin_log, admins
--
--  Vistas (5):
--    v_docentes, v_convenios, v_horarios, v_materias, v_coordinadores
-- ================================================================

SET NAMES utf8mb4;
SET @OLD_UNIQUE_CHECKS      = @@UNIQUE_CHECKS,      UNIQUE_CHECKS      = 0;
SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0;
SET @OLD_SQL_MODE            = @@SQL_MODE,           SQL_MODE = 'TRADITIONAL,ALLOW_INVALID_DATES';

-- ────────────────────────────────────────────────────────────────
-- BASE DE DATOS
-- ────────────────────────────────────────────────────────────────
DROP DATABASE IF EXISTS `kiosko_tsj`;
CREATE DATABASE `kiosko_tsj`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `kiosko_tsj`;


-- ================================================================
-- 1. CONFIGURACIÓN GLOBAL
-- ================================================================

CREATE TABLE `configuracion` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `clave`       VARCHAR(100)  NOT NULL UNIQUE,
  `valor`       TEXT,
  `descripcion` VARCHAR(255),
  `updated_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `configuracion` (`clave`, `valor`, `descripcion`) VALUES
  ('nombre_institucion', 'Tecnológico Superior de Jalisco',       'Nombre de la institución'),
  ('campus',             'Campus Chapala',                         'Campus'),
  ('eslogan',            'Innovar para transformar a México',      'Eslogan del footer'),
  ('descripcion_portal', 'Portal de servicios estudiantiles del Tecnológico Superior de Jalisco — Chapala.', 'Meta description'),
  ('plataforma_url',     '/plataforma',                            'URL base de la plataforma'),
  ('direccion',          'Carretera Chapala-Jocotepec km 7.5, Ajijic, Chapala, Jalisco', 'Dirección física'),
  ('telefono',           '376-766-0000',                           'Teléfono general'),
  ('horario_atencion',   'Lun – Vie: 8:00 – 20:00 h',             'Horario de oficina'),
  ('correo_general',     'campus.chapala@tsj.edu.mx',              'Correo general de contacto'),
  ('correo_biblioteca',  'biblioteca@chapala.tecmm.edu.mx',        'Correo de biblioteca'),
  ('correo_vinculacion', 'vinculacion@chapala.tecmm.edu.mx',       'Correo de vinculación/convenios'),
  ('correo_facturacion', 'facturacion@chapala.tecmm.edu.mx',       'Correo de finanzas'),
  ('correo_escolares',   'escolares@chapala.tecmm.edu.mx',         'Correo de control escolar'),
  ('correo_direccion',   'IlianaJanettHernandezPartida@chapala.tecmm.edu.mx', 'Correo de dirección'),
  ('correo_servicios',   'servicios@chapala.tecmm.edu.mx',         'Correo de servicios generales'),
  ('facebook_url',       'https://www.facebook.com/TecSJ/',        'Facebook'),
  ('youtube_url',        'https://www.youtube.com/@TecSuperiorJalisco', 'YouTube'),
  ('instagram_url',      '',                                        'Instagram'),
  ('twitter_url',        '',                                        'Twitter / X'),
  ('linkedin_url',       '',                                        'LinkedIn'),
  ('sitio_oficial_url',  'https://www.tecmm.edu.mx',               'Sitio web oficial'),
  ('maps_embed_url',     'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1322.9681638304503!2d-103.22284273653298!3d20.303617966560704!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x842f46d5a7843b5f%3A0x12046fea1ab84e7d!2sTecnol%C3%B3gico%20Superior%20de%20Jalisco%20Chapala!5e0!3m2!1ses-419!2smx!4v1780543430963!5m2!1ses-419!2smx', 'URL del iframe de Google Maps'),
  ('maps_link_url',      'https://maps.app.goo.gl/w3rApmQrocT3j5V88', 'Enlace directo a Google Maps'),
  -- Descripciones de Oferta Académica (editables desde el admin)
  ('desc_ISC',   'Desarrolla software, sistemas y soluciones tecnológicas de alto impacto.',     'Descripción pública ISC'),
  ('desc_II',    'Optimiza procesos productivos y gestiona sistemas industriales.',               'Descripción pública II'),
  ('desc_IM',    'Integra mecánica, electrónica y control automático.',                          'Descripción pública IM'),
  ('desc_IADEV', 'Crea personajes, mundos virtuales y efectos para cine y videojuegos.',         'Descripción pública IADEV'),
  ('desc_IGE',   'Dirige organizaciones con enfoque estratégico e innovación.',                  'Descripción pública IGE'),
  ('desc_LG',    'Domina el arte culinario, gestión de restaurantes y cocina creativa.',         'Descripción pública LG');


-- ================================================================
-- 2. INICIO DEL PORTAL (carrusel y avisos)
-- ================================================================

CREATE TABLE `carrusel_fotos` (
  `id`        INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `url`       VARCHAR(1000) NOT NULL,
  `titulo`    VARCHAR(200),
  `subtitulo` VARCHAR(300),
  `activo`    TINYINT(1)    NOT NULL DEFAULT 1,
  `orden`     SMALLINT      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `carrusel_fotos` (`url`, `titulo`, `subtitulo`, `orden`) VALUES
  ('https://placehold.co/1200x400/32129a/ffffff?text=Campus+Chapala', 'Bienvenido al Campus Chapala', 'Tecnológico Superior de Jalisco', 1),
  ('https://placehold.co/1200x400/ec5a68/ffffff?text=Semestre+2025', 'Semestre Agosto-Diciembre 2025', 'Consulta horarios y servicios disponibles', 2),
  ('https://placehold.co/1200x400/1a0960/ffffff?text=Servicios', 'Servicios Académicos', 'Todo lo que necesitas en un solo lugar', 3);


CREATE TABLE `avisos` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `titulo`         VARCHAR(200)  NOT NULL,
  `descripcion`    TEXT,
  `fecha`          DATE          NOT NULL,
  `publicar_desde` DATE          NULL COMMENT 'NULL = visible desde siempre',
  `publicar_hasta` DATE          NULL COMMENT 'NULL = sin fecha de caducidad',
  `activo`         TINYINT(1)    NOT NULL DEFAULT 1,
  `orden`          SMALLINT      NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migración para BDs existentes (ejecutar una vez si la tabla ya existe):
-- ALTER TABLE `avisos`
--   ADD COLUMN `publicar_desde` DATE NULL COMMENT 'NULL = visible desde siempre' AFTER `fecha`,
--   ADD COLUMN `publicar_hasta` DATE NULL COMMENT 'NULL = sin fecha de caducidad' AFTER `publicar_desde`;

INSERT INTO `avisos` (`titulo`, `descripcion`, `fecha`, `orden`) VALUES
  ('Inicio de semestre agosto-diciembre 2025', 'El semestre inicia el 11 de agosto. Consulta tu horario en el módulo de Horarios.', '2025-07-15', 1),
  ('Periodo de inscripciones abiertas',        'Las inscripciones para nuevo ingreso están abiertas hasta el 30 de julio.',          '2025-07-01', 2),
  ('Suspensión de actividades 15 de septiembre','Por motivo del Día de la Independencia no habrá clases el 15 de septiembre.',       '2025-09-01', 3);


-- ================================================================
-- 3. CARRERAS (compartida por todos los módulos)
-- ================================================================

CREATE TABLE `carreras` (
  `id`           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `clave`        VARCHAR(10)    NOT NULL UNIQUE,
  `nombre`       VARCHAR(150)   NOT NULL,
  `color`        VARCHAR(7)     NOT NULL DEFAULT '#32129a' COMMENT 'Color hex de la carrera (#rrggbb)',
  `icono`        VARCHAR(50)    NOT NULL DEFAULT 'school'  COMMENT 'Nombre del ícono de Material Symbols',
  `imagen_url`              VARCHAR(1000)  DEFAULT NULL COMMENT 'URL/ruta de la imagen de portada (card de convenios)',
  `reticula_url`            VARCHAR(1000)  DEFAULT NULL COMMENT 'URL del PDF/imagen del mapa curricular',
  `objetivo_general`        TEXT           DEFAULT NULL,
  `perfil_profesional`      TEXT           DEFAULT NULL,
  `objetivos_educacionales` TEXT           DEFAULT NULL,
  `activo`                  TINYINT(1)     NOT NULL DEFAULT 1,
  `orden`                   SMALLINT       NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `carreras` (`clave`, `nombre`, `color`, `icono`, `orden`) VALUES
  ('ISC',   'Ingeniería en Sistemas Computacionales',             '#32129a', 'computer',                1),
  ('II',    'Ingeniería Industrial',                              '#b45309', 'precision_manufacturing',  2),
  ('IM',    'Ingeniería Mecatrónica',                             '#0369a1', 'settings_suggest',         3),
  ('IADEV', 'Ingeniería en Animación Digital y Efectos Visuales', '#7c3aed', 'animation',                4),
  ('IGE',   'Ingeniería en Gestión Empresarial',                  '#059669', 'business_center',          5),
  ('LG',    'Gastronomía',                                        '#dc2626', 'restaurant',               6);


-- ================================================================
-- 4. VISITANTES
-- ================================================================

-- 4.1 Directorio institucional
CREATE TABLE `directorio` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `nombre`           VARCHAR(150)  NOT NULL,
  `puesto`           VARCHAR(150),
  `correo`           VARCHAR(254),
  `telefono`         VARCHAR(30)   DEFAULT 'S/N',
  `extension`        VARCHAR(20)   DEFAULT NULL,
  `ubicacion_fisica` VARCHAR(200)  DEFAULT NULL,
  `foto`             VARCHAR(500)  COMMENT 'Nombre de archivo en /modulos/visitantes/imagenes/',
  `activo`           TINYINT(1)    NOT NULL DEFAULT 1,
  `orden`            SMALLINT      NOT NULL DEFAULT 0,
  `created_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `directorio` (`nombre`, `puesto`, `correo`, `foto`, `orden`) VALUES
  ('Miguel Ángel Delgado López',        'Sistemas Computacionales', 'miguel.delgado@chapala.tecmm.edu.mx',     'miguel.png', 1),
  ('Julio César Chávez Novoa',           'Sistemas Computacionales', 'julio.chavez@chapala.tecmm.edu.mx',       'julio.png',  2),
  ('Carmen Leticia Salcedo Quevedo',     'Sistemas Computacionales', 'carmen.salcedo@chapala.tecmm.edu.mx',     'carmen.png', 3),
  ('José Jorge Hernández Ochoa',         'Sistemas Computacionales', 'jorge.hernandez@chapala.tecmm.edu.mx',    'jorge.png',  4),
  ('Francisco Javier González Siordia',  'Sistemas Computacionales', 'francisco.gonzales@chapala.tecmm.edu.mx', NULL,         5),
  ('José Guadalupe Gamas Gamas',         'Sistemas Computacionales', 'jose.gamas@chapala.tecmm.edu.mx',         'gamas.png',  6);


-- 4.2 Docentes (sin carrera fija — la relación vive en docente_carrera)
CREATE TABLE `docentes` (
  `id`     INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150)  NOT NULL,
  `correo` VARCHAR(254),
  `foto`   VARCHAR(500)  COMMENT 'Nombre de archivo en /modulos/visitantes/imagenes/',
  `activo` TINYINT(1)    NOT NULL DEFAULT 1,
  `orden`  SMALLINT      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Docentes deduplicados (un registro por persona real)
INSERT INTO `docentes` (`id`, `nombre`, `correo`, `foto`, `orden`) VALUES
  ( 1, 'Miguel Ángel Delgado López',        'miguel.delgado@chapala.tecmm.edu.mx',     'miguel.png', 1),
  ( 2, 'Alberto Chavolla',                   NULL,                                       NULL,         2),
  ( 3, 'Francisco Javier González Siordia', 'francisco.gonzales@chapala.tecmm.edu.mx',  NULL,         3),
  ( 4, 'Julio César Chávez Novoa',           'julio.chavez@chapala.tecmm.edu.mx',        'julio.png',  4),
  ( 5, 'Edgar Martínez',                     NULL,                                       NULL,         5),
  ( 6, 'José Jorge Hernández Ochoa',         'jorge.hernandez@chapala.tecmm.edu.mx',     'jorge.png',  6),
  ( 7, 'Carmen Leticia Salcedo Quevedo',     'carmen.salcedo@chapala.tecmm.edu.mx',      'carmen.png', 7),
  ( 8, 'José Guadalupe Gamas Gamas',         'jose.gamas@chapala.tecmm.edu.mx',          'gamas.png',  8),
  ( 9, 'Francisco Pocholo',                  NULL,                                       NULL,         9),
  (10, 'María Gómez',                        NULL,                                       NULL,        10),
  (11, 'Rodolfo Rojas',                      NULL,                                       NULL,        11),
  (12, 'José Hernández',                     NULL,                                       NULL,        12),
  (13, 'Juan Desales',                       NULL,                                       NULL,        13),
  (14, 'Francisco Luis Juan',                NULL,                                       NULL,        14),
  (15, 'Carlos Ramírez',                     NULL,                                       NULL,        15),
  (16, 'Fidel Rodríguez',                    NULL,                                       NULL,        16),
  (17, 'Alma González',                      NULL,                                       NULL,        17),
  (18, 'José Aguilera',                      NULL,                                       NULL,        18),
  (19, 'María Estrada',                      NULL,                                       NULL,        19),
  (20, 'Lina Corona',                        NULL,                                       NULL,        20),
  (21, 'Jessica Álvarez',                    NULL,                                       NULL,        21),
  (22, 'Jaime Sánchez',                      NULL,                                       NULL,        22),
  (23, 'Yessica Regalado',                   NULL,                                       NULL,        23),
  (24, 'Mayra Hinojoza',                     NULL,                                       NULL,        24);

-- 4.3 Relación muchos-a-muchos: docente ↔ carrera
CREATE TABLE `docente_carrera` (
  `docente_id` INT UNSIGNED NOT NULL,
  `carrera_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`docente_id`, `carrera_id`),
  FOREIGN KEY (`docente_id`) REFERENCES `docentes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`carrera_id`) REFERENCES `carreras`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Asignaciones por carrera
-- ISC (carrera id=1): Delgado, Chavolla, FJ González, JC Chávez, E.Martínez, J.Jorge, Carmen, Gamas
INSERT INTO `docente_carrera` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1);
-- II  (carrera id=2): Delgado, Chavolla, Pocholo, JC Chávez, FJ González, E.Martínez, J.Jorge
INSERT INTO `docente_carrera` VALUES (1,2),(2,2),(9,2),(4,2),(3,2),(5,2),(6,2);
-- IM  (carrera id=3): Delgado, M.Gómez, R.Rojas, J.Hernández, Desales, Gamas
INSERT INTO `docente_carrera` VALUES (1,3),(10,3),(11,3),(12,3),(13,3),(8,3);
-- IADEV (carrera id=4): Delgado, M.Gómez, R.Rojas, FLJuan, JC Chávez, Gamas
INSERT INTO `docente_carrera` VALUES (1,4),(10,4),(11,4),(14,4),(4,4),(8,4);
-- IGE (carrera id=5): C.Ramírez, F.Rodríguez, Chavolla, A.González, J.Aguilera, M.Estrada
INSERT INTO `docente_carrera` VALUES (15,5),(16,5),(2,5),(17,5),(18,5),(19,5);
-- LG  (carrera id=6): L.Corona, J.Álvarez, J.Sánchez, C.Ramírez, Y.Regalado, M.Hinojoza
INSERT INTO `docente_carrera` VALUES (20,6),(21,6),(22,6),(15,6),(23,6),(24,6);


-- 4.3 Coordinadores por carrera
CREATE TABLE `coordinadores` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `carrera_id` INT UNSIGNED  NOT NULL,
  `nombre`     VARCHAR(150)  NOT NULL,
  `correo`     VARCHAR(254),
  `activo`     TINYINT(1)    NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`carrera_id`) REFERENCES `carreras`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `coordinadores` (`carrera_id`, `nombre`, `correo`) VALUES
  (1, 'Claudio Castillo',        'claudio.castillo@chapala.tecmm.edu.mx'),
  (2, 'Leonardo',                'leonardo@chapala.tecmm.edu.mx'),
  (3, 'Iván',                    'ivan@chapala.tecmm.edu.mx'),
  (4, 'Coordinador Animación',   'coord.animacion@chapala.tecmm.edu.mx'),
  (5, 'Pablo Rojas',             'pablo.rojas@chapala.tecmm.edu.mx'),
  (6, 'Coordinador Gastronomía', 'coord.gastronomia@chapala.tecmm.edu.mx');


-- 4.4 Materias por carrera (plan de estudios)
CREATE TABLE `materias` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `carrera_id` INT UNSIGNED  NOT NULL,
  `nombre`     VARCHAR(200)  NOT NULL,
  `orden`      SMALLINT      NOT NULL DEFAULT 0,
  `activo`     TINYINT(1)    NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`carrera_id`) REFERENCES `carreras`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ISC
INSERT INTO `materias` (`carrera_id`, `nombre`, `orden`) VALUES
  (1,'Fundamentos de Programación',       1),(1,'Estructuras de Datos',              2),
  (1,'Bases de Datos',                    3),(1,'Redes de Computadoras',             4),
  (1,'Sistemas Operativos',               5),(1,'Ingeniería de Software',            6),
  (1,'Análisis de Sistemas',              7),(1,'Arquitectura de Computadoras',      8),
  (1,'Desarrollo Web',                    9),(1,'Programación Orientada a Objetos', 10);

-- II
INSERT INTO `materias` (`carrera_id`, `nombre`, `orden`) VALUES
  (2,'Fundamentos de Ingeniería Industrial', 1),(2,'Procesos de Manufactura',          2),
  (2,'Control de Calidad',                   3),(2,'Ergonomía y Seguridad Industrial', 4),
  (2,'Administración de Operaciones',        5),(2,'Logística y Cadena de Suministro', 6),
  (2,'Estadística Aplicada',                 7),(2,'Simulación de Sistemas',           8),
  (2,'Diseño de Plantas Industriales',       9),(2,'Gestión de Proyectos',            10);

-- IM
INSERT INTO `materias` (`carrera_id`, `nombre`, `orden`) VALUES
  (3,'Fundamentos de Mecatrónica', 1),(3,'Electrónica Analógica',      2),
  (3,'Electrónica Digital',        3),(3,'Sistemas de Control',         4),
  (3,'Robótica',                   5),(3,'Programación de PLCs',        6),
  (3,'Neumática e Hidráulica',     7),(3,'Instrumentación',             8),
  (3,'Diseño Mecánico',            9),(3,'Automatización Industrial',  10);

-- IADEV
INSERT INTO `materias` (`carrera_id`, `nombre`, `orden`) VALUES
  (4,'Fundamentos del Diseño Digital', 1),(4,'Animación 2D',                    2),
  (4,'Animación 3D',                   3),(4,'Diseño de Personajes',             4),
  (4,'Efectos Visuales (VFX)',         5),(4,'Composición Digital',              6),
  (4,'Guión y Storyboard',             7),(4,'Post-producción de Video',         8),
  (4,'Realidad Virtual y Aumentada',   9),(4,'Producción Audiovisual',          10);

-- IGE
INSERT INTO `materias` (`carrera_id`, `nombre`, `orden`) VALUES
  (5,'Administración de Empresas',    1),(5,'Contabilidad Financiera',         2),
  (5,'Economía',                      3),(5,'Gestión del Talento Humano',      4),
  (5,'Marketing',                     5),(5,'Finanzas Empresariales',          6),
  (5,'Emprendimiento e Innovación',   7),(5,'Gestión de Proyectos',            8),
  (5,'Comportamiento Organizacional', 9),(5,'Planeación Estratégica',         10);

-- LG
INSERT INTO `materias` (`carrera_id`, `nombre`, `orden`) VALUES
  (6,'Fundamentos de Cocina',            1),(6,'Higiene y Seguridad Alimentaria', 2),
  (6,'Cocina Internacional',             3),(6,'Panadería y Repostería',          4),
  (6,'Nutrición y Dietética',            5),(6,'Enología y Bebidas',              6),
  (6,'Gestión de Alimentos y Bebidas',   7),(6,'Cocina Molecular',                8),
  (6,'Arte Culinario y Presentación',    9),(6,'Costos y Presupuestos en Cocina', 10);


-- 4.4b Atributos de egreso por carrera
CREATE TABLE `atributos_egreso` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `carrera_id` INT UNSIGNED  NOT NULL,
  `texto`      VARCHAR(500)  NOT NULL,
  `orden`      SMALLINT      NOT NULL DEFAULT 0,
  `activo`     TINYINT(1)    NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`carrera_id`) REFERENCES `carreras`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 4.5 Personal administrativo (secretarías)
CREATE TABLE `secretarias` (
  `id`       INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `nombre`   VARCHAR(150)  NOT NULL,
  `rol`      VARCHAR(150),
  `correo`   VARCHAR(254),
  `telefono` VARCHAR(30),
  `activo`   TINYINT(1)    NOT NULL DEFAULT 1,
  `orden`    SMALLINT      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `secretarias` (`nombre`, `rol`, `correo`, `telefono`, `orden`) VALUES
  ('Laura Martínez',  'Secretaria Administrativa',    'laura.martinez@chapala.tecmm.edu.mx',    '331-234-5678', 1),
  ('María López',     'Recepcionista',                'maria.lopez@chapala.tecmm.edu.mx',       '331-456-7890', 2),
  ('Patricia Gómez',  'Secretaria de Dirección',      'patricia.gomez@chapala.tecmm.edu.mx',    '332-567-8901', 3),
  ('Ana Rivera',      'Asistente Académica',          'ana.rivera@chapala.tecmm.edu.mx',        '333-678-9012', 4),
  ('Gabriela Torres', 'Secretaria de Control Escolar','gabriela.torres@chapala.tecmm.edu.mx',   '334-789-0123', 5);


-- 4.6 Configuración de nuevo ingreso
CREATE TABLE `nuevo_ingreso_config` (
  `id`           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `dia_examen`   TINYINT UNSIGNED NOT NULL DEFAULT 20,
  `hora_examen`  TIME             NOT NULL DEFAULT '08:00:00',
  `lugar_examen` VARCHAR(200)     NOT NULL DEFAULT 'Tecnológico Superior de Jalisco, Campus Chapala',
  `requisitos`   TEXT             COMMENT 'JSON: array de strings con requisitos',
  `updated_at`   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nuevo_ingreso_config` (`dia_examen`, `hora_examen`, `lugar_examen`, `requisitos`) VALUES
  (20, '08:00:00', 'Tecnológico Superior de Jalisco, Campus Chapala',
   '["Copia de la identificación oficial.","Certificado de estudios anteriores (original y copia).","Comprobante de domicilio.","Fotografías tamaño infantil (4 piezas).","Formulario de inscripción llenado."]');


-- ================================================================
-- 5. BIBLIOTECA
-- ================================================================

CREATE TABLE `libros` (
  `id`         INT UNSIGNED      NOT NULL AUTO_INCREMENT,
  `codigo`     VARCHAR(30)       NOT NULL UNIQUE COMMENT 'Folio / código del libro',
  `nombre`     VARCHAR(300)      NOT NULL        COMMENT 'Título del libro',
  `autor`      VARCHAR(200),
  `editorial`  VARCHAR(150),
  `categoria`  VARCHAR(100),
  `ejemplares` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `activo`     TINYINT(1)        NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `libros` (`codigo`, `nombre`, `autor`, `editorial`, `categoria`, `ejemplares`) VALUES
  ('BIB-001','Fundamentos de Programación',  'Dennis Ritchie',   'McGraw-Hill',    'Programación',   3),
  ('BIB-002','Estructura de Datos en C++',    'Mark Weiss',       'Pearson',        'Programación',   2),
  ('BIB-003','Bases de Datos Relacionales',   'Ramez Elmasri',    'Addison-Wesley', 'Bases de datos', 4),
  ('BIB-004','Redes de Computadoras',         'Andrew Tanenbaum', 'Pearson',        'Redes',          2),
  ('BIB-005','Ingeniería de Software',        'Ian Sommerville',  'Pearson',        'Ingeniería',     5),
  ('BIB-006','Cálculo',                       'James Stewart',    'Cengage',        'Matemáticas',    6),
  ('BIB-007','Administración de Empresas',    'Harold Koontz',    'McGraw-Hill',    'Administración', 3),
  ('BIB-008','Contabilidad Financiera',       'Gerardo Guajardo', 'McGraw-Hill',    'Finanzas',       2),
  ('BIB-009','Diseño de Sistemas Digitales',  'Morris Mano',      'Pearson',        'Electrónica',    3),
  ('BIB-010','Principios de Electrónica',     'Albert Malvino',   'McGraw-Hill',    'Electrónica',    4);


CREATE TABLE `prestamos` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `libro_id`           INT UNSIGNED NOT NULL,
  `estudiante_nombre`  VARCHAR(150) NOT NULL,
  `estudiante_control` VARCHAR(15)  NOT NULL DEFAULT '' COMMENT 'Número de control; vacío cuando proviene de solicitud pública',
  `carrera`            VARCHAR(150),
  `tipo`               ENUM('prestamo','consulta_sala') NOT NULL DEFAULT 'prestamo',
  `fecha_prestamo`     DATE         NOT NULL,
  `fecha_devolucion`   DATE,
  `devuelto`           TINYINT(1)   NOT NULL DEFAULT 0,
  `fecha_devuelto`     DATETIME,
  `created_at`         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`libro_id`) REFERENCES `libros`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `solicitudes_biblioteca` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `libro_id`           INT UNSIGNED NOT NULL,
  `estudiante_nombre`  VARCHAR(150) NOT NULL,
  `estudiante_control` VARCHAR(15)  NOT NULL DEFAULT '' COMMENT 'Número de control; vacío cuando se solicita desde el portal público',
  `carrera`            VARCHAR(150),
  `tipo`               ENUM('prestamo','consulta_sala') NOT NULL DEFAULT 'prestamo',
  `estado`             ENUM('pendiente','aprobada','rechazada') NOT NULL DEFAULT 'pendiente',
  `created_at`         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`libro_id`) REFERENCES `libros`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Préstamos de controles y equipos audiovisuales (kiosko de biblioteca)
CREATE TABLE `solicitud_controles` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha`          DATE         NOT NULL,
  `nombre_docente` VARCHAR(150) NOT NULL,
  `aula`           VARCHAR(50),
  `recibo`         VARCHAR(50),
  `hora_prestamo`  TIME         NOT NULL,
  `hora_entrega`   TIME         NOT NULL,
  `estado`         ENUM('Pendiente','Aceptado','Rechazado') NOT NULL DEFAULT 'Pendiente',
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================================================================
-- 6. CONVENIOS
-- ================================================================

CREATE TABLE `convenios` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `carrera_id`        INT UNSIGNED,
  `nombre`            VARCHAR(300) NOT NULL  COMMENT 'Nombre de la empresa',
  `tipo_convenio`     VARCHAR(100) NOT NULL DEFAULT 'residencia'
                      COMMENT 'residencia | servicio_social | practicas | otro',
  `sector`            ENUM('privado','publico','ac','otro') NOT NULL DEFAULT 'privado',
  `nombre_contacto`   VARCHAR(200),
  `correo_contacto`   VARCHAR(254),
  `telefono_contacto` VARCHAR(30),
  `logo`              VARCHAR(500) COMMENT 'Ruta o URL del logo',
  `vencimiento`       DATE,
  `activo`            TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`carrera_id`) REFERENCES `carreras`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `convenios` (`carrera_id`, `nombre`, `tipo_convenio`, `sector`, `nombre_contacto`, `correo_contacto`, `vencimiento`) VALUES
  (4, 'Empresa Tecnológica S.A.',  'residencia',     'privado', 'Carlos Mendoza',  'cmendoza@empresa.com',    '2026-12-31'),
  (4, 'Estudio Creativo MX',       'servicio_social','privado', 'Ana Torres',      'ana.torres@estudio.mx',   '2026-06-30'),
  (2, 'Industrias del Bajío S.A.', 'practicas',      'privado', 'Roberto Sánchez', 'rsanchez@industrias.com', '2026-08-15');


CREATE TABLE `sugerencias_empresa` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre_empresa`  VARCHAR(200) NOT NULL,
  `correo_empresa`  VARCHAR(254) NOT NULL,
  `nombre_contacto` VARCHAR(200),
  `estado`          ENUM('pendiente','aceptada','rechazada') NOT NULL DEFAULT 'pendiente',
  `ip_origen`       VARCHAR(45)  COMMENT 'IP del solicitante (rate limiting)',
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================================================================
-- 7. HORARIOS / BUSCAR MAESTRO
-- ================================================================

CREATE TABLE `profesores` (
  `id_profesor` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`      VARCHAR(100) NOT NULL,
  `apellido`    VARCHAR(100) NOT NULL,
  `correo`      VARCHAR(254),
  `foto`        VARCHAR(500) COMMENT 'Ruta a la imagen de perfil',
  `activo`      TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_profesor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `profesores` (`nombre`, `apellido`, `correo`, `foto`) VALUES
  ('Miguel Ángel',  'Delgado López',    'miguel.delgado@chapala.tecmm.edu.mx',    'miguel.png'),
  ('Alberto',       'Chavolla',          'alberto.chavolla@chapala.tecmm.edu.mx',  NULL),
  ('Julio César',   'Chávez Novoa',     'julio.chavez@chapala.tecmm.edu.mx',       'julio.png'),
  ('Francisco',     'González Siordia', 'francisco.gonzales@chapala.tecmm.edu.mx', NULL),
  ('José Jorge',    'Hernández Ochoa',  'jorge.hernandez@chapala.tecmm.edu.mx',    'jorge.png'),
  ('Carmen Leticia','Salcedo Quevedo',  'carmen.salcedo@chapala.tecmm.edu.mx',     'carmen.png'),
  ('José Guadalupe','Gamas Gamas',      'jose.gamas@chapala.tecmm.edu.mx',         'gamas.png'),
  ('Edgar',         'Martínez',          NULL,                                      NULL),
  ('Rodolfo',       'Rojas',             NULL,                                      NULL),
  ('María',         'Gómez',             NULL,                                      NULL);


CREATE TABLE `horarios` (
  `id_horario`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_profesor`    INT UNSIGNED NOT NULL,
  `id_carrera`     INT UNSIGNED,
  `semestre`       VARCHAR(10),
  `imagen_horario` VARCHAR(500) COMMENT 'URL completa al PDF o imagen del horario',
  `activo`         TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_horario`),
  FOREIGN KEY (`id_profesor`) REFERENCES `profesores`(`id_profesor`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_carrera`)  REFERENCES `carreras`(`id`)             ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================================================================
-- 8. REQUISITOS (Residencia Profesional y Servicio Social)
-- ================================================================

-- 8.1 Checklist de requisitos
CREATE TABLE `requisitos_items` (
  `id`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo`   ENUM('residencia','servicio_social') NOT NULL,
  `texto`  TEXT         NOT NULL,
  `orden`  SMALLINT     NOT NULL DEFAULT 0,
  `activo` TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `requisitos_items` (`tipo`, `texto`, `orden`) VALUES
  ('residencia',    'Carta de aceptación de la empresa',              1),
  ('residencia',    'Carta de presentación del estudiante',           2),
  ('residencia',    'Seguro contra accidentes vigente',               3),
  ('residencia',    'Avance mínimo del 85% de créditos',              4),
  ('residencia',    'Plan de trabajo aprobado por coordinador',       5),
  ('residencia',    'Registro en sistema de la SEP',                  6),
  ('residencia',    'Carta de no adeudo de biblioteca',               7),
  ('residencia',    'Formato de evaluación firmado',                  8),
  ('residencia',    'Reporte parcial entregado',                      9),
  ('servicio_social','Avance mínimo del 70% de créditos',            1),
  ('servicio_social','Carta de aceptación de la institución',        2),
  ('servicio_social','Carta de presentación firmada por dirección',  3),
  ('servicio_social','Seguro facultativo del IMSS vigente',          4),
  ('servicio_social','Plan de trabajo aprobado',                     5);


-- 8.2 Fases del proceso (timeline)
CREATE TABLE `timeline_fases` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo`              ENUM('residencia','servicio_social') NOT NULL,
  `titulo`            VARCHAR(150) NOT NULL,
  `descripcion`       TEXT,
  `tiempo_referencia` VARCHAR(100),
  `orden`             SMALLINT     NOT NULL DEFAULT 0,
  `activo`            TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `timeline_fases` (`tipo`, `titulo`, `descripcion`, `tiempo_referencia`, `orden`) VALUES
  ('residencia',    'Solicitud',             'Entrega de documentos iniciales en coordinación',                  '1 semana antes del inicio', 1),
  ('residencia',    'Aceptación',            'Carta de aceptación de la empresa + validación por coordinador',   'Semana 1',                  2),
  ('residencia',    'Inicio de actividades', 'Registro en plataforma SEP + seguro contra accidentes',            'Día 1',                     3),
  ('residencia',    'Seguimiento',           'Entrega de reporte parcial con firma del asesor',                  'A mitad del periodo',       4),
  ('residencia',    'Cierre',                'Entrega del reporte final y presentación ante comité',             'Última semana',             5),
  ('servicio_social','Solicitud',            'Entrega de documentos en coordinación académica',                  '2 semanas antes',           1),
  ('servicio_social','Asignación',           'Asignación de la institución por coordinación',                    'Semana 1',                  2),
  ('servicio_social','Inicio',               'Registro oficial y entrega de carta de inicio',                    'Día 1',                     3),
  ('servicio_social','Reporte bimestral',    'Entrega de reporte bimestral firmado',                             'Cada 2 meses',              4),
  ('servicio_social','Conclusión',           'Carta de término emitida por la institución',                      'Al completar 480 horas',    5);


-- 8.3 Documentos descargables
CREATE TABLE `documentos_descargables` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo`         ENUM('residencia','servicio_social') NOT NULL,
  `nombre`       VARCHAR(200) NOT NULL,
  `url`          VARCHAR(1000) NOT NULL,
  `tipo_archivo` VARCHAR(30)  NOT NULL DEFAULT 'PDF',
  `orden`        SMALLINT     NOT NULL DEFAULT 0,
  `activo`       TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `documentos_descargables` (`tipo`, `nombre`, `url`, `tipo_archivo`, `orden`) VALUES
  ('residencia',     'Solicitud de Residencia',    'https://drive.google.com/file/d/1oJR4zSpAX6o99eMSuqot4T2DOYhlbAFX/view', 'Google Drive', 1),
  ('residencia',     'Seguimiento y Evaluación',   'https://drive.google.com/file/d/1oMtGJNoBKg2Z8n6q1hL04VrRIbzKaWNC/view', 'Google Drive', 2),
  ('servicio_social','Evaluación cualitativa',     'assets/docs/servicio-social/evaluacion-cualitativa.pdf',                  'PDF',          1),
  ('servicio_social','Carta compromiso',            'assets/docs/servicio-social/carta-compromiso.pdf',                        'PDF',          2),
  ('servicio_social','Reporte bimestral 1',         'assets/docs/servicio-social/reporte-bimestral-1.pdf',                     'PDF',          3),
  ('servicio_social','Formato de evaluación',       'assets/docs/servicio-social/formato-evaluacion.pdf',                      'PDF',          4);


-- 8.4 Preguntas frecuentes (FAQ)
-- Incluye tipos: residencia, servicio_social y general (para la portada)
CREATE TABLE `faq` (
  `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo`      ENUM('residencia','servicio_social','general') NOT NULL,
  `pregunta`  TEXT         NOT NULL,
  `respuesta` TEXT         NOT NULL,
  `orden`     SMALLINT     NOT NULL DEFAULT 0,
  `activo`    TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `faq` (`tipo`, `pregunta`, `respuesta`, `orden`) VALUES
  ('residencia',    '¿Cuántos créditos necesito para hacer residencia?',
   'Se requiere un mínimo del 85% de créditos cursados y aprobados del plan de estudios.', 1),
  ('residencia',    '¿Puedo hacer residencia en una empresa fuera de Jalisco?',
   'Sí, siempre que la empresa esté debidamente registrada y el convenio sea aceptado por coordinación.', 2),
  ('residencia',    '¿Cuántas horas dura la residencia profesional?',
   'La residencia profesional tiene una duración de 500 horas distribuidas en el periodo acordado.', 3),
  ('servicio_social','¿Cuántas horas comprende el servicio social?',
   'El servicio social comprende un total de 480 horas efectivas de servicio.', 1),
  ('servicio_social','¿En qué tipo de instituciones puedo realizar mi servicio social?',
   'En instituciones públicas, organizaciones sin fines de lucro o empresas con convenio vigente con el TSJ.', 2),
  ('general',       '¿Cuántas horas de servicio social necesito?',
   'Se requieren 480 horas de servicio social para poder titularse.', 1),
  ('general',       '¿Qué promedio necesito para residencias?',
   'Se requiere un avance mínimo del 85% de créditos cursados y aprobados del plan de estudios.', 2),
  ('general',       '¿Dónde entrego mi constancia de inglés?',
   'La constancia de inglés se entrega en Control Escolar, en el módulo A planta baja.', 3);


-- ================================================================
-- 9. BITÁCORA DEL PANEL ADMIN
-- ================================================================
-- Nota: la tabla `redes_sociales` que existía aquí fue retirada — el footer
-- lee las redes desde las claves *_url de `configuracion` (ver
-- shared/lib/config_data.php). En BDs existentes puede eliminarse con:
-- DROP TABLE IF EXISTS `redes_sociales`;

CREATE TABLE `admin_log` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `modulo`       VARCHAR(50)  NOT NULL COMMENT 'Proceso admin que ejecutó la acción (biblioteca, convenios, …)',
  `accion`       VARCHAR(50)  NOT NULL,
  `detalle`      VARCHAR(500),
  `admin_id`     INT UNSIGNED NULL COMMENT 'ID del admin que ejecutó la acción (NULL = cuenta maestra)',
  `admin_nombre` VARCHAR(150) NULL COMMENT 'Nombre del admin al momento de la acción',
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_admin_log_fecha` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================================================================
-- 10. CUENTAS DE ADMINISTRADOR (multi-usuario)
-- ================================================================
-- Cada persona con acceso al panel tiene su propia cuenta. La cuenta
-- "maestra" definida en shared/config.local.php (GLOBAL_ADMIN_EMAIL/HASH)
-- siempre funciona como respaldo aunque esta tabla esté vacía o la BD falle.

CREATE TABLE `admins` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`        VARCHAR(150) NOT NULL,
  `email`         VARCHAR(254) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `activo`        TINYINT(1)   NOT NULL DEFAULT 1,
  `ultimo_acceso` TIMESTAMP    NULL DEFAULT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admins_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================================================================
-- ÍNDICES ADICIONALES (rendimiento)
-- ================================================================

CREATE INDEX `idx_prestamos_libro_devuelto` ON `prestamos` (`libro_id`, `devuelto`);
CREATE INDEX `idx_prestamos_devuelto`       ON `prestamos` (`devuelto`);
CREATE INDEX `idx_solicitudes_estado`       ON `solicitudes_biblioteca` (`estado`);


-- ================================================================
-- VISTAS ÚTILES
-- ================================================================

CREATE VIEW `v_docentes` AS
  SELECT d.id, d.nombre, d.correo, d.foto, d.activo, d.orden,
         GROUP_CONCAT(c.clave  ORDER BY c.orden SEPARATOR ', ')  AS carreras_claves,
         GROUP_CONCAT(c.nombre ORDER BY c.orden SEPARATOR ' | ') AS carreras_nombres
  FROM `docentes` d
  LEFT JOIN `docente_carrera` dc ON dc.docente_id = d.id
  LEFT JOIN `carreras` c ON c.id = dc.carrera_id
  GROUP BY d.id;

CREATE VIEW `v_convenios` AS
  SELECT cv.id, cv.nombre, cv.tipo_convenio, cv.sector,
         cv.nombre_contacto, cv.correo_contacto, cv.telefono_contacto,
         cv.logo, cv.vencimiento, cv.activo,
         c.id AS carrera_id, c.clave AS carrera_clave, c.nombre AS carrera_nombre
  FROM `convenios` cv
  LEFT JOIN `carreras` c ON cv.carrera_id = c.id;

CREATE VIEW `v_horarios` AS
  SELECT h.id_horario, h.semestre, h.imagen_horario, h.activo,
         p.id_profesor, p.nombre, p.apellido, p.correo AS correo_profesor, p.foto,
         c.id AS carrera_id, c.clave AS carrera_clave, c.nombre AS carrera_nombre
  FROM `horarios` h
  JOIN  `profesores` p ON h.id_profesor = p.id_profesor
  LEFT JOIN `carreras` c ON h.id_carrera = c.id;

CREATE VIEW `v_materias` AS
  SELECT m.id, m.nombre, m.orden, m.activo,
         c.id AS carrera_id, c.clave AS carrera_clave, c.nombre AS carrera_nombre
  FROM `materias` m
  JOIN `carreras` c ON m.carrera_id = c.id;

CREATE VIEW `v_coordinadores` AS
  SELECT co.id, co.nombre, co.correo, co.activo,
         c.id AS carrera_id, c.clave AS carrera_clave, c.nombre AS carrera_nombre
  FROM `coordinadores` co
  JOIN `carreras` c ON co.carrera_id = c.id;


-- ================================================================
-- RESTAURAR SETTINGS
-- ================================================================

SET FOREIGN_KEY_CHECKS   = @OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS        = @OLD_UNIQUE_CHECKS;
SET SQL_MODE             = @OLD_SQL_MODE;


-- ================================================================
-- ¡Listo! Base de datos kiosko_tsj creada y lista para usar.
-- ================================================================
