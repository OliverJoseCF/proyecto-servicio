<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'dashboard';
$adm_title = 'Dashboard';
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Bienvenido al Panel de Administración</h1>
    <p class="adm-page-desc">Gestiona todo el contenido de la plataforma desde aquí.</p>
  </div>
</div>

<div class="adm-pending">
  <span class="material-symbols-rounded">construction</span>
  <span><strong>Modo diseño:</strong> Las interfaces están listas. El guardado de datos se activará al conectar la base de datos unificada.</span>
</div>

<!-- Stats -->
<div class="adm-stats">
  <div class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--blue">
      <span class="material-symbols-rounded">menu_book</span>
    </div>
    <div class="adm-stat-value">—</div>
    <div class="adm-stat-label">Libros en catálogo</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--green">
      <span class="material-symbols-rounded">handshake</span>
    </div>
    <div class="adm-stat-value">—</div>
    <div class="adm-stat-label">Convenios activos</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--orange">
      <span class="material-symbols-rounded">school</span>
    </div>
    <div class="adm-stat-value">40+</div>
    <div class="adm-stat-label">Docentes registrados</div>
  </div>
  <div class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--pink">
      <span class="material-symbols-rounded">calendar_month</span>
    </div>
    <div class="adm-stat-value">—</div>
    <div class="adm-stat-label">Horarios publicados</div>
  </div>
</div>

<!-- Acceso rápido por módulo -->
<h2 style="font-size:1rem;font-weight:700;color:var(--tsj-gray-700);margin:0 0 14px;">Gestión por módulo</h2>
<div class="adm-modules">

  <a href="visitantes.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#ede9ff;color:var(--tsj-blue)">
        <span class="material-symbols-rounded">badge</span>
      </div>
      <p class="adm-module-name">Visitantes</p>
    </div>
    <p class="adm-module-desc">Directorio, docentes, coordinadores, materias, secretarías y contenido de servicios.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Directorio</span>
      <span class="adm-module-tag">Docentes</span>
      <span class="adm-module-tag">Coordinadores</span>
      <span class="adm-module-tag">Materias</span>
      <span class="adm-module-tag">Correos</span>
    </div>
  </a>

  <a href="biblioteca.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#dcfce7;color:#16a34a">
        <span class="material-symbols-rounded">menu_book</span>
      </div>
      <p class="adm-module-name">Biblioteca</p>
    </div>
    <p class="adm-module-desc">Catálogo de libros, préstamos activos y solicitudes pendientes de estudiantes.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Catálogo</span>
      <span class="adm-module-tag">Préstamos</span>
      <span class="adm-module-tag">Solicitudes</span>
    </div>
  </a>

  <a href="convenios.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#fef3c7;color:#b45309">
        <span class="material-symbols-rounded">handshake</span>
      </div>
      <p class="adm-module-name">Convenios</p>
    </div>
    <p class="adm-module-desc">Empresas vinculadas, acuerdos por carrera, contactos y fechas de vencimiento.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Empresas</span>
      <span class="adm-module-tag">Por carrera</span>
      <span class="adm-module-tag">Sugerencias</span>
    </div>
  </a>

  <a href="horarios.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#e0e7ff;color:#4338ca">
        <span class="material-symbols-rounded">calendar_month</span>
      </div>
      <p class="adm-module-name">Buscar Maestro</p>
    </div>
    <p class="adm-module-desc">Gestión de maestros, horarios, fotos de perfil y correos de contacto.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Maestros</span>
      <span class="adm-module-tag">Horarios</span>
      <span class="adm-module-tag">Correos</span>
    </div>
  </a>

  <a href="requisitos.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:#ffe4e8;color:var(--tsj-pink)">
        <span class="material-symbols-rounded">checklist</span>
      </div>
      <p class="adm-module-name">Serv. Social / Residencia</p>
    </div>
    <p class="adm-module-desc">Requisitos, documentos descargables, timeline de pasos y preguntas frecuentes.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Requisitos</span>
      <span class="adm-module-tag">Documentos</span>
      <span class="adm-module-tag">Timeline</span>
      <span class="adm-module-tag">FAQ</span>
    </div>
  </a>

  <a href="configuracion.php" class="adm-module-card">
    <div class="adm-module-card-head">
      <div class="adm-module-icon" style="background:var(--tsj-gray-100);color:var(--tsj-gray-600)">
        <span class="material-symbols-rounded">settings</span>
      </div>
      <p class="adm-module-name">Configuración General</p>
    </div>
    <p class="adm-module-desc">Correos del sistema, redes sociales, información del footer y datos de contacto.</p>
    <div class="adm-module-items">
      <span class="adm-module-tag">Correos</span>
      <span class="adm-module-tag">Redes sociales</span>
      <span class="adm-module-tag">Footer</span>
    </div>
  </a>

</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
