<?php
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
    window.onload = function() {
        fetch('procesos/obtenerLibros.php')
            .then(res => res.json())
            .then(data => {
                if(data.error) throw new Error(data.error);
                window.todosLosLibros = data;
                filtrarLibros();
            })
            .catch(err => {
                document.querySelector("#tablaLibros tbody").innerHTML =
                    `<tr><td colspan="5" class="empty-state"><i class="fas fa-exclamation-circle d-block"></i>Error: ${err.message}</td></tr>`;
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
            tbody.innerHTML = `<tr><td colspan="5" class="empty-state"><i class="fas fa-book-open d-block"></i>No se encontraron libros</td></tr>`;
            return;
        }

        filtrados.forEach(l => {
            const ocupado = l.ocupado > 0;
            const fila    = document.createElement("tr");
            const url     = `solicitudDeLibros.php?titulo=${encodeURIComponent(l.titulo)}&codigo=${encodeURIComponent(l.folio)}`;
            fila.innerHTML = `
                <td class="text-muted">${l.id}</td>
                <td><span class="badge-code">${l.folio}</span></td>
                <td class="text-start">
                    <div class="book-title">${l.titulo}</div>
                    <span class="status-badge ${ocupado ? 'status-busy' : 'status-available'}">
                        ${ocupado ? 'En Préstamo' : 'Disponible'}
                    </span>
                </td>
                <td>${l.autor}</td>
                <td>
                    ${ocupado
                        ? `<button class="btn-disabled" disabled>Ocupado</button>`
                        : `<a href="${url}" class="btn btn-gold btn-sm">Solicitar</a>`
                    }
                </td>`;
            tbody.appendChild(fila);
        });
    }
  </script>

<?php require_once __DIR__ . '/../../shared/footer.php'; ?>
