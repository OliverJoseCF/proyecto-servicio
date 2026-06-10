(function () {
  'use strict';

  var PAGINA_SIZE = 30; // libros visibles por "página"
  var paginaActual = 1;
  var librosFiltrados = [];

  function cargarLibros(callback) {
    fetch('procesos/obtenerLibros.php')
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      })
      .then(function (data) {
        if (data.error) throw new Error(data.error);
        callback(null, data);
      })
      .catch(function (err) { callback(err, null); });
  }

  function crearCard(l) {
    var ocupado = l.ocupado > 0;
    var url = 'solicitudDeLibros.php?titulo=' + encodeURIComponent(l.titulo) +
              '&codigo=' + encodeURIComponent(l.folio);

    var card = document.createElement('div');
    card.className = 'bib-card';

    var stripe = document.createElement('div');
    stripe.className = 'bib-card-stripe bib-card-stripe--' + (ocupado ? 'busy' : 'ok');
    card.appendChild(stripe);

    var body = document.createElement('div');
    body.className = 'bib-card-body';

    var codigo = document.createElement('span');
    codigo.className = 'bib-card-codigo';
    codigo.textContent = l.folio || '—';

    var titulo = document.createElement('div');
    titulo.className = 'bib-card-titulo';
    titulo.textContent = l.titulo || '—';

    var autor = document.createElement('div');
    autor.className = 'bib-card-autor';
    autor.textContent = l.autor ? 'Autor: ' + l.autor : '';

    body.appendChild(codigo);
    body.appendChild(titulo);
    if (l.autor) body.appendChild(autor);
    card.appendChild(body);

    var footer = document.createElement('div');
    footer.className = 'bib-card-footer';

    var status = document.createElement('span');
    status.className = 'bib-status bib-status--' + (ocupado ? 'busy' : 'ok');
    status.textContent = ocupado ? 'En préstamo' : 'Disponible';
    footer.appendChild(status);

    if (ocupado) {
      var btnDis = document.createElement('span');
      btnDis.className = 'bib-btn--disabled';
      btnDis.textContent = 'No disponible';
      footer.appendChild(btnDis);
    } else {
      var a = document.createElement('a');
      a.href = url;
      a.className = 'bib-btn';
      a.setAttribute('aria-label', 'Solicitar préstamo de ' + l.titulo);
      a.innerHTML = '<span class="material-symbols-rounded" style="font-size:15px">book</span>Solicitar';
      footer.appendChild(a);
    }

    card.appendChild(footer);
    return card;
  }

  function renderPagina() {
    var grid = document.getElementById('tablaLibros');
    var paginador = document.getElementById('bib-paginador');
    if (!grid) return;

    // Limpiar solo las cards, no el paginador
    Array.from(grid.querySelectorAll('.bib-card, .bib-empty')).forEach(function(el) { el.remove(); });

    if (librosFiltrados.length === 0) {
      var empty = document.createElement('div');
      empty.className = 'bib-empty';
      var searchEl = document.getElementById('searchInput');
      var text = searchEl ? searchEl.value.trim() : '';
      empty.innerHTML =
        '<span class="material-symbols-rounded">search_off</span>' +
        '<p>' + (text ? 'No se encontraron libros para "' + text + '"' : 'Sin libros en catálogo') + '</p>';
      grid.insertBefore(empty, paginador);
      if (paginador) paginador.style.display = 'none';
      return;
    }

    var inicio = (paginaActual - 1) * PAGINA_SIZE;
    var fin    = Math.min(inicio + PAGINA_SIZE, librosFiltrados.length);
    var frag   = document.createDocumentFragment();

    for (var i = inicio; i < fin; i++) {
      frag.appendChild(crearCard(librosFiltrados[i]));
    }
    grid.insertBefore(frag, paginador);

    // Actualizar paginador
    if (paginador) {
      var totalPags = Math.ceil(librosFiltrados.length / PAGINA_SIZE);
      if (totalPags <= 1) {
        paginador.style.display = 'none';
      } else {
        paginador.style.display = '';
        document.getElementById('bib-pag-info').textContent =
          (inicio + 1) + '–' + fin + ' de ' + librosFiltrados.length + ' libros';
        document.getElementById('bib-pag-prev').disabled = paginaActual <= 1;
        document.getElementById('bib-pag-next').disabled = paginaActual >= totalPags;
      }
    }
  }

  function filtrarLibros() {
    var searchEl = document.getElementById('searchInput');
    var fieldEl  = document.getElementById('filterField');
    var text  = searchEl ? searchEl.value.toLowerCase() : '';
    var field = fieldEl  ? fieldEl.value : 'all';

    if (!window.todosLosLibros) return;

    var camposBusqueda = ['titulo', 'autor', 'folio'];
    librosFiltrados = window.todosLosLibros.filter(function (l) {
      if (field === 'all') {
        return camposBusqueda.some(function (k) {
          return l[k] && l[k].toString().toLowerCase().includes(text);
        });
      }
      return l[field] && l[field].toString().toLowerCase().includes(text);
    });

    paginaActual = 1; // al buscar, volver a página 1
    renderPagina();
  }

  function irPagina(delta) {
    var totalPags = Math.ceil(librosFiltrados.length / PAGINA_SIZE);
    paginaActual = Math.max(1, Math.min(paginaActual + delta, totalPags));
    renderPagina();
    // Scroll suave al inicio del grid
    var grid = document.getElementById('tablaLibros');
    if (grid) grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // Carga inicial
  cargarLibros(function (err, data) {
    var grid = document.getElementById('tablaLibros');
    if (err) {
      if (grid) grid.innerHTML =
        '<div class="bib-empty">' +
        '<span class="material-symbols-rounded">error</span>' +
        '<p>Error al cargar los libros. Intenta recargar la página.</p></div>';
      return;
    }
    window.todosLosLibros = data;
    librosFiltrados = data;
    renderPagina();
  });

  // Refresco cada 60s si el usuario no está escribiendo y la pestaña es visible
  setInterval(function () {
    if (document.hidden) return;
    var searchEl = document.getElementById('searchInput');
    if (searchEl && document.activeElement === searchEl) return;
    cargarLibros(function (err, data) {
      if (err || !data) return;
      window.todosLosLibros = data;
      filtrarLibros();
    });
  }, 60000);

  window.filtrarLibros = filtrarLibros;
  window.irPagina      = irPagina;
})();
