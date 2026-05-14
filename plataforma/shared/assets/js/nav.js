(function () {
    'use strict';

    var btn   = document.getElementById('tsj-menu-btn');
    var panel = document.getElementById('tsj-menu-panel');
    var icon  = document.getElementById('tsj-menu-icon');

    if (!btn || !panel) return;

    /* Detectar base URL desde currentScript (más robusto que buscar por src) */
    var base = '';
    var self = document.currentScript;
    if (self && self.src) {
        base = self.src.replace('/shared/assets/js/nav.js', '');
    } else {
        /* Fallback: buscar en todos los scripts */
        var scripts = document.querySelectorAll('script[src]');
        for (var i = 0; i < scripts.length; i++) {
            var src = scripts[i].getAttribute('src');
            if (src && src.indexOf('/shared/assets/js/nav.js') !== -1) {
                base = src.replace('/shared/assets/js/nav.js', '');
                break;
            }
        }
    }

    function openPanel() {
        panel.classList.add('active');
        btn.setAttribute('aria-expanded', 'true');
        btn.setAttribute('aria-label', 'Cerrar menú');
        if (icon) icon.src = base + '/shared/assets/img/close.svg';
        /* Mover foco al primer item del panel */
        var firstItem = panel.querySelector('a');
        if (firstItem) firstItem.focus();
    }

    function closePanel() {
        panel.classList.remove('active');
        btn.setAttribute('aria-expanded', 'false');
        btn.setAttribute('aria-label', 'Abrir menú');
        if (icon) icon.src = base + '/shared/assets/img/menu.svg';
    }

    btn.addEventListener('click', function () {
        if (panel.classList.contains('active')) {
            closePanel();
        } else {
            openPanel();
        }
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
        link.addEventListener('click', function () {
            closePanel();
        });
    });

    /* Resetear estado ARIA si el viewport pasa a desktop */
    if (window.matchMedia) {
        var mq = window.matchMedia('(min-width: 821px)');
        mq.addEventListener('change', function (e) {
            if (e.matches) closePanel();
        });
    }
})();
