# Prompt maestro (Claude Opus) — Revisión completa del proyecto “Plataforma TSJ Chapala”

Actúa como **arquitecto/a de software + auditor/a de calidad + security reviewer + QA lead**. Tu misión es hacer una **revisión 100% completa y detallada** del repositorio que tienes cargado (código + SQL + configuración + assets), **sin omitir ningún archivo**.

## Objetivo
1) Entender el **objetivo real** de la plataforma y de **cada módulo** (qué problema resuelve y cuál es el flujo de usuario esperado).
2) Verificar si **cada módulo cumple su objetivo** (lógica, flujo, dependencias, BD, UI, seguridad).
3) Producir un plan concreto para dejar el proyecto **listo para lanzamiento** (producción), incluyendo correcciones, priorización y pruebas.

## Reglas críticas (NO negociables)
- **Cobertura total:** no omitas archivos. Antes de concluir, genera un **inventario exhaustivo** (ruta por ruta) y marca cada archivo como: `revisado`, `revisado superficial`, `no revisable` (y por qué).
- **Transparencia:** si algo no puede verificarse (por ejemplo: no puedes ejecutar el servidor/BD), dilo explícitamente y propone cómo validarlo.
- **Cero alucinaciones:** no inventes comportamiento. Todo debe basarse en el código/SQL/config que veas.
- **Enfoque “listo para producción”:** prioriza errores que rompan funcionalidad, seguridad, pérdida de datos, y despliegue.
- **Propuesta accionable:** para cada problema, entrega: severidad, impacto, cómo reproducir, solución propuesta, y (si aplica) un parche tipo diff.

## Contexto técnico (para orientar tu análisis)
- Stack: PHP + MySQL/MariaDB + Apache (mod_rewrite).
- La plataforma está dividida en módulos: **Biblioteca**, **Convenios**, **Horarios**, **Requisitos**, **Visitantes** y un bloque **shared** con auth/config/layout.
- Hay scripts SQL separados por módulo y scripts de migración/setup.

## Estrategia recomendada: revisión por agentes (multi‑agente)
Si tienes capacidad de usar agentes/subagentes, úsala. Si no, **simula** el trabajo por agentes en secciones separadas.

### Agente 0 — “Cartógrafo del repositorio” (obligatorio)
Entregables:
- Árbol completo del repo (incluye carpetas ocultas/config).
- Lista exhaustiva de archivos con: tipo (php/sql/css/js/img/etc), módulo asignado, y criticidad (alto/medio/bajo).
- Identificación de puntos de entrada: páginas principales, logins, endpoints de procesos, includes compartidos.

### Agentes por módulo (1 por módulo)
Crea un agente para cada módulo: Biblioteca, Convenios, Horarios, Requisitos, Visitantes.
Para cada módulo, entrega:
- Objetivo del módulo (1–3 frases) deducido del código.
- Mapa de rutas/URLs/páginas y flujos de usuario.
- Dependencias: archivos compartidos, helpers, includes, assets, BD.
- Revisión de lógica (CRUD, estados, validaciones), manejo de errores, consistencia.
- Seguridad específica del módulo (inyecciones, XSS, CSRF, uploads, authz).
- Estado “funciona para su objetivo”: `sí`, `parcial`, `no`, con evidencia.
- Lista de fixes priorizados y parches sugeridos.

### Agente “Shared/Core”
Revisa lo transversal:
- Configuración (defaults vs overrides), separación de secretos, .gitignore.
- Autenticación/autorización: sesiones, control de acceso por rol, cierre de sesión, protección de páginas.
- Encabezados de seguridad, rate limiting, sanitización/escaping, patrones de acceso a BD.
- Consistencia de layout (header/footer), routing/URLs y dependencias comunes.

### Agente “Base de datos y migraciones”
Revisa SQL:
- Esquema por módulo: tablas, llaves, índices, constraints, tipos.
- Consistencia entre SQL y queries en PHP.
- Migraciones/scripts especiales y orden de ejecución.
- Seguridad: mínimos privilegios, usuarios, riesgos de datos sensibles.
- Integridad: borrados, cascadas, referencias huérfanas, estados.

### Agente “Producción/Release”
Entrega:
- Checklist de lanzamiento: configuración, HTTPS, permisos de uploads, `display_errors`, logging.
- Riesgos de despliegue: rutas absolutas, permisos, dependencias de XAMPP, path separators, case sensitivity.
- Plan de hardening: permisos, headers, CSRF, sesiones, límites de subida.

## Lista de chequeos mínimos (úsala como guía)
### Correctitud funcional
- Flujos completos: login → acciones admin → logout.
- CRUD: crear/editar/eliminar con validación y manejo de errores.
- Estados: cambios de estado consistentes (ej. devuelto/no devuelto; aprobado/rechazado).
- Manejo de archivos: nombres, path traversal, tipos MIME, tamaño, colisiones, permisos.

### Seguridad
- SQLi: usa prepared statements o escaping correcto; jamás concatenar input directo.
- XSS: escaping de output; sanitización de input (especialmente en campos texto).
- CSRF: tokens en formularios sensibles.
- AuthZ: evitar acceso directo a endpoints de procesos sin sesión/rol.
- Sesiones: cookies seguras en prod (Secure/HttpOnly/SameSite), regeneración de sesión.
- Headers: CSP (si aplica), X-Frame-Options, X-Content-Type-Options, Referrer-Policy.
- Archivos locales: evitar incluir archivos desde input; validar rutas.

### Calidad y mantenimiento
- Duplicación, rutas hardcodeadas, includes frágiles.
- Manejo de errores centralizado vs disperso.
- Logs: útiles sin exponer datos sensibles.
- Separación de config por entorno.

## Cómo reportar (formato de salida)
### 1) Resumen ejecutivo (1 página)
- Qué es el proyecto y qué módulos tiene.
- Estado general: `listo`, `casi listo`, `no listo`.
- Top 10 issues bloqueantes (con severidad y módulo).

### 2) Cobertura 100% (obligatorio)
- Tabla/lista: **cada archivo** → estado de revisión + nota corta.
- Si el repo es grande, divide la tabla en varias respuestas, pero no omitas.

### 3) Informe por módulo (obligatorio)
Para cada módulo:
- Objetivo + flujo
- Qué funciona / qué falla
- Issues priorizados
- Parches recomendados (diff por archivo)
- Pruebas manuales sugeridas (pasos concretos)

### 4) Informe transversal (shared/core)
- Auth, config, headers, rate limit, includes, patrones comunes.

### 5) Informe de BD
- Hallazgos de esquema, queries incongruentes, datos.

### 6) Plan de remediación
- Roadmap por fases (bloqueantes → importantes → nice-to-have)
- Estimación relativa (S/M/L) y dependencias.

### 7) Preguntas para cerrar huecos
- Solo las necesarias para confirmar suposiciones (máximo 10), con impacto.

## Restricciones de ejecución (si estás en Claude Code)
- Respeta las reglas del repositorio para herramientas/permisos si existen.
- Si necesitas ejecutar comandos adicionales (tests, linters, etc.) y no tienes permiso, pide explícitamente autorización.

---

Empieza ahora con el Agente 0 (inventario completo) y luego ejecuta el resto de agentes. No concluyas hasta entregar cobertura 100% y plan de lanzamiento.
