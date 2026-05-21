# Guía de instalación — Plataforma TSJ Chapala

## Requisitos

- PHP 8.0+ con extensiones: `pdo_mysql`, `mysqli`, `fileinfo`, `mbstring`, `gd`
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

Las credenciales de BD por defecto (XAMPP: `root` sin contraseña) ya vienen en
`plataforma/shared/config.php`, así que **no necesitas tocar la BD para desarrollo**.

Sin embargo, los paneles de administración **sí requieren** los hashes de admin, y por
defecto están vacíos (el login se bloquea con "El sistema aún no está configurado").
Crea el archivo local de overrides a partir de la plantilla:

```bash
cp plataforma/shared/config.local.example.php plataforma/shared/config.local.php
```

> Este archivo está en `.gitignore` y **no existe en un clon nuevo** — debes crearlo.

Genera los hashes de admin de Biblioteca y Horarios y cópialos en ese archivo:

```bash
php plataforma/modulos/biblioteca/tools/setup_password.php
php plataforma/modulos/horarios/tools/setup_password.php
```

El módulo Convenios también necesita su propio archivo local:

```bash
cp plataforma/modulos/convenios/src/config.example.php \
   plataforma/modulos/convenios/src/config.local.php
```

Genera el hash de la contraseña admin de Convenios:

```bash
php plataforma/modulos/convenios/tools/setup_password.php
```

Copia el hash resultante en `convenios/src/config.local.php`.

### 2.2 Producción

Copia la plantilla compartida y edita con tus valores reales:

```bash
cp plataforma/shared/config.local.example.php plataforma/shared/config.local.php
nano plataforma/shared/config.local.php
```

Genera nuevos hashes de admin para Biblioteca y Horarios:

```bash
php plataforma/modulos/biblioteca/tools/setup_password.php
php plataforma/modulos/horarios/tools/setup_password.php
```

Haz lo mismo para Convenios:

```bash
cp plataforma/modulos/convenios/src/config.example.php \
   plataforma/modulos/convenios/src/config.local.php
php plataforma/modulos/convenios/tools/setup_password.php
```

Copia los hashes en sus respectivos `config.local.php`.

---

## 3. Importar base de datos

```sql
-- En MySQL (como root), en este orden:
SOURCE plataforma/sql/biblioteca.sql;
SOURCE plataforma/sql/convenios.sql;
SOURCE plataforma/sql/horarios.sql;
```

**OBLIGATORIO:** Ejecuta la migración de rutas de horarios:

```bash
mysql -u root < plataforma/sql/migrate_horarios_paths.sql
```

Luego ejecuta el script de setup (crea usuario `tsjplat` con permisos mínimos):

```bash
mysql -u root < plataforma/sql/setup.sql
```

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

## 6. Credenciales de acceso

Los módulos con panel de administración requieren las credenciales definidas en los respectivos
`config.local.php`. Consulta los archivos de plantilla `config.example.php` o `config.local.example.php`
para saber qué valores configurar.

| Módulo | URL |
|---|---|
| Biblioteca Admin | `/plataforma/modulos/biblioteca/login.php` |
| Horarios Admin | `/plataforma/modulos/horarios/login.php` |
| Convenios Admin | `/plataforma/modulos/convenios/index.php` |

---

## 7. Checklist producción

- [ ] `shared/config.local.php` con credenciales reales (usuario `tsjplat`, contraseña ≥16 chars)
- [ ] `convenios/src/config.local.php` creado a partir de `config.example.php`
- [ ] Hashes de admin generados con `tools/setup_password.php` en los 3 módulos
- [ ] `display_errors = Off` y `log_errors = On` en `php.ini`
- [ ] HTTPS habilitado + redirect HTTP→HTTPS
- [ ] Directorios de upload con permisos correctos (no 777)
- [ ] `tools/.htaccess` presente en biblioteca/tools, horarios/tools, convenios/tools
- [ ] Dumps SQL importados en orden: biblioteca → convenios → horarios
- [ ] `migrate_horarios_paths.sql` ejecutado después de importar horarios.sql
- [ ] `setup.sql` ejecutado para crear usuario `tsjplat`
- [ ] Imágenes/PDFs de horarios copiados a `plataforma/modulos/horarios/horarios/`
- [ ] MTA configurado (sendmail/SMTP) — requerido por la función "Sugerir empresa" de
      Convenios, que usa `mail()`. Sin un MTA válido el envío falla con Error 500.

> **Nota sobre MySQL en Linux:** importa los dumps respetando el nombre **en minúsculas** de
> las tablas. Las aplicaciones consultan `carreras`, `horarios`, `profesores`, `materias`,
> `libros`, `convenios` en minúsculas; con `lower_case_table_names=0` (default en Linux) los
> nombres son sensibles a mayúsculas.

---

## Estructura de módulos

```
plataforma/
├── index.php               ← Portal principal
├── shared/                 ← Auth, config, header, footer, theme
│   ├── config.php          ← Config con defaults dev (versioned)
│   ├── config.local.php    ← Overrides producción (gitignored)
│   └── config.local.example.php  ← Plantilla para producción
├── modulos/
│   ├── biblioteca/         ← Catálogo + préstamos
│   ├── convenios/          ← CRUD convenios empresas
│   │   └── src/
│   │       ├── config.php          ← Template versionado
│   │       ├── config.local.php    ← Hash admin real (gitignored)
│   │       └── config.example.php  ← Plantilla para producción
│   ├── horarios/           ← Búsqueda maestros/horarios
│   ├── visitantes/         ← Directorio institucional (estático)
│   └── requisitos/         ← Residencia y servicio social
└── sql/
    ├── biblioteca.sql
    ├── convenios.sql
    ├── horarios.sql
    ├── migrate_horarios_paths.sql  ← Ejecutar SIEMPRE tras horarios.sql
    └── setup.sql                   ← Usuario DB tsjplat + permisos
```
