<?php
require_once dirname(__DIR__) . '/shared/lib/auth.php';
$loginUrl = (defined('PLATAFORMA_URL') ? PLATAFORMA_URL : '/plataforma') . '/login.php';
if (!isGlobalAdmin()) { header('Location: ' . $loginUrl); exit; }

$adm_page  = 'requisitos';
$adm_title = 'Serv. Social / Residencia';
require_once __DIR__ . '/_layout.php';
?>

<div class="adm-page-header">
  <div>
    <h1 class="adm-page-title">Gestión de Serv. Social / Residencia</h1>
    <p class="adm-page-desc">Requisitos, documentos descargables, timeline y preguntas frecuentes.</p>
  </div>
</div>
<div class="adm-pending">
  <span class="material-symbols-rounded">construction</span>
  Los cambios se guardarán al conectar la base de datos. Actualmente muestra el contenido actual.
</div>

<div class="adm-tabs">
  <?php foreach (['residencia'=>'Residencia Profesional','social'=>'Servicio Social'] as $k=>$l): ?>
    <button class="adm-tab <?= $k==='residencia'?'active':'' ?>"
            data-tab-group="req" data-tab="<?= $k ?>" onclick="showTab('req','<?= $k ?>')">
      <?= $l ?>
    </button>
  <?php endforeach; ?>
</div>

<!-- ══ Residencia ════════════════════════════════════════════ -->
<div class="adm-tab-panel active" data-tab-group="req" data-tab="residencia">

  <!-- Checklist de requisitos -->
  <div class="adm-section" style="margin-bottom:20px">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">checklist</span> Checklist de requisitos</h3>
      <button class="adm-btn adm-btn--primary adm-btn--sm pending-db"><span class="material-symbols-rounded">add</span> Agregar ítem</button>
    </div>
    <div class="adm-section-body">
      <div class="adm-list-editor">
        <?php foreach (['Carta de aceptación de la empresa','Carta de presentación del estudiante','Seguro contra accidentes vigente','Avance mínimo del 85% de créditos','Plan de trabajo aprobado por coordinador','Registro en sistema de la SEP','Carta de no adeudo de biblioteca','Formato de evaluación firmado','Reporte parcial entregado'] as $item): ?>
        <div class="adm-list-item">
          <span class="adm-list-item-drag material-symbols-rounded">drag_indicator</span>
          <input type="text" value="<?= htmlspecialchars($item) ?>">
          <button class="adm-btn adm-btn--danger adm-btn--sm pending-db"><span class="material-symbols-rounded">delete</span></button>
        </div>
        <?php endforeach; ?>
        <button class="adm-list-add pending-db"><span class="material-symbols-rounded">add</span> Agregar requisito</button>
      </div>
      <div class="adm-form-actions" style="margin-top:14px">
        <button class="adm-btn adm-btn--primary pending-db"><span class="material-symbols-rounded">save</span> Guardar cambios</button>
      </div>
    </div>
  </div>

  <!-- Timeline de pasos -->
  <div class="adm-section" style="margin-bottom:20px">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">timeline</span> Fases del proceso (Timeline)</h3>
      <button class="adm-btn adm-btn--primary adm-btn--sm pending-db"><span class="material-symbols-rounded">add</span> Agregar fase</button>
    </div>
    <div class="adm-section-body">
      <?php
      $fases = [
        ['Solicitud','Entrega de documentos iniciales en coordinación','1 semana antes del inicio'],
        ['Aceptación','Carta de aceptación de la empresa + validación por coordinador','Semana 1'],
        ['Inicio de actividades','Registro en plataforma SEP + seguro contra accidentes','Día 1'],
        ['Seguimiento','Entrega de reporte parcial con firma del asesor','A mitad del periodo'],
        ['Cierre','Entrega del reporte final y presentación ante comité','Última semana'],
      ];
      foreach ($fases as $i => $f): ?>
      <div class="adm-form-card" style="margin-bottom:12px;padding:16px">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
          <span style="background:var(--tsj-blue);color:#fff;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0"><?= $i+1 ?></span>
          <strong style="color:var(--tsj-blue)"><?= htmlspecialchars($f[0]) ?></strong>
          <button class="adm-btn adm-btn--danger adm-btn--sm pending-db" style="margin-left:auto"><span class="material-symbols-rounded">delete</span></button>
        </div>
        <div class="adm-form-grid cols-2">
          <div class="adm-field"><label>Descripción</label><input type="text" value="<?= htmlspecialchars($f[1]) ?>"></div>
          <div class="adm-field"><label>Tiempo / Fecha referencia</label><input type="text" value="<?= htmlspecialchars($f[2]) ?>"></div>
        </div>
      </div>
      <?php endforeach; ?>
      <div class="adm-form-actions">
        <button class="adm-btn adm-btn--primary pending-db"><span class="material-symbols-rounded">save</span> Guardar fases</button>
      </div>
    </div>
  </div>

  <!-- Documentos descargables -->
  <div class="adm-section" style="margin-bottom:20px">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">download</span> Documentos descargables</h3>
      <button class="adm-btn adm-btn--primary adm-btn--sm pending-db"><span class="material-symbols-rounded">add</span> Agregar documento</button>
    </div>
    <div class="adm-section-body">
      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead><tr><th>Nombre del documento</th><th>URL / Enlace</th><th>Tipo</th><th>Acciones</th></tr></thead>
          <tbody>
            <?php
            $docs = [
              ['Solicitud de Residencia','https://drive.google.com/file/d/1oJR4zSpAX6o99eMSuqot4T2DOYhlbAFX/view','Google Drive'],
              ['Seguimiento y Evaluación','https://drive.google.com/file/d/1oMtGJNoBKg2Z8n6q1hL04VrRIbzKaWNC/view','Google Drive'],
            ];
            foreach ($docs as $d): ?>
            <tr>
              <td style="font-weight:600"><?= htmlspecialchars($d[0]) ?></td>
              <td><a href="<?= htmlspecialchars($d[1]) ?>" target="_blank" style="color:var(--tsj-blue);font-size:12.5px"><?= htmlspecialchars(substr($d[1],0,45)) ?>…</a></td>
              <td><span class="adm-status adm-status--info"><?= htmlspecialchars($d[2]) ?></span></td>
              <td class="actions">
                <button class="adm-btn adm-btn--ghost adm-btn--sm pending-db"><span class="material-symbols-rounded">edit</span></button>
                <button class="adm-btn adm-btn--danger adm-btn--sm pending-db"><span class="material-symbols-rounded">delete</span></button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="adm-form-card" style="margin-top:14px">
        <form class="pending-db">
          <div class="adm-form-grid cols-3">
            <div class="adm-field"><label>Nombre del documento</label><input type="text" placeholder="Ej. Solicitud de Residencia"></div>
            <div class="adm-field"><label>URL del archivo</label><input type="url" placeholder="https://drive.google.com/…"></div>
            <div class="adm-field"><label>Tipo</label><select><option>Google Drive</option><option>Archivo local</option><option>Otro</option></select></div>
          </div>
          <div class="adm-form-actions">
            <button type="submit" class="adm-btn adm-btn--primary"><span class="material-symbols-rounded">save</span> Guardar documento</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- FAQ -->
  <div class="adm-section">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">help</span> Preguntas frecuentes (FAQ)</h3>
      <button class="adm-btn adm-btn--primary adm-btn--sm pending-db"><span class="material-symbols-rounded">add</span> Agregar pregunta</button>
    </div>
    <div class="adm-section-body">
      <?php
      $faqs = [
        ['¿Cuántos créditos necesito para hacer residencia?','Se requiere un mínimo del 85% de créditos cursados y aprobados del plan de estudios.'],
        ['¿Puedo hacer residencia en una empresa fuera de Jalisco?','Sí, siempre que la empresa esté debidamente registrada y el convenio sea aceptado por coordinación.'],
        ['¿Cuántas horas dura la residencia profesional?','La residencia profesional tiene una duración de 500 horas distribuidas en el periodo acordado.'],
      ];
      foreach ($faqs as $i => $q): ?>
      <div class="adm-form-card" style="margin-bottom:12px;padding:16px">
        <div class="adm-form-grid cols-1">
          <div class="adm-field"><label>Pregunta <?= $i+1 ?></label><input type="text" value="<?= htmlspecialchars($q[0]) ?>"></div>
          <div class="adm-field"><label>Respuesta</label><textarea><?= htmlspecialchars($q[1]) ?></textarea></div>
        </div>
        <div style="margin-top:8px;text-align:right">
          <button class="adm-btn adm-btn--danger adm-btn--sm pending-db"><span class="material-symbols-rounded">delete</span> Eliminar</button>
        </div>
      </div>
      <?php endforeach; ?>
      <div class="adm-form-actions">
        <button class="adm-btn adm-btn--primary pending-db"><span class="material-symbols-rounded">save</span> Guardar FAQ</button>
      </div>
    </div>
  </div>
</div>

<!-- ══ Servicio Social ════════════════════════════════════════ -->
<div class="adm-tab-panel" data-tab-group="req" data-tab="social">
  <div class="adm-section" style="margin-bottom:20px">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">checklist</span> Checklist de requisitos</h3>
      <button class="adm-btn adm-btn--primary adm-btn--sm pending-db"><span class="material-symbols-rounded">add</span> Agregar ítem</button>
    </div>
    <div class="adm-section-body">
      <div class="adm-list-editor">
        <?php foreach (['Avance mínimo del 70% de créditos','Carta de aceptación de la institución','Carta de presentación firmada por dirección','Seguro facultativo del IMSS vigente','Plan de trabajo aprobado'] as $item): ?>
        <div class="adm-list-item">
          <span class="adm-list-item-drag material-symbols-rounded">drag_indicator</span>
          <input type="text" value="<?= htmlspecialchars($item) ?>">
          <button class="adm-btn adm-btn--danger adm-btn--sm pending-db"><span class="material-symbols-rounded">delete</span></button>
        </div>
        <?php endforeach; ?>
        <button class="adm-list-add pending-db"><span class="material-symbols-rounded">add</span> Agregar requisito</button>
      </div>
      <div class="adm-form-actions" style="margin-top:14px">
        <button class="adm-btn adm-btn--primary pending-db"><span class="material-symbols-rounded">save</span> Guardar cambios</button>
      </div>
    </div>
  </div>
  <div class="adm-section">
    <div class="adm-section-header">
      <h3 class="adm-section-title"><span class="material-symbols-rounded">download</span> Documentos descargables</h3>
      <button class="adm-btn adm-btn--primary adm-btn--sm pending-db"><span class="material-symbols-rounded">add</span> Agregar</button>
    </div>
    <div class="adm-section-body">
      <div class="adm-table-wrap">
        <table class="adm-table">
          <thead><tr><th>Documento</th><th>Ruta / URL</th><th>Acciones</th></tr></thead>
          <tbody>
            <?php foreach (['Evaluación cualitativa','Carta compromiso','Reporte bimestral 1','Formato de evaluación'] as $d): ?>
            <tr>
              <td style="font-weight:600"><?= htmlspecialchars($d) ?></td>
              <td style="color:var(--tsj-gray-500);font-size:12.5px">assets/docs/servicio-social/<?= strtolower(str_replace(' ','-',$d)) ?>.pdf</td>
              <td class="actions">
                <button class="adm-btn adm-btn--ghost adm-btn--sm pending-db"><span class="material-symbols-rounded">upload</span> Reemplazar</button>
                <button class="adm-btn adm-btn--danger adm-btn--sm pending-db"><span class="material-symbols-rounded">delete</span></button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_end.php'; ?>
