// js/modal.js  —  Lógica del modal de horarios
(function () {
  'use strict';

  var modal      = document.getElementById('modalHorario');
  var contentDiv = document.getElementById('modalContent');
  var closeBtn   = modal ? modal.querySelector('.modal-close') : null;
  var overlay    = modal ? modal.querySelector('.modal-overlay') : null;

  if (!modal || !contentDiv) return;

  function openModal(url) {
    // PDF: abrir en nueva pestaña — más confiable en todos los navegadores
    if (/\.pdf($|\?)/i.test(url)) {
      window.open(url, '_blank', 'noopener,noreferrer');
      return;
    }

    // Imagen: mostrar en modal
    contentDiv.innerHTML = '';
    var img = document.createElement('img');
    img.src   = url;
    img.alt   = 'Horario del maestro';
    img.style.cssText = 'width:100%;height:auto;display:block;border-radius:8px';
    img.onerror = function () {
      // Si la imagen falla, abrir en nueva pestaña como fallback
      window.open(url, '_blank', 'noopener,noreferrer');
      closeModal();
    };
    contentDiv.appendChild(img);

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    if (closeBtn) closeBtn.focus();
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    contentDiv.innerHTML = '';
    document.body.style.overflow = '';
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
