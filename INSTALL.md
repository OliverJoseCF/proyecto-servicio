# Guía de instalación — Plataforma TSJ Chapala

## Requisitos

- PHP 8.0+ con extensiones: `pdo_mysql`, `mysqli`, `fileinfo`, `mbstring`, `gd`
- MySQL 5.7+ / MariaDB 10.3+
- Apache con `mod_rewrite`
- XAMPP 8.x (desarrollo) o servidor Linux con Apache (producción)

---

## 1. Clonar el repositorio

```bash
git clone <url-del-repo>
cd proyecto-servicio
```

---

## 2. Importar la base de datos

Solo hay **una** base de datos unificada: `kiosko_tsj`.

1. Abre phpMyAdmin (`http://localhost/phpmyadmin`)
2. Haz clic en **Nueva** (panel izquierdo)
3. Ve a la pestaña **SQL**
4. Abre el archivo `kiosko_tsj.sql` que está en la raíz del repo, copia todo su contenido y pégalo
5. Clic en **Continuar**

El script crea la BD, todas las tablas y los datos iniciales automáticamente.

> **Alternativa desde terminal:**
> ```bash
> mysql -u root < kiosko_tsj.sql
> ```

---

## 3. Crear el archivo de configuración local

Este archivo **no existe en un clon nuevo** (está en `.gitignore`). Debes crearlo:

```bash
# Windows (PowerShell)
copy plataforma\shared\config.local.example.php plataforma\shared\config.local.php

# Mac/Linux
cp plataforma/shared/config.local.example.php plataforma/shared/config.local.php
```

Abre `plataforma/shared/config.local.php` y ajusta:

```php
// Para desarrollo local con XAMPP (root sin contraseña) no necesitas cambiar
// DB_HOST, DB_USER, DB_PASS — los defaults en config.php ya funcionan.

// Solo DEBES configurar esto para poder entrar al admin:
define('GLOBAL_ADMIN_EMAIL', 'admin@chapala.tecmm.edu.mx');  // el correo que quieras
define('GLOBAL_ADMIN_HASH',  '<<GENERA EL HASH — ver paso 4>>');
```

> Para desarrollo con XAMPP (root sin contraseña), **las credenciales de BD ya vienen
> configuradas por defecto** en `config.php`. Solo necesitas el hash del admin.

---

## 4. Generar el hash de contraseña del administrador

Ejecuta desde la terminal (PowerShell o CMD en Windows):

```bash
# Windows XAMPP
C:\xampp\php\php.exe -r "echo password_hash('TU_CLAVE', PASSWORD_BCRYPT, ['cost'=>12]);"

# Mac/Linux
php -r "echo password_hash('TU_CLAVE', PASSWORD_BCRYPT, ['cost'=>12]);"
```

Copia el hash resultante (`$2y$12$…`) y pégalo como valor de `GLOBAL_ADMIN_HASH` en
`plataforma/shared/config.local.php`.

> También puedes usar el script interactivo:
> ```bash
> php plataforma/modulos/biblioteca/tools/setup_password.php
> ```
> (muestra las instrucciones exactas con el hash listo para copiar)

---

## 5. Configurar Apache

Agrega un alias en `httpd-vhosts.conf` (XAMPP: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`):

```apache
Alias /plataforma "C:/xampp/htdocs/proyecto-servicio/plataforma"
<Directory "C:/xampp/htdocs/proyecto-servicio/plataforma">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

> Ajusta la ruta si tu repo está en una ubicación diferente.

Reinicia Apache desde el panel de XAMPP. Accede en: `http://localhost/plataforma`

---

## 6. Crear directorios de uploads

```bash
# Windows (PowerShell)
mkdir plataforma\modulos\horarios\horarios
mkdir plataforma\modulos\convenios\src\pages\upload

# Mac/Linux
mkdir -p plataforma/modulos/horarios/horarios
mkdir -p plataforma/modulos/convenios/src/pages/upload
chmod 755 plataforma/modulos/horarios/horarios
chmod 755 plataforma/modulos/convenios/src/pages/upload
```

---

## 7. Módulo Convenios — config local (solo si usas login propio de Convenios)

```bash
# Windows
copy plataforma\modulos\convenios\src\config.example.php plataforma\modulos\convenios\src\config.local.php

# Mac/Linux
cp plataforma/modulos/convenios/src/config.example.php plataforma/modulos/convenios/src/config.local.php
```

Genera el hash del admin de Convenios:

```bash
php plataforma/modulos/convenios/tools/setup_password.php
```

Pega el hash en `convenios/src/config.local.php` → `ADMIN_PASSWORD_HASH`.

> El panel de administración principal (`/plataforma/admin/`) ya **no requiere** este paso —
> solo lo necesitas si usas el admin propio del módulo Convenios directamente.

---

## 8. Verificar que funciona

| Qué probar | URL |
|---|---|
| Portal principal | `http://localhost/plataforma` |
| Login admin global | `http://localhost/plataforma/login.php` |
| Panel de administración | `http://localhost/plataforma/admin/` |
| Biblioteca (módulo) | `http://localhost/plataforma/modulos/biblioteca/` |
| Convenios (módulo) | `http://localhost/plataforma/modulos/convenios/` |
| Horarios / Buscar maestro | `http://localhost/plataforma/modulos/horarios/` |

---

## Checklist rápida para un clon nuevo

- [ ] BD `kiosko_tsj` importada desde `kiosko_tsj.sql`
- [ ] `plataforma/shared/config.local.php` creado (a partir de `config.local.example.php`)
- [ ] `GLOBAL_ADMIN_HASH` configurado en `config.local.php`
- [ ] Alias Apache configurado y reiniciado
- [ ] Carpeta `plataforma/modulos/horarios/horarios/` existe
- [ ] Carpeta `plataforma/modulos/convenios/src/pages/upload/` existe
- [ ] (Opcional) `convenios/src/config.local.php` con `ADMIN_PASSWORD_HASH`

---

## Estructura del proyecto

```
proyecto-servicio/
├── kiosko_tsj.sql              ← BD unificada (importar primero)
├── INSTALL.md                  ← Esta guía
└── plataforma/
    ├── index.php               ← Portal principal
    ├── login.php               ← Login admin global
    ├── admin/                  ← Panel de administración
    │   ├── index.php           ← Dashboard
    │   ├── biblioteca.php
    │   ├── convenios.php
    │   ├── horarios.php
    │   ├── visitantes.php
    │   ├── requisitos.php
    │   ├── configuracion.php
    │   └── procesos/           ← Handlers de guardado (PHP/JSON)
    ├── shared/
    │   ├── config.php              ← Defaults de desarrollo (versionado)
    │   ├── config.local.php        ← Credenciales reales (gitignored, CREAR)
    │   └── config.local.example.php ← Plantilla
    ├── modulos/
    │   ├── biblioteca/
    │   ├── convenios/
    │   │   └── src/
    │   │       ├── config.example.php
    │   │       └── config.local.php  ← (gitignored, CREAR si se usa)
    │   ├── horarios/
    │   │   └── horarios/         ← Archivos de horario subidos (gitignored)
    │   ├── visitantes/
    │   └── requisitos/
    └── sql/
        └── setup.sql             ← Crea usuario tsjplat (producción)
```

---

## Solo para producción (hosting con cPanel)

Esta sección es para cuando el proyecto se sube a un servidor web real (hosting compartido).

### 1. Subir los archivos

1. Accede al **cPanel** del hosting → **Administrador de archivos**
2. Entra a la carpeta `public_html/`
3. Sube el `.zip` del proyecto y descomprímelo ahí
4. La estructura debe quedar: `public_html/plataforma/`

### 2. Crear la base de datos en el hosting

1. En cPanel → **Bases de datos MySQL** → crea una nueva BD (ej: `kiosko_tsj`)
2. Crea un usuario MySQL y asígnale **todos los privilegios** sobre esa BD
3. Abre **phpMyAdmin**, selecciona la BD recién creada
4. Pestaña **Importar** → sube el archivo `kiosko_tsj.sql`

> En muchos hostings el nombre real de la BD queda como `nombreusuario_kiosko_tsj`.
> Usa el nombre exacto que aparece en cPanel.

### 3. Editar `config.local.php`

Abre `plataforma/shared/config.local.php` desde el Administrador de archivos y ajusta:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'usuario_mysql_del_hosting');   // el que creaste en cPanel
define('DB_PASS', 'contraseña_del_usuario');
define('DB_NAME', 'nombre_exacto_de_la_bd');      // ej: cuenta_kiosko_tsj
```

El resto del archivo (URL, hash de admin) no necesita cambiarse.

### 4. Verificar que funciona

Accede a `http://tudominio.com/plataforma` — debe cargar el portal.
El admin está en `http://tudominio.com/plataforma/login.php`

**Credenciales de administrador:**
- Email: `admin@chapala.tecmm.edu.mx`
- Contraseña: `Admin2024!TSJ`

### Checklist de producción

- [ ] BD `kiosko_tsj` importada desde `kiosko_tsj.sql`
- [ ] `config.local.php` actualizado con credenciales del hosting
- [ ] Portal carga en `http://tudominio.com/plataforma`
- [ ] Login admin funciona
- [ ] Carpeta `plataforma/modulos/horarios/horarios/` existe y tiene permisos de escritura (755)
- [ ] Carpeta `plataforma/modulos/convenios/src/pages/upload/` existe y tiene permisos de escritura (755)

---

## Solo para producción avanzada (servidor Linux propio)

Ejecuta `plataforma/sql/setup.sql` para crear un usuario MySQL con mínimos privilegios:

```bash
# Edita primero TU_CLAVE_SEGURA en setup.sql, luego:
mysql -u root < plataforma/sql/setup.sql
```

Y en `config.local.php` usa ese usuario en lugar de root:

```php
define('DB_USER', 'tsjplat');
define('DB_PASS', 'TU_CLAVE_SEGURA');
```

Checklist adicional:

- [ ] `display_errors = Off` y `log_errors = On` en `php.ini`
- [ ] HTTPS habilitado + redirect HTTP→HTTPS
- [ ] Directorios de upload sin permisos 777
- [ ] MTA configurado si usas la función "Sugerir empresa" de Convenios (usa `mail()`)
