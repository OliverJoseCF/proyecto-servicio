# Guía de instalación — Plataforma TSJ Chapala

## Requisitos

- PHP 8.0+ con extensiones: `pdo_mysql`, `mysqli`, `fileinfo`, `mbstring`
- MySQL 5.7+ / MariaDB 10.3+
- Apache con `mod_rewrite`
- XAMPP 8.x (desarrollo) o servidor Linux con Apache (producción)

---

## 1. Clonar y configurar

```bash
git clone <url-del-repo>
cd proyecto-servicio
```

---

## 2. Configurar credenciales

### 2.1 Desarrollo local (XAMPP)

El archivo `plataforma/shared/config.local.php` ya existe con las credenciales de XAMPP por defecto:

- **BD:** `root` / sin contraseña
- **Admin Biblioteca:** `admin` / `Admin.Biblioteca2024!`
- **Admin Horarios:** `admin@tecsj.edu.mx` / `Admin.Horarios2024!`

> Este archivo está en `.gitignore` y no se sube al repo.

### 2.2 Producción

Copia la plantilla y edita con tus valores reales:

```bash
cp plataforma/shared/config.local.example.php plataforma/shared/config.local.php
nano plataforma/shared/config.local.php
```

Genera nuevos hashes de admin:

```bash
php plataforma/modulos/biblioteca/tools/setup_password.php
php plataforma/modulos/horarios/tools/setup_password.php
```

Copia los hashes resultantes en `config.local.php`.

---

## 3. Importar base de datos

```sql
-- En MySQL (como root):
SOURCE plataforma/sql/biblioteca.sql;
SOURCE plataforma/sql/convenios.sql;
SOURCE plataforma/sql/horarios.sql;
```

Luego ejecuta el script de setup (edita primero la contraseña en la línea `TU_CLAVE_SEGURA`):

```bash
mysql -u root < plataforma/sql/setup.sql
```

Esto crea el usuario `tsjplat` con permisos mínimos y migra las rutas de imágenes de horarios.

---

## 4. Directorios de uploads

```bash
mkdir -p plataforma/modulos/convenios/src/pages/upload
mkdir -p plataforma/modulos/horarios/horarios
chmod 755 plataforma/modulos/convenios/src/pages/upload
chmod 755 plataforma/modulos/horarios/horarios
```

---

## 5. Configurar Apache

Añade un alias en `httpd-vhosts.conf` o en `httpd.conf`:

```apache
Alias /plataforma "C:/xampp/htdocs/proyecto-servicio/plataforma"
<Directory "C:/xampp/htdocs/proyecto-servicio/plataforma">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

Reinicia Apache. Accede en: `http://localhost/plataforma`

---

## 6. Credenciales de acceso (desarrollo)

| Módulo | URL | Usuario | Contraseña |
|---|---|---|---|
| Biblioteca Admin | `/plataforma/modulos/biblioteca/login.php` | `admin` | `Admin.Biblioteca2024!` |
| Horarios Admin | `/plataforma/modulos/horarios/login.php` | `admin@tecsj.edu.mx` | `Admin.Horarios2024!` |
| Convenios Admin | `/plataforma/modulos/convenios/index.php` | `admin@tecsj.edu.mx` | *(ver `src/config.php`)* |

---

## 7. Checklist producción

- [ ] `config.local.php` con credenciales reales (usuario `tsjplat`, contraseña ≥16 chars)
- [ ] Hashes de admin generados con `tools/setup_password.php`
- [ ] `display_errors = Off` en `php.ini`
- [ ] HTTPS habilitado + redirect HTTP→HTTPS
- [ ] Directorios de upload con permisos correctos (no 777)
- [ ] `tools/.htaccess` presente en biblioteca/tools, horarios/tools, convenios/tools
- [ ] Dumps SQL importados + `setup.sql` ejecutado
- [ ] Imágenes de horarios copiadas a `plataforma/modulos/horarios/horarios/`

---

## Estructura de módulos

```
plataforma/
├── index.php               ← Portal principal
├── shared/                 ← Auth, config, header, footer, theme
│   ├── config.php          ← Config con defaults dev
│   ├── config.local.php    ← Overrides producción (gitignored)
│   └── config.local.example.php  ← Plantilla para producción
├── modulos/
│   ├── biblioteca/         ← Catálogo + préstamos
│   ├── convenios/          ← CRUD convenios empresas
│   ├── horarios/           ← Búsqueda maestros/horarios
│   ├── visitantes/         ← Directorio institucional (estático)
│   └── requisitos/         ← Residencia y servicio social
└── sql/
    ├── biblioteca.sql
    ├── convenios.sql
    ├── horarios.sql
    ├── setup.sql           ← Usuario DB + migración de paths
    └── migrate_horarios_paths.sql
```
