(function () {
  'use strict';

  function esc(str) {
    var d = document.createElement('div');
    d.textContent = String(str == null ? '' : str);
    return d.innerHTML;
  }

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
      .catch(function (err) {
        callback(err, null);
      });
  }

  function filtrarLibros() {
    var searchEl = document.getElementById('searchInput');
    var fieldEl  = document.getElementById('filterField');
    var tbody    = document.querySelector('#tablaLibros tbody');
    if (!tbody) return;

    var text  = searchEl ? searchEl.value.toLowerCase() : '';
    var field = fieldEl  ? fieldEl.value                : 'all';
    tbody.innerHTML = '';

    if (!window.todosLosLibros) return;

    var camposBusqueda = ['titulo', 'autor', 'folio'];
    var filtrados = window.todosLosLibros.filter(function (l) {
      if (field === 'all') {
        return camposBusqueda.some(function (k) {
          return l[k] && l[k].toString().toLowerCase().includes(text);
        });
      }
      return l[field] && l[field].toString().toLowerCase().includes(text);
    });

    if (filtrados.length === 0) {
      tbody.innerHTML =
        '<tr><td colspan="5" class="empty-state">' +
        '<i class="fas fa-book-open d-block" aria-hidden="true"></i>' +
        'No se encontraron libros</td></tr>';
      return;
    }

    filtrados.forEach(function (l) {
      var ocupado = l.ocupado > 0;
      var url = 'solicitudDeLibros.php?titulo=' + encodeURIComponent(l.titulo) +
                '&codigo=' + encodeURIComponent(l.folio);

      var fila = document.createElement('tr');

      var tdId  = document.createElement('td'); tdId.className = 'text-muted'; tdId.textContent = l.id;
      var tdCod = document.createElement('td');
      var badge = document.createElement('span'); badge.className = 'badge-code'; badge.textContent = l.folio;
      tdCod.appendChild(badge);

      var tdTit  = document.createElement('td'); tdTit.className = 'text-start';
      var divTit = document.createElement('div'); divTit.className = 'book-title'; divTit.textContent = l.titulo;
      var spanSt = document.createElement('span');
      spanSt.className = 'status-badge ' + (ocupado ? 'status-busy' : 'status-available');
      spanSt.textContent = ocupado ? 'En Préstamo' : 'Disponible';
      tdTit.appendChild(divTit); tdTit.appendChild(spanSt);

      var tdAut = document.createElement('td'); tdAut.textContent = l.autor;
      var tdAcc = document.createElement('td');

      if (ocupado) {
        var btn = document.createElement('button');
        btn.className = 'btn-disabled'; btn.disabled = true; btn.textContent = 'Ocupado';
        btn.setAttribute('aria-label', 'Libro ' + l.titulo + ' no disponible');
        tdAcc.appendChild(btn);
      } else {
        var a = document.createElement('a');
        a.href = url; a.className = 'btn btn-gold btn-sm';
        a.textContent = 'Solicitar';
        a.setAttribute('aria-label', 'Solicitar préstamo de ' + l.titulo);
        tdAcc.appendChild(a);
      }

      fila.appendChild(tdId); fila.appendChild(tdCod); fila.appendChild(tdTit);
      fila.appendChild(tdAut); fila.appendChild(tdAcc);
      tbody.appendChild(fila);
    });
  }

  // Carga inicial
  cargarLibros(function (err, data) {
    if (err) {
      var tbody = document.querySelector('#tablaLibros tbody');
      if (tbody) {
        tbody.innerHTML =
          '<tr><td colspan="5" class="empty-state">' +
          '<i class="fas fa-exclamation-circle d-block" aria-hidden="true"></i>' +
          'Error al cargar los libros. Intenta recargar la página.' +
          '</td></tr>';
      }
      return;
    }
    window.todosLosLibros = data;
    filtrarLibros();
  });

  // Refresco automático cada 60s — solo actualiza si el usuario no está escribiendo
  setInterval(function () {
    var searchEl = document.getElementById('searchInput');
    var escribiendo = searchEl && document.activeElement === searchEl;
    if (escribiendo) return;

    cargarLibros(function (err, data) {
      if (err || !data) return;
      window.todosLosLibros = data;
      filtrarLibros();
    });
  }, 60000);

  window.filtrarLibros = filtrarLibros;
})();
