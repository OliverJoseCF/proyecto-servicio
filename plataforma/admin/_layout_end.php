    </div><!-- /adm-content -->
  </div><!-- /adm-main -->
</div><!-- /adm-wrap -->

<!-- Toast de notificaciones -->
<div class="adm-toast" id="adm-toast">
  <span class="material-symbols-rounded" aria-hidden="true" id="adm-toast-icon">check_circle</span>
  <span id="adm-toast-msg">OK</span>
</div>

<script>
/* ── Sidebar móvil ── */
function toggleSidebar() {
  document.getElementById('adm-sidebar').classList.toggle('open');
  document.getElementById('adm-overlay').classList.toggle('show');
}
function closeSidebar() {
  document.getElementById('adm-sidebar').classList.remove('open');
  document.getElementById('adm-overlay').classList.remove('show');
}

/* ── Tabs ── */
function showTab(group, tab) {
  document.querySelectorAll('.adm-tab[data-tab-group="' + group + '"]').forEach(function(el) {
    el.classList.remove('active');
  });
  document.querySelectorAll('.adm-tab-panel[data-tab-group="' + group + '"]').forEach(function(el) {
    el.classList.remove('active');
  });
  var btn   = document.querySelector('.adm-tab[data-tab-group="' + group + '"][data-tab="' + tab + '"]');
  var panel = document.querySelector('.adm-tab-panel[data-tab-group="' + group + '"][data-tab="' + tab + '"]');
  if (btn)   btn.classList.add('active');
  if (panel) panel.classList.add('active');
}

/* ── Toast ── */
function showToast(msg, tipo) {
  var t    = document.getElementById('adm-toast');
  var m    = document.getElementById('adm-toast-msg');
  var icon = document.getElementById('adm-toast-icon');
  m.textContent   = msg || 'OK';
  icon.textContent= tipo === 'error' ? 'error' : 'check_circle';
  t.style.background = tipo === 'error' ? '#ef4444' : '#22c55e';
  t.classList.add('show');
  setTimeout(function() { t.classList.remove('show'); }, 3500);
}

/* ── adminFetch: POST a admin/procesos/<modulo>.php ── */
function adminFetch(modulo, data) {
  var base = (document.querySelector('meta[name="plataforma-url"]')?.content || '/plataforma');
  var url  = base + '/admin/procesos/' + modulo + '.php';

  var form = new FormData();
  for (var k in data) form.append(k, data[k]);

  return fetch(url, { method: 'POST', body: form })
    .then(function(res) { return res.json(); })
    .then(function(json) {
      showToast(json.msg, json.ok ? 'ok' : 'error');
      return json;
    })
    .catch(function(err) {
      showToast('Error de red: ' + err.message, 'error');
      return { ok: false };
    });
}

/* ── Manejador global de formularios con data-proc ── */
document.addEventListener('submit', function(e) {
  var form = e.target;
  var proc = form.dataset.proc;
  if (!proc) return;
  e.preventDefault();

  var hasFile = form.querySelector('input[type="file"]');
  var data;

  if (hasFile) {
    // Si hay archivo, usar FormData directo
    data = new FormData(form);
    var base = (document.querySelector('meta[name="plataforma-url"]')?.content || '/plataforma');
    var url  = base + '/admin/procesos/' + proc + '.php';
    var btn  = form.querySelector('[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Guardando…'; }
    fetch(url, { method: 'POST', body: data })
      .then(function(r){ return r.json(); })
      .then(function(json){
        showToast(json.msg, json.ok ? 'ok' : 'error');
        if (json.ok) setTimeout(function(){ location.reload(); }, 1200);
      })
      .catch(function(err){ showToast('Error: ' + err.message, 'error'); })
      .finally(function(){ if (btn) { btn.disabled = false; btn.textContent = 'Guardar'; } });
    return;
  }

  // Sin archivo: recoger datos como objeto
  var obj = {};
  (new FormData(form)).forEach(function(v,k){ obj[k] = v; });

  var btn = form.querySelector('[type="submit"]');
  if (btn) { btn.disabled = true; }

  adminFetch(proc, obj).then(function(json) {
    if (json.ok) {
      // Si el form tiene data-reload, recargar la página
      if (form.dataset.reload !== undefined || form.classList.contains('form-materias')) {
        setTimeout(function(){ location.reload(); }, 900);
      }
    }
    if (btn) btn.disabled = false;
  });
});

/* ── Confirmar eliminación ── */
function confirmarEliminar(modulo, accion, id, rowId) {
  if (!confirm('¿Estás seguro de que deseas eliminar este elemento? Esta acción no se puede deshacer.')) return;
  // Obtener el CSRF del primer formulario de la página
  var csrfEl = document.querySelector('input[name="_csrf"]');
  var csrf   = csrfEl ? csrfEl.value : '';
  adminFetch(modulo, { csrf: csrf, accion: accion, id: id })
    .then(function(json) {
      if (json.ok && rowId) {
        var row = document.getElementById(rowId);
        if (row) row.remove();
      }
    });
}
</script>
</body>
</html>
