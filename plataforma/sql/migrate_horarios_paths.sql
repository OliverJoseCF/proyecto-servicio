-- ============================================================
-- Migración de rutas de imagen_horario — Horarios
-- Ejecutar UNA VEZ después de importar horarios.sql y ANTES de usar la aplicación.
--
-- Problema: el dump generado en el entorno original tiene rutas absolutas
--   /buscarMaterias-main/horarios/<archivo>
-- que apuntaban al directorio de trabajo del proyecto original.
-- En la plataforma integrada el directorio de uploads es relativo al módulo:
--   horarios/<archivo>
-- ============================================================

USE `horarios_db`;

-- Paso 1: Verificar cuántos registros tienen la ruta antigua (debería ser > 0)
SELECT COUNT(*) AS registros_con_ruta_antigua
FROM `horarios`
WHERE `imagen_horario` LIKE '/buscarMaterias-main/horarios/%';

-- Paso 2: Corregir las rutas
UPDATE `horarios`
SET `imagen_horario` = REPLACE(
    `imagen_horario`,
    '/buscarMaterias-main/horarios/',
    'horarios/'
)
WHERE `imagen_horario` LIKE '/buscarMaterias-main/horarios/%';

-- Paso 3: Verificar resultado (debería devolver 0 si la migración fue exitosa)
SELECT COUNT(*) AS registros_con_ruta_antigua_tras_migracion
FROM `horarios`
WHERE `imagen_horario` LIKE '/buscarMaterias-main/horarios/%';

-- Paso 4: Ver las rutas resultantes
SELECT id_horario, imagen_horario FROM `horarios`;

-- ============================================================
-- NOTA: Los archivos físicos deben copiarse manualmente desde:
--   <ruta_original>/buscarMaterias-main/horarios/
-- al directorio:
--   plataforma/modulos/horarios/horarios/
-- ============================================================
