    </div><!-- /adm-content -->
  </div><!-- /adm-main -->
</div><!-- /adm-wrap -->

<!-- Toast de "pendiente BD" -->
<div class="adm-toast" id="adm-toast">
  <span class="material-symbols-rounded" aria-hidden="true">construction</span>
  <span id="adm-toast-msg">Pendiente: requiere conexión a base de datos</span>
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
  // Botones: clase .adm-tab con data-tab-group
  document.querySelectorAll('.adm-tab[data-tab-group="' + group + '"]').forEach(function(el) {
    el.classList.remove('active');
  });
  // Paneles: clase .adm-tab-panel con data-tab-group
  document.querySelectorAll('.adm-tab-panel[data-tab-group="' + group + '"]').forEach(function(el) {
    el.classList.remove('active');
  });
  var btn   = document.querySelector('.adm-tab[data-tab-group="' + group + '"][data-tab="' + tab + '"]');
  var panel = document.querySelector('.adm-tab-panel[data-tab-group="' + group + '"][data-tab="' + tab + '"]');
  if (btn)   btn.classList.add('active');
  if (panel) panel.classList.add('active');
}

/* ── Toast ── */
function showToast(msg) {
  var t = document.getElementById('adm-toast');
  var m = document.getElementById('adm-toast-msg');
  m.textContent = msg || 'Pendiente: requiere conexión a base de datos';
  t.classList.add('show');
  setTimeout(function() { t.classList.remove('show'); }, 3200);
}

/* Intercept forms marcados como "pending-db" */
document.addEventListener('submit', function(e) {
  if (e.target.classList.contains('pending-db')) {
    e.preventDefault();
    showToast('Pendiente: requiere conexión a base de datos');
  }
});

/* Intercept botones marcados como "pending-db" */
document.addEventListener('click', function(e) {
  var btn = e.target.closest('.pending-db');
  if (btn) {
    e.preventDefault();
    showToast(btn.dataset.toast || 'Pendiente: requiere conexión a base de datos');
  }
});
</script>
</body>
</html>
