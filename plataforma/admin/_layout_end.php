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
      if (form.dataset.reload !== undefined) {
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
  adminFetch(modulo, { _csrf: csrf, accion: accion, id: id })
    .then(function(json) {
      if (json.ok && rowId) {
        var row = document.getElementById(rowId);
        if (row) row.remove();
      }
    });
}

/* ── Sortable para .adm-list-editor (mouse + touch) ─────────────
   Usa eventos de mouse directamente — más confiable que HTML5 drag.
   El handle .adm-list-item-drag inicia el arrastre.
─────────────────────────────────────────────────────────────── */
(function () {
  var state = null; // { ghost, item, editor, offsetY, placeholder }

  function getItemAt(editor, y) {
    var items = Array.from(editor.querySelectorAll('.adm-list-item'));
    for (var i = 0; i < items.length; i++) {
      var r = items[i].getBoundingClientRect();
      if (y < r.top + r.height / 2) return items[i];
    }
    return null; // insertar al final
  }

  function onMouseMove(e) {
    if (!state) return;
    var y = e.clientY;

    // Mover el ghost
    state.ghost.style.top = (y - state.offsetY + window.scrollY) + 'px';

    // Mover el placeholder dentro del editor
    var target = getItemAt(state.editor, y);
    if (target) {
      state.editor.insertBefore(state.placeholder, target);
    } else {
      state.editor.appendChild(state.placeholder);
    }
  }

  function onMouseUp() {
    if (!state) return;

    // Insertar el ítem real donde está el placeholder
    state.editor.insertBefore(state.item, state.placeholder);
    state.placeholder.remove();
    state.ghost.remove();

    // Restaurar apariencia
    state.item.style.opacity = '';
    state.item.style.pointerEvents = '';

    document.removeEventListener('mousemove', onMouseMove);
    document.removeEventListener('mouseup',   onMouseUp);
    document.body.style.userSelect = '';
    state = null;
  }

  // Delegar en document — funciona aunque el DOM cambie
  document.addEventListener('mousedown', function (e) {
    var handle = e.target.closest('.adm-list-item-drag');
    if (!handle) return;
    var item   = handle.closest('.adm-list-item');
    var editor = item  && item.closest('.adm-list-editor');
    if (!item || !editor) return;

    e.preventDefault();
    document.body.style.userSelect = 'none';

    var rect    = item.getBoundingClientRect();
    var offsetY = e.clientY - rect.top;

    // Ghost: clon visual que sigue al cursor
    var ghost = item.cloneNode(true);
    ghost.style.cssText =
      'position:fixed;left:' + rect.left + 'px;top:' + rect.top + 'px;' +
      'width:' + rect.width + 'px;opacity:.85;pointer-events:none;z-index:9999;' +
      'box-shadow:0 8px 24px rgba(20,10,80,.18);border-radius:8px;' +
      'background:#fff;border:1.5px solid var(--tsj-blue)';
    document.body.appendChild(ghost);

    // Placeholder: mantiene el espacio en la lista
    var ph = document.createElement('div');
    ph.className = 'adm-list-item';
    ph.style.cssText =
      'opacity:0;pointer-events:none;height:' + rect.height + 'px;' +
      'box-sizing:border-box;border:2px dashed var(--tsj-blue);background:var(--tsj-blue-50)';
    editor.insertBefore(ph, item);

    // Ocultar el ítem original
    item.style.opacity       = '0';
    item.style.pointerEvents = 'none';

    state = { ghost: ghost, item: item, editor: editor, offsetY: offsetY, placeholder: ph };

    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup',   onMouseUp);
  });
})();
</script>
</body>
</html>
