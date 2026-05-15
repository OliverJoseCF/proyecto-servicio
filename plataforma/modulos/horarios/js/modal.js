// js/modal.js  —  Lógica del modal de horarios (compartida)
(function () {
  'use strict';

  var modal      = document.getElementById('modalHorario');
  var contentDiv = document.getElementById('modalContent');
  var closeBtn   = modal ? modal.querySelector('.modal-close') : null;
  var overlay    = modal ? modal.querySelector('.modal-overlay') : null;

  if (!modal || !contentDiv) return;

  function openModal(url) {
    contentDiv.innerHTML = '';

    if (/\.pdf$/i.test(url)) {
      var obj   = document.createElement('object');
      obj.data  = url;
      obj.type  = 'application/pdf';
      obj.width = '100%';
      obj.height = '600px';
      obj.title = 'Horario del maestro (PDF)';
      /* Fallback para navegadores sin visor PDF */
      var fallback = document.createElement('p');
      fallback.style.padding = '1rem';
      var fallbackText = document.createTextNode('No se pudo mostrar el PDF. ');
      var fallbackLink = document.createElement('a');
      fallbackLink.href = url;
      fallbackLink.target = '_blank';
      fallbackLink.rel = 'noopener noreferrer';
      fallbackLink.textContent = 'Descargarlo aquí';
      fallback.appendChild(fallbackText);
      fallback.appendChild(fallbackLink);
      fallback.appendChild(document.createTextNode('.'));
      obj.appendChild(fallback);
      contentDiv.appendChild(obj);
    } else {
      var img = document.createElement('img');
      img.src = url;
      img.alt = 'Horario del maestro';
      img.style.width = '100%';
      contentDiv.appendChild(img);
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    /* Mover foco al botón de cierre para accesibilidad */
    if (closeBtn) closeBtn.focus();
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    contentDiv.innerHTML = '';
    document.body.style.overflow = '';

    /* Devolver el foco al enlace que abrió el modal */
    if (window._modalOpener) {
      window._modalOpener.focus();
      window._modalOpener = null;
    }
  }

  document.querySelectorAll('.open-modal').forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      window._modalOpener = link;
      openModal(this.href);
    });
  });

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (overlay)  overlay.addEventListener('click', closeModal);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('is-open')) {
      closeModal();
    }
  });
})();
