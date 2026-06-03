(function () {
    'use strict';

    var btn   = document.getElementById('tsj-menu-btn');
    var panel = document.getElementById('tsj-menu-panel');

    if (!btn || !panel) return;

    /* Ícono Material Symbols dentro del botón */
    var iconSpan = btn.querySelector('.material-symbols-rounded');

    function openPanel() {
        panel.classList.add('active');
        btn.setAttribute('aria-expanded', 'true');
        btn.setAttribute('aria-label', 'Cerrar menú');
        if (iconSpan) iconSpan.textContent = 'close';
        var firstItem = panel.querySelector('a');
        if (firstItem) firstItem.focus();
    }

    function closePanel() {
        panel.classList.remove('active');
        btn.setAttribute('aria-expanded', 'false');
        btn.setAttribute('aria-label', 'Abrir menú');
        if (iconSpan) iconSpan.textContent = 'menu';
    }

    btn.addEventListener('click', function () {
        panel.classList.contains('active') ? closePanel() : openPanel();
    });

    /* Cerrar al hacer click fuera */
    document.addEventListener('click', function (e) {
        if (!panel.contains(e.target) && !btn.contains(e.target)) {
            closePanel();
        }
    });

    /* Cerrar con ESC */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && panel.classList.contains('active')) {
            closePanel();
            btn.focus();
        }
    });

    /* Cerrar al navegar a cualquier link del panel */
    panel.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closePanel);
    });

    /* Resetear si el viewport pasa a desktop */
    if (window.matchMedia) {
        window.matchMedia('(min-width: 821px)').addEventListener('change', function (e) {
            if (e.matches) closePanel();
        });
    }
})();
