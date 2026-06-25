# Plataforma de Servicios Estudiantiles — TSJ Chapala

Portal web institucional del Tecnológico Superior de Jalisco con sede en Chapala.
Desarrollado como proyecto de servicio social / residencias profesionales.

## Descripción

Sistema web que centraliza los servicios digitales del plantel en un único portal accesible para alumnos, docentes y público en general, con un panel de administración para el personal institucional.

## Módulos incluidos

| Módulo | Descripción |
|---|---|
| **Portal principal** | Página de inicio con acceso a todos los servicios |
| **Directorio institucional** | Docentes, coordinadores y personal administrativo por carrera |
| **Biblioteca** | Catálogo de libros, préstamos y solicitudes en línea |
| **Convenios** | Directorio de empresas con convenio y formulario de sugerencias |
| **Horarios** | Consulta de horarios de maestros por carrera y semestre |
| **Requisitos** | Guías de residencias profesionales y servicio social |
| **Panel de administración** | Gestión completa de todos los módulos, bitácora y respaldos |

## Requisitos técnicos

- PHP 8.0 o superior (extensiones: `pdo_mysql`, `mysqli`, `mbstring`, `gd`, `fileinfo`)
- MySQL 5.7+ o MariaDB 10.3+
- Apache con `mod_rewrite` habilitado
- XAMPP 8.x (desarrollo local) o servidor Linux con Apache (producción)

## Instalación rápida

Consulta [INSTALL.md](INSTALL.md) para instrucciones detalladas paso a paso.

**Resumen:**
1. Importar `kiosko_tsj.sql` en MySQL/phpMyAdmin
2. Copiar `plataforma/shared/config.local.example.php` → `plataforma/shared/config.local.php` y ajustar credenciales
3. Configurar alias de Apache apuntando a la carpeta `plataforma/`
4. Acceder a `http://localhost/plataforma`

## Credenciales de acceso

Panel de administración: `http://localhost/plataforma/login.php`

| Campo | Valor |
|---|---|
| Usuario (email) | `admin@chapala.tecmm.edu.mx` |
| Contraseña | `Admin2026!TSJ` |

> Cambia la contraseña despues de la instalacion editando `plataforma/shared/config.local.php`.

## Estructura del proyecto

```
proyecto-servicio/
├── kiosko_tsj.sql          Base de datos completa (importar primero)
├── INSTALL.md              Guia de instalacion detallada
├── README.md               Este archivo
└── plataforma/
    ├── index.php           Portal principal
    ├── login.php           Acceso al panel admin
    ├── admin/              Panel de administracion
    ├── modulos/
    │   ├── biblioteca/
    │   ├── convenios/
    │   ├── horarios/
    │   ├── visitantes/
    │   └── requisitos/
    └── shared/             Configuracion y layouts compartidos
```

## Desarrollo

Proyecto desarrollado con PHP puro, MySQL, HTML5, CSS3 y JavaScript vanilla.
Sin frameworks externos — solo dependencias CDN para iconos (Remix Icons) y fuentes (Google Fonts).
