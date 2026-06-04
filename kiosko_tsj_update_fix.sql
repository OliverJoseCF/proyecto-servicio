-- ================================================================
-- Ejecutar con kiosko_tsj YA SELECCIONADA en el panel izquierdo
-- NO uses "Nueva" — haz clic en kiosko_tsj primero
-- ================================================================

-- 1. Avisos
CREATE TABLE IF NOT EXISTS `avisos` (
  `id`          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `titulo`      VARCHAR(200)   NOT NULL,
  `descripcion` TEXT,
  `fecha`       DATE           NOT NULL,
  `activo`      TINYINT(1)     NOT NULL DEFAULT 1,
  `orden`       SMALLINT       NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `avisos` (`id`,`titulo`, `descripcion`, `fecha`, `orden`) VALUES
  (1,'Inicio de semestre agosto-diciembre 2025', 'El semestre inicia el 11 de agosto. Consulta tu horario en el módulo de Horarios.', '2025-07-15', 1),
  (2,'Periodo de inscripciones abiertas', 'Las inscripciones para nuevo ingreso están abiertas hasta el 30 de julio.', '2025-07-01', 2),
  (3,'Suspensión de actividades 15 de septiembre', 'Por motivo del Día de la Independencia no habrá clases el 15 de septiembre.', '2025-09-01', 3);

-- 2. FAQ general
ALTER TABLE `faq`
  MODIFY COLUMN `tipo` ENUM('residencia','servicio_social','general') NOT NULL;

INSERT IGNORE INTO `faq` (`tipo`, `pregunta`, `respuesta`, `orden`) VALUES
  ('general', '¿Cuántas horas de servicio social necesito?', 'Se requieren 480 horas de servicio social para poder titularse.', 1),
  ('general', '¿Qué promedio necesito para residencias?', 'Se requiere un avance mínimo del 85% de créditos cursados y aprobados del plan de estudios.', 2),
  ('general', '¿Dónde entrego mi constancia de inglés?', 'La constancia de inglés se entrega en Control Escolar, en el módulo A planta baja.', 3);

-- 3. Carrusel
CREATE TABLE IF NOT EXISTS `carrusel_fotos` (
  `id`        INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `url`       VARCHAR(1000) NOT NULL,
  `titulo`    VARCHAR(200),
  `subtitulo` VARCHAR(300),
  `activo`    TINYINT(1)    NOT NULL DEFAULT 1,
  `orden`     SMALLINT      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `carrusel_fotos` (`id`,`url`, `titulo`, `subtitulo`, `orden`) VALUES
  (1,'https://placehold.co/1200x400/32129a/ffffff?text=Campus+Chapala', 'Bienvenido al Campus Chapala', 'Tecnológico Superior de Jalisco', 1),
  (2,'https://placehold.co/1200x400/ec5a68/ffffff?text=Semestre+2025', 'Semestre Agosto-Diciembre 2025', 'Consulta horarios y servicios disponibles', 2),
  (3,'https://placehold.co/1200x400/1a0960/ffffff?text=Servicios', 'Servicios Académicos', 'Todo lo que necesitas en un solo lugar', 3);

-- 4. Directorio: columnas nuevas (IF NOT EXISTS evita error si ya existen)
ALTER TABLE `directorio`
  ADD COLUMN IF NOT EXISTS `extension`        VARCHAR(20)  DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `ubicacion_fisica` VARCHAR(200) DEFAULT NULL;

-- 5. Convenios: columna sector
ALTER TABLE `convenios`
  ADD COLUMN IF NOT EXISTS `sector`
    ENUM('privado','publico','ac','otro') NOT NULL DEFAULT 'privado'
    AFTER `tipo_convenio`;
