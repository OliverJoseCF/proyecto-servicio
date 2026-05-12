(function () {
    var btn   = document.getElementById('tsj-menu-btn');
    var panel = document.getElementById('tsj-menu-panel');
    var icon  = document.getElementById('tsj-menu-icon');

    if (!btn || !panel) return;

    // Detecta la ruta base de los assets compartidos desde la URL del script
    var scripts = document.querySelectorAll('script[src]');
    var base = '';
    for (var i = 0; i < scripts.length; i++) {
        var src = scripts[i].getAttribute('src');
        if (src && src.indexOf('/shared/assets/js/nav.js') !== -1) {
            base = src.replace('/shared/assets/js/nav.js', '');
            break;
        }
    }

    btn.addEventListener('click', function () {
        var open = panel.classList.toggle('active');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (icon) {
            icon.src = open
                ? base + '/shared/assets/img/close.svg'
                : base + '/shared/assets/img/menu.svg';
        }
    });

    document.addEventListener('click', function (e) {
        if (!panel.contains(e.target) && !btn.contains(e.target)) {
            panel.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
            if (icon) icon.src = base + '/shared/assets/img/menu.svg';
        }
    });
})();
