<?php
require_once __DIR__ . '/../../shared/lib/auth.php';

$flash_ok    = $_SESSION['flash_ok']    ?? null; unset($_SESSION['flash_ok']);
$flash_error = $_SESSION['flash_error'] ?? null; unset($_SESSION['flash_error']);

$tsj_module    = 'biblioteca';
$tsj_title     = 'Biblioteca — Catálogo de Libros';
$tsj_extra_css = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Outfit:wght@300;400;500;600&display=swap',
    'assets/css/buscar.css',
];
require_once __DIR__ . '/../../shared/header.php';
?>

  <div class="grain"></div>

  <?php if ($flash_ok): ?>
  <div class="container" style="padding-top:16px;">
    <div class="tsj-alert tsj-alert--success" role="alert"><?= htmlspecialchars($flash_ok, ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <?php elseif ($flash_error): ?>
  <div class="container" style="padding-top:16px;">
    <div class="tsj-alert tsj-alert--error" role="alert"><?= htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <?php endif; ?>

  <!-- Encabezado de página -->
  <div class="page-header">
    <div class="page-header-line"></div>
    <h1>Catálogo de <span class="gold">Libros</span></h1>
    <p class="page-header-sub">Busca y solicita préstamos del acervo bibliotecario</p>
  </div>

  <div class="container pb-5">

    <!-- Búsqueda -->
    <div class="search-card">
      <div class="row g-3 align-items-end">
        <div class="col-md-3 col-sm-4">
          <label class="search-label">Filtrar por</label>
          <select id="filterField" class="form-select">
            <option value="all">Todos los campos</option>
            <option value="titulo">Título</option>
            <option value="autor">Autor</option>
          </select>
        </div>
        <div class="col-md-9 col-sm-8">
          <label class="search-label">Buscar libro</label>
          <input type="text" id="searchInput" class="form-control" placeholder="Escriba título o autor..." onkeyup="filtrarLibros()">
        </div>
      </div>
    </div>

    <!-- Tabla -->
    <div class="table-wrapper">
      <div class="table-responsive">
        <table class="table align-middle text-center" id="tablaLibros">
          <thead>
            <tr>
              <th>ID</th><th>Código</th><th class="text-start">Título / Estado</th><th>Autor</th><th>Acción</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

  </div>

  <script>
    function esc(str) {
        const d = document.createElement('div');
        d.textContent = String(str ?? '');
        return d.innerHTML;
    }

    window.onload = function() {
        fetch('procesos/obtenerLibros.php')
            .then(res => res.json())
            .then(data => {
                if(data.error) throw new Error(esc(data.error));
                window.todosLosLibros = data;
                filtrarLibros();
            })
            .catch(err => {
                document.querySelector("#tablaLibros tbody").innerHTML =
                    '<tr><td colspan="5" class="empty-state"><i class="fas fa-exclamation-circle d-block"></i>Error al cargar los libros.</td></tr>';
            });
    };

    function filtrarLibros() {
        const text  = document.getElementById('searchInput').value.toLowerCase();
        const field = document.getElementById('filterField').value;
        const tbody = document.querySelector("#tablaLibros tbody");
        tbody.innerHTML = "";
        if (!window.todosLosLibros) return;

        const filtrados = window.todosLosLibros.filter(l => {
            if (field === 'all') return Object.values(l).some(v => v && v.toString().toLowerCase().includes(text));
            return l[field] && l[field].toString().toLowerCase().includes(text);
        });

        if (filtrados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="empty-state"><i class="fas fa-book-open d-block"></i>No se encontraron libros</td></tr>';
            return;
        }

        filtrados.forEach(l => {
            const ocupado = l.ocupado > 0;
            const url     = 'solicitudDeLibros.php?titulo=' + encodeURIComponent(l.titulo) + '&codigo=' + encodeURIComponent(l.folio);

            const fila = document.createElement("tr");

            const tdId    = document.createElement("td"); tdId.className = "text-muted"; tdId.textContent = l.id;
            const tdCod   = document.createElement("td"); const badge = document.createElement("span"); badge.className = "badge-code"; badge.textContent = l.folio; tdCod.appendChild(badge);
            const tdTit   = document.createElement("td"); tdTit.className = "text-start";
            const divTit  = document.createElement("div"); divTit.className = "book-title"; divTit.textContent = l.titulo;
            const spanSt  = document.createElement("span"); spanSt.className = "status-badge " + (ocupado ? "status-busy" : "status-available"); spanSt.textContent = ocupado ? "En Préstamo" : "Disponible";
            tdTit.appendChild(divTit); tdTit.appendChild(spanSt);
            const tdAut   = document.createElement("td"); tdAut.textContent = l.autor;
            const tdAcc   = document.createElement("td");
            if (ocupado) {
                const btn = document.createElement("button"); btn.className = "btn-disabled"; btn.disabled = true; btn.textContent = "Ocupado"; tdAcc.appendChild(btn);
            } else {
                const a = document.createElement("a"); a.href = url; a.className = "btn btn-gold btn-sm"; a.textContent = "Solicitar"; tdAcc.appendChild(a);
            }
            fila.appendChild(tdId); fila.appendChild(tdCod); fila.appendChild(tdTit); fila.appendChild(tdAut); fila.appendChild(tdAcc);
            tbody.appendChild(fila);
        });
    }
  </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
